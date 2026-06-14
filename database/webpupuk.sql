-- ============================================
-- SIPUPUK Database Schema
-- Sistem Informasi Stok dan Distribusi Pupuk
-- Bersubsidi Desa Lumaring
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;

DROP DATABASE IF EXISTS `webpupuk`;
CREATE DATABASE `webpupuk` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `webpupuk`;

-- ============================================
-- 1. Table: admin (Admin)
-- ============================================
CREATE TABLE `admin` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin: admin / admin123
-- Password hash: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO `admin` (`username`, `password`, `nama_lengkap`) VALUES
('admin', '$2y$10$lvXI5M98mk/r0qjLUNLsV.US6URNeKZ2sz7wHYiLthwfX7NHnvrL6', 'Admin');

-- ============================================
-- 2. Table: pupuk
-- ============================================
CREATE TABLE `pupuk` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_pupuk` VARCHAR(100) NOT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `ukuran_kemasan` VARCHAR(50) NOT NULL,
  `harga_per_sak` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `kegunaan` TEXT DEFAULT NULL,
  `kandungan` TEXT DEFAULT NULL,
  `stok` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 3. Table: kelompok_tani
-- ============================================
CREATE TABLE `kelompok_tani` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_kelompok` VARCHAR(100) NOT NULL,
  `ketua_petani_id` INT(11) DEFAULT NULL,
  `ketua_kelompok` VARCHAR(100) NOT NULL,
  `jumlah_anggota` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_kelompok_ketua` (`ketua_petani_id`),
  CONSTRAINT `fk_kelompok_ketua` FOREIGN KEY (`ketua_petani_id`) REFERENCES `petani` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 4. Table: petani
-- ============================================
CREATE TABLE `petani` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_petani` VARCHAR(100) NOT NULL,
  `nik` VARCHAR(16) DEFAULT NULL,
  `id_kelompok` INT(11) NOT NULL,
  `luas_lahan` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `alamat` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_petani_kelompok` (`id_kelompok`),
  CONSTRAINT `fk_petani_kelompok` FOREIGN KEY (`id_kelompok`) REFERENCES `kelompok_tani` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 5. Table: stok
-- ============================================
CREATE TABLE `stok` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tanggal` DATE NOT NULL,
  `id_pupuk` INT(11) NOT NULL,
  `jumlah` INT(11) NOT NULL,
  `keterangan` TEXT,
  `bukti` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_stok_pupuk` (`id_pupuk`),
  CONSTRAINT `fk_stok_pupuk` FOREIGN KEY (`id_pupuk`) REFERENCES `pupuk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- ============================================
-- 7. Table: alokasi
-- ============================================
CREATE TABLE `alokasi` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_petani` INT(11) NOT NULL,
  `id_pupuk` INT(11) NOT NULL,
  `jumlah` INT(11) NOT NULL DEFAULT 0,
  `periode` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_alokasi_petani` (`id_petani`),
  KEY `fk_alokasi_pupuk` (`id_pupuk`),
  CONSTRAINT `fk_alokasi_petani` FOREIGN KEY (`id_petani`) REFERENCES `petani` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_alokasi_pupuk` FOREIGN KEY (`id_pupuk`) REFERENCES `pupuk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 8. Table: penyaluran
-- ============================================
CREATE TABLE `penyaluran` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tanggal` DATE NOT NULL,
  `id_petani` INT(11) NOT NULL,
  `id_pupuk` INT(11) NOT NULL,
  `jumlah` INT(11) NOT NULL,
  `status` ENUM('Disalurkan') DEFAULT 'Disalurkan',
  `bukti` VARCHAR(255) DEFAULT NULL,
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_penyaluran_petani` (`id_petani`),
  KEY `fk_penyaluran_pupuk` (`id_pupuk`),
  CONSTRAINT `fk_penyaluran_petani` FOREIGN KEY (`id_petani`) REFERENCES `petani` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_penyaluran_pupuk` FOREIGN KEY (`id_pupuk`) REFERENCES `pupuk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 9. Table: informasi
-- ============================================
CREATE TABLE `informasi` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(200) NOT NULL,
  `isi` TEXT NOT NULL,
  `tanggal` DATE NOT NULL,
  `aktif` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- SEED DATA
-- ============================================

-- Pupuk
INSERT INTO `pupuk` (`nama_pupuk`, `foto`, `ukuran_kemasan`, `harga_per_sak`, `kegunaan`, `kandungan`, `stok`) VALUES
('UREA', NULL, '50 kg', 112500, 'Mempercepat pertumbuhan vegetatif tanaman (daun, batang, akar), membuat daun tampak lebih hijau segar, meningkatkan jumlah anakan, serta meningkatkan kadar protein pada hasil panen.', 'Nitrogen (N) 46%', 4500),
('NPK PHONSKA', NULL, '50 kg', 115000, 'Menyediakan nutrisi makro lengkap dan seimbang untuk memperkuat perakaran, batang, mempercepat pembungaan, meningkatkan kualitas buah/biji, serta menambah daya tahan tanaman terhadap penyakit.', 'Nitrogen (N) 15%, Fosfat (P2O5) 15%, Kalium (K2O) 15%, Sulfur (S) 10%', 3200),
('SP-36', NULL, '50 kg', 120000, 'Merangsang pertumbuhan akar baru yang kuat dan dalam, mempercepat pembentukan bunga serta buah/biji, merangsang pembelahan sel, dan mempercepat pematangan buah.', 'Fosfat (P2O5) 36%, Sulfur (S) 5%', 1800),
('ZA', NULL, '50 kg', 85000, 'Membantu pembentukan klorofil daun, memperbaiki rasa dan warna hasil panen, meningkatkan ketahanan tanaman terhadap suhu rendah, serta mencegah tanaman kerdil karena kekurangan sulfur.', 'Nitrogen (N) 21%, Sulfur (S) 24%', 299),
('ORGANIK', NULL, '40 kg', 32000, 'Memperbaiki struktur fisik, biologi, dan kimia tanah, meningkatkan kapasitas tukar kation tanah, gemburkan lahan, serta ramah lingkungan untuk keberlanjutan lahan pertanian.', 'C-Organik min. 15%, Bahan Organik min. 25%, Rasio C/N 15-25, Kadar Air maks. 25%', 2500);

