<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><?= $title ?></h4>
                    <div class="card-tools">
                        <a href="<?= base_url('sales') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                        </a>
                        <a href="<?= base_url('sales/receipt_pdf/' . $transaction['header']->no_transaksi) ?>" 
                           class="btn btn-primary btn-sm" target="_blank">
                            <i class="fas fa-file-pdf"></i> Cetak PDF
                        </a>
                        <a href="<?= base_url('sales/receipt/' . $transaction['header']->no_transaksi) ?>" 
                           class="btn btn-outline-secondary btn-sm" target="_blank">
                            <i class="fas fa-print"></i> Preview HTML
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <?php if ($this->session->flashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $this->session->flashdata('success') ?>
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <!-- Transaction Header Info -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Informasi Transaksi</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>No. Transaksi:</strong></td>
                                            <td><?= $transaction['header']->no_transaksi ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal:</strong></td>
                                            <td><?= date('d/m/Y', strtotime($transaction['header']->tgl_transaksi)) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Customer:</strong></td>
                                            <td><?= $transaction['header']->customer ?></td>
                                        </tr>
                                        <?php if (!empty($transaction['header']->kode_promo)): ?>
                                        <tr>
                                            <td><strong>Kode Promo:</strong></td>
                                            <td><?= $transaction['header']->kode_promo ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Transaction Summary -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Ringkasan Transaksi</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Total Barang:</strong></td>
                                            <td class="text-right">Rp <?= number_format($transaction['header']->total_bayar, 0, ',', '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>PPN (10%):</strong></td>
                                            <td class="text-right">Rp <?= number_format($transaction['header']->ppn, 0, ',', '.') ?></td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Grand Total:</strong></td>
                                            <td class="text-right"><strong>Rp <?= number_format($transaction['header']->grand_total, 0, ',', '.') ?></strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Transaction Details -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Detail Barang</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th width="15%">Kode Barang</th>
                                                    <th width="35%">Nama Barang</th>
                                                    <th width="10%">Qty</th>
                                                    <th width="15%">Harga</th>
                                                    <th width="10%">Discount</th>
                                                    <th width="15%">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $no = 1; ?>
                                                <?php foreach ($transaction['details'] as $detail): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= $detail->kode_barang ?></td>
                                                    <td><?= $detail->nama_barang ?></td>
                                                    <td class="text-center"><?= $detail->qty ?></td>
                                                    <td class="text-right">Rp <?= number_format($detail->harga, 0, ',', '.') ?></td>
                                                    <td class="text-right">Rp <?= number_format($detail->discount, 0, ',', '.') ?></td>
                                                    <td class="text-right">Rp <?= number_format($detail->subtotal, 0, ',', '.') ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-active">
                                                    <th colspan="6" class="text-right">Total:</th>
                                                    <th class="text-right">Rp <?= number_format($transaction['header']->total_bayar, 0, ',', '.') ?></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card-tools {
    float: right;
}

.table-borderless td {
    border: none;
    padding: 0.5rem 0.75rem;
}

.alert {
    margin-bottom: 1rem;
}

@media print {
    .card-tools {
        display: none !important;
    }
}
</style>