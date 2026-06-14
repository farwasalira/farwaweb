<?php
require_once dirname(__DIR__) . '/config/database.php';

// Fix Duplicate NIK in petani (Update the second one)
$dups = $conn->query("SELECT id, nik FROM petani WHERE nik = '7371010101900001' ORDER BY id ASC");
if ($dups && $dups->num_rows > 1) {
    $dups->fetch_assoc(); // Skip first
    $row = $dups->fetch_assoc();
    $id = $row['id'];
    $new_nik = '7371010101900099'; // Just make it unique for now
    $conn->query("UPDATE petani SET nik = '$new_nik' WHERE id = $id");
    echo "Fixed duplicate NIK for petani ID $id\n";
}

// Fix Duplicate Kelompok 'Suka Maju'
$kel = $conn->query("SELECT id FROM kelompok_tani WHERE nama_kelompok = 'Suka Maju' ORDER BY id ASC");
if ($kel && $kel->num_rows > 1) {
    $kel->fetch_assoc(); // Skip first
    $row = $kel->fetch_assoc();
    $id = $row['id'];
    $conn->query("UPDATE kelompok_tani SET nama_kelompok = 'Suka Maju 2' WHERE id = $id");
    echo "Fixed duplicate kelompok Suka Maju (ID $id renamed to Suka Maju 2)\n";
}

// Fix Duplicate Ketua (petani_id = 1 leads 2 groups)
$ketua = $conn->query("SELECT id FROM kelompok_tani WHERE ketua_petani_id = 1 ORDER BY id ASC");
if ($ketua && $ketua->num_rows > 1) {
    $ketua->fetch_assoc(); // Skip first
    $row = $ketua->fetch_assoc();
    $id = $row['id'];
    // Find a random petani that is not leading any group
    $avail = $conn->query("SELECT id FROM petani WHERE id NOT IN (SELECT ketua_petani_id FROM kelompok_tani WHERE ketua_petani_id IS NOT NULL) LIMIT 1");
    if ($avail && $avail->num_rows > 0) {
        $new_ketua = $avail->fetch_assoc()['id'];
        $conn->query("UPDATE kelompok_tani SET ketua_petani_id = $new_ketua WHERE id = $id");
        echo "Fixed duplicate ketua for kelompok ID $id (new ketua: $new_ketua)\n";
    } else {
        $conn->query("UPDATE kelompok_tani SET ketua_petani_id = NULL WHERE id = $id");
        echo "Fixed duplicate ketua for kelompok ID $id (set to NULL)\n";
    }
}
?>
