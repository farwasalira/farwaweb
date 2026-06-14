<?php
// Query live statistics for counters
$count_petani = 0;
$count_poktan = 0;
$count_pupuk = 0;
$total_distribusi = 0;

if (isset($conn)) {
    // 1. Registered Farmers
    $q_petani = $conn->query("SELECT COUNT(*) as c FROM petani");
    if ($q_petani) $count_petani = $q_petani->fetch_assoc()['c'];

    // 2. Kelompok Tani
    $q_poktan = $conn->query("SELECT COUNT(*) as c FROM kelompok_tani");
    if ($q_poktan) $count_poktan = $q_poktan->fetch_assoc()['c'];

    // 3. Pupuk varieties
    $q_pupuk = $conn->query("SELECT COUNT(*) as c FROM pupuk");
    if ($q_pupuk) $count_pupuk = $q_pupuk->fetch_assoc()['c'];

    // 4. Total Penyaluran (dalam kg)
    $q_dist = $conn->query("SELECT SUM(jumlah) as s FROM penyaluran");
    if ($q_dist) {
        $row_dist = $q_dist->fetch_assoc();
        $total_distribusi = $row_dist['s'] ?? 0;
    }
}
?>

<!-- Hero Section -->
<section class="hero position-relative d-flex align-items-center justify-content-center text-center text-white overflow-hidden" style="min-height: 95vh; background: #0f172a; padding: 120px 0 60px;">
    
    <!-- Background Carousel Slider -->
    <div id="heroCarousel" class="carousel slide carousel-fade position-absolute top-0 start-0 w-100 h-100" data-bs-ride="carousel" data-bs-interval="5000" style="z-index: 1;">
        <div class="carousel-inner h-100">
            <!-- Slide 1 -->
            <div class="carousel-item active h-100">
                <div class="w-100 h-100" style="background: linear-gradient(rgba(19, 106, 60, 0.68), rgba(15, 23, 42, 0.88)), url('<?= $assets_url ?>/img/sawah.jpg') no-repeat center bottom; background-size: cover;"></div>
            </div>
            <!-- Slide 2 -->
            <div class="carousel-item h-100">
                <div class="w-100 h-100" style="background: linear-gradient(rgba(19, 106, 60, 0.68), rgba(15, 23, 42, 0.88)), url('<?= $assets_url ?>/img/padi_hijau.jpg') no-repeat center center; background-size: cover;"></div>
            </div>
            <!-- Slide 3 -->
            <div class="carousel-item h-100">
                <div class="w-100 h-100" style="background: linear-gradient(rgba(19, 106, 60, 0.68), rgba(15, 23, 42, 0.88)), url('<?= $assets_url ?>/img/panen_padi.jpg') no-repeat center center; background-size: cover;"></div>
            </div>
        </div>
    </div>
 
    <!-- Floating background circles for modern abstract visual depth -->
    <div class="floating-circle floating-circle-1" style="z-index: 2;"></div>
    <div class="floating-circle floating-circle-2" style="z-index: 2;"></div>
    
    <div class="container position-relative z-3">
        <h1 class="fw-extrabold text-white mb-3" style="font-weight: 800; line-height: 1.25;">
            <span class="d-block animate-slide-top">Sistem Informasi Stok dan Distribusi</span>
            <span class="d-block"><span class="text-transparent bg-clip-text d-inline-block split-chars" style="background-image: linear-gradient(135deg, #10b981 0%, #34d399 100%);">Pupuk Bersubsidi</span></span>
        </h1>
        
        <p class="lead text-white-50 mx-auto mb-5 animate-slide-bottom delay-1100" style="max-width: 680px; font-size: 1.05rem; line-height: 1.6;">
            Menyediakan informasi stok, alokasi, dan penyaluran pupuk bersubsidi di Desa Lumaring secara mudah dan transparan.
        </p>
        
        <div class="d-flex flex-wrap gap-3 justify-content-center animate-slide-bottom delay-1400">
            <a href="#tentang-sistem" class="btn btn-outline-light px-5 py-3 rounded-pill text-white fw-bold d-flex align-items-center gap-2 shadow-lg" style="font-size: 0.95rem; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important; -webkit-backdrop-filter: blur(10px);">
                Selengkapnya <i class="bx bx-down-arrow-alt fs-5 animate-bounce"></i>
            </a>
        </div>
    </div>

    <!-- Wavy Curve bottom divider -->
    <div class="position-absolute bottom-0 start-0 w-100" style="overflow: hidden; line-height: 0; z-index: 3;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" preserveAspectRatio="none" style="position: relative; display: block; width: calc(100% + 1.3px); height: 50px;">
            <path d="M1440,120L0,120L0,0C120,40,240,60,360,60C480,60,600,40,720,20C840,0,960,0,1080,20C1200,40,1320,80,1440,100Z" fill="#ffffff"></path>
        </svg>
    </div>
