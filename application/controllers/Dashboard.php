<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Penjualan_model');
        $this->load->model('Master_barang_model');
        $this->load->model('Promo_model');
    }

    /**
     * Dashboard - Main page with statistics
     */
    public function index()
    {
        $data['title'] = 'Dashboard - Simple POS';
        
        // Get today's statistics
        $today = date('Y-m-d');
        $data['today_transactions'] = $this->Penjualan_model->count_transactions($today, $today);
        $data['today_sales'] = $this->_get_today_sales();
        
        // Get this month's statistics
        $month_start = date('Y-m-01');
        $month_end = date('Y-m-t');
        $data['month_transactions'] = $this->Penjualan_model->count_transactions($month_start, $month_end);
        $data['month_sales'] = $this->_get_month_sales();
        
        // Get total counts
        $data['total_products'] = $this->Master_barang_model->count_all();
        $data['total_promos'] = count($this->Promo_model->get_all());
        
        // Get recent transactions
        $data['recent_transactions'] = $this->Penjualan_model->get_all(5);
        
        // Get top products this month
        $data['top_products'] = $this->Penjualan_model->get_top_products(5, $month_start, $month_end);
        
        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Get today's total sales
     */
    private function _get_today_sales()
    {
        $today = date('Y-m-d');
        $report = $this->Penjualan_model->get_sales_report($today, $today);
        return !empty($report) ? $report[0]->total_grand_total : 0;
    }

    /**
     * Get this month's total sales
     */
    private function _get_month_sales()
    {
        $month_start = date('Y-m-01');
        $month_end = date('Y-m-t');
        $report = $this->Penjualan_model->get_sales_report($month_start, $month_end);
        
        $total = 0;
        foreach ($report as $row) {
            $total += $row->total_grand_total;
        }
        
        return $total;
    }
}