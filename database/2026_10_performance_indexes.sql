-- Performance indexes for Toko Oli 711.
--
-- The schema had no indexes besides the auto-increment primary keys, so every
-- report joined penjualan (20k rows) with penjualan_detail (33k rows) by full
-- scan. Indexes change no data and no query results; they only make lookups fast.
--
-- Safe to run more than once (MariaDB: CREATE INDEX IF NOT EXISTS).
-- Non-unique on purpose: the existing data has a few duplicated kode_jual /
-- kode_beli values, and a UNIQUE index would refuse to build.
--
-- Run:  mysql -u <user> -p <database> < 2026_10_performance_indexes.sql

-- Sales: header lookups by code and date, detail lookups by sale and item
CREATE INDEX IF NOT EXISTS idx_penjualan_kode_jual        ON penjualan (kode_jual);
CREATE INDEX IF NOT EXISTS idx_penjualan_tanggal_jual     ON penjualan (tanggal_jual);
CREATE INDEX IF NOT EXISTS idx_penjualan_tanggal_jual_date ON penjualan (tanggal_jual_date);
CREATE INDEX IF NOT EXISTS idx_penjualan_kode_karyawan    ON penjualan (kode_karyawan);
CREATE INDEX IF NOT EXISTS idx_penjualan_detail_kode_jual ON penjualan_detail (kode_jual);
CREATE INDEX IF NOT EXISTS idx_penjualan_detail_barang    ON penjualan_detail (kode_barang);

-- Purchases
CREATE INDEX IF NOT EXISTS idx_pembelian_kode_beli        ON pembelian (kode_beli);
CREATE INDEX IF NOT EXISTS idx_pembelian_tanggal_beli     ON pembelian (tanggal_beli);
CREATE INDEX IF NOT EXISTS idx_pembelian_tanggal_beli_date ON pembelian (tanggal_beli_date);
CREATE INDEX IF NOT EXISTS idx_pembelian_detail_kode_beli ON pembelian_detail (kode_beli);
CREATE INDEX IF NOT EXISTS idx_pembelian_detail_barang    ON pembelian_detail (kode_barang);

-- Items, stock history, and the cashier attempt limit
CREATE INDEX IF NOT EXISTS idx_barang_kode_merk           ON barang (kode_merk);
CREATE INDEX IF NOT EXISTS idx_stok_history_barang_waktu  ON barang_stok_history (kode_barang, createddate);
CREATE INDEX IF NOT EXISTS idx_percobaan_karyawan_barang  ON percobaan_karyawan (id_karyawan, id_barang, isactive);
