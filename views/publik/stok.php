<?php
$pupuk_list = $conn->query("SELECT * FROM pupuk ORDER BY FIELD(nama_pupuk, 'UREA', 'NPK PHONSKA', 'NPK PELANGI', 'ORGANIK', 'ZA') ASC");
?>
<!-- Page Header Spacer -->
<div style="height: 40px; background-color: transparent;"></div>

<section class="py-5" style="background: #f8fafc; min-height: 80vh;">
    <div class="container py-4">
        <div class="row justify-content-center text-center mb-5 animate__animated animate__fadeIn">
            <div class="col-lg-7">
                <span class="section-label">Data Stok</span>
                <h2 class="section-title">Ketersediaan Stok Pupuk</h2>
                <p class="text-secondary" style="font-size: 0.92rem;">Informasi jumlah persediaan pupuk bersubsidi yang tersedia di kios pengecer Desa Lumaring.</p>
            </div>
        </div>
 
        <!-- Search & Filter Control Panel -->
        <div class="row mb-5 justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 rounded-4 p-3 shadow-sm bg-white" style="border: 1px solid rgba(226, 232, 240, 0.8) !important;">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <!-- Search Box -->
                        <div class="w-100" style="max-width: 320px;">
                            <div class="input-group mb-0" style="border: 1.5px solid var(--slate-200); border-radius: 30px; background: var(--slate-50); overflow: hidden; transition: var(--transition);">
                                <span class="input-group-text bg-transparent border-0 text-emerald" style="padding-left: 16px;"><i class="bx bx-search-alt" style="font-size: 1.15rem;"></i></span>
                                <input type="text" id="stokSearchInput" placeholder="Cari nama pupuk..." class="form-control bg-transparent border-0 shadow-none py-2" style="font-size: 0.88rem; padding-left: 5px;">
                            </div>
                        </div>
                        <!-- Filter Pills -->
                        <div class="d-flex flex-row gap-2 justify-content-start justify-content-md-end align-items-center flex-nowrap" id="stokFilters" style="overflow-x: auto; -webkit-overflow-scrolling: touch; max-width: 100%;">
                            <button type="button" class="filter-pill active" data-filter="all">Semua</button>
                            <button type="button" class="filter-pill" data-filter="success"><i class="bx bxs-circle me-1 text-success"></i>Tersedia</button>
                            <button type="button" class="filter-pill" data-filter="warning"><i class="bx bxs-circle me-1 text-warning"></i>Terbatas</button>
                            <button type="button" class="filter-pill" data-filter="danger"><i class="bx bxs-circle me-1 text-danger"></i>Habis</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 
        <div class="row g-4 justify-content-center animate__animated animate__fadeInUp" id="stokCardContainer">
            <?php while($row=$pupuk_list->fetch_assoc()): 
                $st=getStokStatus($row['stok']); 
                // Determine icon and color based on fertilizer name
                $pupuk_lower = strtolower($row['nama_pupuk']);
                $card_color = '#10b981'; // default emerald
                $bg_color_subtle = '#ecfdf5';
                if (strpos($pupuk_lower, 'urea') !== false) {
                    $card_color = '#3b82f6'; // blue
                    $bg_color_subtle = '#eff6ff';
                } elseif (strpos($pupuk_lower, 'phonska') !== false || strpos($pupuk_lower, 'npk') !== false) {
                    $card_color = '#f59e0b'; // amber
                    $bg_color_subtle = '#fffbeb';
                } elseif (strpos($pupuk_lower, 'za') !== false) {
                    $card_color = '#8b5cf6'; // violet
                    $bg_color_subtle = '#f5f3ff';
                }
            ?>
            <div class="col-md-6 col-lg-4 stok-card-item" data-nama="<?= strtolower($row['nama_pupuk']) ?>" data-status="<?= $st['class'] ?>">
                <div class="card border-0 rounded-4 shadow-sm hover-up overflow-hidden h-100 transition bg-white" style="border: 1px solid rgba(226, 232, 240, 0.8) !important; max-width: 330px; margin: 0 auto;">
                    <!-- Top Accent Line -->
                    <div style="height: 4px; width: 100%; background-color: <?=$card_color?>;"></div>
                    
                    <!-- Visual representation at the top of the card -->
                    <div class="text-center d-flex flex-column align-items-center justify-content-center" style="background: #ffffff; height: 190px; padding: 24px 16px 8px 16px; border-bottom: 1px solid rgba(226, 232, 240, 0.5); overflow: hidden;">
                        <?php if ($row['foto']): ?>
                            <a href="javascript:void(0)" onclick="openLightbox('<?=$uploads_url?>/pupuk/<?=$row['foto']?>')" title="Klik untuk memperbesar" style="display: contents;">
                                <img src="<?=$uploads_url?>/pupuk/<?=$row['foto']?>" alt="<?=$row['nama_pupuk']?>" class="card-img-hover" style="max-height: 100%; max-width: 100%; object-fit: contain; cursor: zoom-in;">
                            </a>
                        <?php else: ?>
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-pill d-flex align-items-center justify-content-center shadow-sm mb-2" style="width: 52px; height: 52px; background: <?=$bg_color_subtle?>; color: <?=$card_color?>;">
                                    <i class="bx bxs-leaf" style="font-size: 1.5rem;"></i>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background: <?=$card_color?>; color: #fff; font-size: 0.72rem;"><?=$row['ukuran_kemasan']?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h4 class="fw-bold text-slate-800 mb-0" style="font-size: 1.25rem;"><?=$row['nama_pupuk']?></h4>
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold <?=$st['class']==='success'?'bg-success-subtle text-success':($st['class']==='warning'?'bg-warning-subtle text-warning':'bg-danger-subtle text-danger')?>" style="font-size: 0.75rem;">
                                <i class="bx bxs-circle align-middle me-1" style="font-size: 0.45rem;"></i> <?=$st['label']?>
                            </span>
                        </div>
                        
                        <div class="stok-meter mb-3">
                            <div class="d-flex justify-content-between text-muted mb-1" style="font-size: 0.78rem;">
                                <span>Sisa Stok</span>
                                <span class="fw-bold text-dark"><?=number_format($row['stok'] / ($row['berat_kemasan_kg'] ?: 50),0,',','.')?> sak</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 8px;">
                                <?php 
                                    $max_capacity = 10000; // default benchmark capacity
                                    $percent = min(100, ($row['stok'] / $max_capacity) * 100);
                                ?>
                                <div class="progress-bar rounded-pill" role="progressbar" style="width: <?=$percent?>%; background-color: <?=$card_color?>;" aria-valuenow="<?=$percent?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="row g-2 border-top pt-3 text-center" style="font-size: 0.82rem;">
                            <div class="col-6 border-end">
                                <div class="text-muted mb-0.5">Berat Kemasan</div>
                                <div class="fw-extrabold text-slate-800" style="font-size: 0.95rem;"><?=$row['ukuran_kemasan']?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted mb-0.5">Harga per Sak</div>
                                <div class="fw-extrabold text-slate-800" style="font-size: 0.95rem;"><?=formatRupiah($row['harga_per_sak'])?></div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="mt-4 pt-1">
                            <button type="button" class="btn w-100 rounded-pill py-2.5 fw-bold d-flex align-items-center justify-content-center transition shadow-sm detail-btn" 
                                    style="border: 2px solid <?=$card_color?>; background-color: transparent; color: <?=$card_color?>; font-size: 0.85rem;"
                                    onmouseover="this.style.backgroundColor='<?=$card_color?>'; this.style.color='#fff'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.08)';"
                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='<?=$card_color?>'; this.style.boxShadow='none';"
                                    data-nama="<?= htmlspecialchars($row['nama_pupuk']) ?>"
                                    data-foto="<?= $row['foto'] ? $uploads_url . '/pupuk/' . $row['foto'] : '' ?>"
                                    data-ukuran="<?= htmlspecialchars($row['ukuran_kemasan']) ?>"
                                    data-stok="<?= number_format($row['stok'] / ($row['berat_kemasan_kg'] ?: 50), 0, ',', '.') ?>"
                                    data-stok-class="<?= $st['class'] ?>"
                                    data-stok-label="<?= $st['label'] ?>"
                                    data-harga-sak="<?= htmlspecialchars(formatRupiah($row['harga_per_sak'])) ?>"
                                    data-raw-harga="<?= $row['harga_per_sak'] ?>"
                                    data-raw-berat="<?= $row['berat_kemasan_kg'] ?: 50 ?>"
                                    data-deskripsi="<?= htmlspecialchars($row['deskripsi'] ?? '-') ?>"
                                    data-color="<?= $card_color ?>"
                                    data-bg-color="<?= $bg_color_subtle ?>">
                                <i class="bx bx-info-circle align-middle me-1.5" style="font-size: 1.1rem;"></i> Lihat Detail Pupuk
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile;?>
        </div>

        <!-- Fallback Empty State -->
        <div id="emptyState" class="text-center py-5 d-none animate__animated animate__fadeIn">
            <div class="rounded-pill d-inline-flex align-items-center justify-content-center bg-light text-muted mb-3" style="width: 70px; height: 70px;">
                <i class="bx bx-package" style="font-size: 2.2rem;"></i>
            </div>
            <h4 class="fw-bold text-slate-800 mb-1" style="font-size: 1.15rem;">Stok Pupuk Tidak Ditemukan</h4>
            <p class="text-muted" style="font-size: 0.85rem; max-width: 320px; margin: 0 auto;">Coba kata kunci pencarian lain atau pilih filter ketersediaan lainnya.</p>
        </div>
    </div>
