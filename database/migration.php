<?php
/**
 * Database Migration Script
 * SIPUPUK - Sistem Informasi Stok dan Distribusi Pupuk
 */

require_once dirname(__DIR__) . '/config/database.php';

echo "<h3>Memulai Migrasi Database SIPUPUK...</h3><pre>";

// 1. Alter kelompok_tani table to add ketua_petani_id
echo "1. Memperbarui tabel `kelompok_tani`... ";
$check_col_k = $conn->query("SHOW COLUMNS FROM `kelompok_tani` LIKE 'ketua_petani_id'");
if ($check_col_k && $check_col_k->num_rows > 0) {
    echo "Sudah diperbarui sebelumnya.\n";
} else {
    if ($conn->query("ALTER TABLE `kelompok_tani` ADD COLUMN `ketua_petani_id` INT(11) NULL DEFAULT NULL AFTER `nama_kelompok`")) {
        if ($conn->query("ALTER TABLE `kelompok_tani` ADD CONSTRAINT `fk_kelompok_ketua` FOREIGN KEY (`ketua_petani_id`) REFERENCES `petani` (`id`) ON DELETE SET NULL")) {
            echo "Sukses.\n";
        } else {
            echo "Gagal menambahkan foreign key: " . $conn->error . "\n";
        }
    } else {
        echo "Gagal menambahkan kolom: " . $conn->error . "\n";
    }
}

// 2. Update existing groups to set their ketua_petani_id
echo "2. Menyinkronkan ketua kelompok tani awal... ";
$update_k1 = $conn->query("UPDATE `kelompok_tani` SET `ketua_petani_id` = 1, `ketua_kelompok` = 'Ahmad Sudirman' WHERE `id` = 1");
$update_k2 = $conn->query("UPDATE `kelompok_tani` SET `ketua_petani_id` = 4, `ketua_kelompok` = 'Dedi Supriadi' WHERE `id` = 2");
$update_k3 = $conn->query("UPDATE `kelompok_tani` SET `ketua_petani_id` = 7, `ketua_kelompok` = 'Gunawan Hidayat' WHERE `id` = 3");
if ($update_k1 && $update_k2 && $update_k3) {
    echo "Sukses.\n";
} else {
    echo "Gagal: " . $conn->error . "\n";
}

echo "\n<b>Migrasi Selesai! Basis data SIPUPUK siap digunakan.</b></pre>";
?>
