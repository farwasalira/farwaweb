<!-- Stok View (Khusus Stok Masuk) -->
<div class="card animate__animated animate__fadeIn">
    <div class="card-header" style="flex-wrap: wrap; gap: 10px;">
        <h3><i class="bx bx-log-in-circle text-success"></i> Data Catatan Stok Masuk</h3>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form method="GET" action="" style="display: flex; gap: 8px;">
                <input type="hidden" name="page" value="stok">
                <input type="hidden" name="tab" value="masuk">
                <input type="date" name="d" class="form-control" value="<?= isset($_GET['d']) ? htmlspecialchars($_GET['d']) : '' ?>" max="<?= date('Y-m-d') ?>" style="padding: 6px 12px; font-size: 0.85rem; border-radius: 6px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-filter-alt"></i> Filter</button>
                <?php if(isset($_GET['d']) && $_GET['d'] !== ''): ?>
                    <a href="<?= $admin_url ?>?page=stok&tab=masuk" class="btn btn-outline btn-sm"><i class="bx bx-x"></i></a>
                <?php endif; ?>
            </form>
            <button class="btn btn-primary btn-sm" onclick="document.getElementById('modalMasuk').classList.add('active')">
                <i class="bx bx-plus"></i> Tambah Data
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>Tanggal Masuk</th>
                    <th>UREA</th>
                    <th>PHONSKA</th>
                    <th>NPK PELANGI</th>
                    <th>ORGANIK</th>
                    <th>ZA</th>
                    <th>Jumlah Total</th>
                    <th>Nama Sopir</th>
                    <th style="width: 150px; text-align: center;">Bukti Pengiriman</th>
                    <th style="width: 100px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = isset($offset) ? $offset + 1 : 1; while ($row = $stok_list->fetch_assoc()): ?>
                <tr>
                    <td style="text-align: center; font-weight: 600; color: var(--gray-500);"><?= $no++ ?></td>
                    <td>
                        <div style="font-weight: 600; color: var(--dark);"><?= formatTanggalShort($row['tanggal']) ?></div>
                    </td>
                    <td><?= number_format($row['urea'], 0, ',', '.') ?> sak</td>
                    <td><?= number_format($row['phonska'], 0, ',', '.') ?> sak</td>
                    <td><?= number_format($row['sp36'], 0, ',', '.') ?> sak</td>
                    <td><?= number_format($row['organik'], 0, ',', '.') ?> sak</td>
                    <td><?= number_format($row['za'], 0, ',', '.') ?> sak</td>
                    <td>
                        <span class="badge badge-success px-2 py-1" style="font-size: 0.8rem; font-weight: 700;">
                            <i class="bx bx-plus-circle align-middle me-1"></i> <?= number_format($row['total'], 0, ',', '.') ?> sak
                        </span>
                    </td>
                    <td>
                        <strong><?= !empty($row['nama_sopir']) ? $row['nama_sopir'] : '-' ?></strong>
                    </td>
                    <td style="text-align: center;">
                        <?php if (!empty($row['bukti_foto'])): ?>
                        <div style="display: inline-flex; align-items: center; justify-content: center;">
                            <a href="javascript:void(0)" onclick="openLightbox('<?= $base_url ?>/uploads/bukti/<?= $row['bukti_foto'] ?>')" title="Klik untuk memperbesar">
                                <img src="<?= $base_url ?>/uploads/bukti/<?= $row['bukti_foto'] ?>" alt="Bukti Stok Masuk" style="width: 42px; height: 42px; border-radius: 8px; object-fit: cover; border: 1.5px solid var(--gray-200); box-shadow: 0 2px 5px rgba(0,0,0,0.06); transition: transform 0.2s; cursor: zoom-in;" onmouseover="this.style.transform='scale(1.12)'" onmouseout="this.style.transform='scale(1)'">
                            </a>
                        </div>
                        <?php else: ?>
                        <span class="text-muted" style="font-size: 0.8rem;">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center;">
                        <div class="btn-group">
                            <a href="<?= $admin_url ?>?page=stok&action=edit_masuk&tanggal=<?= $row['tanggal'] ?>&sopir=<?= urlencode($row['nama_sopir']) ?>&bukti=<?= urlencode($row['bukti_foto'] ?? '') ?>" class="btn btn-warning btn-xs" title="Edit catatan pengiriman ini"><i class="bx bx-edit"></i></a>
                            <a href="<?= $admin_url ?>?page=stok&action=delete_masuk&tanggal=<?= $row['tanggal'] ?>&sopir=<?= urlencode($row['nama_sopir']) ?>&bukti=<?= urlencode($row['bukti_foto'] ?? '') ?>" class="btn btn-danger btn-xs" title="Hapus catatan stok masuk ini"><i class="bx bx-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($stok_list->num_rows === 0): ?>
                <tr>
                    <td colspan="11" style="text-align: center; padding: 40px;" class="text-muted">
                        <i class="bx bx-package display-4 mb-2"></i>
                        <p class="mb-0 fw-semibold">Belum ada catatan stok masuk.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if (isset($total_pages) && $total_pages > 1): ?>
    <div class="pagination">
        <?php if ($page_num > 1): ?>
            <a href="<?= $admin_url ?>?page=stok&tab=masuk&p=<?= $page_num - 1 ?><?= $search_date ? '&d='.urlencode($search_date) : '' ?>">&laquo; Sebelumnya</a>
        <?php endif; ?>
        
        <?php 
        $start_page = max(1, $page_num - 2);
        $end_page = min($total_pages, $page_num + 2);
        if ($start_page > 1) { echo '<span>...</span>'; }
        for ($i = $start_page; $i <= $end_page; $i++): 
        ?>
            <a href="<?= $admin_url ?>?page=stok&tab=masuk&p=<?= $i ?><?= $search_date ? '&d='.urlencode($search_date) : '' ?>" class="<?= $i === $page_num ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($end_page < $total_pages) { echo '<span>...</span>'; } ?>

        <?php if ($page_num < $total_pages): ?>
            <a href="<?= $admin_url ?>?page=stok&tab=masuk&p=<?= $page_num + 1 ?><?= $search_date ? '&d='.urlencode($search_date) : '' ?>">Selanjutnya &raquo;</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Stok Masuk -->
