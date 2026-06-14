<?php
/**
 * SIPUPUK - Database Constraint Migration
 * Menambahkan constraint UNIQUE pada kolom-kolom yang wajib unik:
 * - petani.nik
 * - kelompok_tani.nama_kelompok
 * - kelompok_tani.ketua_petani_id
 * - pupuk.nama_pupuk
 */

require_once dirname(__DIR__) . '/config/database.php';

echo "<h2>SIPUPUK — Migrasi Constraint Database</h2><pre>";
$errors = [];
$success = [];

// Helper
function tryQuery($conn, $sql, $label) {
    if ($conn->query($sql)) {
        echo "✅ $label — Sukses.\n";
        return true;
    } else {
        echo "⚠️  $label — " . $conn->error . "\n";
        return false;
    }
}

// ==============================================================
// 1. petani.nik → UNIQUE & VARCHAR(16)
// ==============================================================
echo "\n--- 1. Tipe data VARCHAR(16) & UNIQUE constraint pada petani.nik ---\n";

// Pastikan tipe data petani.nik adalah VARCHAR(16)
$check_type = $conn->query("SHOW COLUMNS FROM `petani` LIKE 'nik'");
if ($check_type && $check_type->num_rows > 0) {
    $col_info = $check_type->fetch_assoc();
    if (strpos(strtolower($col_info['Type']), 'varchar(16)') === false) {
        tryQuery($conn, "ALTER TABLE `petani` MODIFY `nik` VARCHAR(16) NULL DEFAULT NULL", "Mengubah tipe data petani.nik menjadi VARCHAR(16)");
    } else {
        echo "ℹ️  Tipe data petani.nik sudah VARCHAR(16).\n";
    }
}

// Cek dulu apakah constraint sudah ada
$res = $conn->query("SHOW INDEX FROM petani WHERE Key_name = 'nik' OR Key_name = 'unique_nik'");
if ($res && $res->num_rows > 0) {
    echo "ℹ️  UNIQUE(nik) sudah ada.\n";
} else {
    // Cek apakah ada duplikat NIK dulu sebelum menambah constraint
    $dup = $conn->query("SELECT nik, COUNT(*) as c FROM petani WHERE nik IS NOT NULL AND nik != '' GROUP BY nik HAVING c > 1");
    if ($dup && $dup->num_rows > 0) {
        echo "❌ Tidak bisa menambahkan UNIQUE(nik): ada NIK duplikat!\n";
        while ($row = $dup->fetch_assoc()) {
            echo "   → NIK duplikat: {$row['nik']} (muncul {$row['c']}x) — Hapus/perbaiki dulu sebelum menjalankan ini.\n";
        }
    } else {
        tryQuery($conn, "ALTER TABLE `petani` ADD UNIQUE KEY `unique_nik` (`nik`)", "UNIQUE(petani.nik)");
    }
}

// ==============================================================
// 2. kelompok_tani.nama_kelompok → UNIQUE
// ==============================================================
echo "\n--- 2. UNIQUE constraint pada kelompok_tani.nama_kelompok ---\n";

$res2 = $conn->query("SHOW INDEX FROM kelompok_tani WHERE Key_name = 'unique_nama_kelompok'");
if ($res2 && $res2->num_rows > 0) {
    echo "ℹ️  UNIQUE(nama_kelompok) sudah ada.\n";
} else {
    $dup2 = $conn->query("SELECT nama_kelompok, COUNT(*) as c FROM kelompok_tani GROUP BY nama_kelompok HAVING c > 1");
    if ($dup2 && $dup2->num_rows > 0) {
        echo "❌ Ada nama kelompok duplikat:\n";
        while ($row = $dup2->fetch_assoc()) {
            echo "   → \"{$row['nama_kelompok']}\" muncul {$row['c']}x — Hapus dulu duplikatnya.\n";
        }
    } else {
        tryQuery($conn, "ALTER TABLE `kelompok_tani` ADD UNIQUE KEY `unique_nama_kelompok` (`nama_kelompok`)", "UNIQUE(kelompok_tani.nama_kelompok)");
    }
}

// ==============================================================
// 3. kelompok_tani.ketua_petani_id → UNIQUE
// ==============================================================
echo "\n--- 3. UNIQUE constraint pada kelompok_tani.ketua_petani_id ---\n";

