<?php
/**
 * Laporan Controller (Rekap Bulanan)
 */

$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');
if ($tahun > (int)date('Y')) {
    $tahun = (int)date('Y'); // Batasi maksimum tahun ini
}
$jenis = isset($_GET['jenis']) ? sanitize($_GET['jenis']) : 'penyaluran';

// 1. Ambil semua master data pupuk untuk dinamisasi kolom
$pupukData = [];
$resPupuk = $conn->query("SELECT id, nama_pupuk FROM pupuk ORDER BY FIELD(nama_pupuk, 'UREA', 'NPK PHONSKA', 'NPK PELANGI', 'ORGANIK', 'ZA') ASC");
while ($p = $resPupuk->fetch_assoc()) {
    $pupukData[] = $p;
}

// 2. Siapkan matriks data bulanan
// Format: $rekap[bulan][id_pupuk] = jumlah
$rekap = [];
for ($i = 1; $i <= 12; $i++) {
    $rekap[$i] = [];
    foreach ($pupukData as $p) {
        $rekap[$i][$p['id']] = 0;
    }
}

// 3. Ambil data agregat dari database berdasarkan jenis laporan (dihitung per sak)
if ($jenis === 'penyaluran') {
    $q = $conn->query("
        SELECT MONTH(d.tanggal) as bulan, d.id_pupuk, SUM(d.jumlah / COALESCE(NULLIF(p.berat_kemasan_kg, 0), 50)) as total
        FROM penyaluran d
        JOIN pupuk p ON d.id_pupuk = p.id
        WHERE YEAR(d.tanggal) = $tahun
        GROUP BY MONTH(d.tanggal), d.id_pupuk
    ");
    while ($row = $q->fetch_assoc()) {
        $rekap[(int)$row['bulan']][$row['id_pupuk']] += $row['total'];
    }
} elseif ($jenis === 'stok') {
    $q = $conn->query("
        SELECT MONTH(s.tanggal) as bulan, s.id_pupuk, SUM(s.jumlah / COALESCE(NULLIF(p.berat_kemasan_kg, 0), 50)) as total
        FROM stok s
        JOIN pupuk p ON s.id_pupuk = p.id
        WHERE YEAR(s.tanggal) = $tahun
        GROUP BY MONTH(s.tanggal), s.id_pupuk
    ");
    while ($row = $q->fetch_assoc()) {
        $rekap[(int)$row['bulan']][$row['id_pupuk']] += $row['total'];
    }
}

// 4. Dapatkan daftar tahun unik untuk dropdown filter
$years = [];
$res = $conn->query("
    SELECT DISTINCT YEAR(tanggal) as yr FROM penyaluran 
    UNION 
    SELECT DISTINCT YEAR(tanggal) as yr FROM stok 
    ORDER BY yr DESC
");
while ($r = $res->fetch_assoc()) {
    $years[] = $r['yr'];
}
if (!in_array(date('Y'), $years)) {
    $years[] = date('Y');
    rsort($years); // Sort descending
}
