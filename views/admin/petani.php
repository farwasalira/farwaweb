<!-- Data Petani View -->
<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 10px;">
        <h3><i class="bx bx-user"></i> Data Petani</h3>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form method="GET" action="" style="display: flex; gap: 8px;">
                <input type="hidden" name="page" value="petani">
                <input type="text" name="q" class="form-control" placeholder="Cari NIK atau Nama..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" style="width: 220px; padding: 6px 12px; font-size: 0.85rem; border-radius: 6px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-search"></i></button>
                <?php if(isset($_GET['q']) && $_GET['q'] !== ''): ?>
                    <a href="<?= $admin_url ?>?page=petani" class="btn btn-outline btn-sm"><i class="bx bx-x"></i></a>
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
                    <th>Nama Petani</th>
                    <th>NIK</th>
                    <th>Kelompok</th>
                    <th>Luas Lahan</th>
                    <th>Status</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = isset($offset) ? $offset + 1 : 1; while ($row = $petani_list->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= $row['nama_petani'] ?></strong></td>
                    <td><?= $row['nik'] ?></td>
                    <td><span class="badge badge-info"><?= $row['nama_kelompok'] ?></span></td>
                    <td><?= $row['luas_lahan'] ?> Ha</td>
                    <td>
                        <?php if ($row['status'] === 'Aktif'): ?>
                        <span class="badge badge-success"><i class="bx bx-check-circle"></i> Aktif</span>
                        <?php else: ?>
                        <span class="badge badge-danger"><i class="bx bx-x-circle"></i> Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $row['alamat'] ?></td>
                    <td>
                        <div class="btn-group">
                            <a href="<?= $admin_url ?>?page=petani&action=edit&id=<?= $row['id'] ?>" class="btn btn-warning btn-xs"><i class="bx bx-edit"></i></a>
                            <a href="<?= $admin_url ?>?page=petani&action=delete&id=<?= $row['id'] ?>" class="btn btn-danger btn-xs"><i class="bx bx-trash"></i></a>
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
            <a href="<?= $admin_url ?>?page=petani&p=<?= $page_num - 1 ?><?= $search ? '&q='.urlencode($search) : '' ?>">&laquo; Sebelumnya</a>
        <?php endif; ?>
        
        <?php 
        $start_page = max(1, $page_num - 2);
        $end_page = min($total_pages, $page_num + 2);
        if ($start_page > 1) { echo '<span>...</span>'; }
        for ($i = $start_page; $i <= $end_page; $i++): 
        ?>
            <a href="<?= $admin_url ?>?page=petani&p=<?= $i ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="<?= $i === $page_num ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($end_page < $total_pages) { echo '<span>...</span>'; } ?>

        <?php if ($page_num < $total_pages): ?>
            <a href="<?= $admin_url ?>?page=petani&p=<?= $page_num + 1 ?><?= $search ? '&q='.urlencode($search) : '' ?>">Selanjutnya &raquo;</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal">
        <div class="modal-header">
            <h3>Tambah Petani</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="<?= $admin_url ?>?page=petani&action=store">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Petani</label>
                    <input type="text" name="nama_petani" class="form-control" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">NIK <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="nik" class="form-control" maxlength="16" minlength="16" pattern="[0-9]{16}" required inputmode="numeric" placeholder="16 digit angka NIK KTP">
                        <small class="text-muted" style="font-size:0.75rem;">NIK harus tepat 16 digit angka dan bersifat unik.</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kelompok Tani</label>
                        <select name="id_kelompok" class="form-control" onchange="document.getElementById('nama_kelompok_baru').style.display = this.value === 'baru' ? 'block' : 'none'; if(this.value === 'baru') { document.getElementById('nama_kelompok_baru').setAttribute('required', 'required'); } else { document.getElementById('nama_kelompok_baru').removeAttribute('required'); }" required>
                            <option value="">Pilih Kelompok</option>
                            <option value="baru" style="font-weight:bold; color:#0d6efd;">+ Buat Kelompok Baru</option>
                            <?php while ($k = $kelompok_list->fetch_assoc()): ?>
                            <option value="<?= $k['id'] ?>"><?= $k['nama_kelompok'] ?></option>
                            <?php endwhile; ?>
                        </select>
                        <input type="text" name="nama_kelompok_baru" id="nama_kelompok_baru" class="form-control" placeholder="Masukkan Nama Kelompok Baru" style="display:none; margin-top:8px;">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Luas Lahan (Ha) <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="luas_lahan" class="form-control" step="0.01" min="0.01" max="10" required>
                        <small class="text-muted" style="font-size:0.75rem;">Maks. 10 Ha (regulasi pupuk bersubsidi)</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <input type="text" name="alamat" class="form-control">
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
<!-- Modal Edit -->
<div class="modal-overlay active" id="modalEdit">
    <div class="modal">
        <div class="modal-header">
            <h3>Edit Petani</h3>
            <a href="<?= $admin_url ?>?page=petani" class="modal-close">&times;</a>
        </div>
        <form method="POST" action="<?= $admin_url ?>?page=petani&action=update&id=<?= $edit_data['id'] ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Petani</label>
                    <input type="text" name="nama_petani" class="form-control" value="<?= $edit_data['nama_petani'] ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">NIK <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="nik" class="form-control" value="<?= $edit_data['nik'] ?>" maxlength="16" minlength="16" pattern="[0-9]{16}" required inputmode="numeric">
                        <small class="text-muted" style="font-size:0.75rem;">NIK harus tepat 16 digit angka dan bersifat unik.</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kelompok Tani</label>
                        <select name="id_kelompok" class="form-control" required>
                            <?php
                            $kel = $conn->query("SELECT * FROM kelompok_tani ORDER BY nama_kelompok");
                            while ($k = $kel->fetch_assoc()): ?>
                            <option value="<?= $k['id'] ?>" <?= $k['id'] == $edit_data['id_kelompok'] ? 'selected' : '' ?>><?= $k['nama_kelompok'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Luas Lahan (Ha) <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="luas_lahan" class="form-control" step="0.01" min="0.01" max="10" value="<?= $edit_data['luas_lahan'] ?>" required>
                        <small class="text-muted" style="font-size:0.75rem;">Maks. 10 Ha (regulasi pupuk bersubsidi)</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="Aktif" <?= $edit_data['status'] === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="Nonaktif" <?= $edit_data['status'] === 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <input type="text" name="alamat" class="form-control" value="<?= $edit_data['alamat'] ?>">
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?= $admin_url ?>?page=petani" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

