<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf 
{
    protected $dompdf;
    
    public function __construct()
    {
        // Configure dompdf options
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('chroot', FCPATH);
        
        // Create dompdf instance
        $this->dompdf = new Dompdf($options);
    }
    
    /**
     * Load HTML content
     */
    public function loadHtml($html)
    {
        $this->dompdf->loadHtml($html);
    }
    
    /**
     * Set paper size and orientation
     */
    public function setPaper($size = 'A4', $orientation = 'portrait')
    {
        $this->dompdf->setPaper($size, $orientation);
    }
    
    /**
     * Render the PDF
     */
    public function render()
    {
        $this->dompdf->render();
    }
    
    /**
     * Output PDF to browser
     */
    public function stream($filename = 'document.pdf', $options = array())
    {
        $this->dompdf->stream($filename, $options);
    }
    
    /**
     * Get PDF output as string
     */
    public function output()
    {
        return $this->dompdf->output();
    }
    
    /**
     * Generate receipt PDF for POS system
     */
    public function generateReceipt($transaction_data, $output_type = 'stream')
    {
        // Generate HTML from transaction data
        $html = $this->_generateReceiptHtml($transaction_data);
        
        // Load HTML and configure for receipt
        $this->loadHtml($html);
        $this->setPaper(array(0, 0, 226.77, 566.93), 'portrait'); // 80mm x 200mm thermal receipt size
        $this->render();
        
        if ($output_type === 'stream') {
            $filename = 'receipt_' . $transaction_data['header']->no_transaksi . '.pdf';
            $this->stream($filename, array('Attachment' => false)); // Open in browser, not download
        } else {
            return $this->output();
        }
    }
    
    /**
     * Generate HTML content for receipt
     */
    private function _generateReceiptHtml($transaction)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Receipt - ' . $transaction['header']->no_transaksi . '</title>
            <style>
                body {
                    font-family: "DejaVu Sans", Arial, sans-serif;
                    font-size: 12px;
                    line-height: 1.4;
                    margin: 0;
                    padding: 10px;
                    color: #000;
                }
                
                .receipt-header {
                    text-align: center;
                    border-bottom: 2px solid #000;
                    padding-bottom: 8px;
                    margin-bottom: 10px;
                }
                
                .store-name {
                    font-size: 16px;
                    font-weight: bold;
                    margin-bottom: 3px;
                }
                
                .store-info {
                    font-size: 10px;
                    margin: 1px 0;
                }
                
                .transaction-info {
                    margin-bottom: 10px;
                    font-size: 11px;
                }
                
                .info-row {
                    display: table;
                    width: 100%;
                    margin: 2px 0;
                }
                
                .info-label {
                    display: table-cell;
                    width: 40%;
                }
                
                .info-value {
                    display: table-cell;
                    text-align: right;
                }
                
                .items-section {
                    border-top: 1px dashed #000;
                    border-bottom: 1px dashed #000;
                    padding: 8px 0;
                    margin: 8px 0;
                }
                
                .item-row {
                    margin: 4px 0;
                }
                
                .item-name {
                    font-weight: bold;
                    font-size: 11px;
                }
                
                .item-details {
                    display: table;
                    width: 100%;
                    font-size: 10px;
                }
                
                .item-qty-price {
                    display: table-cell;
                    width: 60%;
                }
                
                .item-subtotal {
                    display: table-cell;
                    text-align: right;
                }
                
                .item-discount {
                    display: table;
                    width: 100%;
                    font-size: 10px;
                    color: #666;
                }
                
                .discount-label {
                    display: table-cell;
                    width: 60%;
                }
                
                .discount-amount {
                    display: table-cell;
                    text-align: right;
                }
                
                .totals {
                    margin-top: 8px;
                    font-size: 11px;
                }
                
                .total-row {
                    display: table;
                    width: 100%;
                    margin: 2px 0;
                }
                
                .total-label {
                    display: table-cell;
                    width: 60%;
                }
                
                .total-amount {
                    display: table-cell;
                    text-align: right;
                }
                
                .grand-total {
                    font-weight: bold;
                    font-size: 13px;
                    border-top: 1px solid #000;
                    padding-top: 4px;
                    margin-top: 4px;
                }
                
                .footer {
                    text-align: center;
                    margin-top: 15px;
                    border-top: 1px dashed #000;
                    padding-top: 8px;
                    font-size: 9px;
                }
                
                .footer div {
                    margin: 2px 0;
                }
            </style>
        </head>
        <body>
            <div class="receipt-header">
                <div class="store-name">SIMPLE POS CI3</div>
                <div class="store-info">Jl. Example Street No. 123</div>
                <div class="store-info">Tel: (021) 1234-5678</div>
                <div class="store-info">Email: info@simplepos.com</div>
            </div>

            <div class="transaction-info">
                <div class="info-row">
                    <div class="info-label">No. Transaksi:</div>
                    <div class="info-value">' . $transaction['header']->no_transaksi . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal:</div>
                    <div class="info-value">' . date('d/m/Y H:i', strtotime($transaction['header']->tgl_transaksi)) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Customer:</div>
                    <div class="info-value">' . $transaction['header']->customer . '</div>
                </div>';
                
        if (!empty($transaction['header']->kode_promo)) {
            $html .= '
                <div class="info-row">
                    <div class="info-label">Promo:</div>
                    <div class="info-value">' . $transaction['header']->kode_promo . '</div>
                </div>';
        }
        
        $html .= '
            </div>

            <div class="items-section">';
            
        foreach ($transaction['details'] as $item) {
            $html .= '
                <div class="item-row">
                    <div class="item-name">' . $item->nama_barang . '</div>
                    <div class="item-details">
                        <div class="item-qty-price">' . $item->qty . ' x Rp ' . number_format($item->harga, 0, ',', '.') . '</div>
                        <div class="item-subtotal">Rp ' . number_format($item->subtotal, 0, ',', '.') . '</div>
                    </div>';
                    
            if ($item->discount > 0) {
                $html .= '
                    <div class="item-discount">
                        <div class="discount-label">Discount:</div>
                        <div class="discount-amount">-Rp ' . number_format($item->discount, 0, ',', '.') . '</div>
                    </div>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '
            </div>

            <div class="totals">
                <div class="total-row">
                    <div class="total-label">Subtotal:</div>
                    <div class="total-amount">Rp ' . number_format($transaction['header']->total_bayar, 0, ',', '.') . '</div>
                </div>
                <div class="total-row">
                    <div class="total-label">PPN (10%):</div>
                    <div class="total-amount">Rp ' . number_format($transaction['header']->ppn, 0, ',', '.') . '</div>
                </div>
                <div class="total-row grand-total">
                    <div class="total-label">TOTAL:</div>
                    <div class="total-amount">Rp ' . number_format($transaction['header']->grand_total, 0, ',', '.') . '</div>
                </div>
            </div>

            <div class="footer">
                <div>Terima kasih atas kunjungan Anda!</div>
                <div>Barang yang sudah dibeli tidak dapat dikembalikan</div>
                <div>Simpan struk ini sebagai bukti pembelian</div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}