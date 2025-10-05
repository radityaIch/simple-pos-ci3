<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-box"></i> Master Barang</h2>
        <?php if (isset($keyword)): ?>
            <p class="text-muted">Hasil pencarian: "<?php echo $keyword; ?>"</p>
        <?php endif; ?>
    </div>
    <div>
        <a href="<?php echo base_url('master_barang/add'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Barang
        </a>
    </div>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo base_url('master_barang/search'); ?>" class="row g-3">
            <div class="col-md-8">
                <input type="text" class="form-control" name="q" placeholder="Cari berdasarkan kode atau nama barang..." value="<?php echo isset($keyword) ? $keyword : ''; ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
                <a href="<?php echo base_url('master_barang'); ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-body">
        <?php if (!empty($barang_list)): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Harga</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($barang_list as $barang): ?>
                        <tr>
                            <td><strong><?php echo $barang->kode_barang; ?></strong></td>
                            <td><?php echo $barang->nama_barang; ?></td>
                            <td>Rp <?php echo number_format($barang->harga, 0, ',', '.'); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo base_url('master_barang/view/' . $barang->kode_barang); ?>" 
                                       class="btn btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo base_url('master_barang/edit/' . $barang->kode_barang); ?>" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?php echo base_url('master_barang/delete/' . $barang->kode_barang); ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Yakin ingin menghapus barang ini?')" 
                                       title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (isset($pagination)): ?>
                <div class="d-flex justify-content-center mt-3">
                    <?php echo $pagination; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada data barang</h5>
                <p class="text-muted">
                    <?php if (isset($keyword)): ?>
                        Tidak ditemukan barang dengan kata kunci "<?php echo $keyword; ?>"
                    <?php else: ?>
                        Belum ada data barang. Klik tombol "Tambah Barang" untuk mulai menambahkan data.
                    <?php endif; ?>
                </p>
                <?php if (!isset($keyword)): ?>
                    <a href="<?php echo base_url('master_barang/add'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Barang Pertama
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>