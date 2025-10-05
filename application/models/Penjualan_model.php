<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan_model extends CI_Model {

    private $header_table = 'penjualan_header';
    private $detail_table = 'penjualan_header_detail';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Master_barang_model');
        $this->load->model('Promo_model');
    }

    /**
     * Generate new transaction number
     */
    public function generate_transaction_number()
    {
        $date = date('Ymd');
        $prefix = 'TRX' . $date;
        
        // Get last transaction number for today
        $this->db->like('no_transaksi', $prefix, 'after');
        $this->db->order_by('no_transaksi', 'DESC');
        $this->db->limit(1);
        $last_transaction = $this->db->get($this->header_table)->row();
        
        if ($last_transaction) {
            // Extract sequence number and increment
            $last_sequence = (int) substr($last_transaction->no_transaksi, -3);
            $new_sequence = $last_sequence + 1;
        } else {
            $new_sequence = 1;
        }
        
        // Ensure we don't exceed 999
        if ($new_sequence > 999) {
            $new_sequence = 1;
        }
        
        // Format the transaction number
        $transaction_number = $prefix . str_pad($new_sequence, 3, '0', STR_PAD_LEFT);
        
        // Check if this transaction number already exists (rare case)
        $existing = $this->db->get_where($this->header_table, array('no_transaksi' => $transaction_number))->row();
        if ($existing) {
            // If it exists, increment and try again (up to 10 times)
            for ($i = 1; $i <= 10; $i++) {
                $new_sequence += 1;
                if ($new_sequence > 999) {
                    $new_sequence = 1;
                }
                $transaction_number = $prefix . str_pad($new_sequence, 3, '0', STR_PAD_LEFT);
                $existing = $this->db->get_where($this->header_table, array('no_transaksi' => $transaction_number))->row();
                if (!$existing) {
                    break;
                }
            }
        }
        
        return $transaction_number;
    }

    /**
     * Get all transactions with pagination
     */
    public function get_all($limit = null, $offset = null, $date_from = null, $date_to = null)
    {
        $this->db->select('h.*, COUNT(d.id) as total_items');
        $this->db->from($this->header_table . ' h');
        $this->db->join($this->detail_table . ' d', 'h.no_transaksi = d.no_transaksi', 'left');
        
        if ($date_from) {
            $this->db->where('h.tgl_transaksi >=', $date_from);
        }
        
        if ($date_to) {
            $this->db->where('h.tgl_transaksi <=', $date_to);
        }
        
        $this->db->group_by('h.no_transaksi');
        $this->db->order_by('h.tgl_transaksi', 'DESC');
        
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result();
    }

    /**
     * Get transaction by number
     */
    public function get_by_number($no_transaksi)
    {
        return $this->db->get_where($this->header_table, array('no_transaksi' => $no_transaksi))->row();
    }

    /**
     * Get transaction details
     */
    public function get_transaction_details($no_transaksi)
    {
        $this->db->select('d.*, b.nama_barang');
        $this->db->from($this->detail_table . ' d');
        $this->db->join('master_barang b', 'd.kode_barang = b.kode_barang');
        $this->db->where('d.no_transaksi', $no_transaksi);
        $this->db->order_by('d.id');
        
        return $this->db->get()->result();
    }

    /**
     * Get complete transaction data (header + details)
     */
    public function get_complete_transaction($no_transaksi)
    {
        $header = $this->get_by_number($no_transaksi);
        if (!$header) {
            return null;
        }
        
        $details = $this->get_transaction_details($no_transaksi);
        
        return array(
            'header' => $header,
            'details' => $details
        );
    }

    /**
     * Save transaction (header + details)
     */
    public function save_transaction($header_data, $details_data)
    {
        $this->db->trans_start();
        
        // Insert header
        $this->db->insert($this->header_table, $header_data);
        
        // Insert details
        foreach ($details_data as $detail) {
            $detail['no_transaksi'] = $header_data['no_transaksi'];
            $this->db->insert($this->detail_table, $detail);
        }
        
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }

    /**
     * Calculate transaction totals
     */
    public function calculate_totals($items, $kode_promo = null)
    {
        $total_bayar = 0;
        $calculated_items = array();
        
        // Calculate each item subtotal
        foreach ($items as $item) {
            $barang = $this->Master_barang_model->get_by_kode($item['kode_barang']);
            if (!$barang) {
                return array('error' => 'Barang dengan kode ' . $item['kode_barang'] . ' tidak ditemukan');
            }
            
            $harga = isset($item['harga']) ? $item['harga'] : $barang->harga;
            $qty = $item['qty'];
            $discount = isset($item['discount']) ? $item['discount'] : 0;
            
            $subtotal = ($harga * $qty) - $discount;
            $total_bayar += $subtotal;
            
            $calculated_items[] = array(
                'kode_barang' => $item['kode_barang'],
                'qty' => $qty,
                'harga' => $harga,
                'discount' => $discount,
                'subtotal' => $subtotal
            );
        }
        
        // Calculate PPN (10%)
        $ppn = intval($total_bayar * 0.1);
        
        // Validate promo (no discount calculation - promo is just a label)
        $promo_info = null;
        
        if (!empty($kode_promo)) {
            $promo_result = $this->Promo_model->validate_promo($kode_promo);
            if (isset($promo_result['error'])) {
                return array('error' => $promo_result['error']);
            }
            
            $promo_info = $promo_result['promo_info'];
        }
        
        // Calculate grand total (no promo discount since it's just a label)
        $grand_total = intval($total_bayar + $ppn);
        
        return array(
            'success' => true,
            'items' => $calculated_items,
            'total_bayar' => $total_bayar,
            'ppn' => $ppn,
            'discount_promo' => 0, // No promo discount - use item discounts
            'grand_total' => $grand_total,
            'promo_info' => $promo_info
        );
    }

    /**
     * Validate transaction data
     */
    public function validate_transaction($header_data, $details_data)
    {
        $errors = array();
        
        // Validate header
        if (empty($header_data['customer'])) {
            $errors[] = 'Nama customer harus diisi';
        }
        
        if (empty($details_data) || !is_array($details_data)) {
            $errors[] = 'Detail transaksi harus diisi';
        } else {
            // Validate details
            foreach ($details_data as $i => $detail) {
                if (empty($detail['kode_barang'])) {
                    $errors[] = 'Kode barang pada item ' . ($i + 1) . ' harus diisi';
                }
                
                if (empty($detail['qty']) || $detail['qty'] <= 0) {
                    $errors[] = 'Quantity pada item ' . ($i + 1) . ' harus lebih dari 0';
                }
                
                // Check if barang exists
                if (!empty($detail['kode_barang'])) {
                    $barang = $this->Master_barang_model->get_by_kode($detail['kode_barang']);
                    if (!$barang) {
                        $errors[] = 'Barang dengan kode ' . $detail['kode_barang'] . ' tidak ditemukan';
                    }
                }
            }
        }
        
        return $errors;
    }

    /**
     * Get sales report by date range
     */
    public function get_sales_report($date_from, $date_to)
    {
        $this->db->select('
            DATE(h.tgl_transaksi) as tanggal,
            COUNT(h.no_transaksi) as total_transaksi,
            SUM(h.total_bayar) as total_penjualan,
            SUM(h.ppn) as total_ppn,
            SUM(h.grand_total) as total_grand_total
        ');
        $this->db->from($this->header_table . ' h');
        $this->db->where('h.tgl_transaksi >=', $date_from);
        $this->db->where('h.tgl_transaksi <=', $date_to);
        $this->db->group_by('DATE(h.tgl_transaksi)');
        $this->db->order_by('DATE(h.tgl_transaksi)', 'DESC');
        
        return $this->db->get()->result();
    }

    /**
     * Get top selling products
     */
    public function get_top_products($limit = 10, $date_from = null, $date_to = null)
    {
        $this->db->select('
            d.kode_barang,
            b.nama_barang,
            b.harga,
            SUM(d.qty) as total_qty,
            SUM(d.subtotal) as total_penjualan
        ');
        $this->db->from($this->detail_table . ' d');
        $this->db->join('master_barang b', 'd.kode_barang = b.kode_barang');
        $this->db->join($this->header_table . ' h', 'd.no_transaksi = h.no_transaksi');
        
        if ($date_from) {
            $this->db->where('h.tgl_transaksi >=', $date_from);
        }
        
        if ($date_to) {
            $this->db->where('h.tgl_transaksi <=', $date_to);
        }
        
        $this->db->group_by('d.kode_barang');
        $this->db->order_by('total_qty', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }

    /**
     * Count transactions
     */
    public function count_transactions($date_from = null, $date_to = null)
    {
        if ($date_from) {
            $this->db->where('tgl_transaksi >=', $date_from);
        }
        
        if ($date_to) {
            $this->db->where('tgl_transaksi <=', $date_to);
        }
        
        return $this->db->count_all_results($this->header_table);
    }
}