</section>


<!-- Tentang Sistem Section -->
<section id="tentang-sistem" class="py-5" style="background: #ffffff;">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <!-- Left Column: Stacked Images Layout -->
            <div class="col-lg-6">
                <div class="about-image-wrapper">
                    <!-- Main Image (Padi Hijau) -->
                    <img src="<?= $assets_url ?>/img/kios.jpg" alt="Kios" class="about-img-main">
                    
                    <!-- Overlapping Sub Image (Panen Padi) -->
                    <img src="<?= $assets_url ?>/img/stok.jpg" alt="Ketersediaan Pupuk" class="about-img-sub">


                </div>
            </div>

            <!-- Right Column: Content -->
            <div class="col-lg-6">
                <span class="section-label">Tentang Sistem</span>
                <h2 class="section-title mb-3">Portal Distribusi & Stok Pupuk Bersubsidi</h2>
                <p class="text-secondary mb-4" style="font-size: 0.95rem; line-height: 1.65;">
                    SIPUPUK merupakan sistem informasi yang digunakan untuk mengelola data stok, alokasi, dan penyaluran pupuk bersubsidi di Desa Lumaring.
                </p>
                <p class="text-secondary mb-0" style="font-size: 0.95rem; line-height: 1.65;">
                    Melalui sistem ini, pengguna dapat melihat ketersediaan stok pupuk di kios pengecer, mengecek alokasi pupuk berdasarkan NIK petani, serta melihat riwayat penyaluran pupuk yang telah diterima.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Alur Layanan Section (Interactive Stepper) -->
