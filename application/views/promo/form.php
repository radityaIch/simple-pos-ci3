<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-tags"></i> <?php echo $title; ?></h5>
            </div>
            <div class="card-body">
                <?php echo form_open('', array('class' => 'needs-validation', 'novalidate' => '')); ?>
                <!-- CSRF Token -->
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                
                <div class="mb-3">
                    <label for="kode_promo" class="form-label">Kode Promo *</label>
                    <input type="text" 
                           class="form-control" 
                           id="kode_promo" 
                           name="kode_promo" 
                           value="<?php echo $promo->kode_promo; ?>"
                           <?php echo (strpos($title, 'Edit') !== false) ? 'readonly' : ''; ?>
                           required>
                    <div class="invalid-feedback">Kode promo harus diisi</div>
                </div>
                
                <div class="mb-3">
                    <label for="nama_promo" class="form-label">Nama Promo *</label>
                    <input type="text" 
                           class="form-control" 
                           id="nama_promo" 
                           name="nama_promo" 
                           value="<?php echo $promo->nama_promo; ?>"
                           required>
                    <div class="invalid-feedback">Nama promo harus diisi</div>
                </div>
                
                <div class="mb-3">
                    <label for="ketereangan" class="form-label">Keterangan</label>
                    <textarea class="form-control" 
                              id="ketereangan" 
                              name="ketereangan" 
                              rows="3"><?php echo $promo->ketereangan; ?></textarea>
                    <div class="form-text">Deskripsi singkat tentang promo ini</div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="<?php echo base_url('promo'); ?>" class="btn btn-secondary">
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
                    <li><i class="fas fa-check text-success"></i> Kode promo harus unik</li>
                    <li><i class="fas fa-check text-success"></i> Gunakan kode yang mudah diingat</li>
                    <li><i class="fas fa-check text-success"></i> Nama promo harus jelas</li>
                    <li><i class="fas fa-check text-success"></i> Field bertanda * wajib diisi</li>
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
</script>