-- Kelompok Tani
INSERT INTO `kelompok_tani` (`nama_kelompok`, `ketua_kelompok`, `jumlah_anggota`) VALUES
('Makmur Jaya', 'Pak Sukarno', 25),
('Sumber Rejeki', 'Pak Hartono', 20),
('Suka Maju', 'Pak Darman', 18);

-- Petani
INSERT INTO `petani` (`nama_petani`, `nik`, `id_kelompok`, `luas_lahan`, `alamat`) VALUES
('Ahmad Sudirman', '7371010101900001', 1, 2.5, 'Dusun 1, Desa Lumaring'),
('Budi Santoso', '7371010101900002', 1, 1.8, 'Dusun 1, Desa Lumaring'),
('Cecep Mulyadi', '7371010101900003', 1, 3.0, 'Dusun 2, Desa Lumaring'),
('Dedi Supriadi', '7371010101900004', 2, 2.0, 'Dusun 2, Desa Lumaring'),
('Eko Prasetyo', '7371010101900005', 2, 1.5, 'Dusun 3, Desa Lumaring'),
('Faisal Rahman', '7371010101900006', 2, 2.2, 'Dusun 3, Desa Lumaring'),
('Gunawan Hidayat', '7371010101900007', 3, 1.7, 'Dusun 4, Desa Lumaring'),
('Hasan Basri', '7371010101900008', 3, 2.8, 'Dusun 4, Desa Lumaring'),
('Irfan Maulana', '7371010101900009', 3, 1.3, 'Dusun 5, Desa Lumaring'),
('Joko Widodo', '7371010101900010', 1, 3.5, 'Dusun 5, Desa Lumaring');

-- Stok
INSERT INTO `stok` (`tanggal`, `id_pupuk`, `jumlah`, `keterangan`) VALUES
('2026-01-15', 1, 5000, 'Pengiriman awal tahun'),
('2026-01-15', 2, 3500, 'Pengiriman awal tahun'),
('2026-02-10', 3, 2000, 'Pengiriman periode Februari'),
('2026-02-10', 4, 500, 'Pengiriman periode Februari'),
('2026-03-05', 5, 3000, 'Pengiriman periode Maret'),
('2026-04-01', 1, 2000, 'Pengiriman periode April'),
('2026-04-15', 2, 1500, 'Pengiriman tambahan');



-- Alokasi
INSERT INTO `alokasi` (`id_petani`, `id_pupuk`, `jumlah`, `periode`) VALUES
(1, 1, 200, '2026-S1'),
(1, 2, 150, '2026-S1'),
(1, 5, 100, '2026-S1'),
(2, 1, 180, '2026-S1'),
(2, 2, 120, '2026-S1'),
(3, 1, 250, '2026-S1'),
(3, 3, 200, '2026-S1'),
(4, 1, 200, '2026-S1'),
(4, 2, 173, '2026-S1'),
(5, 1, 157, '2026-S1'),
(5, 4, 100, '2026-S1'),
(6, 1, 220, '2026-S1'),
(7, 1, 170, '2026-S1'),
(7, 2, 130, '2026-S1'),
(8, 1, 280, '2026-S1'),
(9, 1, 130, '2026-S1'),
(10, 1, 350, '2026-S1');

-- Penyaluran
INSERT INTO `penyaluran` (`tanggal`, `id_petani`, `id_pupuk`, `jumlah`, `status`, `keterangan`) VALUES
('2026-05-08', 1, 4, 1, 'Disalurkan', 'Penyaluran ZA'),
('2026-05-07', 1, 2, 297, 'Disalurkan', 'Penyaluran NPK Phonska'),
('2026-05-07', 4, 1, 360, 'Disalurkan', 'Penyaluran Urea'),
('2026-05-02', 7, 1, 157, 'Disalurkan', 'Penyaluran Urea'),
('2026-05-02', 4, 2, 173, 'Disalurkan', 'Penyaluran NPK Phonska'),
('2026-04-25', 3, 1, 250, 'Disalurkan', 'Penyaluran Urea'),
('2026-04-20', 6, 1, 220, 'Disalurkan', 'Penyaluran Urea'),
('2026-04-15', 8, 1, 280, 'Disalurkan', 'Penyaluran Urea');

-- Informasi
INSERT INTO `informasi` (`judul`, `isi`, `tanggal`, `aktif`) VALUES
('Jadwal Distribusi Pupuk Mei 2026', 'Distribusi pupuk bersubsidi untuk periode Mei 2026 akan dilaksanakan mulai tanggal 5 Mei 2026. Harap kelompok tani menyiapkan dokumen yang diperlukan.', '2026-05-01', 1),
('Perubahan Harga Pupuk Bersubsidi', 'Berdasarkan keputusan pemerintah, harga pupuk bersubsidi mengalami penyesuaian mulai 1 April 2026. Silakan hubungi kios untuk informasi lebih lanjut.', '2026-04-01', 1),
('Pendaftaran Anggota Kelompok Tani Baru', 'Bagi petani yang belum terdaftar dalam kelompok tani, silakan mendaftar melalui ketua kelompok tani terdekat paling lambat akhir Maret 2026.', '2026-03-15', 1);

COMMIT;