<section id="alur-layanan" class="py-4" style="background: #f8fafc;">
    <div class="container py-2">
        <div class="row justify-content-center text-center mb-4">
            <div class="col-lg-8">
                <span class="section-label">Alur Layanan</span>
                <h2 class="section-title mb-2">Bagaimana Petani Mendapatkan Pupuk Bersubsidi?</h2>
                <p class="text-secondary" style="font-size: 0.95rem;">
                    Ikuti 3 tahapan utama dari pendaftaran hingga penebusan pupuk bersubsidi secara resmi di Kios Desa Lumaring.
                </p>
            </div>
        </div>

        <div class="stepper-container">
            <div class="row align-items-center g-4">
                <!-- Left Column: Tabs Stepper Buttons -->
                <div class="col-lg-5">
                    <div class="stepper-nav">
                        <!-- Step 1 Button -->
                        <button type="button" class="stepper-btn active" onclick="switchStep(1)">
                            <div class="stepper-badge">1</div>
                            <div>
                                <h4 class="stepper-title">Terdaftar di Kelompok Tani</h4>
                                <p class="stepper-desc">Petani harus terdaftar dalam sistem RDKK (Rencana Definitif Kebutuhan Kelompok) yang dikoordinasikan oleh ketua Poktan.</p>
                            </div>
                        </button>
                        
                        <!-- Step 2 Button -->
                        <button type="button" class="stepper-btn" onclick="switchStep(2)">
                            <div class="stepper-badge">2</div>
                            <div>
                                <h4 class="stepper-title">Mengecek Alokasi & Kuota</h4>
                                <p class="stepper-desc">Periksa kuota pupuk bersubsidi tahunan Anda melalui pencarian NIK di menu Alokasi di portal SIPUPUK.</p>
                            </div>
                        </button>
                        
                        <!-- Step 3 Button -->
                        <button type="button" class="stepper-btn" onclick="switchStep(3)">
                            <div class="stepper-badge">3</div>
                            <div>
                                <h4 class="stepper-title">Penebusan di Kios Resmi</h4>
                                <p class="stepper-desc">Bawa kartu identitas (KTP) ke kios pengecer resmi Desa Lumaring untuk menebus kuota pupuk yang disetujui.</p>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Right Column: Interactive Detail Display cards -->
                <div class="col-lg-7">
                    <div class="stepper-content-wrapper">
                        <!-- Card 1 Detail -->
                        <div class="stepper-content-card active" id="step-content-1">
                            <h4 class="fw-bold mb-3" style="color: var(--primary-dark);">Pendaftaran RDKK Kelompok Tani</h4>
                            <p class="text-secondary" style="font-size: 0.9rem; line-height: 1.65; margin-bottom: 20px;">
                                Langkah awal bagi setiap petani untuk berhak mendapatkan pupuk bersubsidi adalah dengan tergabung dalam salah satu kelompok tani yang sah di Desa Lumaring. Ketua kelompok tani mengumpulkan data kartu keluarga dan NIK anggotanya untuk kemudian diajukan ke dinas pertanian kabupaten untuk divalidasi dan diunggah ke database e-RDKK kementerian.
                            </p>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-emerald-subtle text-emerald"><i class="bx bx-check me-1"></i> Data NIK KK Valid</span>
                                <span class="badge bg-emerald-subtle text-emerald"><i class="bx bx-check me-1"></i> Pemilik Lahan &lt; 2 Hektar</span>
                            </div>
                        </div>

                        <!-- Card 2 Detail -->
                        <div class="stepper-content-card" id="step-content-2">
                            <h4 class="fw-bold mb-3" style="color: var(--primary-dark);">Pengecekan Kuota Alokasi Tahunan</h4>
                            <p class="text-secondary" style="font-size: 0.9rem; line-height: 1.65; margin-bottom: 20px;">
                                Setelah RDKK disetujui, kuota alokasi pupuk per petani didasarkan pada luas lahan masing-masing petani (maksimal 2 Hektar per musim tanam). Melalui website SIPUPUK, Anda bisa memasukkan 16 digit NIK secara langsung untuk melihat jatah kuota Urea, NPK, maupun pupuk organik yang dialokasikan khusus untuk Anda.
                            </p>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-emerald-subtle text-emerald"><i class="bx bx-search-alt me-1"></i> Pencarian Cepat NIK</span>
                                <span class="badge bg-emerald-subtle text-emerald"><i class="bx bx-show me-1"></i> Transparansi Kuota</span>
                            </div>
                        </div>

                        <!-- Card 3 Detail -->
                        <div class="stepper-content-card" id="step-content-3">
                            <h4 class="fw-bold mb-3" style="color: var(--primary-dark);">Penebusan Pupuk di Kios Resmi</h4>
                            <p class="text-secondary" style="font-size: 0.9rem; line-height: 1.65; margin-bottom: 20px;">
                                Petani dapat mendatangi Kios Pengecer Resmi yang ditunjuk untuk wilayah Desa Lumaring dengan membawa dokumen identitas berupa KTP asli. Kios akan memeriksa sisa kuota Anda di sistem dan memproses transaksi penebusan. Setelah ditebus, data penyaluran akan ter-update dan tercatat secara digital di sistem SIPUPUK.
                            </p>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-emerald-subtle text-emerald"><i class="bx bx-wallet me-1"></i> Pembayaran Sesuai HET</span>
                                <span class="badge bg-emerald-subtle text-emerald"><i class="bx bx-receipt me-1"></i> Bukti Penyaluran Digital</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Menu Informasi -->
