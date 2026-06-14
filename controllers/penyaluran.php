<?php
/**
 * Penyaluran / Penyaluran Controller
 */

$upload_dir_bukti = dirname(__DIR__) . '/uploads/bukti';

// Get fertilizer IDs dynamically
$pupuk_map = [];
$res = $conn->query("SELECT id, nama_pupuk, stok, ukuran_kemasan FROM pupuk");
$pupuk_stok = [];
$pupuk_kemasan = [];
while ($row = $res->fetch_assoc()) {
    $name = strtoupper(trim($row['nama_pupuk']));
    $p_id = $row['id'];
    $pupuk_stok[$p_id] = $row['stok'];
    $pupuk_kemasan[$p_id] = (int) filter_var($row['ukuran_kemasan'], FILTER_SANITIZE_NUMBER_INT);
    if (strpos($name, 'UREA') !== false) {
        $pupuk_map['urea'] = $p_id;
    } elseif (strpos($name, 'PHONSKA') !== false) {
        $pupuk_map['phonska'] = $p_id;
    } elseif (strpos($name, 'PELANGI') !== false) {
        $pupuk_map['sp36'] = $p_id;
    } elseif (strpos($name, 'ZA') !== false) {
        $pupuk_map['za'] = $p_id;
    } elseif (strpos($name, 'ORGANIK') !== false) {
        $pupuk_map['organik'] = $p_id;
    }
}


