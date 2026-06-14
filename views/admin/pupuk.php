<!-- Data Pupuk View -->
<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 10px;">
        <h3><i class="bx bx-package"></i> Data Pupuk</h3>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form method="GET" action="" style="display: flex; gap: 8px;">
                <input type="hidden" name="page" value="pupuk">
                <input type="text" name="q" class="form-control" placeholder="Cari nama pupuk..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" style="width: 200px; padding: 6px 12px; font-size: 0.85rem; border-radius: 6px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-search"></i></button>
                <?php if(isset($_GET['q']) && $_GET['q'] !== ''): ?>
                    <a href="<?= $admin_url ?>?page=pupuk" class="btn btn-outline btn-sm"><i class="bx bx-x"></i></a>
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
                    <th>Foto</th>
                    <th>Jenis Pupuk</th>
                    <th>Kemasan</th>
                    <th>Harga/Sak</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = isset($offset) ? $offset + 1 : 1; while ($row = $pupuk_list->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <?php if ($row['foto']): ?>
                            <a href="javascript:void(0)" onclick="openLightbox('<?= $uploads_url ?>/pupuk/<?= $row['foto'] ?>')" title="Klik untuk memperbesar">
                                <img src="<?= $uploads_url ?>/pupuk/<?= $row['foto'] ?>" class="img-thumb" alt="" style="cursor: zoom-in;">
                            </a>
                        <?php else: ?>
                            <div class="img-thumb" style="display:flex;align-items:center;justify-content:center;background:var(--gray-100);"><i class="bx bx-image" style="color:var(--gray-300);font-size:1.2rem;"></i></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= $row['nama_pupuk'] ?></strong>
                        <?php if (!empty($row['deskripsi'])): ?>
                            <div style="font-size: 0.72rem; color: var(--gray-500); margin-top: 4px; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($row['deskripsi']) ?>">
                                <span style="font-weight: 700; color: var(--gray-600);">Deskripsi:</span> <?= htmlspecialchars($row['deskripsi']) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?= $row['ukuran_kemasan'] ?></td>
                    <td><?= formatRupiah($row['harga_per_sak']) ?></td>
                    <td>
                        <?php $st = getStokStatus($row['stok']); ?>
                        <span class="badge badge-<?= $st['class'] ?>"><?= number_format($row['stok'] / ($row['berat_kemasan_kg'] ?: 50), 0, ',', '.') ?> sak</span>
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="<?= $admin_url ?>?page=pupuk&action=edit&id=<?= $row['id'] ?>" class="btn btn-warning btn-xs"><i class="bx bx-edit"></i></a>
                            <a href="<?= $admin_url ?>?page=pupuk&action=delete&id=<?= $row['id'] ?>" class="btn btn-danger btn-xs"><i class="bx bx-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <?php if (isset($total_pages) && $total_pages > 1): ?>
    <div class="pagination">
        <?php if ($page_num > 1): ?>
            <a href="<?= $admin_url ?>?page=pupuk&p=<?= $page_num - 1 ?><?= $search ? '&q='.urlencode($search) : '' ?>">&laquo; Sebelumnya</a>
        <?php endif; ?>
        
        <?php 
        $start_page = max(1, $page_num - 2);
        $end_page = min($total_pages, $page_num + 2);
        if ($start_page > 1) { echo '<span>...</span>'; }
        for ($i = $start_page; $i <= $end_page; $i++): 
        ?>
            <a href="<?= $admin_url ?>?page=pupuk&p=<?= $i ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="<?= $i === $page_num ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($end_page < $total_pages) { echo '<span>...</span>'; } ?>

        <?php if ($page_num < $total_pages): ?>
            <a href="<?= $admin_url ?>?page=pupuk&p=<?= $page_num + 1 ?><?= $search ? '&q='.urlencode($search) : '' ?>">Selanjutnya &raquo;</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah Pupuk -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal">
        <div class="modal-header">
            <h3>Tambah Pupuk</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="<?= $admin_url ?>?page=pupuk&action=store" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama/Jenis Pupuk <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="nama_pupuk" class="form-control" required>
                    <small class="text-muted" style="font-size:0.75rem;">Nama pupuk bersifat unik, tidak boleh duplikat.</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Foto Pupuk</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
                <div class="form-group">
                    <label class="form-label">Berat Kemasan (kg) <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="berat_kemasan_kg" id="beratKemasanTambah" class="form-control" min="1" value="50" required>
                    <small class="text-muted" style="font-size:0.75rem;">Contoh: 50 untuk kemasan 50 kg.</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga per Sak (Rp) <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="harga_per_sak" id="hargaSakTambah" class="form-control" min="1" required>
                </div>
                <div class="form-group" style="margin-top: 15px;">
                    <label class="form-label">Deskripsi Pupuk</label>
                    <textarea name="deskripsi" class="form-control" rows="4" placeholder="Kandungan dan kegunaan pupuk..." style="resize: vertical; min-height: 80px;"></textarea>
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
<div class="modal-overlay active" id="modalEdit">
    <div class="modal">
        <div class="modal-header">
            <h3>Edit Pupuk</h3>
            <a href="<?= $admin_url ?>?page=pupuk" class="modal-close">&times;</a>
        </div>
        <form method="POST" action="<?= $admin_url ?>?page=pupuk&action=update&id=<?= $edit_data['id'] ?>" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama/Jenis Pupuk <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="nama_pupuk" class="form-control" value="<?= $edit_data['nama_pupuk'] ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Foto Pupuk (kosongkan jika tidak diubah)</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                    <?php if ($edit_data['foto']): ?>
                        <p class="form-text">Foto saat ini: <a href="javascript:void(0)" onclick="openLightbox('<?= $uploads_url ?>/pupuk/<?= $edit_data['foto'] ?>')" class="text-success fw-bold" style="cursor: zoom-in;"><?= $edit_data['foto'] ?></a></p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label">Berat Kemasan (kg) <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="berat_kemasan_kg" id="beratKemasanEdit" class="form-control" min="1" value="<?= isset($edit_data['berat_kemasan_kg']) ? $edit_data['berat_kemasan_kg'] : 50 ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga per Sak (Rp) <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="harga_per_sak" id="hargaSakEdit" class="form-control" min="1" value="<?= $edit_data['harga_per_sak'] ?>" required>
                </div>
                
                <div class="form-group" style="margin-top: 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;">
                    <label class="form-label" style="margin-bottom: 4px;"><i class="bx bx-package text-success"></i> Stok Saat Ini</label>
                    <div style="font-size: 1.2rem; font-weight: 700; color: var(--dark);"><?= number_format($edit_data['stok'] / ($edit_data['berat_kemasan_kg'] ?: 50), 0, ',', '.') ?> sak</div>
                    <small class="text-muted" style="font-size:0.72rem;">Stok dihitung otomatis dari stok masuk dan penyaluran. Tidak bisa diubah secara manual.</small>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label class="form-label">Deskripsi Pupuk</label>
                    <textarea name="deskripsi" class="form-control" rows="4" style="resize: vertical; min-height: 80px;"><?= htmlspecialchars($edit_data['deskripsi'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?= $admin_url ?>?page=pupuk" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Lightbox Modal untuk Foto Pupuk -->
<div class="modal-overlay" id="lightboxModal" onclick="closeLightbox()">
    <div class="modal animate__animated animate__zoomIn animate__faster" style="max-width: 650px; padding: 20px; background: var(--white); border-radius: 16px; position: relative;" onclick="event.stopPropagation()">
        <div class="modal-header" style="padding: 0 0 15px 0; border-bottom: 1px solid var(--gray-200); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--dark); margin: 0;"><i class="bx bx-image text-success"></i> Foto Pupuk</h3>
            <button type="button" class="modal-close" onclick="closeLightbox()" style="font-size: 1.8rem; line-height: 1; border: none; background: none; cursor: pointer; color: var(--gray-400); transition: var(--transition);" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--gray-400)'">&times;</button>
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


