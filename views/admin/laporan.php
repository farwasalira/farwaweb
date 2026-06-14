<?php
// Translation array for Indonesian Months
$bulan = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tgl_cetak = date('d') . ' ' . $bulan[(int)date('m')] . ' ' . date('Y');
?>

<!-- Laporan View -->
<style>
/* Default state (Hidden on screen) */
.print-header {
    display: none;
}
.print-footer {
    display: none;
}

@media print {
    /* Hide screen elements */
    .admin-sidebar, .admin-topbar, .topbar-date, .sidebar-toggle, .site-dropdown, .alert, .card-header .btn {
        display: none !important;
    }
    
    /* Hide Filter Form Card completely */
    .no-print {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        margin-bottom: 0 !important;
    }
    
    .card-header {
        border: none !important;
        text-align: center;
        display: block !important;
        padding: 10px 0 !important;
    }
    
    .card-header h3 {
        font-size: 1.4rem !important;
        justify-content: center !important;
        text-transform: uppercase;
        margin-bottom: 8px !important;
        font-weight: 800 !important;
        color: #000 !important;
    }
    
    .card-header h3 i {
        display: none !important; /* Hide icon */
    }
    
    .card-header .badge {
        background: transparent !important;
        color: #000 !important;
        border: none !important;
        font-size: 0.9rem !important;
        font-weight: 500 !important;
        padding: 0 !important;
    }
    
    /* Show print elements */
    .print-header {
        display: block !important;
        margin-bottom: 25px;
    }
    
    .print-footer {
        display: block !important;
        margin-top: 45px;
    }
    
    /* Kop Surat Styling */
    .kop-surat {
        display: flex !important;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
        padding-bottom: 10px;
    }
    
    .kop-logo {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
    }
    
    .kop-logo i {
        font-size: 3.5rem !important;
        color: #10b981 !important;
        border: 2px solid #10b981;
        border-radius: 12px;
        padding: 6px;
    }
    
    .kop-text {
        flex: 1;
        padding-left: 95px;
        padding-right: 20px;
    }
    
    .kop-text h2 {
        display: none;
    }
    
    .kop-text h3 {
        font-size: 1.3rem !important;
        font-weight: 800 !important;
        margin-bottom: 6px !important;
        color: #10b981 !important;
        letter-spacing: -0.3px;
    }
    
    .kop-text p {
        font-size: 0.8rem !important;
        margin: 0 !important;
        color: #374151 !important;
        line-height: 1.4;
    }
    
    .kop-line {
        border-top: 3.5px solid #000;
        margin-top: 6px;
    }
    
    .kop-line-sub {
        border-top: 1.5px solid #000;
        margin-top: 2px;
        margin-bottom: 25px;
    }
    
    /* Table Print Adjustments */
    .data-table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-top: 15px !important;
    }
    
    .data-table thead th {
        background: #f1f5f9 !important;
        color: #000 !important;
        border: 1px solid #94a3b8 !important;
        font-size: 0.8rem !important;
        font-weight: 700 !important;
        text-align: center !important;
        padding: 10px !important;
    }
    
    .data-table tbody td {
        border: 1px solid #cbd5e1 !important;
        color: #000 !important;
        font-size: 0.85rem !important;
        padding: 10px !important;
        background: transparent !important;
    }
    
    /* Hide badges inside table when printing */
    .data-table .badge {
        background: transparent !important;
        color: #000 !important;
        padding: 0 !important;
        font-weight: 600 !important;
        font-size: 0.85rem !important;
        border: none !important;
    }
    
    /* Signatory / Tanda Tangan */
    .signature-section {
        display: flex !important;
        justify-content: flex-end !important;
        margin-top: 50px !important;
        page-break-inside: avoid;
    }
    
    .signature-space {
        width: 250px;
        text-align: center;
        font-size: 0.9rem;
    }
    
    .signature-space p {
        margin: 0 0 5px 0 !important;
    }
    
    .signature-name {
        text-decoration: underline;
        font-size: 0.95rem;
    }
}
</style>

