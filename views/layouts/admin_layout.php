<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - SIPUPUK</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= $assets_url ?>/img/favicon.svg">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Icon Libs -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.8/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $assets_url ?>/css/admin.css?v=<?= filemtime(__DIR__ . '/../../assets/css/admin.css') ?>">
    <!-- SweetAlert2 JS in Header for global confirm dialogs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.8/dist/sweetalert2.all.min.js"></script>
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Admin Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <div class="brand-icon d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px; background: linear-gradient(135deg, #10b981 0%, #10b981 100%); border-radius: 10px; color: #fff;"><i class="bx bxs-leaf animate__animated animate__pulse animate__infinite"></i></div>
        <div class="d-flex flex-column text-start" style="line-height: 1.15;">
            <span style="font-weight: 800; font-size: 1.15rem; color: var(--white);">SIPU<span style="color: #10b981;">PUK</span></span> <br>
            <span style="font-size: 0.65rem; font-weight: 500; color: rgba(255, 255, 255, 0.6); letter-spacing: 0.3px;">Desa Lumaring</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="<?= $admin_url ?>?page=dashboard" class="sidebar-link <?= $page === 'dashboard' ? 'active' : '' ?>">
            <i class="bx bxs-dashboard"></i> Dashboard
        </a>

        <div class="sidebar-dropdown <?= in_array($page, ['pupuk','petani','kelompok']) ? 'open' : '' ?>">
            <div class="sidebar-link" onclick="this.parentElement.classList.toggle('open')">
                <i class="bx bxs-data"></i> Manajemen Data
                <i class="bx bx-chevron-right arrow"></i>
            </div>
            <div class="sidebar-submenu">
                <a href="<?= $admin_url ?>?page=pupuk" class="sidebar-link <?= $page === 'pupuk' ? 'active' : '' ?>">
                    <i class="bx bx-package"></i> Data Pupuk
                </a>
                <a href="<?= $admin_url ?>?page=petani" class="sidebar-link <?= $page === 'petani' ? 'active' : '' ?>">
                    <i class="bx bx-user"></i> Data Petani
                </a>
                <a href="<?= $admin_url ?>?page=kelompok" class="sidebar-link <?= $page === 'kelompok' ? 'active' : '' ?>">
                    <i class="bx bxs-group"></i> Kelompok Tani
                </a>
            </div>
        </div>

        <a href="<?= $admin_url ?>?page=stok" class="sidebar-link <?= $page === 'stok' ? 'active' : '' ?>">
            <i class="bx bxs-box"></i> Stok
        </a>

        <a href="<?= $admin_url ?>?page=alokasi" class="sidebar-link <?= $page === 'alokasi' ? 'active' : '' ?>">
            <i class="bx bx-list-check"></i> Alokasi
        </a>

        <a href="<?= $admin_url ?>?page=penyaluran" class="sidebar-link <?= $page === 'penyaluran' ? 'active' : '' ?>">
            <i class="bx bx-send"></i> Penyaluran
        </a>

        <a href="<?= $admin_url ?>?page=informasi" class="sidebar-link <?= $page === 'informasi' ? 'active' : '' ?>">
            <i class="bx bx-info-circle"></i> Informasi
        </a>

        <a href="<?= $admin_url ?>?page=laporan" class="sidebar-link <?= $page === 'laporan' ? 'active' : '' ?>">
            <i class="bx bxs-report"></i> Laporan
        </a>
        <a href="#" onclick="confirmLogout(event)" class="sidebar-link text-danger" style="color: #ef4444 !important;">
            <i class="bx bx-log-out-circle"></i> Logout
        </a>
    </nav>
</aside>

<script>
function confirmLogout(event) {
    event.preventDefault();
    Swal.fire({
        title: 'Apakah Anda yakin ingin keluar?',
        text: "Anda harus login kembali untuk mengakses panel administrasi.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Keluar!',
        cancelButtonText: 'Batal',
        background: '#1e293b',
        color: '#fff',
        backdrop: `rgba(15,23,42,0.4)`
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= $admin_url ?>?page=login&action=logout';
        }
    });
}
</script>

