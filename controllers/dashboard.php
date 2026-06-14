<?php
/**
 * Dashboard Controller
 * Fetches summary data for dashboard view
 */

// Total jenis pupuk
$total_pupuk = $conn->query("SELECT COUNT(*) as total FROM pupuk")->fetch_assoc()['total'];

// Total kelompok tani
$total_kelompok = $conn->query("SELECT COUNT(*) as total FROM kelompok_tani")->fetch_assoc()['total'];

// Total stok (dalam sak)
$total_stok = $conn->query("SELECT ROUND(COALESCE(SUM(stok / COALESCE(NULLIF(berat_kemasan_kg, 0), 50)), 0)) as total FROM pupuk")->fetch_assoc()['total'];

// Stok menipis (< 300)
$stok_menipis_q = $conn->query("SELECT * FROM pupuk WHERE stok < 300 ORDER BY stok ASC");
$stok_menipis = [];
while ($row = $stok_menipis_q->fetch_assoc()) $stok_menipis[] = $row;
$total_menipis = count($stok_menipis);

// Data stok per pupuk for chart (dalam sak)
$stok_chart = $conn->query("SELECT nama_pupuk, stok, berat_kemasan_kg FROM pupuk ORDER BY id ASC");
$chart_labels = [];
$chart_data = [];
while ($row = $stok_chart->fetch_assoc()) {
    $chart_labels[] = $row['nama_pupuk'];
    $kemasan = (int)($row['berat_kemasan_kg'] ?: 50);
    $chart_data[] = (int)($row['stok'] / $kemasan);
}

// Penyaluran terbaru
$penyaluran_terbaru = $conn->query("
    SELECT d.*, p.nama_petani, kt.nama_kelompok, pu.nama_pupuk
    FROM penyaluran d
    JOIN petani p ON d.id_petani = p.id
    JOIN kelompok_tani kt ON p.id_kelompok = kt.id
    JOIN pupuk pu ON d.id_pupuk = pu.id
    ORDER BY d.tanggal DESC, d.id DESC
    LIMIT 5
");

