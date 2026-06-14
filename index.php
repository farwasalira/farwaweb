<?php
/**
 * Public Router - index.php
 * SIPUPUK - Sistem Informasi Stok dan Distribusi Pupuk
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/libraries/helpers.php';

$page = isset($_GET['page']) ? sanitize($_GET['page']) : 'beranda';

$allowed_pages = ['beranda', 'stok', 'alokasi', 'penyaluran', 'informasi'];

if (!in_array($page, $allowed_pages)) {
    $page = 'beranda';
}



// Set up the view file to render inside the layout
$view_file = __DIR__ . '/views/publik/' . $page . '.php';

// Include consolidated public layout
include __DIR__ . '/views/layouts/public_layout.php';
