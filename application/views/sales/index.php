<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-list"></i> Daftar Transaksi Penjualan</h2>
    </div>
    <div>
        <a href="<?php echo base_url('sales/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Transaksi Baru
        </a>
    </div>
</div>

<!-- Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tanggal Dari</label>
                <input type="date" class="form-control" name="date_from" value="<?php echo $date_from; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Tanggal Sampai</label>
                <input type="date" class="form-control" name="date_to" value="<?php echo $date_to; ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="<?php echo base_url('sales'); ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-body">
        <?php if (!empty($transactions)): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th>Promo</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $trx): ?>
                        <tr>
                            <td>
                                <strong><?php echo $trx->no_transaksi; ?></strong>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($trx->tgl_transaksi)); ?></td>
                            <td><?php echo $trx->customer; ?></td>
                            <td>
                                <?php if (!empty($trx->kode_promo)): ?>
                                    <span class="badge bg-success"><?php echo $trx->kode_promo; ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo $trx->total_items; ?> item</span>
                            </td>
                            <td>
                                <strong>Rp <?php echo number_format($trx->grand_total, 0, ',', '.'); ?></strong>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo base_url('sales/view/' . $trx->no_transaksi); ?>" 
                                       class="btn btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo base_url('sales/receipt_pdf/' . $trx->no_transaksi); ?>" 
                                       class="btn btn-secondary" target="_blank" title="Print Receipt PDF">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (isset($pagination) && !empty($pagination)): ?>
                <div class="d-flex justify-content-center mt-3">
                    <?php echo $pagination; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada transaksi</h5>
                <p class="text-muted">
                    <?php if (!empty($date_from) || !empty($date_to)): ?>
                        Tidak ditemukan transaksi dalam rentang tanggal yang dipilih.
                    <?php else: ?>
                        Belum ada transaksi penjualan. Mulai buat transaksi pertama Anda.
                    <?php endif; ?>
                </p>
                <a href="<?php echo base_url('sales/create'); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Buat Transaksi Pertama
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>