<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Promo_model extends CI_Model {

    private $table = 'promo';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all promo
     */
    public function get_all()
    {
        $this->db->order_by('kode_promo', 'ASC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Get promo by kode_promo
     */
    public function get_by_kode($kode_promo)
    {
        return $this->db->get_where($this->table, array('kode_promo' => $kode_promo))->row();
    }

    /**
     * Get promo by kode (simplified)
     */
    public function get_active_promo($kode_promo)
    {
        return $this->get_by_kode($kode_promo);
    }

    /**
     * Insert new promo
     */
    public function insert($data)
    {
        // Check if kode_promo already exists
        if ($this->get_by_kode($data['kode_promo'])) {
            return false;
        }

        return $this->db->insert($this->table, $data);
    }

    /**
     * Update promo
     */
    public function update($kode_promo, $data)
    {
        $this->db->where('kode_promo', $kode_promo);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete promo
     */
    public function delete($kode_promo)
    {
        // Check if promo is used in any transaction
        $this->db->where('kode_promo', $kode_promo);
        $usage_count = $this->db->count_all_results('penjualan_header');
        
        if ($usage_count > 0) {
            return false; // Cannot delete if used in transactions
        }

        $this->db->where('kode_promo', $kode_promo);
        return $this->db->delete($this->table);
    }

    /**
     * Validate promo code (promo is just a label, no discount calculation)
     */
    public function validate_promo($kode_promo)
    {
        $promo = $this->get_active_promo($kode_promo);
        
        if (!$promo) {
            return array('error' => 'Kode promo tidak valid');
        }

        return array(
            'success' => true,
            'promo_info' => $promo
        );
    }

    /**
     * Calculate discount amount (deprecated - use individual item discounts in penjualan_header_detail)
     * @deprecated This method is deprecated. Discounts should be calculated from penjualan_header_detail.discount field
     */
    public function calculate_discount($kode_promo, $total_bayar)
    {
        // Return no discount since promo is just a label
        // Actual discounts are handled in penjualan_header_detail.discount field
        $promo = $this->get_active_promo($kode_promo);
        
        if (!$promo) {
            return array('error' => 'Kode promo tidak valid');
        }

        return array(
            'success' => true,
            'discount' => 0, // No promo discount calculation
            'promo_info' => $promo
        );
    }

    /**
     * Validate promo data
     */
    public function validate($data, $is_update = false)
    {
        $errors = array();

        // Validate kode_promo
        if (empty($data['kode_promo'])) {
            $errors[] = 'Kode promo harus diisi';
        } elseif (!$is_update && $this->get_by_kode($data['kode_promo'])) {
            $errors[] = 'Kode promo sudah ada';
        }

        // Validate nama_promo
        if (empty($data['nama_promo'])) {
            $errors[] = 'Nama promo harus diisi';
        }

        return $errors;
    }

    /**
     * Get promo for select dropdown
     */
    public function get_dropdown()
    {
        $this->db->select('kode_promo, nama_promo');
        $this->db->order_by('nama_promo', 'ASC');
        $result = $this->db->get($this->table)->result();
        
        $dropdown = array('' => 'Pilih Promo (Opsional)');
        foreach ($result as $row) {
            $dropdown[$row->kode_promo] = $row->nama_promo;
        }
        
        return $dropdown;
    }
}