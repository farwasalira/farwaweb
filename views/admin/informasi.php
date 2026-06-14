<!-- Informasi View -->
<div class="card animate__animated animate__fadeIn">
    <div class="card-header">
        <h3><i class="bx bx-info-circle"></i> Kelola Informasi</h3>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('modalTambah').classList.add('active')">
            <i class="bx bx-plus"></i> Tambah Data</button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>Judul Informasi</th>
                    <th>Isi Informasi </th>
                    <th style="width: 150px;">Tanggal</th>
                    <th style="width: 120px; text-align: center;">Status</th>
                    <th style="width: 120px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = $informasi_list->fetch_assoc()): ?>
                <tr>
                    <td style="text-align: center; font-weight: 600; color: var(--gray-500);"><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($row['judul']) ?></strong></td>
                    <td>
                        <div style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($row['isi']) ?>">
                            <?= htmlspecialchars($row['isi']) ?>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--dark);"><?= formatTanggalShort($row['tanggal']) ?></div>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge <?= $row['aktif'] == 1 ? 'badge-success' : 'badge-danger' ?>">
                            <?= $row['aktif'] == 1 ? 'Aktif' : 'Draft' ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <div class="btn-group" style="justify-content: center;">
                            <a href="<?= $admin_url ?>?page=informasi&action=edit_informasi&id=<?= $row['id'] ?>" class="btn btn-warning btn-xs"><i class="bx bx-edit"></i></a>
                            <a href="<?= $admin_url ?>?page=informasi&action=delete_informasi&id=<?= $row['id'] ?>" class="btn btn-danger btn-xs"><i class="bx bx-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($informasi_list->num_rows === 0): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px;" class="text-muted">
                        <i class="bx bx-bell-off display-4 mb-2"></i>
                        <p class="mb-0 fw-semibold">Belum ada data informasi.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Informasi -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal">
        <div class="modal-header">
            <h3>Tambah Informasi</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="<?= $admin_url ?>?page=informasi&action=store_informasi">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Judul Informasi</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Isi Informasi</label>
                    <textarea name="isi" class="form-control" rows="6" required style="resize: vertical; min-height: 120px;"></textarea>
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-top: 15px;">
                    <input type="checkbox" name="aktif" id="aktifTambah" value="1" checked style="width: 18px; height: 18px; cursor: pointer;">
                    <label for="aktifTambah" style="cursor: pointer; font-size: 0.9rem; font-weight: 500;">Aktifkan / Tampilkan Informasi</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="this.closest('.modal-overlay').classList.remove('active')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php if ($edit_informasi): ?>
<!-- Modal Edit Informasi -->
<div class="modal-overlay active" id="modalEdit">
    <div class="modal">
        <div class="modal-header">
            <h3>Edit Informasi</h3>
            <a href="<?= $admin_url ?>?page=informasi" class="modal-close">&times;</a>
        </div>
        <form method="POST" action="<?= $admin_url ?>?page=informasi&action=update_informasi&id=<?= $edit_informasi['id'] ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Judul Informasi</label>
                    <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($edit_informasi['judul']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= $edit_informasi['tanggal'] ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Isi Informasi</label>
                    <textarea name="isi" class="form-control" rows="6" required style="resize: vertical; min-height: 120px;"><?= htmlspecialchars($edit_informasi['isi']) ?></textarea>
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-top: 15px;">
                    <input type="checkbox" name="aktif" id="aktifEdit" value="1" <?= $edit_informasi['aktif'] == 1 ? 'checked' : '' ?> style="width: 18px; height: 18px; cursor: pointer;">
                    <label for="aktifEdit" style="cursor: pointer; font-size: 0.9rem; font-weight: 500;">Aktifkan / Tampilkan Informasi</label>
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?= $admin_url ?>?page=informasi" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>




