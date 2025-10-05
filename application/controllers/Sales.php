<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Penjualan_model');
        $this->load->model('Master_barang_model');
        $this->load->model('Promo_model');
        $this->load->library('pagination');
        $this->load->library('form_validation');
        $this->load->library('pdf');
        $this->load->library('excel_export');
        // Security features are automatically available when csrf_protection is enabled in config
    }

    /**
     * Index - List all transactions
     */
    public function index()
    {
        $data['title'] = 'Daftar Transaksi Penjualan';
        
        // Get filter parameters
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        
        // Pagination config
        $config['base_url'] = base_url('sales/index');
        $config['total_rows'] = $this->Penjualan_model->count_transactions($date_from, $date_to);
        $config['per_page'] = 10;
        $config['uri_segment'] = 3;
        $config['reuse_query_string'] = TRUE;
        
        $this->pagination->initialize($config);
        
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data['transactions'] = $this->Penjualan_model->get_all($config['per_page'], $page, $date_from, $date_to);
        $data['pagination'] = $this->pagination->create_links();
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        
        $this->load->view('templates/header', $data);
        $this->load->view('sales/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Create new transaction
     */
    public function create()
    {
        $data['title'] = 'Transaksi Penjualan Baru';
        
        if ($this->input->post()) {
            $this->_handle_transaction_form();
        } else {
            $data['no_transaksi'] = $this->Penjualan_model->generate_transaction_number();
            $data['barang_dropdown'] = $this->Master_barang_model->get_dropdown();
            $data['promo_dropdown'] = $this->Promo_model->get_dropdown();
            
            $this->load->view('templates/header', $data);
            $this->load->view('sales/create', $data);
            $this->load->view('templates/footer');
        }
    }

    /**
     * View transaction detail
     */
    public function view($no_transaksi)
    {
        $data['title'] = 'Detail Transaksi';
        $transaction = $this->Penjualan_model->get_complete_transaction($no_transaksi);
        
        if (!$transaction) {
            show_404();
        }
        
        $data['transaction'] = $transaction;
        
        $this->load->view('templates/header', $data);
        $this->load->view('sales/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Print receipt (HTML version - kept for compatibility)
     */
    public function receipt($no_transaksi)
    {
        $transaction = $this->Penjualan_model->get_complete_transaction($no_transaksi);
        
        if (!$transaction) {
            show_404();
        }
        
        $data['transaction'] = $transaction;
        
        $this->load->view('sales/receipt', $data);
    }

    /**
     * Generate PDF receipt
     */
    public function receipt_pdf($no_transaksi)
    {
        $transaction = $this->Penjualan_model->get_complete_transaction($no_transaksi);
        
        if (!$transaction) {
            show_404();
        }
        
        // Generate PDF using our PDF library
        $this->pdf->generateReceipt($transaction, 'stream');
    }

    /**
     * Export sales report to Excel
     */
    public function export_excel()
    {
        // CSRF verification is automatically handled by CodeIgniter when csrf_protection is enabled
        $date_from = $this->input->get('date_from') ?: date('Y-m-01');
        $date_to = $this->input->get('date_to') ?: date('Y-m-d');
        
        // Get sales report data
        $sales_report = $this->Penjualan_model->get_sales_report($date_from, $date_to);
        
        // Calculate summary
        $total_transactions = $this->Penjualan_model->count_transactions($date_from, $date_to);
        $total_sales = 0;
        $total_ppn = 0;
        $total_grand = 0;
        
        foreach ($sales_report as $row) {
            $total_sales += $row->total_penjualan;
            $total_ppn += $row->total_ppn;
            $total_grand += $row->total_grand_total;
        }
        
        $summary = array(
            'total_transactions' => $total_transactions,
            'total_sales' => $total_sales,
            'total_ppn' => $total_ppn,
            'total_grand' => $total_grand
        );
        
        // Export to Excel
        $this->excel_export->exportSalesReport($sales_report, $summary, $date_from, $date_to);
    }

    /**
     * Export top products to Excel
     */
    public function export_top_products()
    {
        // CSRF verification is automatically handled by CodeIgniter when csrf_protection is enabled
        $date_from = $this->input->get('date_from') ?: date('Y-m-01');
        $date_to = $this->input->get('date_to') ?: date('Y-m-d');
        
        // Get top products data
        $top_products = $this->Penjualan_model->get_top_products(50, $date_from, $date_to); // Get top 50
        
        // Export to Excel
        $this->excel_export->exportTopProducts($top_products, $date_from, $date_to);
    }

    /**
     * Get CSRF token for AJAX requests
     */
    public function get_csrf_token()
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'csrf_token' => $this->security->get_csrf_hash()
            )));
    }

    /**
     * Calculate transaction totals via AJAX
     */
    public function calculate()
    {
        // For AJAX requests, CSRF verification is handled automatically
        $items = json_decode($this->input->post('items'), true);
        $kode_promo = $this->input->post('kode_promo');
        
        if (empty($items)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Data item tidak valid'
                )));
            return;
        }
        
        $result = $this->Penjualan_model->calculate_totals($items, $kode_promo);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    /**
     * Get sales report
     */
    public function report()
    {
        $data['title'] = 'Laporan Penjualan';
        
        $date_from = $this->input->get('date_from') ?: date('Y-m-01');
        $date_to = $this->input->get('date_to') ?: date('Y-m-d');
        
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['sales_report'] = $this->Penjualan_model->get_sales_report($date_from, $date_to);
        $data['top_products'] = $this->Penjualan_model->get_top_products(10, $date_from, $date_to);
        
        // Calculate totals
        $data['total_transactions'] = $this->Penjualan_model->count_transactions($date_from, $date_to);
        $total_sales = 0;
        $total_ppn = 0;
        $total_grand = 0;
        
        foreach ($data['sales_report'] as $row) {
            $total_sales += $row->total_penjualan;
            $total_ppn += $row->total_ppn;
            $total_grand += $row->total_grand_total;
        }
        
        $data['summary'] = array(
            'total_sales' => $total_sales,
            'total_ppn' => $total_ppn,
            'total_grand' => $total_grand
        );
        
        $this->load->view('templates/header', $data);
        $this->load->view('sales/report', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Handle transaction form submission
     */
    private function _handle_transaction_form()
    {
        $header_data = array(
            'no_transaksi' => $this->input->post('no_transaksi'),
            'tgl_transaksi' => date('Y-m-d'),
            'customer' => $this->input->post('customer'),
            'kode_promo' => $this->input->post('kode_promo')
        );
        
        // Parse items data
        $items_raw = json_decode($this->input->post('items_data'), true);
        
        if (empty($items_raw)) {
            $this->session->set_flashdata('error', 'Data item transaksi tidak valid');
            redirect('sales/create');
        }
        
        // Validate transaction
        $validation_errors = $this->Penjualan_model->validate_transaction($header_data, $items_raw);
        
        if (!empty($validation_errors)) {
            $this->session->set_flashdata('error', implode('<br>', $validation_errors));
            redirect('sales/create');
        }
        
        // Calculate totals
        $calculation = $this->Penjualan_model->calculate_totals($items_raw, $header_data['kode_promo']);
        
        if (isset($calculation['error'])) {
            $this->session->set_flashdata('error', $calculation['error']);
            redirect('sales/create');
        }
        
        // Complete header data
        $header_data['total_bayar'] = $calculation['total_bayar'];
        $header_data['ppn'] = $calculation['ppn'];
        $header_data['grand_total'] = $calculation['grand_total'];
        
        // Save transaction
        if ($this->Penjualan_model->save_transaction($header_data, $calculation['items'])) {
            $this->session->set_flashdata('success', 'Transaksi berhasil disimpan');
            redirect('sales/view/' . $header_data['no_transaksi']);
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan transaksi');
            redirect('sales/create');
        }
    }
}