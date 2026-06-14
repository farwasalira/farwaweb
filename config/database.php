<?php
/**
 * Database Configuration
 * SIPUPUK - Sistem Informasi Stok dan Distribusi Pupuk
 */

$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'webpupuk';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Base URL configuration
$base_url = '/webpupuk';
$admin_url = $base_url . '/admin.php';
$public_url = $base_url . '/index.php';
$assets_url = $base_url . '/assets';
$uploads_url = $base_url . '/uploads';

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
