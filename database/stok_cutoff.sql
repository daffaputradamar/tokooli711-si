-- Stock Cutoff monthly snapshot table
-- Run once against the bengkel database

CREATE TABLE IF NOT EXISTS `stok_cutoff` (
    `id`              INT           NOT NULL AUTO_INCREMENT,
    `periode`         VARCHAR(7)    NOT NULL COMMENT 'YYYY-MM',
    `kode_barang`     VARCHAR(50)   NOT NULL,
    `nama_barang`     VARCHAR(150)  NOT NULL,
    `merk`            VARCHAR(100)  DEFAULT NULL,
    `stok`            DECIMAL(10,2) NOT NULL DEFAULT 0,
    `harga_beli`      DECIMAL(15,2) NOT NULL DEFAULT 0,
    `harga_jual`      DECIMAL(15,2) NOT NULL DEFAULT 0,
    `nilai_stok_beli` DECIMAL(18,2) GENERATED ALWAYS AS (`stok` * `harga_beli`) STORED,
    `nilai_stok_jual` DECIMAL(18,2) GENERATED ALWAYS AS (`stok` * `harga_jual`) STORED,
    `created_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_by`      VARCHAR(50)   DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_periode_barang` (`periode`, `kode_barang`),
    KEY `idx_periode`    (`periode`),
    KEY `idx_kode_barang` (`kode_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Monthly end-of-month stock cut-off snapshots';
