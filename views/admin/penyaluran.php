<!-- Data Penyaluran View -->
<style>
.quota-info-panel {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    padding: 16px;
    margin-top: 15px;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
}
.quota-info-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.quota-info-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 8px;
}
.quota-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px;
    text-align: center;
    transition: all 0.2s ease;
}
.quota-item:hover {
    border-color: #10b981;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1);
}
.quota-name {
    font-size: 0.72rem;
    font-weight: 800;
    color: #334155;
    margin-bottom: 4px;
}
.quota-val {
    font-size: 0.85rem;
    font-weight: 700;
}
.quota-val.sisa-safe {
    color: #10b981;
}
.quota-val.sisa-warning {
    color: #f59e0b;
}
.quota-val.sisa-empty {
    color: #ef4444;
}
.quota-lbl {
    font-size: 0.65rem;
    color: #64748b;
    margin-top: 2px;
}
.duplicate-warning {
    background: #fef3c7;
    border: 1px solid #fcd34d;
    border-radius: 8px;
    padding: 10px 12px;
    color: #92400e;
    font-size: 0.82rem;
    font-weight: 600;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
    animation: pulseWarning 2s infinite ease-in-out;
}
@keyframes pulseWarning {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.85; }
}
.suggestions-list {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    max-height: 200px;
    overflow-y: auto;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1050;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.suggestion-item {
    padding: 10px 14px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.85rem;
    transition: background-color 0.15s ease;
    text-align: left;
}
.suggestion-item:last-child {
    border-bottom: none;
}
.suggestion-item:hover {
    background-color: #f1f5f9;
}
.suggestion-item strong {
    color: #1e293b;
    display: block;
}
.suggestion-item span {
    color: #64748b;
    font-size: 0.78rem;
}
</style>

<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 10px;">
        <h3><i class="bx bx-send"></i> Data Penyaluran Pupuk</h3>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form method="GET" action="" style="display: flex; gap: 8px;">
                <input type="hidden" name="page" value="penyaluran">
                <input type="text" name="q" class="form-control" placeholder="Cari NIK atau Nama..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" style="width: 220px; padding: 6px 12px; font-size: 0.85rem; border-radius: 6px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-search"></i></button>
                <?php if(isset($_GET['q']) && $_GET['q'] !== ''): ?>
                    <a href="<?= $admin_url ?>?page=penyaluran" class="btn btn-outline btn-sm"><i class="bx bx-x"></i></a>
                <?php endif; ?>
            </form>
            <button class="btn btn-primary btn-sm" onclick="document.getElementById('modalTambah').classList.add('active')">
                <i class="bx bx-plus"></i> Tambah Data
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Petani</th>
                    <th>Kelompok</th>
                    <th>UREA</th>
                    <th>PHONSKA</th>
                    <th>NPK PELANGI</th>
                    <th>ORGANIK</th>
                    <th>ZA</th>
                    <th>Jumlah Total</th>
                    <th>Harga Total</th>
                    <th>Bukti</th>
                    <th style="width: 100px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = isset($offset) ? $offset + 1 : 1; while ($row = $penyaluran_list->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= formatTanggalShort($row['tanggal']) ?></td>
                    <td><strong><?= $row['nama_petani'] ?></strong></td>
                    <td><?= $row['nama_kelompok'] ?></td>
                    <td><?= number_format($row['urea'], 0, ',', '.') ?> sak</td>
                    <td><?= number_format($row['phonska'], 0, ',', '.') ?> sak</td>
                    <td><?= number_format($row['sp36'], 0, ',', '.') ?> sak</td>
                    <td><?= number_format($row['organik'], 0, ',', '.') ?> sak</td>
                    <td><?= number_format($row['za'], 0, ',', '.') ?> sak</td>
                    <td><strong><?= number_format($row['total'], 0, ',', '.') ?> sak</strong></td>
                    <td><strong>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></strong></td>
                    <td style="text-align: center;">
                        <?php if ($row['bukti']): ?>
                        <a href="javascript:void(0)" onclick="openLightbox('<?= $uploads_url ?>/bukti/<?= $row['bukti'] ?>')" class="btn btn-info btn-xs" title="Lihat bukti pengiriman">
                            <i class="bx bx-image"></i>
                        </a>
                        <?php else: ?>
                        <span style="color: var(--gray-400)">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center;">
                        <div class="btn-group">
                            <a href="<?= $admin_url ?>?page=penyaluran&action=edit&id_petani=<?= $row['id_petani'] ?>&tanggal=<?= $row['tanggal'] ?>&status=<?= urlencode($row['status']) ?>" class="btn btn-warning btn-xs" title="Edit"><i class="bx bx-edit"></i></a>
                            <a href="<?= $admin_url ?>?page=penyaluran&action=delete&id_petani=<?= $row['id_petani'] ?>&tanggal=<?= $row['tanggal'] ?>&status=<?= urlencode($row['status']) ?>" class="btn btn-danger btn-xs" title="Hapus"><i class="bx bx-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($penyaluran_list->num_rows === 0): ?>
                <tr>
                    <td colspan="13" style="text-align: center; padding: 40px; color: var(--gray-400);">Belum ada data penyaluran pupuk.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if (isset($total_pages) && $total_pages > 1): ?>
    <div class="pagination">
        <?php if ($page_num > 1): ?>
            <a href="<?= $admin_url ?>?page=penyaluran&p=<?= $page_num - 1 ?><?= $search ? '&q='.urlencode($search) : '' ?>">&laquo; Sebelumnya</a>
        <?php endif; ?>
        
        <?php 
        $start_page = max(1, $page_num - 2);
        $end_page = min($total_pages, $page_num + 2);
        if ($start_page > 1) { echo '<span>...</span>'; }
        for ($i = $start_page; $i <= $end_page; $i++): 
        ?>
            <a href="<?= $admin_url ?>?page=penyaluran&p=<?= $i ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="<?= $i === $page_num ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($end_page < $total_pages) { echo '<span>...</span>'; } ?>

        <?php if ($page_num < $total_pages): ?>
            <a href="<?= $admin_url ?>?page=penyaluran&p=<?= $page_num + 1 ?><?= $search ? '&q='.urlencode($search) : '' ?>">Selanjutnya &raquo;</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal">
        <div class="modal-header">
            <h3>Tambah Penyaluran</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="<?= $admin_url ?>?page=penyaluran&action=store" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group" style="position: relative;">
                        <label class="form-label">Petani (NIK / Nama)</label>
                        <input type="hidden" name="id_petani" id="selectPetaniTambah" required>
                        <input type="text" id="searchPetaniTambah" class="form-control" placeholder="Ketik NIK atau Nama Petani..." autocomplete="off" required>
                        <div id="suggestionsTambah" class="suggestions-list" style="display:none;"></div>
                    </div>
                </div>
                
                <!-- Dynamic Quota Info Panel Tambah -->
                <div id="quotaPanelTambah" class="quota-info-panel" style="display:none; margin-bottom: 20px;"></div>
                
                <h4 style="font-size: 0.95rem; font-weight: 700; margin: 20px 0 10px; color: var(--dark); border-bottom: 1px solid var(--gray-200); padding-bottom: 6px;">
                    <i class="bx bx-package text-success"></i> Jumlah Penyaluran Pupuk (sak)
                </h4>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">UREA (sak)</label>
                        <input type="number" name="urea" class="form-control" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">NPK PHONSKA (sak)</label>
                        <input type="number" name="phonska" class="form-control" min="0" value="0">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">NPK PELANGI (sak)</label>
                        <input type="number" name="sp36" class="form-control" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ORGANIK (sak)</label>
                        <input type="number" name="organik" class="form-control" min="0" value="0">
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 25px;">
                    <label class="form-label">ZA (sak)</label>
                    <input type="number" name="za" class="form-control" min="0" value="0" style="max-width: calc(50% - 9px);">
                </div>

                <div class="form-group" style="margin-bottom: 25px; background: var(--gray-50); padding: 15px; border-radius: 10px; border: 1px solid var(--gray-200);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 700; color: var(--dark); font-size: 0.95rem;">Estimasi Total Harga:</span>
                        <span id="totalHargaTambah" style="font-weight: 800; color: var(--success); font-size: 1.25rem;">Rp 0</span>
                    </div>
                </div>

                <div class="form-row" style="border-top: 1px solid var(--gray-200); padding-top: 20px;">
                    <div class="form-group">
                        <label class="form-label">Foto Bukti Penerimaan <span style="color:#ef4444;">*</span></label>
                        <input type="file" name="bukti" class="form-control" accept="image/*" required>
                        <input type="hidden" name="status" value="Disalurkan">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Keterangan / Catatan Tambahan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="contoh: Diambil langsung oleh petani">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="this.closest('.modal-overlay').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php if ($edit_data): ?>
<!-- Modal Edit Penyaluran -->
<div class="modal-overlay active" id="modalEdit">
    <div class="modal animate__animated animate__fadeInDown animate__faster">
        <div class="modal-header">
            <h3>Edit Penyaluran Pupuk</h3>
            <a href="<?= $admin_url ?>?page=penyaluran" class="modal-close">&times;</a>
        </div>
        <form method="POST" action="<?= $admin_url ?>?page=penyaluran&action=update&id_petani_old=<?= $edit_data['id_petani'] ?>&tanggal_old=<?= $edit_data['tanggal'] ?>&status_old=<?= urlencode($edit_data['status']) ?>&bukti_old=<?= urlencode($edit_data['bukti'] ?? '') ?>&keterangan_old=<?= urlencode($edit_data['keterangan'] ?? '') ?>" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= $edit_data['tanggal'] ?>" max="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group" style="position: relative;">
                        <label class="form-label">Petani (NIK / Nama)</label>
                        <input type="hidden" name="id_petani" id="selectPetaniEdit" value="<?= $edit_data['id_petani'] ?>" required>
                        <?php 
                        $selected_petani_text = '';
                        if ($petani_options) {
                            $petani_options->data_seek(0);
                            while ($p = $petani_options->fetch_assoc()) {
                                if ($p['id'] == $edit_data['id_petani']) {
                                    $selected_petani_text = $p['nama_petani'] . " (" . $p['nik'] . ") - " . $p['nama_kelompok'];
                                    break;
                                }
                            }
                        }
                        ?>
                        <input type="text" id="searchPetaniEdit" class="form-control" placeholder="Ketik NIK atau Nama Petani..." autocomplete="off" value="<?= htmlspecialchars($selected_petani_text) ?>" required>
                        <div id="suggestionsEdit" class="suggestions-list" style="display:none;"></div>
                    </div>
                </div>
                
                <!-- Dynamic Quota Info Panel Edit -->
                <div id="quotaPanelEdit" class="quota-info-panel" style="display:none; margin-bottom: 20px;"></div>
                
                <h4 style="font-size: 0.95rem; font-weight: 700; margin: 20px 0 10px; color: var(--dark); border-bottom: 1px solid var(--gray-200); padding-bottom: 6px;">
                    <i class="bx bx-package text-success"></i> Jumlah Penyaluran Pupuk (sak)
                </h4>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">UREA (sak)</label>
                        <input type="number" name="urea" class="form-control" min="0" value="<?= (int)$edit_data['urea'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">NPK PHONSKA (sak)</label>
                        <input type="number" name="phonska" class="form-control" min="0" value="<?= (int)$edit_data['phonska'] ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">NPK PELANGI (sak)</label>
                        <input type="number" name="sp36" class="form-control" min="0" value="<?= (int)$edit_data['sp36'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ORGANIK (sak)</label>
                        <input type="number" name="organik" class="form-control" min="0" value="<?= (int)$edit_data['organik'] ?>">
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 25px;">
                    <label class="form-label">ZA (sak)</label>
                    <input type="number" name="za" class="form-control" min="0" value="<?= (int)$edit_data['za'] ?>" style="max-width: calc(50% - 9px);">
                </div>

                <div class="form-group" style="margin-bottom: 25px; background: var(--gray-50); padding: 15px; border-radius: 10px; border: 1px solid var(--gray-200);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 700; color: var(--dark); font-size: 0.95rem;">Estimasi Total Harga:</span>
                        <span id="totalHargaEdit" style="font-weight: 800; color: var(--success); font-size: 1.25rem;">Rp 0</span>
                    </div>
                </div>

                <div class="form-row" style="border-top: 1px solid var(--gray-200); padding-top: 20px;">
                    <div class="form-group">
                        <label class="form-label">Foto Bukti Penerimaan <small class="text-muted">(Biarkan kosong jika tidak diubah)</small></label>
                        <input type="file" name="bukti" class="form-control" accept="image/*">
                        <input type="hidden" name="status" value="Disalurkan">
                        <?php if ($edit_data['bukti']): ?>
                        <div style="margin-top: 8px; font-size: 0.8rem; color: var(--gray-500);">
                            <i class="bx bx-image"></i> File saat ini: <a href="javascript:void(0)" onclick="openLightbox('<?= $uploads_url ?>/bukti/<?= $edit_data['bukti'] ?>')" class="text-success fw-bold"><?= $edit_data['bukti'] ?></a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" value="<?= $edit_data['keterangan'] ?>">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?= $admin_url ?>?page=penyaluran" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Lightbox Modal untuk Bukti Penyaluran -->
<div class="modal-overlay" id="lightboxModal" onclick="closeLightbox()">
    <div class="modal animate__animated animate__zoomIn animate__faster" style="max-width: 650px; padding: 20px; background: var(--white); border-radius: 16px; position: relative;" onclick="event.stopPropagation()">
        <div class="modal-header" style="padding: 0 0 15px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--dark); margin: 0;"><i class="bx bx-image text-success"></i> Bukti Penyaluran</h3>
            <button type="button" class="modal-close" onclick="closeLightbox()" style="font-size: 1.8rem; line-height: 1; border: none; background: none; cursor: pointer; color: var(--gray-400); transition: var(--transition);" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--gray-400)'">&times;</button>
        </div>
        <div style="text-align: center; background: var(--gray-50); border-radius: 10px; padding: 10px; border: 1px dashed var(--gray-200);">
            <img id="lightboxImage" src="" alt="Bukti Penyaluran" style="max-width: 100%; max-height: 60vh; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
        </div>
        <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="btn btn-primary" onclick="closeLightbox()" style="font-weight: 600; min-width: 100px;">
                <i class="bx bx-arrow-back"></i> Kembali
            </button>
        </div>
    </div>
