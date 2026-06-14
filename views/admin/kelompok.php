<!-- Kelompok Tani View -->
<?php
// Fetch all farmers for the dropdown
$petani_list_raw = $conn->query("SELECT id, nama_petani, nik FROM petani ORDER BY nama_petani ASC");
$petani_options = [];
if ($petani_list_raw) {
    while ($p = $petani_list_raw->fetch_assoc()) {
        $petani_options[] = $p;
    }
}
?>
<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 10px;">
        <h3><i class="bx bxs-group"></i> Data Kelompok Tani</h3>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form method="GET" action="" style="display: flex; gap: 8px;">
                <input type="hidden" name="page" value="kelompok">
                <input type="text" name="q" class="form-control" placeholder="Cari nama kelompok..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" style="width: 220px; padding: 6px 12px; font-size: 0.85rem; border-radius: 6px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-search"></i></button>
                <?php if(isset($_GET['q']) && $_GET['q'] !== ''): ?>
                    <a href="<?= $admin_url ?>?page=kelompok" class="btn btn-outline btn-sm"><i class="bx bx-x"></i></a>
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
                    <th>ID Poktan</th>
                    <th>Nama Kelompok</th>
                    <th>Ketua</th>
                    <th>Anggota</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = $kelompok_list->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['kode_kelompok'] ?? '-') ?></td>
                    <td><strong><?= htmlspecialchars($row['nama_kelompok']) ?></strong></td>
                    <td><strong><?= htmlspecialchars(!empty($row['ketua_kelompok']) ? $row['ketua_kelompok'] : '-') ?></strong></td>
                    <td><span class="badge badge-info"><?= $row['jumlah_anggota'] ?> orang</span></td>
                    <td>
                        <div class="btn-group">
                            <a href="<?= $admin_url ?>?page=kelompok&action=edit&id=<?= $row['id'] ?>" class="btn btn-warning btn-xs"><i class="bx bx-edit"></i></a>
                            <a href="<?= $admin_url ?>?page=kelompok&action=delete&id=<?= $row['id'] ?>" class="btn btn-danger btn-xs"><i class="bx bx-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal">
        <div class="modal-header">
            <h3>Tambah Kelompok Tani</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="<?= $admin_url ?>?page=kelompok&action=store">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">ID Poktan</label>
                    <input type="text" name="kode_kelompok" class="form-control" placeholder="Contoh: 1058514" required oninvalid="this.setCustomValidity('Harap isi ID Poktan.')" oninput="this.setCustomValidity('')">
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Kelompok</label>
                    <input type="text" name="nama_kelompok" class="form-control" required oninvalid="this.setCustomValidity('Harap isi Nama Kelompok.')" oninput="this.setCustomValidity('')">
                </div>
                <div class="form-group">
                    <label class="form-label">Jumlah Anggota</label>
                    <input type="number" name="jumlah_anggota" class="form-control" min="0" value="0" required oninvalid="this.setCustomValidity('Harap isi Jumlah Anggota.')" oninput="this.setCustomValidity('')">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Ketua Kelompok </label>
                        <select name="ketua_petani_id" class="form-control">
                            <option value="">-- Pilih Ketua Kelompok --</option>
                            <?php foreach ($petani_options as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama_petani']) ?> (NIK: <?= htmlspecialchars($p['nik']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
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
<div class="modal-overlay active">
    <div class="modal">
        <div class="modal-header">
            <h3>Edit Kelompok Tani</h3>
            <a href="<?= $admin_url ?>?page=kelompok" class="modal-close">&times;</a>
        </div>
        <form method="POST" action="<?= $admin_url ?>?page=kelompok&action=update&id=<?= $edit_data['id'] ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">ID Poktan</label>
                    <input type="text" name="kode_kelompok" class="form-control" value="<?= htmlspecialchars($edit_data['kode_kelompok'] ?? '') ?>" required oninvalid="this.setCustomValidity('Harap isi ID Poktan.')" oninput="this.setCustomValidity('')">
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Kelompok</label>
                    <input type="text" name="nama_kelompok" class="form-control" value="<?= $edit_data['nama_kelompok'] ?>" required oninvalid="this.setCustomValidity('Harap isi Nama Kelompok.')" oninput="this.setCustomValidity('')">
                </div>
                <div class="form-group">
                    <label class="form-label">Jumlah Anggota</label>
                    <input type="number" name="jumlah_anggota" class="form-control" min="0" value="<?= htmlspecialchars($edit_data['jumlah_anggota'] ?? 0) ?>" required oninvalid="this.setCustomValidity('Harap isi Jumlah Anggota.')" oninput="this.setCustomValidity('')">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Ketua Kelompok <small class="text-muted">(Opsional, bisa diisi nanti)</small></label>
                        <select name="ketua_petani_id" class="form-control">
                            <option value="">-- Pilih Ketua Kelompok --</option>
                            <?php foreach ($petani_options as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= (isset($edit_data['ketua_petani_id']) && $edit_data['ketua_petani_id'] == $p['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nama_petani']) ?> (NIK: <?= htmlspecialchars($p['nik']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?= $admin_url ?>?page=kelompok" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

