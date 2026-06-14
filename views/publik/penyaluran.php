<?php
$search_nik = isset($_GET['nik']) ? sanitize($_GET['nik']) : '';
$dist = null;

if (!empty($search_nik)) {
    $safe_nik = $conn->real_escape_string($search_nik);
    $dist = $conn->query("
        SELECT 
            d.tanggal,
            d.id_petani,
            d.status,
            d.bukti,
            d.keterangan,
            pt.nama_petani,
            pt.nik,
            kt.nama_kelompok,
            COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'UREA' THEN d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) as urea,
            COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'NPK PHONSKA' THEN d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) as phonska,
            COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'NPK PELANGI' THEN d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) as sp36,
            COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'ZA' THEN d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) as za,
            COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'ORGANIK' THEN d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) as organik,
            COALESCE(SUM(d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED)), 0) as total
        FROM penyaluran d
        JOIN petani pt ON d.id_petani = pt.id
        JOIN kelompok_tani kt ON pt.id_kelompok = kt.id
        JOIN pupuk pu ON d.id_pupuk = pu.id
        WHERE pt.nik = '$safe_nik'
        GROUP BY d.tanggal, d.id_petani, d.status, d.bukti, d.keterangan
        ORDER BY d.tanggal DESC
    ");
}
?>
<!-- Page Header Spacer -->
<div style="height: 40px; background-color: transparent;"></div>