// Endpoint AJAX get_quota
if ($action === 'get_quota' && isset($_GET['id_petani'])) {
    header('Content-Type: application/json');
    $id_petani = (int)$_GET['id_petani'];
    $tanggal = isset($_GET['tanggal']) ? sanitize($_GET['tanggal']) : date('Y-m-d');
    $exclude_tanggal = isset($_GET['exclude_tanggal']) ? sanitize($_GET['exclude_tanggal']) : '';
    $exclude_status = isset($_GET['exclude_status']) ? sanitize($_GET['exclude_status']) : '';
    
    // Ambil data alokasi untuk petani ini
    $alokasi_res = $conn->query("SELECT id_pupuk, jumlah, periode FROM alokasi WHERE id_petani = $id_petani");
    $allocations = [];
    $active_period = '2026';
    while ($row = $alokasi_res->fetch_assoc()) {
        $allocations[$row['id_pupuk']] = [
            'jumlah' => $row['jumlah'],
            'periode' => $row['periode']
        ];
        $active_period = $row['periode'];
    }
    
    // Hitung total pupuk yang sudah disalurkan (kecuali yang sedang diedit)
    $redeemed_query = "
        SELECT id_pupuk, SUM(jumlah) as total 
        FROM penyaluran 
        WHERE id_petani = $id_petani AND status = 'Disalurkan'
    ";
    if (!empty($exclude_tanggal) && !empty($exclude_status)) {
        $redeemed_query .= " AND NOT (tanggal = '$exclude_tanggal' AND status = '$exclude_status')";
    }
    $redeemed_query .= " GROUP BY id_pupuk";
    
    $redeemed_res = $conn->query($redeemed_query);
    $redeemed = [];
    while ($row = $redeemed_res->fetch_assoc()) {
        $redeemed[$row['id_pupuk']] = (int)$row['total'];
    }
    
    // Cek apakah petani sudah melakukan penebusan pupuk hari ini
    $duplicate_check = $conn->query("
        SELECT status, COUNT(*) as count 
        FROM penyaluran 
        WHERE id_petani = $id_petani AND tanggal = '$tanggal'
        GROUP BY status
    ");
    $has_transaction_today = ($duplicate_check && $duplicate_check->num_rows > 0);
    
    // Histori 3 transaksi terakhir petani
    $recent_transactions = [];
    $recent_res = $conn->query("
        SELECT d.tanggal, d.jumlah, d.status, p.nama_pupuk 
        FROM penyaluran d 
        JOIN pupuk p ON d.id_pupuk = p.id 
        WHERE d.id_petani = $id_petani 
        ORDER BY d.tanggal DESC, d.id DESC 
        LIMIT 3
    ");
    while ($row = $recent_res->fetch_assoc()) {
        $recent_transactions[] = [
            'tanggal' => formatTanggalShort($row['tanggal']),
            'jumlah' => $row['jumlah'],
            'status' => $row['status'],
            'nama_pupuk' => $row['nama_pupuk']
        ];
    }
    
    $fertilizer_keys = ['urea', 'phonska', 'sp36', 'za', 'organik'];
    $data = [];
    foreach ($fertilizer_keys as $key) {
        $p_id = $pupuk_map[$key] ?? null;
        $allocated_qty = $p_id && isset($allocations[$p_id]) ? (int)$allocations[$p_id]['jumlah'] : 0;
        $redeemed_qty = $p_id && isset($redeemed[$p_id]) ? (int)$redeemed[$p_id] : 0;
        $remaining = max(0, $allocated_qty - $redeemed_qty);
        
        $data[$key] = [
            'pupuk_id' => $p_id,
            'kemasan' => $p_id && isset($pupuk_kemasan[$p_id]) ? $pupuk_kemasan[$p_id] : 50,
            'alokasi' => $allocated_qty,
            'tebus' => $redeemed_qty,
            'sisa' => $remaining,
            'stok_gudang' => $p_id && isset($pupuk_stok[$p_id]) ? (int)$pupuk_stok[$p_id] : 0
        ];
    }
    
    echo json_encode([
        'success' => true,
        'id_petani' => $id_petani,
        'active_period' => $active_period,
        'has_transaction_today' => $has_transaction_today,
        'recent_transactions' => $recent_transactions,
        'quota' => $data
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'store') {
        $tanggal = sanitize($_POST['tanggal']);
        $id_petani = (int)$_POST['id_petani'];
        $status = 'Disalurkan';
        $keterangan = sanitize($_POST['keterangan']);
        $bukti = null;

        // Validasi: tanggal tidak boleh masa depan
        if ($tanggal > date('Y-m-d')) {
            setFlash('danger', 'Tanggal penyaluran tidak boleh melebihi hari ini (' . date('d/m/Y') . ').');
            redirect($admin_url . '?page=penyaluran');
        }

        $fertilizers = ['urea', 'phonska', 'sp36', 'za', 'organik'];

        // 1. Validasi Stok Fisik Gudang
        $insufficient = [];
        foreach ($fertilizers as $key) {
            if (isset($_POST[$key]) && (int)$_POST[$key] > 0) {
                $p_id = $pupuk_map[$key] ?? null;
                $kemasan = $p_id ? $pupuk_kemasan[$p_id] : 50;
                $qty_kg = (int)$_POST[$key] * $kemasan; // Konversi sak ke kg
                
                if ($p_id && $pupuk_stok[$p_id] < $qty_kg) {
                    $insufficient[] = strtoupper($key);
                }
            }
        }

        if (!empty($insufficient)) {
            setFlash('danger', 'Gagal mencatat: Stok pupuk di gudang tidak mencukupi untuk ' . implode(', ', $insufficient));
            redirect($admin_url . '?page=penyaluran');
        }

        // 2. Validasi Sisa Alokasi Petani
        $exceeded_allocation = [];
        foreach ($fertilizers as $key) {
            if (isset($_POST[$key]) && (int)$_POST[$key] > 0) {
                $p_id = $pupuk_map[$key] ?? null;
                $kemasan = $p_id ? $pupuk_kemasan[$p_id] : 50;
                $qty_kg = (int)$_POST[$key] * $kemasan;
                
                if ($p_id) {
                    if ($key === 'za') {
                        continue;
                    }
                    // Ambil total alokasi
                    $alloc_res = $conn->query("SELECT jumlah FROM alokasi WHERE id_petani = $id_petani AND id_pupuk = $p_id")->fetch_assoc();
                    $alloc_qty = $alloc_res ? (int)$alloc_res['jumlah'] : 0;
                    
                    // Ambil yang sudah terpakai (status Disalurkan)
                    $red_res = $conn->query("SELECT SUM(jumlah) as total FROM penyaluran WHERE id_petani = $id_petani AND id_pupuk = $p_id AND status = 'Disalurkan'")->fetch_assoc();
                    $red_qty = $red_res['total'] ? (int)$red_res['total'] : 0;
                    
                    $remaining_alloc = max(0, $alloc_qty - $red_qty);
                    if ($qty_kg > $remaining_alloc) {
                        $exceeded_allocation[] = strtoupper($key) . " (Kuota: {$alloc_qty} kg, Tebus: {$red_qty} kg, Sisa: {$remaining_alloc} kg, Input: {$qty_kg} kg)";
                    }
                }
            }
        }

        if (!empty($exceeded_allocation)) {
            setFlash('danger', 'Gagal mencatat: Jumlah penyaluran melebihi sisa alokasi kuota petani untuk ' . implode(', ', $exceeded_allocation));
            redirect($admin_url . '?page=penyaluran');
        }

        // 3. File Upload dan Validasi Gambar Bukti
        if (!empty($_FILES['bukti']['name'])) {
            $result = uploadFile($_FILES['bukti'], $upload_dir_bukti);
            if ($result['success']) {
                $bukti = $result['filename'];
            } else {
                setFlash('danger', 'Gagal mencatat: Gagal mengunggah foto bukti (' . $result['message'] . ')');
                redirect($admin_url . '?page=penyaluran');
            }
        }

        // Status 'Disalurkan' has no photo requirement constraint, photo is optional

        $has_saved = false;
        foreach ($fertilizers as $key) {
            if (isset($_POST[$key]) && (int)$_POST[$key] > 0) {
                $id_pupuk = $pupuk_map[$key] ?? null;
                $kemasan = $id_pupuk ? $pupuk_kemasan[$id_pupuk] : 50;
                $jumlah_kg = (int)$_POST[$key] * $kemasan;
                
                if ($id_pupuk) {
                    $stmt = $conn->prepare("INSERT INTO penyaluran (tanggal, id_petani, id_pupuk, jumlah, status, bukti, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("siissss", $tanggal, $id_petani, $id_pupuk, $jumlah_kg, $status, $bukti, $keterangan);

                    if ($stmt->execute()) {
                        $conn->query("UPDATE pupuk SET stok = stok - $jumlah_kg WHERE id = $id_pupuk");
                        $has_saved = true;
                    }
                    $stmt->close();
                }
            }
        }

        if ($has_saved) {
            setFlash('success', 'Penyaluran berhasil dicatat.');
        } else {
            setFlash('danger', 'Gagal mencatat penyaluran (Masukkan setidaknya satu jenis pupuk).');
        }
        redirect($admin_url . '?page=penyaluran');
    }

    if ($action === 'update') {
        $id_petani_old = (int)$_GET['id_petani_old'];
        $tanggal_old = sanitize($_GET['tanggal_old']);
        $status_old = sanitize($_GET['status_old']);
        $bukti_old = sanitize($_GET['bukti_old']);
        $keterangan_old = sanitize($_GET['keterangan_old']);

        $id_petani_new = (int)$_POST['id_petani'];
        $tanggal_new = sanitize($_POST['tanggal']);
        $status_new = 'Disalurkan';
        $keterangan_new = sanitize($_POST['keterangan']);
        $bukti_new = $bukti_old;

        // Fetch old transactions
        $old_records = [];
        $res_old = $conn->query("SELECT id_pupuk, jumlah FROM penyaluran WHERE id_petani = $id_petani_old AND tanggal = '$tanggal_old' AND status = '$status_old'");
        while ($row = $res_old->fetch_assoc()) {
            $old_records[] = $row;
            // Restore stock temporarily for validation
            $conn->query("UPDATE pupuk SET stok = stok + {$row['jumlah']} WHERE id = {$row['id_pupuk']}");
        }

        // Delete old records temporarily
        $conn->query("DELETE FROM penyaluran WHERE id_petani = $id_petani_old AND tanggal = '$tanggal_old' AND status = '$status_old'");

        // Get updated stocks
        $res_stok = $conn->query("SELECT id, stok FROM pupuk");
        $pupuk_stok_curr = [];
        while ($row = $res_stok->fetch_assoc()) {
            $pupuk_stok_curr[$row['id']] = $row['stok'];
        }

        // Validate new stocks and allocations
        $fertilizers = ['urea', 'phonska', 'sp36', 'za', 'organik'];
        $insufficient = [];
        $exceeded_allocation = [];

        foreach ($fertilizers as $key) {
            if (isset($_POST[$key]) && (int)$_POST[$key] > 0) {
                $p_id = $pupuk_map[$key] ?? null;
                $kemasan = $p_id ? $pupuk_kemasan[$p_id] : 50;
                $qty_kg = (int)$_POST[$key] * $kemasan;
                
                if ($p_id) {
                    // Cek stok fisik
                    if ($pupuk_stok_curr[$p_id] < $qty_kg) {
                        $insufficient[] = strtoupper($key);
                    }

                    if ($key === 'za') {
                        continue;
                    }

                    // Cek sisa alokasi kuota petani (karena data transaksi lama sudah dihapus sementara, hitungan ini akurat)
                    $alloc_res = $conn->query("SELECT jumlah FROM alokasi WHERE id_petani = $id_petani_new AND id_pupuk = $p_id")->fetch_assoc();
                    $alloc_qty = $alloc_res ? (int)$alloc_res['jumlah'] : 0;
                    
                    $red_res = $conn->query("SELECT SUM(jumlah) as total FROM penyaluran WHERE id_petani = $id_petani_new AND id_pupuk = $p_id AND status = 'Disalurkan'")->fetch_assoc();
                    $red_qty = $red_res['total'] ? (int)$red_res['total'] : 0;
                    
                    $remaining_alloc = max(0, $alloc_qty - $red_qty);
                    if ($qty_kg > $remaining_alloc) {
                        $exceeded_allocation[] = strtoupper($key) . " (Kuota: {$alloc_qty} kg, Tebus: {$red_qty} kg, Sisa: {$remaining_alloc} kg, Input: {$qty_kg} kg)";
                    }
                }
            }
        }

        // Rollback if validation fails
        if (!empty($insufficient) || !empty($exceeded_allocation)) {
            foreach ($old_records as $rec) {
                $conn->query("UPDATE pupuk SET stok = stok - {$rec['jumlah']} WHERE id = {$rec['id_pupuk']}");
                $stmt = $conn->prepare("INSERT INTO penyaluran (tanggal, id_petani, id_pupuk, jumlah, status, bukti, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("siissss", $tanggal_old, $id_petani_old, $rec['id_pupuk'], $rec['jumlah'], $status_old, $bukti_old, $keterangan_old);
                $stmt->execute();
                $stmt->close();
            }
            
            $msg = '';
            if (!empty($insufficient)) {
                $msg .= 'Stok gudang tidak mencukupi untuk ' . implode(', ', $insufficient) . '. ';
            }
            if (!empty($exceeded_allocation)) {
                $msg .= 'Penyaluran melebihi sisa alokasi kuota untuk ' . implode(', ', $exceeded_allocation) . '.';
            }

            setFlash('danger', 'Gagal memperbarui: ' . $msg);
            redirect($admin_url . '?page=penyaluran');
        }

        // Image upload
        if (!empty($_FILES['bukti']['name'])) {
            $result = uploadFile($_FILES['bukti'], $upload_dir_bukti);
            if ($result['success']) {
                if ($bukti_old && file_exists($upload_dir_bukti . '/' . $bukti_old)) {
                    @unlink($upload_dir_bukti . '/' . $bukti_old);
                }
                $bukti_new = $result['filename'];
            } else {
                // Rollback
                foreach ($old_records as $rec) {
                    $conn->query("UPDATE pupuk SET stok = stok - {$rec['jumlah']} WHERE id = {$rec['id_pupuk']}");
                    $stmt = $conn->prepare("INSERT INTO penyaluran (tanggal, id_petani, id_pupuk, jumlah, status, bukti, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("siissss", $tanggal_old, $id_petani_old, $rec['id_pupuk'], $rec['jumlah'], $status_old, $bukti_old, $keterangan_old);
                    $stmt->execute();
                    $stmt->close();
                }
                setFlash('danger', 'Gagal memperbarui: Gagal mengunggah foto bukti (' . $result['message'] . ')');
                redirect($admin_url . '?page=penyaluran');
            }
        }

        // Status 'Disalurkan' has no photo requirement constraint, photo is optional

        // Insert new records
        $has_saved = false;
        foreach ($fertilizers as $key) {
            if (isset($_POST[$key]) && (int)$_POST[$key] > 0) {
                $id_pupuk = $pupuk_map[$key] ?? null;
                $kemasan = $id_pupuk ? $pupuk_kemasan[$id_pupuk] : 50;
                $jumlah_kg = (int)$_POST[$key] * $kemasan;
                
                if ($id_pupuk) {
                    $stmt = $conn->prepare("INSERT INTO penyaluran (tanggal, id_petani, id_pupuk, jumlah, status, bukti, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("siissss", $tanggal_new, $id_petani_new, $id_pupuk, $jumlah_kg, $status_new, $bukti_new, $keterangan_new);
                    if ($stmt->execute()) {
                        $conn->query("UPDATE pupuk SET stok = stok - $jumlah_kg WHERE id = $id_pupuk");
                        $has_saved = true;
                    }
                    $stmt->close();
                }
            }
        }

        if ($has_saved) {
            setFlash('success', 'Data penyaluran berhasil diperbarui.');
        } else {
            // Rollback
            foreach ($old_records as $rec) {
                $stmt = $conn->prepare("INSERT INTO penyaluran (tanggal, id_petani, id_pupuk, jumlah, status, bukti, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("siissss", $tanggal_old, $id_petani_old, $rec['id_pupuk'], $rec['jumlah'], $status_old, $bukti_old, $keterangan_old);
                $stmt->execute();
                $stmt->close();
            }
            setFlash('danger', 'Gagal memperbarui data penyaluran.');
        }
        redirect($admin_url . '?page=penyaluran');
    }
}

if ($action === 'delete' && isset($_GET['id_petani'])) {
    $id_petani = (int)$_GET['id_petani'];
    $tanggal = sanitize($_GET['tanggal']);
    $status = sanitize($_GET['status']);

    $res = $conn->query("SELECT id_pupuk, jumlah, bukti FROM penyaluran WHERE id_petani = $id_petani AND tanggal = '$tanggal' AND status = '$status'");
    $bukti = null;
    while ($row = $res->fetch_assoc()) {
        $conn->query("UPDATE pupuk SET stok = stok + {$row['jumlah']} WHERE id = {$row['id_pupuk']}");
        $bukti = $row['bukti'];
    }

    if ($bukti && file_exists($upload_dir_bukti . '/' . $bukti)) {
        @unlink($upload_dir_bukti . '/' . $bukti);
    }

    $conn->query("DELETE FROM penyaluran WHERE id_petani = $id_petani AND tanggal = '$tanggal' AND status = '$status'");
    setFlash('success', 'Data penyaluran berhasil dihapus.');
    redirect($admin_url . '?page=penyaluran');
}

// Group list by transaction metadata and pivot fertilizer quantities
$search = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$where = "";
if ($search !== '') {
    $q = $conn->real_escape_string($search);
    $where = " WHERE pt.nama_petani LIKE '%$q%' OR pt.nik LIKE '%$q%' ";
}

$limit = 10;
$page_num = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($page_num < 1) $page_num = 1;
$offset = ($page_num - 1) * $limit;

$total_query = $conn->query("SELECT COUNT(DISTINCT CONCAT(d.tanggal, d.id_petani, d.status, IFNULL(d.bukti,''), IFNULL(d.keterangan,''))) as count FROM penyaluran d JOIN petani pt ON d.id_petani = pt.id $where");
$total_rows = $total_query->fetch_assoc()['count'];
$total_pages = ceil($total_rows / $limit);

$penyaluran_list = $conn->query("
    SELECT 
        d.tanggal,
        d.id_petani,
        d.status,
        d.bukti,
        d.keterangan,
        pt.nama_petani,
        kt.nama_kelompok,
        COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'UREA' THEN d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) as urea,
        COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'NPK PHONSKA' THEN d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) as phonska,
        COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'NPK PELANGI' THEN d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) as sp36,
        COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'ZA' THEN d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) as za,
        COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'ORGANIK' THEN d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) as organik,
        COALESCE(SUM(d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED)), 0) as total,
        COALESCE(SUM(d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED) * pu.harga_per_sak), 0) as total_harga
    FROM penyaluran d
    JOIN petani pt ON d.id_petani = pt.id
    JOIN kelompok_tani kt ON pt.id_kelompok = kt.id
    JOIN pupuk pu ON d.id_pupuk = pu.id
    $where
    GROUP BY d.tanggal, d.id_petani, d.status, d.bukti, d.keterangan
    ORDER BY d.tanggal DESC
    LIMIT $limit OFFSET $offset
");

$edit_data = null;
if ($action === 'edit' && isset($_GET['id_petani'])) {
    $id_petani = (int)$_GET['id_petani'];
    $tanggal = sanitize($_GET['tanggal']);
    $status = sanitize($_GET['status']);

    $res = $conn->query("
        SELECT 
            d.tanggal,
            d.id_petani,
            d.status,
            d.bukti,
            d.keterangan,
            COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'UREA' THEN d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) as urea,
            COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'NPK PHONSKA' THEN d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) as phonska,
            COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'NPK PELANGI' THEN d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) as sp36,
            COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'ZA' THEN d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) as za,
            COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'ORGANIK' THEN d.jumlah / CAST(REPLACE(pu.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) as organik
        FROM penyaluran d
        JOIN pupuk pu ON d.id_pupuk = pu.id
        WHERE d.id_petani = $id_petani AND d.tanggal = '$tanggal' AND d.status = '$status'
        GROUP BY d.tanggal, d.id_petani, d.status, d.bukti, d.keterangan
    ");
    if ($res->num_rows > 0) {
        $edit_data = $res->fetch_assoc();
    }
}

// Fetch active farmers, and if we are editing, fetch the farmer being edited even if they are inactive
$petani_query = "
    SELECT pt.id, pt.nama_petani, pt.nik, kt.nama_kelompok, pt.status 
    FROM petani pt 
    JOIN kelompok_tani kt ON pt.id_kelompok = kt.id 
    WHERE pt.status = 'Aktif'
";
if ($edit_data) {
    $petani_query .= " OR pt.id = " . $edit_data['id_petani'];
}
$petani_query .= " ORDER BY pt.nama_petani ASC";
$petani_options = $conn->query($petani_query);

$pupuk_options = $conn->query("SELECT id, nama_pupuk, stok, harga_per_sak FROM pupuk ORDER BY FIELD(nama_pupuk, 'UREA', 'NPK PHONSKA', 'NPK PELANGI', 'ORGANIK', 'ZA') ASC");