$res3 = $conn->query("SHOW INDEX FROM kelompok_tani WHERE Key_name = 'unique_ketua_petani'");
if ($res3 && $res3->num_rows > 0) {
    echo "ℹ️  UNIQUE(ketua_petani_id) sudah ada.\n";
} else {
    $dup3 = $conn->query("SELECT ketua_petani_id, COUNT(*) as c FROM kelompok_tani WHERE ketua_petani_id IS NOT NULL GROUP BY ketua_petani_id HAVING c > 1");
    if ($dup3 && $dup3->num_rows > 0) {
        echo "❌ Ada ketua yang memimpin lebih dari satu kelompok:\n";
        while ($row = $dup3->fetch_assoc()) {
            echo "   → petani_id={$row['ketua_petani_id']} memimpin {$row['c']} kelompok.\n";
        }
    } else {
        tryQuery($conn, "ALTER TABLE `kelompok_tani` ADD UNIQUE KEY `unique_ketua_petani` (`ketua_petani_id`)", "UNIQUE(kelompok_tani.ketua_petani_id)");
    }
}

// ==============================================================
// 4. pupuk.nama_pupuk → UNIQUE
// ==============================================================
echo "\n--- 4. UNIQUE constraint pada pupuk.nama_pupuk ---\n";

$res4 = $conn->query("SHOW INDEX FROM pupuk WHERE Key_name = 'unique_nama_pupuk'");
if ($res4 && $res4->num_rows > 0) {
    echo "ℹ️  UNIQUE(nama_pupuk) sudah ada.\n";
} else {
    $dup4 = $conn->query("SELECT nama_pupuk, COUNT(*) as c FROM pupuk GROUP BY nama_pupuk HAVING c > 1");
    if ($dup4 && $dup4->num_rows > 0) {
        echo "❌ Ada nama pupuk duplikat:\n";
        while ($row = $dup4->fetch_assoc()) {
            echo "   → \"{$row['nama_pupuk']}\" muncul {$row['c']}x.\n";
        }
    } else {
        tryQuery($conn, "ALTER TABLE `pupuk` ADD UNIQUE KEY `unique_nama_pupuk` (`nama_pupuk`)", "UNIQUE(pupuk.nama_pupuk)");
    }
}

// ==============================================================
// 5. petani.status default 'Aktif' (jika belum)
// ==============================================================
echo "\n--- 5. Pastikan kolom status pada tabel petani ---\n";
$res5 = $conn->query("SHOW COLUMNS FROM petani LIKE 'status'");
if ($res5 && $res5->num_rows > 0) {
    echo "ℹ️  Kolom status sudah ada pada tabel petani.\n";
} else {
    tryQuery($conn, "ALTER TABLE `petani` ADD COLUMN `status` ENUM('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif' AFTER `alamat`", "Kolom status petani");
}

// ==============================================================
// 6. pupuk: tambah kolom berat_kemasan_kg (pisahkan angka dari teks)
// ==============================================================
echo "\n--- 6. Tambah kolom berat_kemasan_kg pada tabel pupuk ---\n";
$res6 = $conn->query("SHOW COLUMNS FROM pupuk LIKE 'berat_kemasan_kg'");
if ($res6 && $res6->num_rows > 0) {
    echo "ℹ️  Kolom berat_kemasan_kg sudah ada.\n";
} else {
    if (tryQuery($conn, "ALTER TABLE `pupuk` ADD COLUMN `berat_kemasan_kg` INT(11) NOT NULL DEFAULT 50 AFTER `ukuran_kemasan`", "Kolom berat_kemasan_kg")) {
        // Auto-populate dari ukuran_kemasan existing (ambil angka pertama)
        $conn->query("UPDATE pupuk SET berat_kemasan_kg = CAST(SUBSTRING_INDEX(ukuran_kemasan, ' ', 1) AS UNSIGNED) WHERE berat_kemasan_kg = 50 OR berat_kemasan_kg IS NULL");
        echo "   → Nilai berat_kemasan_kg otomatis dihitung dari data existing.\n";
    }
}

echo "\n<b>✅ Migrasi selesai.</b></pre>";
echo "<p><a href='/webpupuk/admin.php?page=dashboard'>← Kembali ke Dashboard</a></p>";
?>
