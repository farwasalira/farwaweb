<?php
/**
 * Helper Functions
 * SIPUPUK - Sistem Informasi Stok dan Distribusi Pupuk
 */

// Check if admin is logged in
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Check if logged in user is admin
function isAdmin() {
    return isLoggedIn();
}

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit;
}

// Flash message
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Format currency
function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

// Format date
function formatTanggal($date) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $d = new DateTime($date);
    return $d->format('d') . ' ' . $bulan[(int)$d->format('m')] . ' ' . $d->format('Y');
}

// Format short date
function formatTanggalShort($date) {
    $bulan = [
        1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'
    ];
    $d = new DateTime($date);
    return $d->format('d') . ' ' . $bulan[(int)$d->format('m')] . ' ' . $d->format('Y');
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Upload file
function uploadFile($file, $target_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp']) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error upload file'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_types)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan'];
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB max
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (max 5MB)'];
    }

    $filename = uniqid() . '_' . time() . '.' . $ext;
    $target_path = $target_dir . '/' . $filename;

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $filename];
    }

    return ['success' => false, 'message' => 'Gagal upload file'];
}

// Get stok status
function getStokStatus($stok) {
    if ($stok <= 0) return ['label' => 'Habis', 'class' => 'danger'];
    if ($stok < 300) return ['label' => 'Menipis', 'class' => 'warning'];
    return ['label' => 'Tersedia', 'class' => 'success'];
}

// Get distribusi status badge class
function getStatusBadge($status) {
    switch ($status) {
        case 'Disalurkan': return 'badge-info';
        case 'Diterima': return 'badge-success';
        case 'Pending': return 'badge-warning';
        default: return 'badge-secondary';
    }
}

// Simple function to mask NIK for public view security
function maskNIK($nik) {
    if (!$nik) return '-';
    if (strlen($nik) < 12) return $nik;
    return substr($nik, 0, 6) . '******' . substr($nik, -4);
}


