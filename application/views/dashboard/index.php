<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
        <p class="text-muted">Selamat datang di Simple POS CodeIgniter 3</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo $today_transactions; ?></h4>
                        <p class="mb-0">Transaksi Hari Ini</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>Rp <?php echo number_format($today_sales, 0, ',', '.'); ?></h4>
                        <p class="mb-0">Penjualan Hari Ini</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo $total_products; ?></h4>
                        <p class="mb-0">Total Produk</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?php echo $month_transactions; ?></h4>
                        <p class="mb-0">Transaksi Bulan Ini</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bolt"></i> Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="<?php echo base_url('sales/create'); ?>" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-plus"></i><br>Transaksi Baru
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?php echo base_url('master_barang'); ?>" class="btn btn-info btn-lg w-100">
                            <i class="fas fa-box"></i><br>Master Barang
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?php echo base_url('sales'); ?>" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-list"></i><br>Daftar Transaksi
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?php echo base_url('sales/report'); ?>" class="btn btn-warning btn-lg w-100">
                            <i class="fas fa-chart-bar"></i><br>Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions & Top Products -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-clock"></i> Transaksi Terbaru</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_transactions)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>No. Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Items</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_transactions as $trx): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo base_url('sales/view/' . $trx->no_transaksi); ?>">
                                            <?php echo $trx->no_transaksi; ?>
                                        </a>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($trx->tgl_transaksi)); ?></td>
                                    <td><?php echo $trx->customer; ?></td>
                                    <td>Rp <?php echo number_format($trx->grand_total, 0, ',', '.'); ?></td>
                                    <td><?php echo $trx->total_items; ?> item</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Belum ada transaksi</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-star"></i> Produk Terlaris Bulan Ini</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($top_products)): ?>
                    <?php foreach ($top_products as $product): ?>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <div>
                            <strong><?php echo $product->nama_barang; ?></strong><br>
                            <small class="text-muted"><?php echo $product->kode_barang; ?></small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary"><?php echo $product->total_qty; ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">Belum ada data penjualan</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>