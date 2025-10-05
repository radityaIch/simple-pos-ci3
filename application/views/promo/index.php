<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-tags"></i> Master Promo</h2>
    </div>
    <div>
        <a href="<?php echo base_url('promo/add'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Promo
        </a>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-body">
        <?php if (!empty($promo_list)): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Kode Promo</th>
                            <th>Nama Promo</th>
                            <th>Keterangan</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($promo_list as $promo): ?>
                        <tr>
                            <td><strong><?php echo $promo->kode_promo; ?></strong></td>
                            <td><?php echo $promo->nama_promo; ?></td>
                            <td><?php echo $promo->ketereangan; ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo base_url('promo/view/' . $promo->kode_promo); ?>" 
                                       class="btn btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo base_url('promo/edit/' . $promo->kode_promo); ?>" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- Delete form with POST method for CSRF protection -->
                                    <?php 
                                    // Generate fresh CSRF token for each form
                                    $csrf = array(
                                        'name' => $this->security->get_csrf_token_name(),
                                        'hash' => $this->security->get_csrf_hash()
                                    );
                                    ?>
                                    <form method="POST" action="<?php echo base_url('promo/delete/' . $promo->kode_promo); ?>" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus promo ini?')">
                                        <input type="hidden" name="<?php echo $csrf['name']; ?>" value="<?php echo $csrf['hash']; ?>">
                                        <button type="submit" class="btn btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada data promo</h5>
                <p class="text-muted">Belum ada data promo. Klik tombol "Tambah Promo" untuk mulai menambahkan data.</p>
                <a href="<?php echo base_url('promo/add'); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Promo Pertama
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