<div class="admin-main">
    <div class="admin-topbar">
        <button class="sidebar-toggle" onclick="toggleSidebar()"><i class="bx bx-menu"></i></button>
        <?php $display_page_title = $page === 'informasi' ? 'informasi' : $page; ?>
        <h1 class="page-title"><?= ucfirst(str_replace('_', ' ', $display_page_title)) ?></h1>
        <div class="topbar-right">
            <div class="site-dropdown">
                <button class="site-dropdown-btn"><i class="bx bx-globe"></i> Kembali ke Web <i class="bx bx-chevron-down"></i></button>
                <div class="site-dropdown-content">
                    <a href="<?= $public_url ?>?page=beranda"><i class="bx bx-home-alt"></i> Beranda Utama</a>
                    <a href="<?= $public_url ?>?page=stok"><i class="bx bx-package"></i> Stok Pupuk</a>
                    <a href="<?= $public_url ?>?page=alokasi"><i class="bx bx-user-check"></i> Alokasi</a>
                    <a href="<?= $public_url ?>?page=penyaluran"><i class="bx bx-send"></i> Penyaluran</a>
                    <a href="<?= $public_url ?>?page=informasi"><i class="bx bx-info-circle"></i> Informasi</a>
                </div>
            </div>
            <span class="topbar-date"><i class="bx bx-calendar"></i> <?= formatTanggalShort(date('Y-m-d')) ?></span>
            <div class="profile-dropdown">
                <button class="profile-dropdown-btn">
                    <div class="profile-avatar-circle">AD</div>
                </button>
                <div class="profile-dropdown-content">
                    <div class="profile-header">
                        <div class="name"><?= ($_SESSION['admin_nama'] ?? 'Admin') === 'Administrator' ? 'Admin' : htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?></div>
                        <div class="role">Login sebagai Admin</div>
                    </div>
                    <div class="profile-divider"></div>
                    <a href="<?= $admin_url ?>?page=ubah_password" class="password-link"><i class="bx bx-key"></i> Ubah Password</a>
                </div>
            </div>
        </div>
    </div>
    <div class="admin-content">
        <?php
        // Flash messages
        $flash = getFlash();
        if ($flash) {
            echo '<div class="alert alert-' . $flash['type'] . '">';
            echo '<i class="bx ' . ($flash['type'] === 'success' ? 'bx-check-circle' : 'bx-error-circle') . '"></i>';
            echo '<span>' . $flash['message'] . '</span>';
            echo '<button class="alert-close" onclick="this.parentElement.remove()">&times;</button>';
            echo '</div>';
        }

        if (isset($view_file) && file_exists($view_file)) {
            include $view_file;
        } else {
            echo '<div style="padding:40px;text-align:center;"><h2>Halaman tidak ditemukan</h2></div>';
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?= $assets_url ?>/js/admin.js?v=<?= filemtime(__DIR__ . '/../../assets/js/admin.js') ?>"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Ubah pesan validasi HTML5 bawaan browser menjadi Bahasa Indonesia
    var elements = document.querySelectorAll('input, select, textarea');
    elements.forEach(function(el) {
        el.addEventListener('invalid', function(e) {
            if (e.target.validity.valueMissing) {
                if(e.target.tagName === 'SELECT') {
                    e.target.setCustomValidity('Silakan pilih salah satu opsi dalam daftar ini.');
                } else if (e.target.type === 'file') {
                    e.target.setCustomValidity('Silakan unggah file/foto yang diminta.');
                } else {
                    e.target.setCustomValidity('Mohon isi kolom ini, data tidak boleh kosong.');
                }
            } else if (e.target.validity.rangeOverflow) {
                e.target.setCustomValidity('Nilai harus kurang dari atau sama dengan ' + e.target.max + '.');
            } else if (e.target.validity.rangeUnderflow) {
                e.target.setCustomValidity('Nilai harus lebih dari atau sama dengan ' + e.target.min + '.');
            } else {
                e.target.setCustomValidity('Data yang dimasukkan tidak valid.');
            }
        });
        el.addEventListener('input', function(e) {
            e.target.setCustomValidity('');
        });
        el.addEventListener('change', function(e) {
            e.target.setCustomValidity('');
        });
    });
});
</script>
</body>
</html>