</section>

<!-- Modal Detail Pupuk -->
<div class="detail-modal-overlay" id="detailModal">
    <div class="detail-modal-content">
        <!-- Close Button -->
        <button type="button" class="btn-close" onclick="closeDetailModal()" style="position: absolute; right: 20px; top: 20px; background: #f1f5f9; border: none; width: 36px; height: 36px; border-radius: 50%; font-size: 1.2rem; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #64748b; z-index: 10; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'; this.style.color='#0f172a';" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b';">&times;</button>
        
        <div class="row g-0">
            <!-- Left Side: Visual & Quick Stats -->
            <div class="col-md-4 d-flex flex-column" id="modalLeftCol" style="background: #f8fafc; padding: 40px 30px; border-right: 1px solid #f1f5f9; text-align: center;">
                <div class="d-flex align-items-center justify-content-center rounded-4 mb-4 shadow-sm" id="modalImageWrapper" style="background: #fff; height: 180px; padding: 20px; border: 1px solid #f1f5f9;">
                    <img id="modalImage" src="" alt="" style="max-height: 100%; max-width: 100%; object-fit: contain; cursor: zoom-in;">
                </div>
                
                <h4 class="fw-bold text-slate-800 mb-1" id="modalNama" style="font-size: 1.4rem;"></h4>
                <div class="mb-4">
                    <span class="badge rounded-pill fw-bold" id="modalUkuran" style="font-size: 0.78rem; padding: 5px 12px; background: #e2e8f0; color: #475569;"></span>
                </div>
                
                <!-- Quick Stats Box -->
                <div class="p-3 rounded-4 text-start mb-3" style="background: #fff; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-secondary" style="font-size: 0.78rem;">Status:</span>
                        <span class="badge rounded-pill px-3 py-1 fw-bold" id="modalStokStatus" style="font-size: 0.75rem;"></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary" style="font-size: 0.78rem;">Stok Tersedia:</span>
                        <span class="fw-bold text-dark" id="modalStok" style="font-size: 0.9rem;"></span>
                    </div>
                </div>
                
                <!-- Price Box -->
                <div class="p-3 rounded-4 text-start" style="background: #fff; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-secondary" style="font-size: 0.78rem;">Berat Kemasan:</span>
                        <span class="fw-bold text-slate-800" id="modalHargaKg" style="font-size: 0.9rem;"></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary" style="font-size: 0.78rem;">Harga / Sak:</span>
                        <span class="fw-bold text-slate-800" id="modalHargaSak" style="font-size: 0.9rem;"></span>
                    </div>
                </div>
            </div>
            
            <!-- Right Side: Details -->
            <div class="col-md-8 d-flex flex-column justify-content-center" style="padding: 40px; text-align: left;">
                <div class="mb-4">
                    <span class="text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 1.5px; color: #10b981; display: block; margin-bottom: 4px;">Detail Deskripsi</span>
                    <h3 class="fw-bold text-slate-800" style="font-size: 1.35rem;">Informasi Lengkap Pupuk</h3>
                </div>
                
                <!-- Deskripsi Box -->
                <div class="mb-4 p-3 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-pill d-flex align-items-center justify-content-center me-2.5" id="modalDeskripsiIcon" style="width: 28px; height: 28px; color: #fff;">
                            <i class="bx bxs-detail" style="font-size: 1rem;"></i>
                        </div>
                        <h5 class="fw-bold text-slate-800 mb-0" style="font-size: 0.88rem;">Deskripsi Pupuk</h5>
                    </div>
                    <p class="text-secondary mb-0" id="modalDeskripsi" style="font-size: 0.82rem; line-height: 1.6; white-space: pre-line;"></p>
                </div>

                <!-- Interactive Calculator Box -->
                <div class="p-3 rounded-4" style="background: #ecfdf5; border: 1px solid #d1fae5; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.05);">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-pill d-flex align-items-center justify-content-center me-2 text-white" style="width: 28px; height: 28px; background-color: #10b981;">
                            <i class="bx bx-calculator" style="font-size: 1rem;"></i>
                        </div>
                        <h5 class="fw-bold text-slate-800 mb-0" style="font-size: 0.88rem;">Kalkulator Estimasi Penebusan</h5>
                    </div>
                    <div class="row g-2 align-items-center mt-1">
                        <div class="col-5">
                            <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 700; color: var(--slate-600);">Jumlah Tebus (Sak):</label>
                            <input type="number" id="calcBags" value="1" min="1" class="form-control py-1 px-2 text-center" style="font-size: 0.85rem; height: 34px; border-radius: 8px; border: 1.5px solid var(--slate-200);">
                        </div>
                        <div class="col-7">
                            <div class="d-flex flex-column text-end">
                                <span class="text-secondary" style="font-size: 0.72rem; font-weight: 600;">Total Estimasi Harga:</span>
                                <span class="fw-extrabold text-success mb-0" id="calcTotalHarga" style="font-size: 1.15rem; color: #10b981 !important; font-weight: 800;">Rp 0</span>
                                <span class="text-muted" id="calcTotalBerat" style="font-size: 0.68rem; margin-top: -2px; font-weight: 500;">Total Berat: 50 kg</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentPupukPrice = 0;
