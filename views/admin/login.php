<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin SIPUPUK</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= $assets_url ?>/img/favicon.svg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $assets_url ?>/css/admin.css?v=<?= filemtime(__DIR__ . '/../../assets/css/admin.css') ?>">
</head>
<body>
<div class="login-page">
    <div class="login-bg-bubble login-bg-bubble-1"></div>
    <div class="login-bg-bubble login-bg-bubble-2"></div>
    
    <div class="login-container">
        <div class="login-split-card">
            <!-- Left Side: Visual Branding Panel -->
            <div class="login-visual-panel">
                <div class="login-visual-content">
                    <div class="login-brand-group">
                        <div class="login-brand-icon">
                            <i class="bx bxs-leaf"></i>
                        </div>
                        <div class="login-brand-text">
                            <span class="login-brand-name-main">SIPU<span style="color: #10b981;">PUK</span></span>
                            <span class="login-brand-name-sub">Desa Lumaring</span>
                        </div>
                    </div>
                    
                    <div class="login-visual-text-group">
                        <h1 class="login-visual-title">Sistem Informasi Pupuk Bersubsidi</h1>
                        <p class="login-visual-subtitle">Pengelolaan stok, alokasi, dan penyaluran
pupuk bersubsidi Desa Lumaring.</p>
                    </div>
                    
                    <div style="font-size: 0.75rem; opacity: 0.7; font-weight: 500;">
                        &copy; <?= date('Y') ?> Admin SIPUPUK. Hak cipta dilindungi.
                    </div>
                </div>
                
                <!-- Vertical Wave Divider SVG -->
                <div class="wavy-divider">
                    <svg viewBox="0 0 120 1200" preserveAspectRatio="none" style="height: 100%; width: 100%;">
                        <path d="M0,0 C50,150 90,300 60,450 C30,600 70,750 90,900 C100,1050 70,1150 60,1200 L120,1200 L120,0 Z" fill="#ffffff"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Right Side: Login Form Panel -->
            <div class="login-form-panel">
                <!-- Mobile brand logo (visible only on mobile stacked layout) -->
                <div class="login-mobile-brand">
                    <div class="brand-icon d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px; background: linear-gradient(135deg, #10b981 0%, #10b981 100%); border-radius: 10px; color: #fff; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);">
                        <i class="bx bxs-leaf animate__animated animate__pulse animate__infinite" style="font-size: 1.35rem;"></i>
                    </div>
                    <div class="login-brand-text">
                        <span class="login-brand-name-main" style="color: var(--dark); font-size: 1.2rem; font-weight: 800;">SIPU<span style="color: #10b981;">PUK</span></span>
                        <span class="login-brand-name-sub" style="color: var(--gray-500); font-size: 0.68rem; font-weight: 600;">Desa Lumaring</span>
                    </div>
                </div>
                
                <div class="login-form-header">
                    <h2 class="login-form-title">LOGIN</h2>
                    <p class="login-form" style="color: var(--dark); font-size: 0.78rem; font-weight: 100;">Masukkan username dan password untuk melanjutkan.</p>
                </div>
                
                <?php if (isset($_SESSION['login_error'])): ?>
                <div class="login-alert login-alert-danger animate__animated animate__fadeIn">
                    <i class="bx bx-error-circle"></i>
                    <span><?= $_SESSION['login_error'] ?></span>
                    <?php unset($_SESSION['login_error']); ?>
                </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['login_success'])): ?>
                <div class="login-alert login-alert-success animate__animated animate__fadeIn">
                    <i class="bx bx-check-circle"></i>
                    <span><?= $_SESSION['login_success'] ?></span>
                    <?php unset($_SESSION['login_success']); ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="<?= $admin_url ?>?page=login&action=login">
                    <?php if (isset($_GET['redirect'])): ?>
                        <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect']) ?>">
                    <?php endif; ?>
                    
                    <div class="input-group-modern">
                        <label class="form-label-modern">Username</label>
                        <div class="input-wrapper-modern">
                            <i class="bx bxs-user input-icon-modern"></i>
                            <input type="text" name="username" class="form-control-modern" placeholder="Masukkan username" required autofocus autocomplete="off" oninvalid="this.setCustomValidity('Harap isi username Anda.')" oninput="this.setCustomValidity('')">
                        </div>
                    </div>
                    
                    <div class="input-group-modern">
                        <label class="form-label-modern">Password</label>
                        <div class="input-wrapper-modern">
                            <i class="bx bxs-lock-alt input-icon-modern"></i>
                            <input type="password" name="password" id="loginPassword" class="form-control-modern" placeholder="Masukkan password" required autocomplete="current-password" oninvalid="this.setCustomValidity('Harap isi password Anda.')" oninput="this.setCustomValidity('')">
                            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('loginPassword', this)">
                                <i class="bx bx-show"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="login-btn-container">
                        <button type="submit" class="btn-login-modern">
                            Login <i class="bx bx-right-arrow-alt" style="font-size: 1.15rem;"></i>
                        </button>
                    </div>
                    
                    <a href="<?= $public_url ?>" class="back-to-home">
                        <i class="bx bx-left-arrow-alt" style="font-size: 1.15rem;"></i> Kembali ke Beranda
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, toggleBtn) {
    const input = document.getElementById(inputId);
    const icon = toggleBtn.querySelector('i');
    if (input && icon) {
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bx-show');
            icon.classList.add('bx-hide');
            toggleBtn.style.color = '#10b981'; // light green highlight
        } else {
            input.type = 'password';
            icon.classList.remove('bx-hide');
            icon.classList.add('bx-show');
            toggleBtn.style.color = 'var(--gray-400)';
        }
    }
}
</script>
</body>
</html>
