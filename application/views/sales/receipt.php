<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - <?php echo $transaction['header']->no_transaksi; ?></title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            max-width: 300px;
        }
        
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .store-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .store-info {
            font-size: 10px;
            margin: 2px 0;
        }
        
        .transaction-info {
            margin-bottom: 15px;
        }
        
        .transaction-info div {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        
        .items-table {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 10px 0;
            margin-bottom: 10px;
        }
        
        .item-row {
            margin: 5px 0;
        }
        
        .item-name {
            font-weight: bold;
        }
        
        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        
        .totals {
            margin-top: 10px;
        }
        
        .totals div {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        
        .grand-total {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-size: 10px;
        }
        
        .no-print {
            margin: 20px 0;
            text-align: center;
        }
        
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>

    <div class="receipt-header">
        <div class="store-name">SIMPLE POS CI3</div>
        <div class="store-info">Jl. Example Street No. 123</div>
        <div class="store-info">Tel: (021) 1234-5678</div>
        <div class="store-info">Email: info@simplepos.com</div>
    </div>

    <div class="transaction-info">
        <div>
            <span>No. Transaksi:</span>
            <span><?php echo $transaction['header']->no_transaksi; ?></span>
        </div>
        <div>
            <span>Tanggal:</span>
            <span><?php echo date('d/m/Y H:i', strtotime($transaction['header']->tgl_transaksi)); ?></span>
        </div>
        <div>
            <span>Customer:</span>
            <span><?php echo $transaction['header']->customer; ?></span>
        </div>
        <?php if (!empty($transaction['header']->kode_promo)): ?>
        <div>
            <span>Promo:</span>
            <span><?php echo $transaction['header']->kode_promo; ?></span>
        </div>
        <?php endif; ?>
    </div>

    <div class="items-table">
        <?php foreach ($transaction['details'] as $item): ?>
        <div class="item-row">
            <div class="item-name"><?php echo $item->nama_barang; ?></div>
            <div class="item-details">
                <span><?php echo $item->qty; ?> x Rp <?php echo number_format($item->harga, 0, ',', '.'); ?></span>
                <span>Rp <?php echo number_format($item->subtotal, 0, ',', '.'); ?></span>
            </div>
            <?php if ($item->discount > 0): ?>
            <div class="item-details">
                <span>Discount:</span>
                <span>-Rp <?php echo number_format($item->discount, 0, ',', '.'); ?></span>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="totals">
        <div>
            <span>Subtotal:</span>
            <span>Rp <?php echo number_format($transaction['header']->total_bayar, 0, ',', '.'); ?></span>
        </div>
        <div>
            <span>PPN (10%):</span>
            <span>Rp <?php echo number_format($transaction['header']->ppn, 0, ',', '.'); ?></span>
        </div>
        <div class="grand-total">
            <span>TOTAL:</span>
            <span>Rp <?php echo number_format($transaction['header']->grand_total, 0, ',', '.'); ?></span>
        </div>
    </div>

    <div class="footer">
        <div>Terima kasih atas kunjungan Anda!</div>
        <div>Barang yang sudah dibeli tidak dapat dikembalikan</div>
        <div>Simpan struk ini sebagai bukti pembelian</div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); };
    </script>
</body>
</html>