<div class="print-header">
    <div class="kop-surat">
        <div class="kop-logo">
            <i class="bx bxs-leaf"></i>
        </div>
        <div class="kop-text">
            <h3>KIOS PENGECER RESMI SIPUPUK - DESA LUMARING</h3>
            <p>Alamat: Jl. Poros Desa Lumaring, Kec. Larompong, Kab. Luwu, Sulawesi Selatan</p>
            <p>Telp/WA: 0813-5555-1234</p>
        </div>
    </div>
    <div class="kop-line"></div>
    <div class="kop-line-sub"></div>
</div>

<div class="card no-print" style="margin-bottom:24px">
    <div class="card-body">
        <form method="GET" action="<?=$admin_url?>" style="display:flex;gap:14px;align-items:flex-end;flex-wrap:wrap">
            <input type="hidden" name="page" value="laporan">
            <div class="form-group" style="margin:0"><label class="form-label">Jenis Laporan</label>
                <select name="jenis" class="form-control"><option value="penyaluran" <?=$jenis==='penyaluran'?'selected':''?>>Penyaluran</option><option value="stok" <?=$jenis==='stok'?'selected':''?>>Stok Masuk</option></select>
            </div>
            <div class="form-group" style="margin:0"><label class="form-label">Tahun</label>
                <input type="number" name="tahun" class="form-control" value="<?=$tahun?>" min="2000" max="<?=date('Y')?>" style="width: 90px; padding: 8px;">
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-search"></i> Filter</button>
            <button type="button" class="btn btn-info btn-sm" onclick="window.print()"><i class="bx bx-printer"></i> Cetak</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="bx bxs-report"></i> Rekap <?=ucfirst(str_replace('_',' ',$jenis))?> Tahunan</h3>
        <span class="badge badge-secondary">Tahun <?=$tahun?></span>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Bulan</th>
                    <?php foreach ($pupukData as $p): ?>
                        <th style="text-align:center"><?=$p['nama_pupuk']?><span class="no-print"> (sak)</span></th>
                    <?php endforeach; ?>
                    <th style="text-align:center">Total<span class="no-print"> (sak)</span></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grandTotal = 0;
                $totalPerPupuk = [];
                foreach ($pupukData as $p) {
                    $totalPerPupuk[$p['id']] = 0;
                }

                for ($i = 1; $i <= 12; $i++): 
                    $rowTotal = 0;
                ?>
                <tr>
                    <td><?=$i?></td>
                    <td><strong><?=$bulan[$i]?></strong></td>
                    <?php foreach ($pupukData as $p): 
                        $jml = $rekap[$i][$p['id']];
                        $rowTotal += $jml;
                        $totalPerPupuk[$p['id']] += $jml;
                    ?>
                        <td style="text-align:center; color: <?=$jml>0?'#000':'#94a3b8'?>;">
                            <?= $jml > 0 ? number_format($jml, 0, ',', '.') : '-' ?>
                        </td>
                    <?php endforeach; ?>
                    <td style="text-align:center; font-weight:bold;">
                        <?= $rowTotal > 0 ? number_format($rowTotal, 0, ',', '.') : '-' ?>
                    </td>
                </tr>
                <?php 
                    $grandTotal += $rowTotal;
                endfor; 
                ?>
                <tr style="background:var(--gray-50);font-weight:700">
                    <td colspan="2" style="text-align:right">TOTAL KESELURUHAN:</td>
                    <?php foreach ($pupukData as $p): ?>
                        <td style="text-align:center"><?= $totalPerPupuk[$p['id']] > 0 ? number_format($totalPerPupuk[$p['id']], 0, ',', '.') : '-' ?></td>
                    <?php endforeach; ?>
                    <td style="text-align:center"><?= $grandTotal > 0 ? number_format($grandTotal, 0, ',', '.') : '-' ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="print-footer">
    <div class="signature-section">
        <div class="signature-space">
            <p>Lumaring, <?=$tgl_cetak?></p>
            <p>Pengelola Kios SIPUPUK,</p>
            <div style="height: 75px;"></div>
            <p class="signature-name"><strong><?=$_SESSION['admin_nama'] ?? 'Admin'?></strong></p>
            <p style="font-size: 0.78rem; color: #4b5563; margin-top: 4px !important;">ID Pengecer: SIP-987032</p>
        </div>
    </div>
</div>