let currentPupukWeight = 50;

document.addEventListener('DOMContentLoaded', function() {
    // 1. Live Search & Status Filters
    const searchInput = document.getElementById('stokSearchInput');
    const filterPills = document.querySelectorAll('.filter-pill');
    const cards = document.querySelectorAll('.stok-card-item');
    const emptyState = document.getElementById('emptyState');
    let currentFilter = 'all';
    let searchQuery = '';

    function filterCards() {
        let visibleCount = 0;
        cards.forEach(card => {
            const nama = card.getAttribute('data-nama');
            const status = card.getAttribute('data-status');
            
            const matchesSearch = nama.includes(searchQuery);
            const matchesFilter = (currentFilter === 'all' || status === currentFilter);
            
            if (matchesSearch && matchesFilter) {
                card.classList.remove('d-none');
                // Trigger reflow for transition
                card.style.opacity = '0';
                card.style.transform = 'translateY(15px)';
                setTimeout(() => {
                    card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 10);
                visibleCount++;
            } else {
                card.classList.add('d-none');
            }
        });

        if (visibleCount === 0) {
            emptyState.classList.remove('d-none');
        } else {
            emptyState.classList.add('d-none');
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            searchQuery = this.value.toLowerCase().trim();
            filterCards();
        });
    }

    filterPills.forEach(pill => {
        pill.addEventListener('click', function() {
            filterPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter');
            filterCards();
        });
    });

    // 2. Details Buttons mapping
    const detailButtons = document.querySelectorAll('.detail-btn');
    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            const data = {
                nama: this.getAttribute('data-nama'),
                foto: this.getAttribute('data-foto'),
                ukuran: this.getAttribute('data-ukuran'),
                stok: this.getAttribute('data-stok'),
                stok_class: this.getAttribute('data-stok-class'),
                stok_label: this.getAttribute('data-stok-label'),
                harga_sak: this.getAttribute('data-harga-sak'),
                raw_harga: parseFloat(this.getAttribute('data-raw-harga')) || 0,
                raw_berat: parseFloat(this.getAttribute('data-raw-berat')) || 50,
                deskripsi: this.getAttribute('data-deskripsi'),
                color: this.getAttribute('data-color'),
                bg_color: this.getAttribute('data-bg-color')
            };
            openDetailModal(data);
        });
    });

    // 3. Calculator Event Listeners
    const calcBagsInput = document.getElementById('calcBags');
    if (calcBagsInput) {
        calcBagsInput.addEventListener('input', updateCalculator);
        calcBagsInput.addEventListener('change', updateCalculator);
    }

    // Close on clicking outside modal content
    const modalEl = document.getElementById('detailModal');
    if (modalEl) {
        modalEl.addEventListener('click', function(event) {
            if (event.target === this) {
                closeDetailModal();
            }
        });
    }
    
    const modalImg = document.getElementById('modalImage');
    if (modalImg) {
        modalImg.addEventListener('click', function() {
            if (this.src) {
                openLightbox(this.src);
            }
        });
    }
});

