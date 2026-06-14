<?php
// Query active announcements
$informasi = $conn->query("SELECT * FROM informasi WHERE aktif=1 ORDER BY tanggal DESC");

$informasi_list = [];
if ($informasi && $informasi->num_rows > 0) {
    while ($row = $informasi->fetch_assoc()) {
        $row['tanggal_formatted'] = formatTanggalShort($row['tanggal']);
        $informasi_list[] = $row;
    }
}

// Calculate if Kios is currently open or closed
$tz = new DateTimeZone('Asia/Makassar'); // WITA
$now = new DateTime('now', $tz);
$day = intval($now->format('w')); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
$hour = intval($now->format('G')); // 0-23
$minute = intval($now->format('i'));
$time_float = $hour + ($minute / 60.0);

$is_open = false;
if ($day >= 1 && $day <= 6) { // Monday to Saturday
    if ($time_float >= 8.0 && $time_float < 17.0) {
        $is_open = true;
    }
}
?>
<div style="height: 40px; background-color: transparent;"></div>

<section class="py-5" style="background: #f8fafc; min-height: 80vh;">
    <div class="container py-4">

    <!-- Header -->
    <div class="row justify-content-center text-center mb-5 animate__animated animate__fadeIn">
        <div class="col-lg-8">
            <span class="section-label">Informasi Layanan</span>
            <h2 class="section-title">Informasi & Kontak Kios</h2>
            <p class="text-secondary" style="font-size: 0.92rem;">
                Lihat informasi kios pengecer pupuk bersubsidi serta informasi terkait layanan dan penyaluran pupuk di Desa Lumaring.
            </p>
        </div>
    </div>

    <div class="row g-4 justify-content-center animate__animated animate__fadeInUp">

        <!-- Informasi Kios -->
        <div class="col-lg-5">
            <div class="card border-0 rounded-4 shadow-sm h-100 bg-white hover-up transition overflow-hidden"
                style="border: 1px solid rgba(226, 232, 240, 0.8) !important;">

                <!-- Cover Photo Banner -->
                <div class="position-relative" style="height: 140px; overflow: hidden; background: #0f172a;">
                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(16, 185, 129, 0.1), rgba(15, 23, 42, 0.82)); z-index: 1;"></div>
                    <img src="<?= $assets_url ?>/img/kios.jpg" alt="UD. Tani Winalda" style="width: 100%; height: 100%; object-fit: cover; filter: brightness(0.95);">
                    <div class="position-absolute bottom-0 start-0 p-4 w-100 text-white" style="z-index: 2;">
                        <h4 class="fw-bold mb-0 text-white" style="font-size: 1.25rem; letter-spacing: -0.5px;">UD. Tani Winalda</h4>
                        <span class="text-white-50 font-monospace" style="font-size: 0.72rem; letter-spacing: 0.5px;">Kios Pengecer Resmi</span>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <span class="fw-bold text-slate-800" style="font-size: 0.95rem;">Status Operasional</span>
                        <!-- Live Status Badge -->
                        <?php if ($is_open): ?>
                            <span class="badge bg-success-subtle text-success align-middle d-inline-flex align-items-center animate-pulse" style="font-size: 0.75rem; border: 1px solid rgba(16, 185, 129, 0.2); padding: 5px 12px;">
                                <span class="spinner-grow spinner-grow-sm text-success me-1.5" role="status" style="width: 8px; height: 8px; animation-duration: 1.2s;"></span> Buka Sekarang
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger align-middle d-inline-flex align-items-center" style="font-size: 0.75rem; border: 1px solid rgba(239, 68, 68, 0.2); padding: 5px 12px;">
                                <span class="d-inline-block rounded-circle bg-danger me-1.5" style="width: 8px; height: 8px;"></span> Tutup
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Alamat -->
                    <div class="d-flex align-items-start gap-3 mb-4 card-contact-item" style="transition: var(--transition);">
                        <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm icon-holder"
                            style="width: 44px; height: 44px; background: #ecfdf5; color: #10b981; flex-shrink: 0; transition: var(--transition);">
                            <i class="bx bxs-map" style="font-size: 1.35rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Alamat Kios</h6>
                            <p class="text-muted mb-0" style="font-size: 0.85rem; line-height: 1.5;">
                                Dusun Lumaring, Desa Lumaring, Kecamatan Larompong, Kabupaten Luwu.
                            </p>
                        </div>
                    </div>

                    <!-- Telepon -->
                    <div class="d-flex align-items-start gap-3 mb-4 card-contact-item" style="transition: var(--transition);">
                        <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm icon-holder"
                            style="width: 44px; height: 44px; background: #eff6ff; color: #3b82f6; flex-shrink: 0; transition: var(--transition);">
                            <i class="bx bxs-phone" style="font-size: 1.35rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold text-dark mb-1">Kontak Kios</h6>
                            <div class="d-flex align-items-center">
                                <span class="text-muted font-monospace" style="font-size: 0.85rem; font-weight: 500;">0813-5555-1234</span>
                                <button class="btn btn-sm btn-light p-1 rounded-circle ms-2 d-flex align-items-center justify-content-center" onclick="copyContactPhone('0813-5555-1234')" title="Salin Kontak" style="width: 26px; height: 26px; border: 1px solid #e2e8f0; background: #f8fafc;">
                                    <i class="bx bx-copy" style="font-size: 0.82rem;"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Jam Operasional -->
                    <div class="d-flex align-items-start gap-3 mb-4 card-contact-item" style="transition: var(--transition);">
                        <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm icon-holder"
                            style="width: 44px; height: 44px; background: #fffbeb; color: #f59e0b; flex-shrink: 0; transition: var(--transition);">
                            <i class="bx bxs-time" style="font-size: 1.35rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Jam Operasional</h6>
                            <p class="text-muted mb-0" style="font-size: 0.85rem; line-height: 1.5;">
                                Senin - Sabtu : 08.00 - 17.00 WITA
                                <br>
                                Minggu dan Hari Libur : Tutup
                            </p>
                        </div>
                    </div>

                    <!-- Peta Lokasi Kios -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2" style="font-size: 0.9rem;">
                            <i class="bx bxs-map-alt text-success"></i> Lokasi Peta Kios
                        </h6>
                        <div class="overflow-hidden rounded-3 shadow-sm position-relative" style="border: 1.5px solid var(--slate-200);">
                            <iframe width="100%" height="180" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?bbox=120.34656%2C-3.52552%2C120.35656%2C-3.51552&amp;layer=mapnik&amp;marker=-3.52052%2C120.35156" style="border: 0; display: block;"></iframe>
                        </div>
                        <div class="text-end mt-2">
                            <a href="https://www.google.com/maps/search/?api=1&query=-3.520524930822248,120.3515601387173" target="_blank" class="text-success fw-bold text-decoration-none" style="font-size: 0.8rem; transition: var(--transition);">
                                <i class="bx bx-navigation"></i> Buka Petunjuk Arah <i class="bx bx-right-arrow-alt align-middle"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Tombol WA -->
                    <div class="border-top pt-4">
                        <a href="https://wa.me/6285941923299"
                            target="_blank"
                            class="btn btn-success w-100 rounded-pill py-2.5 fw-bold d-inline-flex align-items-center justify-content-center gap-2 shadow-sm transition hover-up"
                            style="background:#25d366;border:none;box-shadow: 0 4px 12px rgba(37, 211, 102, 0.25);">
                            <i class="bx bxl-whatsapp" style="font-size:1.4rem;"></i>
                            Hubungi Kios melalui WhatsApp
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Informasi Layanan -->
        <div class="col-lg-7">
            <div class="card border-0 rounded-4 shadow-sm p-4 h-100 bg-white"
                style="border: 1px solid rgba(226, 232, 240, 0.8) !important;">

                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
                    <h4 class="fw-bold text-dark mb-0">
                        <i class="bx bxs-info-circle text-success align-middle me-2"></i>
                        Informasi Terbaru
                    </h4>
                    
                    <!-- Search Box -->
                    <div style="width: 250px; max-width: 100%;">
                        <div class="input-group shadow-sm border rounded-pill bg-light overflow-hidden px-2 py-0.5" style="border: 1.5px solid var(--slate-200) !important;">
                            <span class="input-group-text bg-transparent border-0 p-0 me-2 text-muted" style="padding-left: 8px !important;"><i class="bx bx-search"></i></span>
                            <input type="text" id="infoSearch" class="form-control bg-transparent border-0 p-0" placeholder="Cari pengumuman..." style="font-size: 0.82rem; height: 30px;">
                            <button class="btn btn-transparent border-0 text-muted p-0 ms-2" id="clearSearch" style="display:none; width:auto; height:auto; background:none;"><i class="bx bx-x fs-5"></i></button>
                        </div>
                    </div>
                </div>

                <div class="timeline-container" id="infoContainer">
                    <div class="timeline-line"></div>

                    <?php if(count($informasi_list) > 0): foreach($informasi_list as $row):?>

                    <div class="timeline-item transition" data-title="<?= htmlspecialchars(strtolower($row['judul'])) ?>" data-content="<?= htmlspecialchars(strtolower($row['isi'])) ?>" onclick="openAnnouncementModal('<?= htmlspecialchars(addslashes($row['judul'])) ?>', '<?= $row['tanggal_formatted'] ?>', '<?= htmlspecialchars(addslashes($row['isi'])) ?>')">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-success-subtle text-success" style="font-size:0.7rem; padding:4px 10px;">
                                    <i class="bx bx-calendar me-1"></i> <?= $row['tanggal_formatted'] ?>
                                </span>
                            </div>
                            <h5 class="fw-bold text-dark mb-2 info-title timeline-item-title" style="font-size:1rem; transition: var(--transition);">
                                <?= htmlspecialchars($row['judul']) ?>
                            </h5>
                            <p class="text-secondary mb-0 info-content timeline-item-excerpt">
                                <?= htmlspecialchars($row['isi']) ?>
                            </p>
                            <div class="text-end mt-2">
                                <span class="text-success fw-bold" style="font-size:0.8rem;">
                                    Baca Selengkapnya <i class="bx bx-right-arrow-alt align-middle"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <?php endforeach; ?>
                    
                    <!-- Fallback Search Empty State -->
                    <div class="text-center py-5" id="emptySearchState" style="display:none; padding-left: 0 !important; margin-left: -32px;">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-light text-muted mb-3" style="width: 60px; height: 60px;">
                            <i class="bx bx-search-alt" style="font-size: 1.8rem;"></i>
                        </div>
                        <h6 class="fw-bold text-slate-800 mb-1">Pengumuman Tidak Ditemukan</h6>
                        <p class="text-secondary mx-auto mb-0" style="font-size: 0.82rem; max-width: 300px;">
                            Tidak ada pengumuman yang cocok dengan kata kunci pencarian Anda.
                        </p>
                    </div>
                    
                    <?php else:?>

                    <div class="text-center py-5" style="margin-left: -32px;">
                        <i class="bx bx-info-circle text-muted display-4 mb-3"></i>
                        <p class="text-muted mb-0">
                            Belum ada informasi yang tersedia.
                        </p>
                    </div>

                    <?php endif;?>

                </div>

            </div>
        </div>

    </div>