<section class="py-5" style="background: #ffffff;">
    <div class="container py-4">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-7">
                <span class="section-label">Layanan Kami</span>
                <h2 class="section-title">Menu Portal Informasi</h2>
                <p class="text-secondary" style="font-size: 0.92rem;">
                    Akses cepat data stok, alokasi pupuk per petani, riwayat transaksi penyaluran, serta informasi penting lainnya.
                </p>
            </div>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Stok Card -->
            <div class="col-sm-6 col-lg-3">
                <a href="<?=$public_url?>?page=stok" class="card border-0 rounded-4 p-4 text-center h-100 shadow-sm hover-up transition bg-white text-decoration-none info-card">
                    <div class="info-card-icon green">
                        <i class="bx bxs-package" style="font-size: 1.65rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2 text-dark" style="font-size: 1.05rem;">Stok Pupuk</h5>
                    <p class="text-muted mb-0" style="font-size: 0.82rem;">
                        Pantau sisa ketersediaan stok pupuk bersubsidi terkini yang ada di kios pengecer resmi.
                    </p>
                </a>
            </div>

            <!-- Alokasi Card -->
            <div class="col-sm-6 col-lg-3">
                <a href="<?=$public_url?>?page=alokasi" class="card border-0 rounded-4 p-4 text-center h-100 shadow-sm hover-up transition bg-white text-decoration-none info-card">
                    <div class="info-card-icon blue">
                        <i class="bx bxs-group" style="font-size: 1.65rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2 text-dark" style="font-size: 1.05rem;">Alokasi Kuota</h5>
                    <p class="text-muted mb-0" style="font-size: 0.82rem;">
                        Periksa jatah alokasi pupuk bersubsidi Anda untuk tahun ini berdasarkan NIK yang terdaftar.
                    </p>
                </a>
            </div>

            <!-- Penyaluran Card -->
            <div class="col-sm-6 col-lg-3">
                <a href="<?=$public_url?>?page=penyaluran" class="card border-0 rounded-4 p-4 text-center h-100 shadow-sm hover-up transition bg-white text-decoration-none info-card">
                    <div class="info-card-icon orange">
                        <i class="bx bxs-truck" style="font-size: 1.65rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2 text-dark" style="font-size: 1.05rem;">Riwayat Salur</h5>
                    <p class="text-muted mb-0" style="font-size: 0.82rem;">
                        Periksa riwayat penebusan dan penyaluran pupuk bersubsidi yang telah diserahkan kepada petani.
                    </p>
                </a>
            </div>

            <!-- Informasi Card -->
            <div class="col-sm-6 col-lg-3">
                <a href="<?=$public_url?>?page=informasi" class="card border-0 rounded-4 p-4 text-center h-100 shadow-sm hover-up transition bg-white text-decoration-none info-card">
                    <div class="info-card-icon red">
                        <i class="bx bxs-info-circle" style="font-size: 1.65rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2 text-dark" style="font-size: 1.05rem;">Info & Kontak</h5>
                    <p class="text-muted mb-0" style="font-size: 0.82rem;">
                        Temukan pengumuman terbaru, profil kios, kontak bantuan, dan berita penting seputar distribusi pupuk.
                    </p>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container py-4">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-7">
                <span class="section-label">Tanya Jawab</span>
                <h2 class="section-title">Pertanyaan Sering Diajukan (FAQ)</h2>
                <p class="text-secondary" style="font-size: 0.92rem;">
                    Temukan jawaban atas beberapa pertanyaan umum mengenai alokasi dan tata cara tebus pupuk bersubsidi.
                </p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="accordion faq-accordion" id="faqAccordion">
                    <!-- FAQ 1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                Siapa saja yang berhak mendapatkan pupuk bersubsidi?
                                <div class="faq-icon-holder">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </button>
                        </h2>
                        <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Sesuai peraturan kementerian pertanian, petani yang berhak mendapatkan pupuk bersubsidi adalah mereka yang tergabung dalam kelompok tani yang sah, terdaftar di RDKK, memiliki luas lahan pertanian maksimal 2 Hektar per musim tanam, serta berfokus pada komoditas pangan pokok (padi, jagung, kedelai), hortikultura (cabai, bawang merah, bawang putih), atau perkebunan rakyat tertentu.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                Bagaimana cara mengecek sisa jatah kuota pupuk saya?
                                <div class="faq-icon-holder">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </button>
                        </h2>
                        <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Anda dapat mengecek sisa alokasi kuota secara langsung pada website ini. Di bagian atas halaman beranda, atau melalui menu <b>Alokasi</b>, masukkan 16 digit NIK Anda pada kotak pencarian lalu klik "Cari". Sistem akan menampilkan tabel rincian jatah pupuk Anda beserta kuota yang sudah diambil dan sisa kuota yang dapat ditebus.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                Apa syarat membawa berkas saat melakukan penebusan pupuk di kios?
                                <div class="faq-icon-holder">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </button>
                        </h2>
                        <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Saat mendatangi Kios Pengecer Resmi Desa Lumaring, Anda cukup membawa <b>Kartu Tanda Penduduk (KTP) asli</b> Anda. Pemilik Kios akan memindai KTP Anda atau mencocokkan NIK Anda di aplikasi penebusan pupuk untuk mencatat transaksi secara resmi. Transaksi wajib dilakukan oleh petani yang bersangkutan atau anggota keluarga dalam satu Kartu Keluarga yang diberi kuasa.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                Apa yang harus saya lakukan jika NIK saya tidak ditemukan dalam sistem?
                                <div class="faq-icon-holder">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </button>
                        </h2>
                        <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Jika NIK Anda tidak ditemukan saat pencarian alokasi, silakan hubungi Ketua Kelompok Tani Anda untuk mengonfirmasi apakah data Anda sudah diajukan ke dinas terkait dan diunggah ke e-RDKK kementerian. Anda juga bisa mendatangi dinas pertanian terdekat atau penyuluh pertanian lapangan (PPL) Desa Lumaring untuk mendapatkan klarifikasi lebih lanjut.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JS Interactive Scripts -->