</div>

<script>
<?php
$prices_map = [];
if ($pupuk_options) {
    $pupuk_options->data_seek(0);
    while ($p = $pupuk_options->fetch_assoc()) {
        $name = strtoupper(trim($p['nama_pupuk']));
        $price = (float)$p['harga_per_sak'];
        if (strpos($name, 'UREA') !== false) {
            $prices_map['urea'] = $price;
        } elseif (strpos($name, 'PHONSKA') !== false) {
            $prices_map['phonska'] = $price;
        } elseif (strpos($name, 'PELANGI') !== false) {
            $prices_map['sp36'] = $price;
        } elseif (strpos($name, 'ZA') !== false) {
            $prices_map['za'] = $price;
        } elseif (strpos($name, 'ORGANIK') !== false) {
            $prices_map['organik'] = $price;
        }
    }
}
?>
const fertilizerPrices = <?= json_encode($prices_map) ?>;

// Active farmers JSON data
const allFarmers = [
    <?php 
    if ($petani_options) {
        $petani_options->data_seek(0);
        while ($p = $petani_options->fetch_assoc()): 
    ?>
    {
        id: <?= $p['id'] ?>,
        nama: <?= json_encode($p['nama_petani']) ?>,
        nik: <?= json_encode($p['nik']) ?>,
        kelompok: <?= json_encode($p['nama_kelompok']) ?>
    },
    <?php 
        endwhile;
    } 
    ?>
];

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

