<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_barang_model extends CI_Model {

    private $table = 'master_barang';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all barang with pagination
     */
    public function get_all($limit = null, $offset = null)
    {
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get($this->table)->result();
    }

    /**
     * Get barang by kode_barang
     */
    public function get_by_kode($kode_barang)
    {
        return $this->db->get_where($this->table, array('kode_barang' => $kode_barang))->row();
    }

    /**
     * Search barang by name or code
     */
    public function search($keyword)
    {
        $this->db->like('nama_barang', $keyword);
        $this->db->or_like('kode_barang', $keyword);
        return $this->db->get($this->table)->result();
    }

    /**
     * Get total count for pagination
     */
    public function count_all()
    {
        return $this->db->count_all($this->table);
    }

    /**
     * Insert new barang
     */
    public function insert($data)
    {
        // Check if kode_barang already exists
        if ($this->get_by_kode($data['kode_barang'])) {
            return false;
        }

        return $this->db->insert($this->table, $data);
    }

    /**
     * Update barang
     */
    public function update($kode_barang, $data)
    {
        $this->db->where('kode_barang', $kode_barang);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete barang
     */
    public function delete($kode_barang)
    {
        // Check if barang is used in any transaction
        $this->db->where('kode_barang', $kode_barang);
        $usage_count = $this->db->count_all_results('penjualan_header_detail');
        
        if ($usage_count > 0) {
            return false; // Cannot delete if used in transactions
        }

        $this->db->where('kode_barang', $kode_barang);
        return $this->db->delete($this->table);
    }

    /**
     * Validate barang data
     */
    public function validate($data, $is_update = false)
    {
        $errors = array();

        // Validate kode_barang
        if (empty($data['kode_barang'])) {
            $errors[] = 'Kode barang harus diisi';
        } elseif (!$is_update && $this->get_by_kode($data['kode_barang'])) {
            $errors[] = 'Kode barang sudah ada';
        }

        // Validate nama_barang
        if (empty($data['nama_barang'])) {
            $errors[] = 'Nama barang harus diisi';
        }

        // Validate harga
        if (empty($data['harga']) || !is_numeric($data['harga']) || $data['harga'] <= 0) {
            $errors[] = 'Harga harus berupa angka positif';
        }

        return $errors;
    }

    /**
     * Get barang for select dropdown
     */
    public function get_dropdown()
    {
        $this->db->select('kode_barang, nama_barang, harga');
        $this->db->order_by('nama_barang', 'ASC');
        $result = $this->db->get($this->table)->result();
        
        $dropdown = array();
        foreach ($result as $row) {
            $dropdown[$row->kode_barang] = $row->nama_barang . ' - Rp ' . number_format($row->harga, 0, ',', '.');
        }
        
        return $dropdown;
    }
}