function openDetailModal(data) {
    const modal = document.getElementById('detailModal');
    if (!modal) return;
    
    // Set text contents
    document.getElementById('modalNama').textContent = data.nama;
    document.getElementById('modalUkuran').textContent = 'Kemasan: ' + data.ukuran;
    document.getElementById('modalStok').textContent = data.stok + ' sak';
    document.getElementById('modalHargaKg').textContent = data.ukuran;
    document.getElementById('modalHargaSak').textContent = data.harga_sak;
    document.getElementById('modalDeskripsi').textContent = data.deskripsi;
    
    // Set badge status
    const statusBadge = document.getElementById('modalStokStatus');
    statusBadge.textContent = data.stok_label;
    statusBadge.className = 'badge rounded-pill px-3 py-1 fw-bold ';
    if (data.stok_class === 'success') {
        statusBadge.className += 'bg-success-subtle text-success';
    } else if (data.stok_class === 'warning') {
        statusBadge.className += 'bg-warning-subtle text-warning';
    } else {
        statusBadge.className += 'bg-danger-subtle text-danger';
    }
    
    // Set Left Side Background subtly
    document.getElementById('modalLeftCol').style.backgroundColor = data.bg_color;
    
    // Image handling
    const img = document.getElementById('modalImage');
    const imgWrapper = document.getElementById('modalImageWrapper');
    if (data.foto) {
        img.src = data.foto;
        img.alt = data.nama;
        imgWrapper.style.setProperty('display', 'flex', 'important');
    } else {
        imgWrapper.style.setProperty('display', 'none', 'important');
    }
    
    // Icons styling
    document.getElementById('modalDeskripsiIcon').style.backgroundColor = data.color;
    
    // Set raw parameters for calculator
    currentPupukPrice = data.raw_harga;
    currentPupukWeight = data.raw_berat;
    
    // Reset calculator input
    const calcBagsInput = document.getElementById('calcBags');
    if (calcBagsInput) {
        calcBagsInput.value = 1;
    }
    updateCalculator();

    // Show Modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden'; // prevent background scrolling
}

