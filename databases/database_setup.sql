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

SHOW TABLES;

SELECT COUNT(*) as total_products FROM master_barang;
SELECT COUNT(*) as total_promos FROM promo;
SELECT COUNT(*) as total_transactions FROM penjualan_header;