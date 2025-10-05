<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Excel_export 
{
    protected $spreadsheet;
    protected $worksheet;
    
    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->worksheet = $this->spreadsheet->getActiveSheet();
    }
    
    /**
     * Export sales report to Excel
     */
    public function exportSalesReport($sales_data, $summary_data, $date_from, $date_to)
    {
        // Set document properties
        $this->spreadsheet->getProperties()
            ->setCreator('Simple POS CI3')
            ->setTitle('Laporan Penjualan')
            ->setSubject('Sales Report')
            ->setDescription('Sales report generated from Simple POS CI3')
            ->setKeywords('sales report excel export')
            ->setCategory('Report');
            
        // Set worksheet title
        $this->worksheet->setTitle('Laporan Penjualan');
        
        // Create header section
        $this->_createHeader($date_from, $date_to);
        
        // Create summary section
        $this->_createSummary($summary_data);
        
        // Create data table
        $this->_createDataTable($sales_data);
        
        // Auto-size columns
        $this->_autoSizeColumns();
        
        // Generate and download file
        $filename = 'laporan_penjualan_' . date('Y-m-d_H-i-s') . '.xlsx';
        $this->_downloadExcel($filename);
    }
    
    /**
     * Create header section
     */
    private function _createHeader($date_from, $date_to)
    {
        // Main title
        $this->worksheet->setCellValue('A1', 'LAPORAN PENJUALAN');
        $this->worksheet->mergeCells('A1:E1');
        $this->worksheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $this->worksheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Company info
        $this->worksheet->setCellValue('A2', 'SIMPLE POS CI3');
        $this->worksheet->mergeCells('A2:E2');
        $this->worksheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $this->worksheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Date range
        $this->worksheet->setCellValue('A3', 'Periode: ' . date('d/m/Y', strtotime($date_from)) . ' - ' . date('d/m/Y', strtotime($date_to)));
        $this->worksheet->mergeCells('A3:E3');
        $this->worksheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Generated date
        $this->worksheet->setCellValue('A4', 'Digenerate pada: ' . date('d/m/Y H:i:s'));
        $this->worksheet->mergeCells('A4:E4');
        $this->worksheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->worksheet->getStyle('A4')->getFont()->setSize(10);
    }
    
    /**
     * Create summary section
     */
    private function _createSummary($summary_data)
    {
        $row = 6;
        
        // Summary title
        $this->worksheet->setCellValue('A' . $row, 'RINGKASAN');
        $this->worksheet->mergeCells('A' . $row . ':E' . $row);
        $this->worksheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $this->worksheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Summary data
        $row++;
        $this->worksheet->setCellValue('A' . $row, 'Total Transaksi:');
        $this->worksheet->setCellValue('B' . $row, $summary_data['total_transactions'] ?? 0);
        $this->worksheet->setCellValue('C' . $row, 'Total Penjualan:');
        $this->worksheet->setCellValue('D' . $row, 'Rp ' . number_format($summary_data['total_sales'] ?? 0, 0, ',', '.'));
        
        $row++;
        $this->worksheet->setCellValue('A' . $row, 'Total PPN:');
        $this->worksheet->setCellValue('B' . $row, 'Rp ' . number_format($summary_data['total_ppn'] ?? 0, 0, ',', '.'));
        $this->worksheet->setCellValue('C' . $row, 'Grand Total:');
        $this->worksheet->setCellValue('D' . $row, 'Rp ' . number_format($summary_data['total_grand'] ?? 0, 0, ',', '.'));
        
        // Style summary section
        $summaryRange = 'A6:E' . $row;
        $this->worksheet->getStyle($summaryRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $this->worksheet->getStyle('A6:E6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE6E6FA');
    }
    
    /**
     * Create data table
     */
    private function _createDataTable($sales_data)
    {
        $row = 10;
        
        // Table headers
        $headers = ['Tanggal', 'Total Transaksi', 'Total Penjualan', 'PPN', 'Grand Total'];
        $col = 'A';
        foreach ($headers as $header) {
            $this->worksheet->setCellValue($col . $row, $header);
            $this->worksheet->getStyle($col . $row)->getFont()->setBold(true);
            $this->worksheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD3D3D3');
            $col++;
        }
        
        // Table data
        $row++;
        $startDataRow = $row;
        
        if (!empty($sales_data)) {
            foreach ($sales_data as $data) {
                $this->worksheet->setCellValue('A' . $row, date('d/m/Y', strtotime($data->tanggal)));
                $this->worksheet->setCellValue('B' . $row, $data->total_transaksi);
                $this->worksheet->setCellValue('C' . $row, 'Rp ' . number_format($data->total_penjualan, 0, ',', '.'));
                $this->worksheet->setCellValue('D' . $row, 'Rp ' . number_format($data->total_ppn, 0, ',', '.'));
                $this->worksheet->setCellValue('E' . $row, 'Rp ' . number_format($data->total_grand_total, 0, ',', '.'));
                
                // Align numbers to right
                $this->worksheet->getStyle('B' . $row . ':E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                $row++;
            }
        } else {
            $this->worksheet->setCellValue('A' . $row, 'Tidak ada data');
            $this->worksheet->mergeCells('A' . $row . ':E' . $row);
            $this->worksheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }
        
        // Style table
        $tableRange = 'A10:E' . ($row - 1);
        $this->worksheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Header row style
        $this->worksheet->getStyle('A10:E10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
    
    /**
     * Auto-size columns
     */
    private function _autoSizeColumns()
    {
        foreach (range('A', 'E') as $col) {
            $this->worksheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
    
    /**
     * Download Excel file
     */
    private function _downloadExcel($filename)
    {
        $writer = new Xlsx($this->spreadsheet);
        
        // Set headers for file download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        
        // Save to output
        $writer->save('php://output');
        exit;
    }
    
    /**
     * Export top products to Excel
     */
    public function exportTopProducts($products_data, $date_from, $date_to)
    {
        // Set document properties
        $this->spreadsheet->getProperties()
            ->setCreator('Simple POS CI3')
            ->setTitle('Produk Terlaris')
            ->setSubject('Top Products Report')
            ->setDescription('Top products report generated from Simple POS CI3');
            
        // Set worksheet title
        $this->worksheet->setTitle('Produk Terlaris');
        
        // Header
        $this->worksheet->setCellValue('A1', 'LAPORAN PRODUK TERLARIS');
        $this->worksheet->mergeCells('A1:E1');
        $this->worksheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $this->worksheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Date range
        $this->worksheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($date_from)) . ' - ' . date('d/m/Y', strtotime($date_to)));
        $this->worksheet->mergeCells('A2:E2');
        $this->worksheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Table headers
        $row = 4;
        $headers = ['No', 'Kode Barang', 'Nama Barang', 'Total Qty', 'Total Penjualan'];
        $col = 'A';
        foreach ($headers as $header) {
            $this->worksheet->setCellValue($col . $row, $header);
            $this->worksheet->getStyle($col . $row)->getFont()->setBold(true);
            $this->worksheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD3D3D3');
            $col++;
        }
        
        // Data
        $row++;
        $no = 1;
        foreach ($products_data as $product) {
            $this->worksheet->setCellValue('A' . $row, $no++);
            $this->worksheet->setCellValue('B' . $row, $product->kode_barang);
            $this->worksheet->setCellValue('C' . $row, $product->nama_barang);
            $this->worksheet->setCellValue('D' . $row, $product->total_qty);
            $this->worksheet->setCellValue('E' . $row, 'Rp ' . number_format($product->total_penjualan, 0, ',', '.'));
            
            // Align numbers to right
            $this->worksheet->getStyle('D' . $row . ':E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $row++;
        }
        
        // Style table
        $tableRange = 'A4:E' . ($row - 1);
        $this->worksheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Auto-size columns
        $this->_autoSizeColumns();
        
        // Download
        $filename = 'produk_terlaris_' . date('Y-m-d_H-i-s') . '.xlsx';
        $this->_downloadExcel($filename);
    }
}