function updateCalculator() {
    const calcBagsInput = document.getElementById('calcBags');
    if (!calcBagsInput) return;
    
    const bags = Math.max(1, parseInt(calcBagsInput.value, 10) || 1);
    const totalPrice = bags * currentPupukPrice;
    const totalWeight = bags * currentPupukWeight;
    
    document.getElementById('calcTotalHarga').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
    document.getElementById('calcTotalBerat').textContent = `Total Berat: ${totalWeight} kg`;
}

function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = ''; // restore scrolling
    }
}

// Close on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') closeDetailModal();
});
</script>

<!-- Lightbox Modal untuk Foto Pupuk -->
<div class="lightbox-modal-overlay" id="lightboxModal" onclick="closeLightbox()">
    <div class="lightbox-modal-content" onclick="event.stopPropagation()">
        <div class="modal-header" style="padding: 0 0 15px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--dark); margin: 0;"><i class="bx bx-image text-success"></i> Foto Pupuk</h3>
            <button type="button" class="btn-close" onclick="closeLightbox()" style="background: #f1f5f9; border: none; width: 36px; height: 36px; border-radius: 50%; font-size: 1.2rem; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #64748b; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'; this.style.color='#0f172a';" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b';">&times;</button>
        </div>
        <div style="text-align: center; background: var(--gray-50); border-radius: 10px; padding: 10px; border: 1px dashed var(--gray-200);">
            <img id="lightboxImage" src="" alt="Foto Pupuk" style="max-width: 100%; max-height: 60vh; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
        </div>
        <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="btn btn-primary" onclick="closeLightbox()" style="font-weight: 600; min-width: 100px;">
                <i class="bx bx-arrow-back"></i> Kembali
            </button>
        </div>
    </div>
</div>

<script>
function openLightbox(imageSrc) {
    const modal = document.getElementById('lightboxModal');
    const img = document.getElementById('lightboxImage');
    if (modal && img) {
        img.src = imageSrc;
        modal.classList.add('active');
    }
}

function closeLightbox() {
    const modal = document.getElementById('lightboxModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

// Close lightbox on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeLightbox();
    }
});
</script>