</div>

<!-- Announcement Detail Modal -->
<div id="infoModalOverlay" class="detail-modal-overlay" onclick="closeInfoModal()">
    <div class="detail-modal-content p-4 animate__animated animate__zoomIn" onclick="event.stopPropagation()" style="max-width: 600px; border-radius: 20px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span id="modalDateBadge" class="badge bg-success-subtle text-success"></span>
            <button type="button" class="btn-close" onclick="closeInfoModal()" style="box-shadow: none;"></button>
        </div>
        <h4 id="modalTitle" class="fw-bold text-slate-800 mb-3" style="line-height: 1.3;"></h4>
        <div id="modalContent" class="text-secondary mb-4" style="font-size: 0.92rem; line-height: 1.7; white-space: pre-wrap;"></div>
        <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <button class="btn btn-outline-secondary rounded-pill px-4" onclick="copyModalLink()"><i class="bx bx-copy me-1"></i> Salin Pengumuman</button>
            <button class="btn btn-success rounded-pill px-4" onclick="closeInfoModal()">Tutup</button>
        </div>
    </div>
</div>

</section>

<!-- Additional Custom Styles -->
<style>
.card-contact-item:hover .icon-holder {
    transform: scale(1.15) rotate(5deg);
}

/* Timeline Styles */
.timeline-container {
    position: relative;
    padding-left: 32px;
}

