<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-box"></i> <?php echo $title; ?></h5>
            </div>
            <div class="card-body">
                <?php echo form_open('', array('class' => 'needs-validation', 'novalidate' => '')); ?>
                
                <div class="mb-3">
                    <label for="kode_barang" class="form-label">Kode Barang *</label>
                    <input type="text" 
                           class="form-control" 
                           id="kode_barang" 
                           name="kode_barang" 
                           value="<?php echo $barang->kode_barang; ?>"
                           <?php echo (strpos($title, 'Edit') !== false) ? 'readonly' : ''; ?>
                           required>
                    <div class="invalid-feedback">Kode barang harus diisi</div>
                </div>
                
                <div class="mb-3">
                    <label for="nama_barang" class="form-label">Nama Barang *</label>
                    <input type="text" 
                           class="form-control" 
                           id="nama_barang" 
                           name="nama_barang" 
                           value="<?php echo $barang->nama_barang; ?>"
                           required>
                    <div class="invalid-feedback">Nama barang harus diisi</div>
                </div>
                
                <div class="mb-3">
                    <label for="harga" class="form-label">Harga *</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" 
                               class="form-control" 
                               id="harga" 
                               name="harga" 
                               value="<?php echo $barang->harga; ?>"
                               min="0"
                               required>
                    </div>
                    <div class="invalid-feedback">Harga harus diisi dengan angka yang valid</div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="<?php echo base_url('master_barang'); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <?php echo form_close(); ?>
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
                    <li><i class="fas fa-check text-success"></i> Kode barang harus unik</li>
                    <li><i class="fas fa-check text-success"></i> Nama barang harus jelas dan deskriptif</li>
                    <li><i class="fas fa-check text-success"></i> Harga dalam rupiah tanpa titik/koma</li>
                    <li><i class="fas fa-check text-success"></i> Field bertanda * wajib diisi</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Bootstrap form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Format number input
document.getElementById('harga').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    e.target.value = value;
});
</script>