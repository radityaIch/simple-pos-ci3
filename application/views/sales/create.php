<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-plus"></i> <?php echo $title; ?></h2>
    </div>
</div>

<form id="transactionForm" method="POST">
    <div class="row">
        <!-- Transaction Header -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6><i class="fas fa-info-circle"></i> Informasi Transaksi</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">No. Transaksi</label>
                        <input type="text" class="form-control" name="no_transaksi" value="<?php echo $no_transaksi; ?>" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="text" class="form-control" value="<?php echo date('d/m/Y'); ?>" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Customer *</label>
                        <input type="text" class="form-control" name="customer" id="customer" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kode Promo</label>
                        <select class="form-select" name="kode_promo" id="kode_promo">
                            <?php foreach ($promo_dropdown as $key => $value): ?>
                                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Items Section -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6><i class="fas fa-shopping-cart"></i> Item Transaksi</h6>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">
                        <i class="fas fa-plus"></i> Tambah Item
                    </button>
                </div>
                <div class="card-body">
                    <div id="itemsContainer">
                        <!-- Items will be added here dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Totals Section -->
    <div class="row">
        <div class="col-md-6 offset-md-6">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-calculator"></i> Total Pembayaran</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td>Subtotal:</td>
                            <td class="text-end" id="subtotal">Rp 0</td>
                        </tr>
                        <tr>
                            <td>PPN (10%):</td>
                            <td class="text-end" id="ppn">Rp 0</td>
                        </tr>
                        <tr id="promoRow" style="display: none;">
                            <td>Diskon Promo:</td>
                            <td class="text-end text-success" id="promoDiscount">-Rp 0</td>
                        </tr>
                        <tr class="table-primary">
                            <td><strong>Grand Total:</strong></td>
                            <td class="text-end"><strong id="grandTotal">Rp 0</strong></td>
                        </tr>
                    </table>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> Simpan Transaksi
                        </button>
                        <a href="<?php echo base_url('sales'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="items_data" id="items_data">
</form>

<!-- Item Row Template -->
<template id="itemTemplate">
    <div class="item-row mb-3 p-3 border rounded">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Barang</label>
                <select class="form-select barang-select" onchange="loadBarangData(this)">
                    <option value="">Pilih Barang</option>
                    <?php foreach ($barang_dropdown as $key => $value): ?>
                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Qty</label>
                <input type="number" class="form-control qty-input" min="1" value="1" onchange="calculateItem(this)">
            </div>
            <div class="col-md-2">
                <label class="form-label">Harga</label>
                <input type="number" class="form-control harga-input" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label">Discount</label>
                <input type="number" class="form-control discount-input" min="0" value="0" onchange="calculateItem(this)">
            </div>
            <div class="col-md-1">
                <label class="form-label">Subtotal</label>
                <input type="text" class="form-control subtotal-display" readonly>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
let itemCount = 0;

function addItem() {
    const template = document.getElementById('itemTemplate');
    const container = document.getElementById('itemsContainer');
    const clone = template.content.cloneNode(true);
    
    // Add unique identifiers
    const itemRow = clone.querySelector('.item-row');
    itemRow.setAttribute('data-item-id', itemCount++);
    
    container.appendChild(clone);
    calculateTotals();
}

function removeItem(button) {
    button.closest('.item-row').remove();
    calculateTotals();
}

function loadBarangData(select) {
    const kodeBarang = select.value;
    const itemRow = select.closest('.item-row');
    
    if (kodeBarang) {
        fetch('<?php echo base_url("master_barang/get_barang_data/"); ?>' + kodeBarang)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    itemRow.querySelector('.harga-input').value = data.data.harga;
                    calculateItem(select);
                }
            });
    } else {
        itemRow.querySelector('.harga-input').value = '';
        itemRow.querySelector('.subtotal-display').value = '';
        calculateTotals();
    }
}

