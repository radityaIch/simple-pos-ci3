<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-box"></i> <?php echo $title; ?></h2>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Detail Barang</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Kode Barang</strong></td>
                        <td><?php echo $barang->kode_barang; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Nama Barang</strong></td>
                        <td><?php echo $barang->nama_barang; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Harga</strong></td>
                        <td>Rp <?php echo number_format($barang->harga, 0, ',', '.'); ?></td>
                    </tr>
                </table>
                
                <div class="d-flex gap-2">
                    <a href="<?php echo base_url('master_barang/edit/' . $barang->kode_barang); ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="<?php echo base_url('master_barang'); ?>" class="btn btn-secondary">
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
                    <li><i class="fas fa-check text-success"></i> Detail lengkap barang</li>
                    <li><i class="fas fa-check text-success"></i> Informasi tidak dapat diubah di halaman ini</li>
                    <li><i class="fas fa-check text-success"></i> Gunakan tombol Edit untuk mengubah data</li>
                </ul>
            </div>
        </div>
    </div>
</div>