<script>
// Tab Timeline Stepper switching function
function switchStep(stepNum) {
    // 1. Update stepper buttons active state
    const buttons = document.querySelectorAll('.stepper-btn');
    buttons.forEach((btn, index) => {
        if (index + 1 === stepNum) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    // 2. Hide all content cards with smooth fading
    const cards = document.querySelectorAll('.stepper-content-card');
    cards.forEach((card) => {
        card.classList.remove('active');
    });

    // 3. Show active content card
    const activeCard = document.getElementById('step-content-' + stepNum);
    if (activeCard) {
        activeCard.classList.add('active');
    }
}

// Split text chars animation
document.addEventListener('DOMContentLoaded', () => {
    // Split text into letters for per-character animation
    const splitElements = document.querySelectorAll('.split-chars');
    splitElements.forEach(el => {
        const text = el.textContent;
        el.innerHTML = '';
        [...text].forEach((char, index) => {
            const span = document.createElement('span');
            if (char === ' ') {
                span.innerHTML = '&nbsp;';
            } else {
                span.textContent = char;
            }
            span.className = 'char-slide-up';
            // Base delay of 0.4s + staggered delay of 0.04s per char
            span.style.animationDelay = `${0.4 + (index * 0.04)}s`;
            el.appendChild(span);
        });
    });
});
</script>