// Global object to store remaining quotas
let activeQuotas = {
    urea: 9999,
    phonska: 9999,
    sp36: 9999,
    za: 9999,
    organik: 9999
};

// Autocomplete suggestion box initializer
function initAutocomplete(inputId, hiddenId, suggestionsId, mode, inputTanggal) {
    const input = document.getElementById(inputId);
    const hidden = document.getElementById(hiddenId);
    const suggestions = document.getElementById(suggestionsId);
    if (!input || !hidden || !suggestions) return;

    input.addEventListener('input', function() {
        const val = this.value.toLowerCase().trim();
        suggestions.innerHTML = '';
        if (val.length === 0) {
            suggestions.style.display = 'none';
            hidden.value = '';
            const panel = document.getElementById('quotaPanel' + mode);
            if (panel) {
                panel.style.display = 'none';
                panel.innerHTML = '';
            }
            return;
        }

        const matches = allFarmers.filter(f => 
            f.nama.toLowerCase().includes(val) || 
            f.nik.toLowerCase().includes(val)
        );

        if (matches.length === 0) {
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'suggestion-item';
            emptyDiv.style.color = '#94a3b8';
            emptyDiv.style.cursor = 'default';
            emptyDiv.innerText = 'Tidak ada petani ditemukan';
            suggestions.appendChild(emptyDiv);
            suggestions.style.display = 'block';
            return;
        }

        matches.forEach(f => {
            const div = document.createElement('div');
            div.className = 'suggestion-item';
            div.innerHTML = `<strong>${f.nama}</strong><span>NIK: ${f.nik} | Kelompok: ${f.kelompok}</span>`;
            div.addEventListener('mousedown', function(e) {
                e.preventDefault();
            });
            div.addEventListener('click', function() {
                input.value = `${f.nama} (${f.nik}) - ${f.kelompok}`;
                hidden.value = f.id;
                suggestions.style.display = 'none';
                
                const dateVal = inputTanggal ? inputTanggal.value : '';
                if (mode === 'Edit') {
                    const excludeTanggal = "<?= $edit_data ? $edit_data['tanggal'] : '' ?>";
                    const excludeStatus = "<?= $edit_data ? $edit_data['status'] : '' ?>";
                    fetchQuota(f.id, mode, dateVal, excludeTanggal, excludeStatus, true);
                } else {
                    fetchQuota(f.id, mode, dateVal, '', '', true);
                }
            });
            suggestions.appendChild(div);
        });
        suggestions.style.display = 'block';
    });

    input.addEventListener('blur', function() {
        setTimeout(() => {
            suggestions.style.display = 'none';
        }, 200);
    });

    input.addEventListener('focus', function() {
        if (this.value.trim().length > 0 && !hidden.value) {
            this.dispatchEvent(new Event('input'));
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const inputTanggalTambah = document.querySelector('#modalTambah input[name="tanggal"]');
    const inputTanggalEdit = document.querySelector('#modalEdit input[name="tanggal"]');

    initAutocomplete('searchPetaniTambah', 'selectPetaniTambah', 'suggestionsTambah', 'Tambah', inputTanggalTambah);
    initAutocomplete('searchPetaniEdit', 'selectPetaniEdit', 'suggestionsEdit', 'Edit', inputTanggalEdit);

    const hiddenTambah = document.getElementById('selectPetaniTambah');
    if (hiddenTambah && inputTanggalTambah) {
        inputTanggalTambah.addEventListener('change', () => {
            if (hiddenTambah.value) {
                fetchQuota(hiddenTambah.value, 'Tambah', inputTanggalTambah.value, '', '', false);
            }
        });
    }

    const hiddenEdit = document.getElementById('selectPetaniEdit');
    if (hiddenEdit && inputTanggalEdit) {
        const fetchEditInitial = () => {
            if (hiddenEdit.value) {
                const excludeTanggal = "<?= $edit_data ? $edit_data['tanggal'] : '' ?>";
                const excludeStatus = "<?= $edit_data ? $edit_data['status'] : '' ?>";
                fetchQuota(hiddenEdit.value, 'Edit', inputTanggalEdit.value, excludeTanggal, excludeStatus, false);
            }
        };
        fetchEditInitial();
        inputTanggalEdit.addEventListener('change', fetchEditInitial);
    }
});

function fetchQuota(idPetani, mode, tanggal, excludeTanggal = '', excludeStatus = '', autoFill = false) {
    const panel = document.getElementById('quotaPanel' + mode);
    if (!panel) return;
    
    if (!idPetani) {
        panel.style.display = 'none';
        panel.innerHTML = '';
        return;
    }
    
    panel.style.display = 'block';
    panel.innerHTML = `
        <div style="text-align:center; padding: 15px; color: var(--gray-500);">
            <i class="bx bx-loader-alt bx-spin" style="font-size: 1.5rem;"></i>
            <div style="font-size: 0.8rem; margin-top: 5px;">Mengambil data kuota petani...</div>
        </div>
    `;
    
    let url = `admin.php?page=penyaluran&action=get_quota&id_petani=${idPetani}&tanggal=${tanggal}`;
    if (excludeTanggal && excludeStatus) {
        url += `&exclude_tanggal=${excludeTanggal}&exclude_status=${excludeStatus}`;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                activeQuotas.urea = { sisa: res.quota.urea.sisa, kemasan: res.quota.urea.kemasan };
                activeQuotas.phonska = { sisa: res.quota.phonska.sisa, kemasan: res.quota.phonska.kemasan };
                activeQuotas.sp36 = { sisa: res.quota.sp36.sisa, kemasan: res.quota.sp36.kemasan };
                activeQuotas.za = { sisa: res.quota.za.sisa, kemasan: res.quota.za.kemasan };
                activeQuotas.organik = { sisa: res.quota.organik.sisa, kemasan: res.quota.organik.kemasan };
                
                let html = '';
                
                if (res.has_transaction_today) {
                    html += `
                        <div class="duplicate-warning">
                            <i class="bx bx-error-circle" style="font-size: 1.2rem;"></i>
                            <span><strong>Peringatan:</strong> Petani ini terdeteksi sudah memiliki transaksi penebusan hari ini (${tanggal})!</span>
                        </div>
                    `;
                }
                
                html += `
                    <div class="quota-info-title">
                        <span><i class="bx bx-user-check text-success"></i> Sisa Kuota Petani</span>
                        <span style="font-weight: 500; font-size: 0.75rem; color: var(--gray-500);">Data Real-time</span>
                    </div>
                    <div class="quota-info-grid">
                `;
                
                const fertilizers = [
                    { key: 'urea', name: 'UREA' },
                    { key: 'phonska', name: 'PHONSKA' },
                    { key: 'sp36', name: 'NPK PELANGI' },
                    { key: 'organik', name: 'ORGANIK' },
                    { key: 'za', name: 'ZA' }
                ];
                
                const form = document.querySelector(mode === 'Tambah' ? '#modalTambah form' : '#modalEdit form');

                fertilizers.forEach(f => {
                    const q = res.quota[f.key];
                    let sisaClass = 'sisa-safe';
                    let sisaValHtml = `${q.sisa} <span style="font-size:0.7rem; font-weight:normal;">kg</span>`;
                    let sisaSakHtml = `~ ${Math.floor(q.sisa / q.kemasan)} sak`;
                    let kuotaHtml = `Kuota: ${q.alokasi} kg`;
                    let tebusHtml = `Tebus: ${q.tebus} kg`;
                    
                    if (f.key === 'za') {
                        sisaClass = 'sisa-safe';
                        sisaValHtml = `<span style="font-size:0.8rem; font-weight:700; color:var(--emerald);">NON-SUBSIDI</span>`;
                        sisaSakHtml = `<span style="color:var(--emerald); font-weight: 600;">Bebas</span>`;
                        kuotaHtml = `Kuota: Tanpa Batas`;
                        tebusHtml = `Tebus: ${q.tebus} kg`;
                    } else {
                        if (q.sisa === 0) sisaClass = 'sisa-empty';
                        else if (q.sisa < 50) sisaClass = 'sisa-warning';
                    }
                    
                    html += `
                        <div class="quota-item">
                            <div class="quota-name">${f.name}</div>
                            <div class="quota-val ${sisaClass}" style="font-size: 0.95rem;">${sisaValHtml}</div>
                            <div class="quota-lbl" style="color:var(--emerald); font-weight: 600;">${sisaSakHtml}</div>
                            <div class="quota-lbl" style="margin-top: 5px;">${kuotaHtml}</div>
                            <div class="quota-lbl" style="color:var(--gray-400);">${tebusHtml}</div>
                        </div>
                    `;

                    const input = form.querySelector(`input[name="${f.key}"]`);
                    if (input) {
                        input.setAttribute('min', '0');
                        if (f.key === 'za') {
                            const maxVal = Math.max(0, Math.floor(q.stok_gudang / q.kemasan));
                            input.setAttribute('max', maxVal);
                            if (autoFill) {
                                input.value = 0;
                            }
                        } else {
                            const maxAlokasi = Math.floor(q.sisa / q.kemasan);
                            const maxStok = Math.floor(q.stok_gudang / q.kemasan);
                            const maxVal = Math.max(0, Math.min(maxAlokasi, maxStok));
                            input.setAttribute('max', maxVal);
                            if (autoFill) {
                                input.value = maxVal;
                            }
                        }
                    }
                });
                
                html += `</div>`;
                
                if (res.recent_transactions && res.recent_transactions.length > 0) {
                    html += `
                        <div style="margin-top: 12px; padding-top: 8px; border-top: 1px dashed var(--gray-200);">
                            <div style="font-size: 0.72rem; font-weight: 700; color: var(--gray-500); margin-bottom: 4px;">Histori 3 Transaksi Terakhir Petani:</div>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                    `;
                    res.recent_transactions.forEach(t => {
                        let badgeClass = 'badge-info';
                        if (t.status === 'Diterima') badgeClass = 'badge-success';
                        else if (t.status === 'Pending') badgeClass = 'badge-warning';
                        
                        html += `
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.7rem; color: var(--gray-600); background: var(--gray-100); padding: 4px 8px; border-radius: 6px;">
                                <span><i class="bx bx-calendar"></i> ${t.tanggal} - <strong>${t.nama_pupuk}</strong> (${t.jumlah} sak)</span>
                                <span class="badge ${badgeClass}" style="font-size: 0.65rem; padding: 2px 6px;">${t.status}</span>
                            </div>
                        `;
                    });
                    html += `
                            </div>
                        </div>
                    `;
                } else {
                    html += `
                        <div style="margin-top: 8px; font-size: 0.7rem; color: var(--gray-400); text-align: center; font-style: italic;">
                            Belum ada riwayat transaksi penyaluran sebelumnya.
                        </div>
                    `;
                }
                
                panel.innerHTML = html;
                updateTotalPrice(mode);
            } else {
                panel.innerHTML = `<div style="padding:10px; color:var(--danger); font-size:0.8rem; text-align:center;">Gagal mengambil info kuota.</div>`;
            }
        })
        .catch(err => {
            console.error(err);
            panel.innerHTML = `<div style="padding:10px; color:var(--danger); font-size:0.8rem; text-align:center;">Koneksi gagal.</div>`;
        });
}