.timeline-line {
    position: absolute;
    top: 8px;
    bottom: 8px;
    left: 9px;
    width: 2px;
    background: #e2e8f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-dot {
    position: absolute;
    left: -32px;
    top: 8px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #ffffff;
    border: 4px solid #10b981;
    z-index: 2;
    transition: all 0.3s ease;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
}

.timeline-item:hover .timeline-dot {
    background: #10b981;
    transform: scale(1.25);
    box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.25);
}

.timeline-content-card {
    background: #f8fafc;
    border: 1px solid rgba(226, 232, 240, 0.8);
    border-radius: 16px;
    padding: 20px;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.timeline-item:hover .timeline-content-card {
    background: #ffffff;
    transform: translateX(6px);
    border-color: rgba(16, 185, 129, 0.25);
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.05);
}

.timeline-item:hover .timeline-item-title {
    color: var(--primary-dark) !important;
}

.timeline-item-excerpt {
    font-size: 0.85rem;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.6;
    }
}
.animate-pulse {
    animation: pulse 2s infinite ease-in-out;
}
</style>

<script>
// Open detailed announcement modal
function openAnnouncementModal(title, date, content) {
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalDateBadge').innerHTML = '<i class="bx bx-calendar me-1"></i> ' + date;
    document.getElementById('modalContent').innerText = content;
    
    const overlay = document.getElementById('infoModalOverlay');
    overlay.style.display = 'flex';
    setTimeout(() => {
        overlay.classList.add('active');
    }, 10);
}

