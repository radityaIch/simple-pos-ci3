CREATE DATABASE IF NOT EXISTS simple_pos_ci3;
USE simple_pos_ci3;

CREATE TABLE `penjualan_header` (
  `no_transaksi` varchar(255) UNIQUE PRIMARY KEY,
  `tgl_transaksi` date,
  `customer` varchar(255),
  `kode_promo` varchar(255),
  `total_bayar` int,
  `ppn` int,
  `grand_total` int
);

CREATE TABLE `penjualan_header_detail` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `no_transaksi` varchar(255),
  `kode_barang` varchar(255),
  `qty` int,
  `harga` int,
  `discount` int,
  `subtotal` int
);

CREATE TABLE `master_barang` (
  `kode_barang` varchar(255) UNIQUE PRIMARY KEY,
  `nama_barang` varchar(255),
  `harga` int
);

CREATE TABLE `promo` (
  `kode_promo` varchar(255) UNIQUE PRIMARY KEY,
  `nama_promo` varchar(255),
  `ketereangan` varchar(255)
);

ALTER TABLE `penjualan_header_detail` ADD FOREIGN KEY (`no_transaksi`) REFERENCES `penjualan_header` (`no_transaksi`);

ALTER TABLE `penjualan_header_detail` ADD FOREIGN KEY (`kode_barang`) REFERENCES `master_barang` (`kode_barang`);

ALTER TABLE `penjualan_header` ADD FOREIGN KEY (`kode_promo`) REFERENCES `promo` (`kode_promo`);

-- ========================================
-- Sample Data for Testing
-- ========================================

-- Master Barang Sample Data
INSERT INTO `master_barang` (`kode_barang`, `nama_barang`, `harga`) VALUES
('BRG001', 'Laptop Dell Inspiron 15', 8500000),
('BRG002', 'Mouse Wireless Logitech', 250000),
('BRG003', 'Keyboard Mechanical RGB', 750000),
('BRG004', 'Monitor LED 24 inch', 2500000),
('BRG005', 'Webcam HD 1080p', 450000),
('BRG006', 'Speaker Bluetooth JBL', 850000),
('BRG007', 'Headset Gaming', 350000),
('BRG008', 'SSD 512GB Samsung', 1200000),
('BRG009', 'RAM DDR4 8GB Corsair', 650000),
('BRG010', 'Power Bank 20000mAh', 300000);

-- Promo Sample Data
INSERT INTO `promo` (`kode_promo`, `nama_promo`, `ketereangan`) VALUES
('DISC10', 'Diskon 10%', 'Diskon 10% untuk pembelian minimal Rp 1.000.000'),
('DISC50K', 'Diskon 50 Ribu', 'Diskon Rp 50.000 untuk pembelian minimal Rp 500.000'),
('NEWCUST', 'New Customer', 'Diskon 15% untuk pelanggan baru minimal Rp 2.000.000'),
('WEEKEND', 'Weekend Sale', 'Diskon Rp 100.000 untuk weekend shopping minimal Rp 1.500.000');

-- Sample Transaction (for testing)
INSERT INTO `penjualan_header` (`no_transaksi`, `tgl_transaksi`, `customer`, `kode_promo`, `total_bayar`, `ppn`, `grand_total`) VALUES
('TRX20241004001', '2024-10-04', 'John Doe', 'DISC10', 9000000, 900000, 9000000);

INSERT INTO `penjualan_header_detail` (`no_transaksi`, `kode_barang`, `qty`, `harga`, `discount`, `subtotal`) VALUES
('TRX20241004001', 'BRG001', 1, 8500000, 0, 8500000),
('TRX20241004001', 'BRG002', 2, 250000, 0, 500000);

-- ========================================
-- Indexes for better performance
-- ========================================
CREATE INDEX idx_penjualan_header_tgl ON penjualan_header(tgl_transaksi);
CREATE INDEX idx_penjualan_detail_transaksi ON penjualan_header_detail(no_transaksi);
CREATE INDEX idx_master_barang_nama ON master_barang(nama_barang);