function updateTotalPrice(mode) {
    const form = document.querySelector(mode === 'Tambah' ? '#modalTambah form' : '#modalEdit form');
    if (!form) return;

    const urea = parseInt(form.querySelector('input[name="urea"]').value) || 0;
    const phonska = parseInt(form.querySelector('input[name="phonska"]').value) || 0;
    const sp36 = parseInt(form.querySelector('input[name="sp36"]').value) || 0;
    const organik = parseInt(form.querySelector('input[name="organik"]').value) || 0;
    const za = parseInt(form.querySelector('input[name="za"]').value) || 0;

    const priceUrea = fertilizerPrices.urea || 0;
    const pricePhonska = fertilizerPrices.phonska || 0;
    const priceSp36 = fertilizerPrices.sp36 || 0;
    const priceOrganik = fertilizerPrices.organik || 0;
    const priceZa = fertilizerPrices.za || 0;

    const total = (urea * priceUrea) + 
                  (phonska * pricePhonska) + 
                  (sp36 * priceSp36) + 
                  (organik * priceOrganik) + 
                  (za * priceZa);

    const formatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    });

    const displaySpan = document.getElementById('totalHarga' + mode);
    if (displaySpan) {
        displaySpan.innerText = formatter.format(total);
    }
}

document.addEventListener('input', function(e) {
    if (e.target.tagName === 'INPUT' && e.target.type === 'number' && e.target.closest('.modal')) {
        const max = parseFloat(e.target.getAttribute('max'));
        const min = parseFloat(e.target.getAttribute('min'));
        let val = parseFloat(e.target.value);
        if (!isNaN(max) && val > max) {
            e.target.value = max;
        }
        if (!isNaN(min) && val < min) {
            e.target.value = min;
        }
        
        // Update price estimate dynamically
        const isEdit = e.target.closest('#modalEdit') !== null;
        updateTotalPrice(isEdit ? 'Edit' : 'Tambah');
    }
});

