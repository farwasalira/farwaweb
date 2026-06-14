<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIPUPUK - Sistem Informasi Stok dan Distribusi Pupuk Bersubsidi Desa Lumaring">
    <title>SIPUPUK - Sistem Informasi Pupuk Bersubsidi Desa Lumaring</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= $assets_url ?>/img/favicon.svg">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icon Libs -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.8/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $assets_url ?>/css/public.css?v=<?= filemtime(__DIR__ . '/../../assets/css/public.css') ?>">
</head>
<body>

<!-- Navbar -->
<nav id="mainNav" class="navbar navbar-expand-lg fixed-top navbar-glass py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold fs-3" href="<?= $public_url ?>" style="letter-spacing: -0.5px;">
            <div class="brand-icon d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px; background: linear-gradient(135deg, #10b981 0%, #10b981 100%); border-radius: 10px; color: #fff;">
                <i class="bx bxs-leaf animate__animated animate__pulse animate__infinite" style="font-size: 1.35rem;"></i>
            </div>
            <div class="d-flex flex-column text-start" style="line-height: 1.15;">
                <span class="text-dark fs-4" style="font-weight: 800;">SIPU<span style="color: #10b981;">PUK</span></span>
                <span class="text-muted" style="font-size: 0.68rem; font-weight: 500; letter-spacing: 0.3px;">Desa Lumaring</span>
            </div>
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="fas fa-bars fs-3 text-dark"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto gap-1 align-items-center">
                <li class="nav-item"><a class="nav-link <?= $page === 'beranda' ? 'active' : '' ?>" href="<?= $public_url ?>?page=beranda">Beranda</a></li>
                <li class="nav-item"><a class="nav-link <?= $page === 'stok' ? 'active' : '' ?>" href="<?= $public_url ?>?page=stok">Stok Pupuk</a></li>
                <li class="nav-item"><a class="nav-link <?= $page === 'alokasi' ? 'active' : '' ?>" href="<?= $public_url ?>?page=alokasi">Alokasi</a></li>
                <li class="nav-item"><a class="nav-link <?= $page === 'penyaluran' ? 'active' : '' ?>" href="<?= $public_url ?>?page=penyaluran">Penyaluran</a></li>
                <li class="nav-item"><a class="nav-link <?= $page === 'informasi' ? 'active' : '' ?>" href="<?= $public_url ?>?page=informasi">Informasi</a></li>
                <?php if (isLoggedIn()): ?>
                <li class="nav-item dropdown ms-lg-2">
                    <a class="dropdown-toggle btn btn-outline-success rounded-pill px-3 py-2 fw-bold d-flex align-items-center gap-1 btn-login-admin" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.85rem; border-width: 2px;">
                        <i class="fas fa-user-circle" style="font-size: 0.95rem;"></i> <?= htmlspecialchars($_SESSION['user_nama'] ?? 'Admin') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 animate__animated animate__fadeInUp" aria-labelledby="navbarDropdown" style="border-radius: 12px; font-size: 0.9rem;">
                        <li><a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2" href="<?= $admin_url ?>"><i class="bx bxs-dashboard text-success" style="font-size: 1.1rem;"></i> Panel Admin</a></li>
                        <li><hr class="dropdown-divider bg-light"></li>
                        <li><a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2 text-danger" href="<?= $admin_url ?>?page=login&action=logout"><i class="bx bx-log-out" style="font-size: 1.1rem;"></i> Logout</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-outline-success rounded-pill px-3 py-2 fw-bold d-flex align-items-center gap-1 btn-login-admin" href="<?= $admin_url ?>" style="font-size: 0.85rem; border-width: 2px;">
                        <i class="fas fa-user-lock" style="font-size: 0.85rem;"></i> Login Admin
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<!-- Main content -->
<?php
if (isset($view_file) && file_exists($view_file)) {
    include $view_file;
} else {
    echo '<div class="container" style="padding:100px 20px;text-align:center;"><h2>Halaman tidak ditemukan</h2></div>';
}
?>

<!-- Footer -->
<footer class="footer py-5 mt-auto">
    <div class="container text-center text-md-start">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0 d-flex flex-column align-items-center align-items-md-start">
                <div class="footer-brand d-flex align-items-center gap-2 mb-2">
                    <div class="brand-icon d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; border-radius: 6px;">
                        <i class="bx bxs-leaf" style="font-size: 0.95rem;"></i>
                    </div>
                    <div class="d-flex flex-column text-start" style="line-height: 1.15;">
                        <span class="text-white" style="font-weight: 800; font-size: 1.1rem;">SIPU<span style="color: var(--primary);">PUK</span></span>
                        <span class="text-muted" style="font-size: 0.65rem; font-weight: 500; letter-spacing: 0.3px;">Desa Lumaring</span>
                    </div>
                </div>
                <p class="mb-0" style="font-size: 0.85rem; font-weight: 500;">Sistem Informasi Stok dan Distribusi Pupuk Bersubsidi Desa Lumaring.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0" style="font-size: 0.82rem;">&copy; <?= date('Y') ?> SIPUPUK Lumaring. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.8/dist/sweetalert2.all.min.js"></script>

<script>
// Navbar scroll effect
window.addEventListener('scroll', () => {
    const nav = document.getElementById('mainNav');
    if (nav) {
        if (window.scrollY > 40) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    }
});
</script>
</body>
</html>
