<?php
/**
 * Admin Router - admin.php
 * SIPUPUK - Sistem Informasi Stok dan Distribusi Pupuk
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/libraries/helpers.php';

$page = isset($_GET['page']) ? sanitize($_GET['page']) : 'dashboard';
$action = isset($_GET['action']) ? sanitize($_GET['action']) : 'index';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Pages that don't require auth
$public_pages = ['login'];

// Check authentication and role
if (!in_array($page, $public_pages)) {
    if (!isLoggedIn()) {
        redirect($admin_url . '?page=login');
    }
    if (!isAdmin()) {
        redirect($public_url);
    }
}

// If logged in and trying to access login, redirect appropriately (unless logging out)
if ($page === 'login' && isLoggedIn() && $action !== 'logout') {
    if (isAdmin()) {
        redirect($admin_url . '?page=dashboard');
    } else {
        redirect($public_url);
    }
}

// Handle controller actions (POST requests)
$controller_name = $page === 'login' ? 'auth' : $page;
$controller_file = __DIR__ . '/controllers/' . $controller_name . '.php';
if (file_exists($controller_file)) {
    include $controller_file;
}

// For login page, don't include admin layout
if ($page === 'login') {
    include __DIR__ . '/views/admin/login.php';
    exit;
}

// Set up the view file to render inside the layout
$allowed_admin_pages = ['dashboard', 'petani', 'pupuk', 'kelompok', 'stok', 'alokasi', 'penyaluran', 'laporan', 'informasi', 'ubah_password'];

if (in_array($page, $allowed_admin_pages)) {
    $view_file = __DIR__ . '/views/admin/' . $page . '.php';
} else {
    $view_file = null;
}

// Include consolidated admin layout
include __DIR__ . '/views/layouts/admin_layout.php';
