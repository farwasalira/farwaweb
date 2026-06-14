<!-- Data Alokasi Pupuk View -->
<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 10px;">
        <h3><i class="bx bx-list-check"></i> Data Alokasi Pupuk</h3>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form method="GET" action="" style="display: flex; gap: 8px;">
                <input type="hidden" name="page" value="alokasi">
                <input type="text" name="q" class="form-control" placeholder="Cari NIK atau Nama..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" style="width: 220px; padding: 6px 12px; font-size: 0.85rem; border-radius: 6px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-search"></i></button>
                <?php if(isset($_GET['q']) && $_GET['q'] !== ''): ?>
                    <a href="<?= $admin_url ?>?page=alokasi" class="btn btn-outline btn-sm"><i class="bx bx-x"></i></a>
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
                    <th>UREA</th>
                    <th>PHONSKA</th>
                    <th>NPK PELANGI</th>
                    <th>ORGANIK</th>
                    <th>ZA</th>
                    <th>Jumlah Total (kg)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = isset($offset) ? $offset + 1 : 1; while ($row = $alokasi_list->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= $row['nama_petani'] ?></strong></td>
                    <td><?= $row['nik'] ?></td>
                    <td><span class="badge badge-info"><?= $row['nama_kelompok'] ?></span></td>
                    <td><?= number_format($row['urea'], 0, ',', '.') ?> kg</td>
                    <td><?= number_format($row['phonska'], 0, ',', '.') ?> kg</td>
                    <td><?= number_format($row['sp36'], 0, ',', '.') ?> kg</td>
                    <td><?= number_format($row['organik'], 0, ',', '.') ?> kg</td>
                    <td><?= number_format($row['za'], 0, ',', '.') ?> kg</td>
                    <td>
                        <strong><?= number_format($row['total'], 0, ',', '.') ?> kg</strong>
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="<?= $admin_url ?>?page=alokasi&action=edit&id_petani=<?= $row['id_petani'] ?>&periode=<?= urlencode($row['periode']) ?>" class="btn btn-warning btn-xs"><i class="bx bx-edit"></i></a>
                            <a href="<?= $admin_url ?>?page=alokasi&action=delete&id_petani=<?= $row['id_petani'] ?>&periode=<?= urlencode($row['periode']) ?>" class="btn btn-danger btn-xs"><i class="bx bx-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($alokasi_list->num_rows === 0): ?>
                <tr>
                    <td colspan="11" style="text-align:center; padding: 40px; color: var(--gray-400);">Belum ada data alokasi pupuk.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if (isset($total_pages) && $total_pages > 1): ?>
    <div class="pagination">
        <?php if ($page_num > 1): ?>
            <a href="<?= $admin_url ?>?page=alokasi&p=<?= $page_num - 1 ?><?= $search ? '&q='.urlencode($search) : '' ?>">&laquo; Sebelumnya</a>
        <?php endif; ?>
        
        <?php 
        $start_page = max(1, $page_num - 2);
        $end_page = min($total_pages, $page_num + 2);
        if ($start_page > 1) { echo '<span>...</span>'; }
        for ($i = $start_page; $i <= $end_page; $i++): 
        ?>
            <a href="<?= $admin_url ?>?page=alokasi&p=<?= $i ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="<?= $i === $page_num ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($end_page < $total_pages) { echo '<span>...</span>'; } ?>

        <?php if ($page_num < $total_pages): ?>
            <a href="<?= $admin_url ?>?page=alokasi&p=<?= $page_num + 1 ?><?= $search ? '&q='.urlencode($search) : '' ?>">Selanjutnya &raquo;</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal">
        <div class="modal-header">
            <h3>Tambah Alokasi</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('active')">&times;</button>
        </div>
        <form method="POST" action="<?= $admin_url ?>?page=alokasi&action=store">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Petani</label>
                    <select name="id_petani" class="form-control" required>
                        <option value="">Pilih Petani</option>
                        <?php while ($p = $petani_options->fetch_assoc()): ?>
                        <option value="<?= $p['id'] ?>"><?= $p['nama_petani'] ?> (<?= $p['nama_kelompok'] ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <input type="hidden" name="periode" value="2026">
                
                <h4 style="font-size: 0.95rem; font-weight: 700; margin: 20px 0 10px; color: var(--dark); border-bottom: 1px solid var(--gray-200); padding-bottom: 6px;">
                    <i class="bx bx-package text-success"></i> Jumlah Alokasi Pupuk (kg)
                </h4>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">UREA (kg)</label>
                        <input type="number" name="urea" class="form-control" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">NPK PHONSKA (kg)</label>
                        <input type="number" name="phonska" class="form-control" min="0" value="0">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">NPK PELANGI (kg)</label>
                        <input type="number" name="sp36" class="form-control" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ORGANIK (kg)</label>
                        <input type="number" name="organik" class="form-control" min="0" value="0">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">ZA (kg)</label>
                    <input type="number" name="za" class="form-control" min="0" value="0" style="max-width: calc(50% - 9px);">
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
<div class="modal-overlay active">
    <div class="modal">
        <div class="modal-header">
            <h3>Edit Alokasi</h3>
            <a href="<?= $admin_url ?>?page=alokasi" class="modal-close">&times;</a>
        </div>
        <form method="POST" action="<?= $admin_url ?>?page=alokasi&action=update&id_petani=<?= $edit_data['id_petani'] ?>&periode=<?= urlencode($edit_data['periode']) ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Petani</label>
                    <select name="id_petani" class="form-control" required>
                        <?php
                        $pt = $conn->query("SELECT pt.id, pt.nama_petani, kt.nama_kelompok FROM petani pt JOIN kelompok_tani kt ON pt.id_kelompok = kt.id ORDER BY pt.nama_petani");
                        while ($p = $pt->fetch_assoc()): ?>
                        <option value="<?= $p['id'] ?>" <?= $p['id'] == $edit_data['id_petani'] ? 'selected' : '' ?>><?= $p['nama_petani'] ?> (<?= $p['nama_kelompok'] ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <input type="hidden" name="periode" value="2026">
                
                <h4 style="font-size: 0.95rem; font-weight: 700; margin: 20px 0 10px; color: var(--dark); border-bottom: 1px solid var(--gray-200); padding-bottom: 6px;">
                    <i class="bx bx-package text-success"></i> Jumlah Alokasi Pupuk (kg)
                </h4>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">UREA (kg)</label>
                        <input type="number" name="urea" class="form-control" min="0" value="<?= (int)$edit_data['urea'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">NPK PHONSKA (kg)</label>
                        <input type="number" name="phonska" class="form-control" min="0" value="<?= (int)$edit_data['phonska'] ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">NPK PELANGI (kg)</label>
                        <input type="number" name="sp36" class="form-control" min="0" value="<?= (int)$edit_data['sp36'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ORGANIK (kg)</label>
                        <input type="number" name="organik" class="form-control" min="0" value="<?= (int)$edit_data['organik'] ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">ZA (kg)</label>
                    <input type="number" name="za" class="form-control" min="0" value="<?= (int)$edit_data['za'] ?>" style="max-width: calc(50% - 9px);">
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?= $admin_url ?>?page=alokasi" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

