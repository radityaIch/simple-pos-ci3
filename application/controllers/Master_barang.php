<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_barang extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Master_barang_model');
        $this->load->library('pagination');
        $this->load->library('form_validation');
    }

    /**
     * Index - List all barang with pagination
     */
    public function index()
    {
        $data['title'] = 'Master Barang';
        
        // Pagination config
        $config['base_url'] = base_url('master_barang/index');
        $config['total_rows'] = $this->Master_barang_model->count_all();
        $config['per_page'] = 10;
        $config['uri_segment'] = 3;
        
        $this->pagination->initialize($config);
        
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data['barang_list'] = $this->Master_barang_model->get_all($config['per_page'], $page);
        $data['pagination'] = $this->pagination->create_links();
        
        $this->load->view('templates/header', $data);
        $this->load->view('master_barang/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Search barang
     */
    public function search()
    {
        $keyword = $this->input->get('q');
        $data['title'] = 'Pencarian Barang: ' . $keyword;
        $data['keyword'] = $keyword;
        
        if (!empty($keyword)) {
            $data['barang_list'] = $this->Master_barang_model->search($keyword);
        } else {
            redirect('master_barang');
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('master_barang/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Add new barang
     */
    public function add()
    {
        $data['title'] = 'Tambah Barang';
        
        if ($this->input->post()) {
            $this->_handle_form('add');
        } else {
            $data['barang'] = (object) array(
                'kode_barang' => '',
                'nama_barang' => '',
                'harga' => ''
            );
            
            $this->load->view('templates/header', $data);
            $this->load->view('master_barang/form', $data);
            $this->load->view('templates/footer');
        }
    }

    /**
     * Edit barang
     */
    public function edit($kode_barang)
    {
        $data['title'] = 'Edit Barang';
        $data['barang'] = $this->Master_barang_model->get_by_kode($kode_barang);
        
        if (!$data['barang']) {
            show_404();
        }
        
        if ($this->input->post()) {
            $this->_handle_form('edit', $kode_barang);
        } else {
            $this->load->view('templates/header', $data);
            $this->load->view('master_barang/form', $data);
            $this->load->view('templates/footer');
        }
    }

    /**
     * View barang detail
     */
    public function view($kode_barang)
    {
        $data['title'] = 'Detail Barang';
        $data['barang'] = $this->Master_barang_model->get_by_kode($kode_barang);
        
        if (!$data['barang']) {
            show_404();
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('master_barang/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Delete barang
     */
    public function delete($kode_barang)
    {
        // Debug: Log request method and POST data
        log_message('debug', 'Delete request method: ' . $this->input->method());
        log_message('debug', 'Is POST: ' . ($this->input->post() ? 'true' : 'false'));
        log_message('debug', 'CSRF Token Name: ' . $this->security->get_csrf_token_name());
        log_message('debug', 'Posted CSRF Token Value: ' . ($this->input->post($this->security->get_csrf_token_name()) ?: 'NOT FOUND'));
        log_message('debug', 'Expected CSRF Token: ' . $this->security->get_csrf_hash());
        
        // Verify it's a POST request with CSRF protection enabled
        if ($this->input->method() !== 'post') {
            $this->session->set_flashdata('error', 'Aksi tidak valid - Bukan permintaan POST');
            redirect('master_barang');
            return;
        }
        
        // CodeIgniter automatically validates CSRF token when csrf_protection is TRUE
        // Proceed with deletion
        $barang = $this->Master_barang_model->get_by_kode($kode_barang);
        
        if (!$barang) {
            show_404();
        }
        
        if ($this->Master_barang_model->delete($kode_barang)) {
            $this->session->set_flashdata('success', 'Barang berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Barang tidak dapat dihapus karena sudah digunakan dalam transaksi');
        }
        
        redirect('master_barang');
    }

    /**
     * Get barang data for AJAX (used in transaction form)
     */
    public function get_barang_data($kode_barang)
    {
        $barang = $this->Master_barang_model->get_by_kode($kode_barang);
        
        if ($barang) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => true,
                    'data' => $barang
                )));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Barang tidak ditemukan'
                )));
        }
    }

    /**
     * Handle form submission
     */
    private function _handle_form($action, $kode_barang = null)
    {
        // CSRF verification is automatically handled by CodeIgniter when csrf_protection is enabled
        $data_input = array(
            'kode_barang' => $this->input->post('kode_barang'),
            'nama_barang' => $this->input->post('nama_barang'),
            'harga' => $this->input->post('harga')
        );
        
        // Validate data
        $is_update = ($action == 'edit');
        $errors = $this->Master_barang_model->validate($data_input, $is_update);
        
        if (empty($errors)) {
            if ($action == 'add') {
                $success = $this->Master_barang_model->insert($data_input);
                $message = $success ? 'Barang berhasil ditambahkan' : 'Gagal menambahkan barang';
            } else {
                $success = $this->Master_barang_model->update($kode_barang, $data_input);
                $message = $success ? 'Barang berhasil diperbarui' : 'Gagal memperbarui barang';
            }
            
            if ($success) {
                $this->session->set_flashdata('success', $message);
                redirect('master_barang');
            } else {
                $this->session->set_flashdata('error', $message);
            }
        } else {
            $this->session->set_flashdata('error', implode('<br>', $errors));
        }
        
        // Reload form with data
        $data['title'] = ($action == 'add') ? 'Tambah Barang' : 'Edit Barang';
        $data['barang'] = (object) $data_input;
        
        $this->load->view('templates/header', $data);
        $this->load->view('master_barang/form', $data);
        $this->load->view('templates/footer');
    }
}