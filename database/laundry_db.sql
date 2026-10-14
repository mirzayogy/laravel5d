-- ============================================================
-- Basis data Laundry (MySQL / MariaDB)
-- Tabel: pelanggan, karyawan, layanan, transaksi,
--        detail_transaksi, pembayaran
-- ============================================================

DROP DATABASE IF EXISTS laundry_db;
CREATE DATABASE laundry_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE laundry_db;

-- ------------------------------------------------------------
-- Tabel master
-- ------------------------------------------------------------

CREATE TABLE pelanggan (
  id_pelanggan INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama         VARCHAR(100) NOT NULL,
  no_hp        VARCHAR(20)  NOT NULL,
  alamat       TEXT,
  PRIMARY KEY (id_pelanggan)
) ENGINE=InnoDB;

CREATE TABLE karyawan (
  id_karyawan INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama        VARCHAR(100) NOT NULL,
  username    VARCHAR(50)  NOT NULL,
  -- simpan hash (mis. password_hash() di PHP), jangan teks asli
  password    VARCHAR(255) NOT NULL,
  role        ENUM('admin', 'kasir') NOT NULL DEFAULT 'kasir',
  PRIMARY KEY (id_karyawan),
  UNIQUE KEY uq_karyawan_username (username)
) ENGINE=InnoDB;

CREATE TABLE layanan (
  id_layanan    INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  nama_layanan  VARCHAR(100)  NOT NULL,
  harga_per_kg  DECIMAL(10,2) NOT NULL,
  estimasi_hari TINYINT UNSIGNED NOT NULL DEFAULT 2,
  PRIMARY KEY (id_layanan)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tabel transaksi
-- ------------------------------------------------------------

CREATE TABLE transaksi (
  id_transaksi INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_pelanggan INT UNSIGNED NOT NULL,
  id_karyawan  INT UNSIGNED NOT NULL,
  tgl_masuk    DATE NOT NULL,
  tgl_selesai  DATE NULL,
  status       ENUM('diterima', 'dicuci', 'selesai', 'diambil')
               NOT NULL DEFAULT 'diterima',
  total_harga  DECIMAL(12,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (id_transaksi),
  KEY idx_transaksi_pelanggan (id_pelanggan),
  KEY idx_transaksi_karyawan (id_karyawan),
  CONSTRAINT fk_transaksi_pelanggan
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan (id_pelanggan)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_transaksi_karyawan
    FOREIGN KEY (id_karyawan) REFERENCES karyawan (id_karyawan)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE detail_transaksi (
  id_detail    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_transaksi INT UNSIGNED NOT NULL,
  id_layanan   INT UNSIGNED NOT NULL,
  berat_kg     DECIMAL(6,2)  NOT NULL,
  subtotal     DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (id_detail),
  KEY idx_detail_transaksi (id_transaksi),
  KEY idx_detail_layanan (id_layanan),
  CONSTRAINT fk_detail_transaksi
    FOREIGN KEY (id_transaksi) REFERENCES transaksi (id_transaksi)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_detail_layanan
    FOREIGN KEY (id_layanan) REFERENCES layanan (id_layanan)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE pembayaran (
  id_pembayaran INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_transaksi  INT UNSIGNED NOT NULL,
  tgl_bayar     DATE NOT NULL,
  metode        ENUM('tunai', 'transfer', 'qris') NOT NULL DEFAULT 'tunai',
  jumlah_bayar  DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (id_pembayaran),
  KEY idx_pembayaran_transaksi (id_transaksi),
  CONSTRAINT fk_pembayaran_transaksi
    FOREIGN KEY (id_transaksi) REFERENCES transaksi (id_transaksi)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Data contoh
-- ------------------------------------------------------------

INSERT INTO pelanggan (nama, no_hp, alamat) VALUES
  ('Budi Santoso', '081234567890', 'Jl. A. Yani KM 36, Banjarbaru'),
  ('Siti Aminah',  '085212345678', 'Jl. Trikora, Banjarbaru');

-- password di bawah hanya placeholder; ganti dengan hash asli
INSERT INTO karyawan (nama, username, password, role) VALUES
  ('Admin Laundry', 'admin', 'GANTI_DENGAN_HASH', 'admin'),
  ('Kasir 1',       'kasir1', 'GANTI_DENGAN_HASH', 'kasir');

INSERT INTO layanan (nama_layanan, harga_per_kg, estimasi_hari) VALUES
  ('Cuci Kering',  5000.00, 2),
  ('Cuci Setrika', 7000.00, 3),
  ('Setrika Saja', 4000.00, 1);

INSERT INTO transaksi
  (id_pelanggan, id_karyawan, tgl_masuk, tgl_selesai, status, total_harga)
VALUES
  (1, 2, '2026-10-05', '2026-10-08', 'dicuci', 35000.00);

INSERT INTO detail_transaksi (id_transaksi, id_layanan, berat_kg, subtotal) VALUES
  (1, 2, 5.00, 35000.00);

INSERT INTO pembayaran (id_transaksi, tgl_bayar, metode, jumlah_bayar) VALUES
  (1, '2026-10-05', 'tunai', 20000.00);

-- ------------------------------------------------------------
-- Contoh query: nota lengkap per transaksi
-- ------------------------------------------------------------
-- SELECT t.id_transaksi, p.nama AS pelanggan, k.nama AS kasir,
--        l.nama_layanan, d.berat_kg, d.subtotal, t.status,
--        COALESCE(SUM(b.jumlah_bayar), 0) AS sudah_dibayar
-- FROM transaksi t
-- JOIN pelanggan p ON p.id_pelanggan = t.id_pelanggan
-- JOIN karyawan  k ON k.id_karyawan  = t.id_karyawan
-- JOIN detail_transaksi d ON d.id_transaksi = t.id_transaksi
-- JOIN layanan   l ON l.id_layanan   = d.id_layanan
-- LEFT JOIN pembayaran b ON b.id_transaksi = t.id_transaksi
-- GROUP BY t.id_transaksi, l.nama_layanan, d.berat_kg, d.subtotal;