<div class="modal-overlay" id="modalMasuk">
    <div class="modal">
        <div class="modal-header">
            <h3>Tambah Catatan Stok Masuk</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="<?= $admin_url ?>?page=stok&action=store_masuk" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="form-group" style="max-width: calc(50% - 9px);">
                    <label class="form-label">Tanggal Masuk</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
                </div>

                <h4 style="font-size: 0.95rem; font-weight: 700; margin: 20px 0 10px; color: var(--dark); border-bottom: 1px solid var(--gray-200); padding-bottom: 6px;">
                    <i class="bx bx-package text-success"></i> Jumlah Pupuk Masuk (sak)
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

                <div class="form-row" style="border-top: 1px solid var(--gray-200); padding-top: 20px;">
                    <div class="form-group">
                        <label class="form-label">Bukti Pengiriman (Foto / Gambar)</label>
                        <input type="file" name="bukti" class="form-control" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Sopir</label>
                        <input type="text" name="nama_sopir" class="form-control" placeholder="Masukkan nama sopir pengirim" required>
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
<!-- Modal Edit Stok Masuk -->
<div class="modal-overlay active">
    <div class="modal">
        <div class="modal-header">
            <h3>Edit Catatan Stok Masuk</h3>
            <a href="<?= $admin_url ?>?page=stok&tab=masuk" class="modal-close">&times;</a>
        </div>
        <form method="POST" action="<?= $admin_url ?>?page=stok&action=update_masuk&tanggal_old=<?= $edit_data['tanggal'] ?>&sopir_old=<?= urlencode($edit_data['nama_sopir']) ?>&bukti_old=<?= urlencode($edit_data['bukti_foto'] ?? '') ?>" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="form-group" style="max-width: calc(50% - 9px);">
                    <label class="form-label">Tanggal Masuk</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= $edit_data['tanggal'] ?>" max="<?= date('Y-m-d') ?>" required>
                </div>

                <h4 style="font-size: 0.95rem; font-weight: 700; margin: 20px 0 10px; color: var(--dark); border-bottom: 1px solid var(--gray-200); padding-bottom: 6px;">
                    <i class="bx bx-package text-success"></i> Jumlah Pupuk Masuk (sak)
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

                <div class="form-row" style="border-top: 1px solid var(--gray-200); padding-top: 20px;">
                    <div class="form-group">
                        <label class="form-label">Bukti Pengiriman (Foto / Gambar) <small class="text-muted">(Biarkan kosong jika tidak ingin mengubah foto)</small></label>
                        <input type="file" name="bukti" class="form-control" accept="image/*">
                        <?php if (!empty($edit_data['bukti_foto'])): ?>
                            <div style="margin-top: 8px; font-size: 0.8rem; color: var(--gray-500);">
                                <i class="bx bx-image"></i> File saat ini: <a href="javascript:void(0)" onclick="openLightbox('<?= $base_url ?>/uploads/bukti/<?= $edit_data['bukti_foto'] ?>')" class="text-success fw-bold"><?= $edit_data['bukti_foto'] ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Sopir</label>
                        <input type="text" name="nama_sopir" class="form-control" value="<?= $edit_data['nama_sopir'] ?>" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?= $admin_url ?>?page=stok&tab=masuk" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Lightbox Modal untuk Bukti Pengiriman -->
<div class="modal-overlay" id="lightboxModal" onclick="closeLightbox()">
    <div class="modal animate__animated animate__zoomIn animate__faster" style="max-width: 650px; padding: 20px; background: var(--white); border-radius: 16px; position: relative;" onclick="event.stopPropagation()">
        <div class="modal-header" style="padding: 0 0 15px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--dark); margin: 0;"><i class="bx bx-image text-success"></i> Bukti Pengiriman</h3>
            <button type="button" class="modal-close" onclick="closeLightbox()" style="font-size: 1.8rem; line-height: 1; border: none; background: none; cursor: pointer; color: var(--gray-400); transition: var(--transition);" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--gray-400)'">&times;</button>
        </div>
        <div style="text-align: center; background: var(--gray-50); border-radius: 10px; padding: 10px; border: 1px dashed var(--gray-200);">
            <img id="lightboxImage" src="" alt="Bukti Pengiriman" style="max-width: 100%; max-height: 60vh; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
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