function calculateItem(element) {
    const itemRow = element.closest('.item-row');
    const qty = parseInt(itemRow.querySelector('.qty-input').value) || 0;
    const harga = parseInt(itemRow.querySelector('.harga-input').value) || 0;
    const discount = parseInt(itemRow.querySelector('.discount-input').value) || 0;
    
    const subtotal = (qty * harga) - discount;
    itemRow.querySelector('.subtotal-display').value = formatCurrency(subtotal);
    
    calculateTotals();
}

function calculateTotals() {
    const items = [];
    let total = 0;
    
    document.querySelectorAll('.item-row').forEach(row => {
        const kodeBarang = row.querySelector('.barang-select').value;
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
        const harga = parseInt(row.querySelector('.harga-input').value) || 0;
        const discount = parseInt(row.querySelector('.discount-input').value) || 0;
        
        if (kodeBarang && qty > 0 && harga > 0) {
            const subtotal = (qty * harga) - discount;
            total += subtotal;
            
            items.push({
                kode_barang: kodeBarang,
                qty: qty,
                harga: harga,
                discount: discount,
                subtotal: subtotal
            });
        }
    });
    
    const ppn = total * 0.1;
    const kodePromo = document.getElementById('kode_promo').value;
    
    // Update display
    document.getElementById('subtotal').textContent = formatCurrency(total);
    document.getElementById('ppn').textContent = formatCurrency(ppn);
    
    // Calculate promo validation (promo is just a label, no discount)
    if (kodePromo && total > 0) {
        validatePromoCode(kodePromo);
    } else {
        document.getElementById('promoRow').style.display = 'none';
        document.getElementById('grandTotal').textContent = formatCurrency(total + ppn);
    }
    
    // Store items data
    document.getElementById('items_data').value = JSON.stringify(items);
}

function validatePromoCode(kodePromo) {
    const formData = new FormData();
    formData.append('kode_promo', kodePromo);
    
    fetch('<?php echo base_url("promo/validate_promo"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Promo is valid, but no discount applied (promo is just a label)
            // Show promo info but no discount amount
            const total = calculateTotalFromItems();
            const ppn = total * 0.1;
            document.getElementById('grandTotal').textContent = formatCurrency(total + ppn);
            
            // Hide promo discount row since no discount is applied
            document.getElementById('promoRow').style.display = 'none';
        } else {
            // Invalid promo
            document.getElementById('promoRow').style.display = 'none';
            const total = calculateTotalFromItems();
            const ppn = total * 0.1;
            document.getElementById('grandTotal').textContent = formatCurrency(total + ppn);
            
            // Optionally show error message
            if (data.message) {
                console.log('Promo error:', data.message);
            }
        }
    })
    .catch(error => {
        console.error('Error validating promo:', error);
        const total = calculateTotalFromItems();
        const ppn = total * 0.1;
        document.getElementById('grandTotal').textContent = formatCurrency(total + ppn);
    });
}

function calculateTotalFromItems() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const kodeBarang = row.querySelector('.barang-select').value;
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
        const harga = parseInt(row.querySelector('.harga-input').value) || 0;
        const discount = parseInt(row.querySelector('.discount-input').value) || 0;
        
        if (kodeBarang && qty > 0 && harga > 0) {
            const subtotal = (qty * harga) - discount;
            total += subtotal;
        }
    });
    return total;
}

function formatCurrency(amount) {
    return 'Rp ' + amount.toLocaleString('id-ID');
}

// Event listeners
document.getElementById('kode_promo').addEventListener('change', calculateTotals);
document.getElementById('transactionForm').addEventListener('submit', function(e) {
    const items = JSON.parse(document.getElementById('items_data').value || '[]');
    if (items.length === 0) {
        e.preventDefault();
        alert('Minimal harus ada 1 item dalam transaksi');
        return false;
    }
    
    if (!document.getElementById('customer').value.trim()) {
        e.preventDefault();
        alert('Nama customer harus diisi');
        return false;
    }
});

// Add first item on page load
window.addEventListener('load', function() {
    addItem();
});
</script>