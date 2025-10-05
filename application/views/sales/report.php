<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><?= $title ?></h4>
                    <div class="card-tools">
                        <a href="<?= base_url('sales/export_excel?' . http_build_query(['date_from' => $date_from, 'date_to' => $date_to])) ?>" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <a href="<?= base_url('sales/export_top_products?' . http_build_query(['date_from' => $date_from, 'date_to' => $date_to])) ?>" 
                           class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar"></i> Export Top Products
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Filter Form -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="<?= base_url('sales/report') ?>" class="form-inline">
                                <div class="form-group mr-3">
                                    <label for="date_from" class="mr-2">Dari Tanggal:</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" 
                                           value="<?= $date_from ?>" required>
                                </div>
                                <div class="form-group mr-3">
                                    <label for="date_to" class="mr-2">Sampai Tanggal:</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" 
                                           value="<?= $date_to ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4><?= $total_transactions ?></h4>
                                            <p class="mb-0">Total Transaksi</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-shopping-cart fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>Rp <?= number_format($summary['total_sales'], 0, ',', '.') ?></h4>
                                            <p class="mb-0">Total Penjualan</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-money-bill-wave fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>Rp <?= number_format($summary['total_ppn'], 0, ',', '.') ?></h4>
                                            <p class="mb-0">Total PPN</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-percent fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>Rp <?= number_format($summary['total_grand'], 0, ',', '.') ?></h4>
                                            <p class="mb-0">Grand Total</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-calculator fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Sales Report Table -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Laporan Penjualan Harian</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="15%">Tanggal</th>
                                                    <th width="15%">Total Transaksi</th>
                                                    <th width="25%">Total Penjualan</th>
                                                    <th width="20%">PPN</th>
                                                    <th width="25%">Grand Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($sales_report)): ?>
                                                    <?php foreach ($sales_report as $report): ?>
                                                    <tr>
                                                        <td><?= date('d/m/Y', strtotime($report->tanggal)) ?></td>
                                                        <td class="text-center"><?= $report->total_transaksi ?></td>
                                                        <td class="text-right">Rp <?= number_format($report->total_penjualan, 0, ',', '.') ?></td>
                                                        <td class="text-right">Rp <?= number_format($report->total_ppn, 0, ',', '.') ?></td>
                                                        <td class="text-right">Rp <?= number_format($report->total_grand_total, 0, ',', '.') ?></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">
                                                            Tidak ada data penjualan dalam periode yang dipilih
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Products -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Produk Terlaris</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($top_products)): ?>
                                        <div class="list-group list-group-flush">
                                            <?php foreach ($top_products as $product): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center p-2">
                                                <div>
                                                    <h6 class="mb-1"><?= $product->nama_barang ?></h6>
                                                    <small class="text-muted"><?= $product->kode_barang ?></small>
                                                </div>
                                                <div class="text-right">
                                                    <span class="badge badge-primary badge-pill"><?= $product->total_qty ?></span>
                                                    <br>
                                                    <small class="text-muted">Rp <?= number_format($product->total_penjualan, 0, ',', '.') ?></small>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-center text-muted">Tidak ada data produk</p>
                                    <?php endif; ?>
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
.form-inline .form-group {
    align-items: center;
}

.card-body h4 {
    font-size: 1.5rem;
    font-weight: bold;
}

.list-group-item {
    border-left: none;
    border-right: none;
    border-top: 1px solid #dee2e6;
}

.list-group-item:first-child {
    border-top: none;
}

.badge-pill {
    font-size: 0.9rem;
}

.card-tools {
    float: right;
}

.card-tools .btn {
    margin-left: 5px;
}

@media (max-width: 768px) {
    .form-inline {
        flex-direction: column;
    }
    
    .form-inline .form-group {
        margin-bottom: 10px;
        width: 100%;
    }
    
    .form-inline .form-control {
        width: 100%;
    }
    
    .card-tools {
        float: none;
        margin-top: 10px;
    }
    
    .card-tools .btn {
        margin: 2px;
        width: 100%;
    }
}
</style>