<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-tags"></i> <?php echo $title; ?></h2>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Detail Promo</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Kode Promo</strong></td>
                        <td><?php echo $promo->kode_promo; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Nama Promo</strong></td>
                        <td><?php echo $promo->nama_promo; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Keterangan</strong></td>
                        <td><?php echo $promo->ketereangan ?: '-'; ?></td>
                    </tr>
                </table>
                
                <div class="d-flex gap-2">
                    <a href="<?php echo base_url('promo/edit/' . $promo->kode_promo); ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="<?php echo base_url('promo'); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-info-circle"></i> Petunjuk</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> Detail lengkap promo</li>
                    <li><i class="fas fa-check text-success"></i> Informasi tidak dapat diubah di halaman ini</li>
                    <li><i class="fas fa-check text-success"></i> Gunakan tombol Edit untuk mengubah data</li>
                    <li><i class="fas fa-exclamation-triangle text-warning"></i> Promo hanya berfungsi sebagai label</li>
                </ul>
                
                <hr>
                
                <h6><i class="fas fa-exclamation-triangle text-warning"></i> Catatan</h6>
                <p class="small text-muted">
                    Promo hanya berfungsi sebagai label atau penanda pada transaksi. 
                    Untuk memberikan diskon, gunakan field "Discount" pada setiap item barang di transaksi.
                </p>
            </div>
        </div>
    </div>
</div>