function validateForm(form, mode) {
    const urea = parseInt(form.querySelector('input[name="urea"]').value) || 0;
    const phonska = parseInt(form.querySelector('input[name="phonska"]').value) || 0;
    const sp36 = parseInt(form.querySelector('input[name="sp36"]').value) || 0;
    const za = parseInt(form.querySelector('input[name="za"]').value) || 0;
    const organik = parseInt(form.querySelector('input[name="organik"]').value) || 0;
    const status = 'Disalurkan';
    const fileInput = form.querySelector('input[name="bukti"]');
    
    if (urea === 0 && phonska === 0 && sp36 === 0 && za === 0 && organik === 0) {
        alert('Gagal menyimpan: Masukkan setidaknya 1 jenis pupuk dengan jumlah > 0 sak.');
        return false;
    }
    
    const getKemasan = (key) => (activeQuotas[key] && typeof activeQuotas[key] === 'object' && activeQuotas[key].kemasan) ? activeQuotas[key].kemasan : 50;
    const getSisa = (key) => (activeQuotas[key] && typeof activeQuotas[key] === 'object' && activeQuotas[key].sisa !== undefined) ? activeQuotas[key].sisa : 999999;

    let errors = [];
    const uKemasan = getKemasan('urea');
    const uSisa = getSisa('urea');
    if (urea * uKemasan > uSisa) errors.push(`UREA (Input: ${urea} sak = ${urea * uKemasan} kg, Maksimal: ${uSisa} kg)`);

    const pKemasan = getKemasan('phonska');
    const pSisa = getSisa('phonska');
    if (phonska * pKemasan > pSisa) errors.push(`NPK PHONSKA (Input: ${phonska} sak = ${phonska * pKemasan} kg, Maksimal: ${pSisa} kg)`);

    const sKemasan = getKemasan('sp36');
    const sSisa = getSisa('sp36');
    if (sp36 * sKemasan > sSisa) errors.push(`NPK PELANGI (Input: ${sp36} sak = ${sp36 * sKemasan} kg, Maksimal: ${sSisa} kg)`);

    const oKemasan = getKemasan('organik');
    const oSisa = getSisa('organik');
    if (organik * oKemasan > oSisa) errors.push(`ORGANIK (Input: ${organik} sak = ${organik * oKemasan} kg, Maksimal: ${oSisa} kg)`);
    
    if (errors.length > 0) {
        alert('Gagal menyimpan: Jumlah penyaluran melebihi sisa alokasi kuota petani untuk:\n' + errors.join('\n'));
        return false;
    }
    
    const hasExistingPhoto = form.innerHTML.includes('File saat ini:');
    if (fileInput && !fileInput.files.length && !hasExistingPhoto) {
        alert('Gagal menyimpan: Wajib menyertakan Foto Bukti Penerimaan sebagai bukti fisik penyaluran.');
        return false;
    }
    
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    const formTambah = document.querySelector('#modalTambah form');
    const formEdit = document.querySelector('#modalEdit form');
    
    if (formTambah) {
        formTambah.addEventListener('submit', function(e) {
            if (!validateForm(formTambah, 'Tambah')) {
                e.preventDefault();
            }
        });
        updateTotalPrice('Tambah');
    }
    
    if (formEdit) {
        formEdit.addEventListener('submit', function(e) {
            if (!validateForm(formEdit, 'Edit')) {
                e.preventDefault();
            }
        });
        updateTotalPrice('Edit');
    }
});
</script>