// Close announcement modal
function closeInfoModal() {
    const overlay = document.getElementById('infoModalOverlay');
    overlay.classList.remove('active');
    setTimeout(() => {
        overlay.style.display = 'none';
    }, 300);
}

// Copy phone contact to clipboard
function copyContactPhone(text) {
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Nomor telepon disalin!',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
    });
}

// Copy announcement text to clipboard
function copyModalLink() {
    const title = document.getElementById('modalTitle').innerText;
    const date = document.getElementById('modalDateBadge').innerText;
    const content = document.getElementById('modalContent').innerText;
    const fullText = `${title}\nTanggal: ${date}\n\n${content}`;
    
    navigator.clipboard.writeText(fullText).then(() => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Pengumuman berhasil disalin!',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('infoSearch');
    const clearBtn = document.getElementById('clearSearch');
    const infoItems = document.querySelectorAll('.timeline-item');
    const emptyState = document.getElementById('emptySearchState');
    const timelineLine = document.querySelector('.timeline-line');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let matchedCount = 0;

            if (query.length > 0) {
                clearBtn.style.display = 'block';
            } else {
                clearBtn.style.display = 'none';
            }

            infoItems.forEach(item => {
                const title = item.getAttribute('data-title');
                const content = item.getAttribute('data-content');

                if (title.includes(query) || content.includes(query)) {
                    item.style.display = 'block';
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'scale(1)';
                    }, 10);
                    matchedCount++;
                } else {
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        item.style.display = 'none';
                    }, 200);
                }
            });

            setTimeout(() => {
                if (matchedCount === 0 && infoItems.length > 0) {
                    emptyState.style.display = 'block';
                    if (timelineLine) timelineLine.style.display = 'none';
                } else {
                    emptyState.style.display = 'none';
                    if (timelineLine) timelineLine.style.display = 'block';
                }
            }, 200);
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            this.style.display = 'none';
            infoItems.forEach(item => {
                item.style.display = 'block';
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'scale(1)';
                }, 10);
            });
            emptyState.style.display = 'none';
            if (timelineLine) timelineLine.style.display = 'block';
            searchInput.focus();
        });
    }
});
</script>
