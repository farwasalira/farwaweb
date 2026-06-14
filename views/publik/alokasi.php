<?php
$search_nik = isset($_GET['nik']) ? sanitize($_GET['nik']) : '';
$alokasi = null;
$nama_petani = '';
$nama_kelompok = '';
$nik = '';
$fertilizer_data = [];

if (!empty($search_nik)) {
    // Escape string just in case
    $safe_nik = $conn->real_escape_string($search_nik);
    
    // Query to get initial allocation and active distribution for each fertilizer type
    $alokasi = $conn->query("
        SELECT 
            pt.nama_petani,
            pt.nik,
            kt.nama_kelompok,
            p.id AS id_pupuk,
            p.nama_pupuk,
            p.ukuran_kemasan,
            p.berat_kemasan_kg,
            COALESCE((SELECT SUM(a.jumlah) FROM alokasi a WHERE a.id_petani = pt.id AND a.id_pupuk = p.id), 0) AS alokasi_awal,
            COALESCE((SELECT SUM(py.jumlah) FROM penyaluran py WHERE py.id_petani = pt.id AND py.id_pupuk = p.id AND py.status = 'Disalurkan'), 0) AS sudah_disalurkan
        FROM pupuk p
        CROSS JOIN petani pt
        JOIN kelompok_tani kt ON pt.id_kelompok = kt.id
        WHERE pt.nik = '$safe_nik'
        ORDER BY p.nama_pupuk ASC
    ");
    
    if ($alokasi && $alokasi->num_rows > 0) {
        $first = $alokasi->fetch_assoc();
        $nama_petani = $first['nama_petani'];
        $nama_kelompok = $first['nama_kelompok'];
        $nik = $first['nik'];
        
        $alokasi->data_seek(0);
        while ($row = $alokasi->fetch_assoc()) {
            $fertilizer_data[] = $row;
        }
    }
}
?>
<div style="height: 40px; background-color: transparent;"></div>

<section class="py-5" style="background: #f8fafc; min-height: 80vh;">
    <div class="container py-4">
        <div class="row justify-content-center text-center mb-5 animate__animated animate__fadeIn">
            <div class="col-lg-7">
                <span class="section-label">Cek Kuota</span>
                <h2 class="section-title">Informasi Alokasi Pupuk</h2>
                <p class="text-secondary" style="font-size: 0.92rem;">Silakan masukkan Nomor Induk Kependudukan (NIK) Anda untuk mengecek jatah/kuota pupuk bersubsidi.</p>
            </div>
        </div>

        <?php if (empty($search_nik) || empty($fertilizer_data)): ?>
        <div class="row justify-content-center mb-5 animate__animated animate__fadeInUp">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 rounded-4 shadow-sm overflow-hidden" style="background: #fff; border: 1px solid rgba(226, 232, 240, 0.8) !important;">
                    <div class="card-body p-4 p-md-5">
                        <form method="GET" action="<?=$public_url?>">
                            <input type="hidden" name="page" value="alokasi">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-slate-700">Nomor Induk Kependudukan (NIK)</label>
                                <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-light border-0"><i class="bx bx-id-card text-muted"></i></span>
                                    <input type="text" name="nik" class="form-control border-0 bg-light" placeholder="Masukkan 16 digit NIK penerima kuota" value="<?=htmlspecialchars($search_nik)?>" required pattern="[0-9]{16}" title="Masukkan 16 digit NIK yang valid" maxlength="16">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100 rounded-3 shadow-sm fw-bold">
                                <i class="bx bx-search-alt me-2"></i> Cek Kuota Alokasi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($search_nik)): ?>
            <?php if (!empty($fertilizer_data)): ?>
                <!-- Profile Banner Card -->
                <div class="card border-0 rounded-4 overflow-hidden mb-4 shadow-sm animate__animated animate__fadeInUp" style="background: linear-gradient(135deg, #115e36 0%, #090d16 100%) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important;">
                    <div class="card-body p-4 p-md-5 text-white position-relative overflow-hidden">
                        <div class="position-absolute rounded-circle" style="width: 250px; height: 250px; background: rgba(52, 211, 153, 0.15); filter: blur(50px); top: -60px; right: -60px; pointer-events: none;"></div>
                        
                        <div class="row align-items-center g-4 position-relative z-2">
                            <div class="col-md-8 d-flex align-items-center gap-3 flex-wrap">
                                <div class="rounded-4 d-flex align-items-center justify-content-center shadow-lg border" style="width: 72px; height: 72px; background: rgba(255, 255, 255, 0.07); border-color: rgba(255, 255, 255, 0.15) !important; backdrop-filter: blur(10px); border-radius: 18px;">
                                    <i class="bx bxs-user-check" style="font-size: 2.3rem; color: #34d399;"></i>
                                </div>
                                <div class="text-start">
                                    <span class="text-emerald-subtle text-uppercase fw-extrabold font-monospace" style="font-size: 0.72rem; letter-spacing: 2.5px; color: #34d399 !important;">Petani Terverifikasi</span>
                                    <h3 class="fw-extrabold mb-1 mt-1 text-white" style="font-size: 1.8rem; letter-spacing: -0.5px;"><?= htmlspecialchars($nama_petani) ?></h3>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); color: #34d399; font-weight: 600; font-size: 0.75rem; padding: 5px 12px; border-radius: 6px;">
                                            <i class="bx bxs-group align-middle me-1"></i> Poktan: <?= htmlspecialchars($nama_kelompok) ?>
                                        </span>
                                        <span class="badge" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); color: rgba(255, 255, 255, 0.7); font-size: 0.75rem; padding: 5px 12px; border-radius: 6px;">
                                            <i class="bx bxs-map align-middle me-1"></i> Desa Lumaring
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end text-start">
                                <div class="text-white-50 mb-2" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 1.5px;">Nomor Induk Kependudukan</div>
                                <div class="d-inline-block px-3 py-2 rounded-3 font-monospace text-emerald border" style="background: rgba(255, 255, 255, 0.05); border-color: rgba(255, 255, 255, 0.1) !important; color: #34d399; letter-spacing: 1px; font-weight: 700; font-size: 1.05rem; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                                    <?= maskNIK($nik) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fertilizer Quota Breakdown Table -->
                <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4 animate__animated animate__fadeInUp" style="background: #fff; border: 1px solid rgba(226, 232, 240, 0.8) !important;">
                    <div class="card-header bg-white p-4 border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold mb-0 text-slate-800" style="font-size: 1.1rem;"><i class="bx bxs-pie-chart-alt-2 text-success align-middle me-1"></i> Rincian Kuota Alokasi Pupuk</h5>
                        <span class="badge bg-emerald-subtle text-emerald" style="background: #ecfdf5; color: #10b981; font-weight: 600; font-size: 0.75rem; padding: 6px 12px; border-radius: 6px;">
                            e-RDKK <?= date('Y') ?>
                        </span>
                    </div>
                    
                    <div class="table-responsive">
                        <?php
                        $cols_data = [];
                        $total_alokasi = 0;
                        $total_alokasi_sak = 0;
                        $total_sisa = 0;
                        $total_sisa_sak = 0;

                        foreach ($fertilizer_data as $row) {
                            $alokasi_awal = $row['alokasi_awal'];
                            $sudah_disalurkan = $row['sudah_disalurkan'];
                            $sisa_kuota = max(0, $alokasi_awal - $sudah_disalurkan);
                            
                            $berat_kemasan = floatval($row['berat_kemasan_kg']);
                            $alokasi_sak = $berat_kemasan > 0 ? ($alokasi_awal / $berat_kemasan) : 0;
                            $sisa_sak = $berat_kemasan > 0 ? ($sisa_kuota / $berat_kemasan) : 0;
                            
                            $total_alokasi += $alokasi_awal;
                            $total_alokasi_sak += $alokasi_sak;
                            $total_sisa += $sisa_kuota;
                            $total_sisa_sak += $sisa_sak;
                            
                            $cols_data[] = [
                                'nama_pupuk' => $row['nama_pupuk'],
                                'alokasi_awal' => $alokasi_awal,
                                'alokasi_sak' => $alokasi_sak,
                                'sisa_kuota' => $sisa_kuota,
                                'sisa_sak' => $sisa_sak
                            ];
                        }

                        $total_alokasi_sak_formatted = ($total_alokasi_sak == (int)$total_alokasi_sak) ? number_format($total_alokasi_sak, 0, ',', '.') : number_format($total_alokasi_sak, 1, ',', '.');
                        $total_sisa_sak_formatted = ($total_sisa_sak == (int)$total_sisa_sak) ? number_format($total_sisa_sak, 0, ',', '.') : number_format($total_sisa_sak, 1, ',', '.');
                        ?>
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                            <thead class="table-light">
                                <tr style="border-bottom: 2px solid #e2e8f0; vertical-align: middle;">
                                    <th class="py-3 px-4 text-muted" style="font-weight: 700; font-size: 0.78rem; text-transform: uppercase;">Keterangan</th>
                                    <?php foreach($cols_data as $col): ?>
                                        <th class="py-3 px-3 text-center text-muted" style="font-weight: 700; font-size: 0.78rem; text-transform: uppercase;"><?= htmlspecialchars($col['nama_pupuk']) ?></th>
                                    <?php endforeach; ?>
                                    <th class="py-3 px-4 text-end text-muted" style="font-weight: 700; font-size: 0.78rem; text-transform: uppercase; width: 160px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="py-3 px-4 fw-bold text-slate-800" style="font-size: 0.92rem;">Alokasi Awal</td>
                                    <?php foreach($cols_data as $col): 
                                        $alokasi_sak_formatted = ($col['alokasi_sak'] == (int)$col['alokasi_sak']) ? number_format($col['alokasi_sak'], 0, ',', '.') : number_format($col['alokasi_sak'], 1, ',', '.');
                                    ?>
                                        <td class="py-3 px-3 text-center">
                                            <span class="d-block fw-bold text-slate-800" style="font-size: 0.95rem;"><?= number_format($col['alokasi_awal'], 0, ',', '.') ?> kg</span>
                                            <span class="d-block text-secondary font-monospace" style="font-size: 0.78rem; font-weight: 500;"><?= $alokasi_sak_formatted ?> sak</span>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="py-3 px-4 text-end text-slate-800" style="font-size: 0.92rem;">
                                        <span class="d-block fw-bold"><?= number_format($total_alokasi, 0, ',', '.') ?> kg</span>
                                        <span class="d-block text-secondary font-monospace" style="font-size: 0.78rem; font-weight: 500;"><?= $total_alokasi_sak_formatted ?> sak</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 fw-bold text-slate-800" style="font-size: 0.92rem;">Sisa Kuota</td>
                                    <?php foreach($cols_data as $col): 
                                        $sisa_kuota = $col['sisa_kuota'];
                                        $sisa_sak_formatted = ($col['sisa_sak'] == (int)$col['sisa_sak']) ? number_format($col['sisa_sak'], 0, ',', '.') : number_format($col['sisa_sak'], 1, ',', '.');
                                    ?>
                                        <td class="py-3 px-3 text-center">
                                            <span class="d-block fw-extrabold <?= $sisa_kuota > 0 ? 'text-success' : 'text-danger' ?>" style="font-size: 1rem; <?= $sisa_kuota > 0 ? 'color: #10b981 !important;' : '' ?>">
                                                <?= number_format($sisa_kuota, 0, ',', '.') ?> kg
                                            </span>
                                            <span class="d-block font-monospace <?= $sisa_kuota > 0 ? 'text-success-emphasis text-emerald' : 'text-danger-emphasis text-danger' ?>" style="font-size: 0.78rem; font-weight: 600; <?= $sisa_kuota > 0 ? 'color: #059669 !important;' : '' ?>">
                                                <?= $sisa_sak_formatted ?> sak <?= $sisa_kuota == 0 ? '(Habis)' : '' ?>
                                            </span>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="py-3 px-4 text-end <?= $total_sisa > 0 ? 'text-success' : 'text-danger' ?>" style="font-size: 1.05rem; <?= $total_sisa > 0 ? 'color: #10b981 !important;' : '' ?>">
                                        <span class="d-block fw-extrabold"><?= number_format($total_sisa, 0, ',', '.') ?> kg</span>
                                        <span class="d-block font-monospace <?= $total_sisa > 0 ? 'text-success-emphasis text-emerald' : 'text-danger-emphasis text-danger' ?>" style="font-size: 0.78rem; font-weight: 600; <?= $total_sisa > 0 ? 'color: #059669 !important;' : '' ?>"><?= $total_sisa_sak_formatted ?> sak</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="p-2 text-center animate__animated animate__fadeInUp d-flex justify-content-center gap-3 flex-wrap">
                    <a href="<?=$public_url?>?page=alokasi" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-2 shadow-sm transition hover-up" style="border: 2px solid var(--slate-300); font-size: 0.92rem; color: var(--slate-600);">
                        <i class="bx bx-refresh" style="font-size: 1.2rem;"></i> Cek NIK Lain
                    </a>
                    <a href="<?=$public_url?>?page=penyaluran&nik=<?=htmlspecialchars($search_nik)?>" class="btn btn-success rounded-pill px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-2 shadow-lg transition hover-up" style="background: var(--primary-gradient); border: none; font-size: 0.92rem; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);">
                        <i class="bx bx-history" style="font-size: 1.2rem;"></i> Lihat Riwayat Penyaluran <i class="bx bx-right-arrow-alt" style="font-size: 1.2rem;"></i>
                    </a>
                </div>
            <?php else: ?>
                <!-- Unregistered NIK Screen -->
                <div class="card border-0 rounded-4 shadow-sm animate__animated animate__fadeInUp overflow-hidden" style="background: #fff; border: 1px solid rgba(226, 232, 240, 0.8) !important;">
                    <div class="card-body text-center py-5">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger mb-3" style="width: 70px; height: 70px;">
                            <i class="bx bx-error-circle" style="font-size: 2.2rem;"></i>
                        </div>
                        <h4 class="fw-bold text-slate-800 mb-2" style="font-size: 1.25rem;">Data NIK Tidak Ditemukan</h4>
                        <p class="text-secondary mx-auto mb-4" style="font-size: 0.88rem; max-width: 420px; line-height: 1.6;">
                            Maaf, NIK <strong><?= htmlspecialchars($search_nik) ?></strong> belum terdaftar dalam sistem e-RDKK Kelompok Tani Desa Lumaring.
                        </p>
                        <div>
                            <a href="<?=$public_url?>?page=alokasi" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold" style="font-size: 0.85rem; border: 2px solid var(--slate-300);">
                                <i class="bx bx-refresh"></i> Coba NIK Lain
                            </a>
                        </div>
                    </div>
                </div>

                <!-- SweetAlert Trigger Script -->
                <script>
                document.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({
                        icon: 'error',
                        title: 'NIK Tidak Terdaftar',
                        html: 'NIK <strong><?= htmlspecialchars($search_nik) ?></strong> tidak terdaftar dalam database e-RDKK Desa Lumaring.<br><br><span style="font-size: 0.85rem; color: #64748b;">Silakan periksa kembali nomor NIK Anda atau hubungi Ketua Kelompok Tani setempat untuk pendaftaran e-RDKK.</span>',
                        confirmButtonColor: '#10b981',
                        confirmButtonText: 'Kembali',
                        customClass: {
                            popup: 'rounded-4'
                        }
                    });
                });
                </script>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>