<section class="py-5" style="background: #f8fafc; min-height: 80vh;">
    <div class="container py-4">
        <div class="row justify-content-center text-center mb-5 animate__animated animate__fadeIn">
            <div class="col-lg-7">
                <span class="section-label">Cek Riwayat</span>
                <h2 class="section-title">Riwayat Penyaluran Pupuk</h2>
                <p class="text-secondary" style="font-size: 0.92rem;">Silakan masukkan Nomor Induk Kependudukan (NIK) Anda untuk mengecek riwayat penyaluran pupuk Anda.</p>
            </div>
        </div>

        <?php if (empty($search_nik) || !$dist || $dist->num_rows == 0): ?>
        <div class="row justify-content-center mb-5 animate__animated animate__fadeInUp">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 rounded-4 shadow-sm overflow-hidden" style="background: #fff; border: 1px solid rgba(226, 232, 240, 0.8) !important;">
                    <div class="card-body p-4 p-md-5">
                        <form method="GET" action="<?=$public_url?>">
                            <input type="hidden" name="page" value="penyaluran">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-slate-700">Nomor Induk Kependudukan (NIK)</label>
                                <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-light border-0"><i class="bx bx-id-card text-muted"></i></span>
                                    <input type="text" name="nik" class="form-control border-0 bg-light" placeholder="Masukkan 16 digit NIK penerima penyaluran" value="<?=htmlspecialchars($search_nik)?>" required pattern="[0-9]{16}" title="Masukkan 16 digit NIK yang valid" maxlength="16">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100 rounded-3 shadow-sm fw-bold">
                                <i class="bx bx-time-five me-2"></i> Cek Riwayat Penyaluran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($search_nik)): ?>
            <div class="card border-0 rounded-4 shadow-sm animate__animated animate__fadeInUp overflow-hidden" style="background: #fff; border: 1px solid rgba(226, 232, 240, 0.8) !important;">
                <div class="card-header bg-white p-4 border-bottom">
                    <h5 class="fw-bold mb-0 text-slate-800" style="font-size: 1.1rem;"><i class="bx bxs-time-five text-success align-middle me-1"></i> Hasil Pencarian Riwayat Penyaluran</h5>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light">
                            <tr style="border-bottom: 2px solid #e2e8f0;">
                                <th class="py-3 px-3 text-muted" style="font-weight: 700; font-size: 0.78rem; text-transform: uppercase; width: 110px;">Tanggal</th>
                                <th class="py-3 px-3 text-muted" style="font-weight: 700; font-size: 0.78rem; text-transform: uppercase;">Nama Petani</th>
                                <th class="py-3 px-3 text-muted" style="font-weight: 700; font-size: 0.78rem; text-transform: uppercase;">Kelompok</th>
                                <th class="py-3 px-3 text-center text-muted" style="font-weight: 700; font-size: 0.78rem; text-transform: uppercase; width: 90px;">UREA</th>
                                <th class="py-3 px-3 text-center text-muted" style="font-weight: 700; font-size: 0.78rem; text-transform: uppercase; width: 90px;">PHONSKA</th>
                                <th class="py-3 px-3 text-center text-muted" style="font-weight: 700; font-size: 0.78rem; text-transform: uppercase; width: 90px;">NPK PELANGI</th>
                                <th class="py-3 px-3 text-center text-muted" style="font-weight: 700; font-size: 0.78rem; text-transform: uppercase; width: 90px;">ORGANIK</th>
                                <th class="py-3 px-3 text-center text-muted" style="font-weight: 700; font-size: 0.78rem; text-transform: uppercase; width: 90px;">ZA</th>
                                <th class="py-3 px-3 text-end text-muted" style="font-weight: 700; font-size: 0.78rem; text-transform: uppercase; width: 130px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($dist && $dist->num_rows>0): while($row=$dist->fetch_assoc()):?>
                            <tr>
                                <td class="py-3 px-3 text-secondary" style="font-size: 0.82rem;"><i class="bx bx-calendar align-middle me-1"></i> <?=formatTanggalShort($row['tanggal'])?></td>
                                <td class="py-3 px-3">
                                    <div class="fw-bold text-dark" style="font-size: 0.92rem;"><?=$row['nama_petani']?></div>
                                    <div class="text-secondary font-monospace" style="font-size: 0.82rem;"><?=maskNIK($row['nik'])?></div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="badge bg-emerald-subtle text-emerald" style="background: #ecfdf5; color: #10b981; font-weight: 600; font-size: 0.75rem; padding: 5px 10px; border-radius: 6px;"><i class="bx bxs-group align-middle me-1"></i> <?=$row['nama_kelompok']?></span>
                                </td>
                                <td class="py-3 px-3 text-center text-secondary"><?=number_format($row['urea'],0,',','.')?> sak</td>
                                <td class="py-3 px-3 text-center text-secondary"><?=number_format($row['phonska'],0,',','.')?> sak</td>
                                <td class="py-3 px-3 text-center text-secondary"><?=number_format($row['sp36'],0,',','.')?> sak</td>
                                <td class="py-3 px-3 text-center text-secondary"><?=number_format($row['organik'],0,',','.')?> sak</td>
                                <td class="py-3 px-3 text-center text-secondary"><?=number_format($row['za'],0,',','.')?> sak</td>
                                <td class="py-3 px-3 text-end">
                                    <span class="fw-extrabold text-slate-900" style="font-size: 0.95rem;"><?=number_format($row['total'],0,',','.')?></span> <span class="text-muted" style="font-size: 0.75rem;">sak</span>
                                </td>
                            </tr>
                            <?php endwhile; else:?>
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted py-3">
                                        <i class="bx bx-error-circle text-muted display-4 mb-2"></i>
                                        <p class="mb-0 fw-semibold">Tidak ada riwayat penyaluran yang ditemukan untuk NIK tersebut.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif;?>
                        </tbody>
                    </table>
                </div>
                
                <div class="p-4 text-center border-top d-flex justify-content-center gap-3 flex-wrap" style="background: #f8fafc;">
                    <a href="<?=$public_url?>?page=penyaluran" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-2 shadow-sm transition hover-up" style="border: 2px solid var(--slate-300); font-size: 0.95rem; color: var(--slate-600);">
                        <i class="bx bx-refresh" style="font-size: 1.2rem;"></i> Cek NIK Lain
                    </a>
                    <a href="<?=$public_url?>?page=alokasi&nik=<?=htmlspecialchars($search_nik)?>" class="btn btn-success rounded-pill px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-2 shadow-sm transition hover-up" style="background: #10b981; border: none; font-size: 0.95rem;">
                        <i class="bx bx-info-circle" style="font-size: 1.2rem;"></i> Lihat Sisa Kuota
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>


