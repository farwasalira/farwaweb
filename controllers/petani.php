<?php
/**
 * Petani Controller
 * CRUD operations for farmer data
 */

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'store') {
        $nama = strtoupper(sanitize($_POST['nama_petani']));
        $nik = sanitize($_POST['nik']);
        $id_kelompok_input = $_POST['id_kelompok'];
        $luas = (float)$_POST['luas_lahan'];
        $alamat = sanitize($_POST['alamat']);
        $status = isset($_POST['status']) ? sanitize($_POST['status']) : 'Aktif';

        // Validasi NIK: wajib, tepat 16 digit, angka saja
        if (empty($nik)) {
            setFlash('danger', 'NIK wajib diisi.');
            redirect($admin_url . '?page=petani');
        }
        if (!ctype_digit($nik) || strlen($nik) !== 16) {
            setFlash('danger', 'NIK harus tepat 16 digit angka. Anda memasukkan ' . strlen($nik) . ' karakter.');
            redirect($admin_url . '?page=petani');
        }
        // Validasi NIK unik
        $nik_check = $conn->query("SELECT id FROM petani WHERE nik = '$nik' LIMIT 1");
        if ($nik_check && $nik_check->num_rows > 0) {
            setFlash('danger', 'NIK ' . $nik . ' sudah terdaftar untuk petani lain. Setiap petani harus memiliki NIK yang unik.');
            redirect($admin_url . '?page=petani');
        }

        // Validasi luas lahan: harus > 0 dan max 10 Ha
        if ($luas <= 0 || $luas > 10) {
            setFlash('danger', 'Luas lahan harus antara 0,01 sampai 10 Ha (sesuai regulasi pupuk bersubsidi).');
            redirect($admin_url . '?page=petani');
        }

        // Handle pembuatan kelompok baru
        $is_new_kelompok = false;
        $nama_kel = '';
        if ($id_kelompok_input === 'baru') {
            $nama_kel = sanitize($_POST['nama_kelompok_baru']);
            if (empty($nama_kel)) {
                setFlash('danger', 'Nama kelompok tani baru wajib diisi.');
                redirect($admin_url . '?page=petani');
            }
            $ins_k = $conn->prepare("INSERT INTO kelompok_tani (nama_kelompok, ketua_kelompok) VALUES (?, '')");
            $ins_k->bind_param("s", $nama_kel);
            $ins_k->execute();
            $id_kelompok = $conn->insert_id;
            $ins_k->close();
            $is_new_kelompok = true;
        } else {
            $id_kelompok = (int)$id_kelompok_input;
        }

        $stmt = $conn->prepare("INSERT INTO petani (nama_petani, nik, id_kelompok, luas_lahan, alamat, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssidss", $nama, $nik, $id_kelompok, $luas, $alamat, $status);

        if ($stmt->execute()) {
            $petani_id = $conn->insert_id;
            
            if ($is_new_kelompok) {
                // Set the new farmer as ketua of the new kelompok
                $conn->query("UPDATE kelompok_tani SET ketua_petani_id = $petani_id, ketua_kelompok = '$nama' WHERE id = $id_kelompok");
                
                setFlash('success', "Petani berhasil ditambahkan dan Kelompok Tani '$nama_kel' berhasil dibuat.");
            } else {
                setFlash('success', 'Data petani berhasil ditambahkan.');
            }
        } else {
            setFlash('danger', 'Gagal menambahkan data petani.');
        }
        $stmt->close();
        redirect($admin_url . '?page=petani');
    }

    if ($action === 'update' && $id > 0) {
        // Get old kelompok for count update
        $old = $conn->query("SELECT id_kelompok FROM petani WHERE id = $id")->fetch_assoc();
        $old_kelompok = $old['id_kelompok'];

        $nama = strtoupper(sanitize($_POST['nama_petani']));
        $nik = sanitize($_POST['nik']);
        $id_kelompok = (int)$_POST['id_kelompok'];
        $luas = (float)$_POST['luas_lahan'];
        $alamat = sanitize($_POST['alamat']);
        $status = sanitize($_POST['status']);

        // Validasi NIK: wajib, tepat 16 digit, angka saja
        if (empty($nik)) {
            setFlash('danger', 'NIK wajib diisi.');
            redirect($admin_url . '?page=petani&action=edit&id=' . $id);
        }
        if (!ctype_digit($nik) || strlen($nik) !== 16) {
            setFlash('danger', 'NIK harus tepat 16 digit angka. Anda memasukkan ' . strlen($nik) . ' karakter.');
            redirect($admin_url . '?page=petani&action=edit&id=' . $id);
        }
        // Validasi NIK unik (kecuali petani yang sedang diedit)
        $nik_check = $conn->query("SELECT id FROM petani WHERE nik = '$nik' AND id != $id LIMIT 1");
        if ($nik_check && $nik_check->num_rows > 0) {
            setFlash('danger', 'NIK ' . $nik . ' sudah terdaftar untuk petani lain.');
            redirect($admin_url . '?page=petani&action=edit&id=' . $id);
        }

        // Validasi luas lahan: harus > 0 dan max 10 Ha
        if ($luas <= 0 || $luas > 10) {
            setFlash('danger', 'Luas lahan harus antara 0,01 sampai 10 Ha (sesuai regulasi pupuk bersubsidi).');
            redirect($admin_url . '?page=petani&action=edit&id=' . $id);
        }

        $stmt = $conn->prepare("UPDATE petani SET nama_petani=?, nik=?, id_kelompok=?, luas_lahan=?, alamat=?, status=? WHERE id=?");
        $stmt->bind_param("ssidssi", $nama, $nik, $id_kelompok, $luas, $alamat, $status, $id);

        if ($stmt->execute()) {
            setFlash('success', 'Data petani berhasil diperbarui.');
        } else {
            setFlash('danger', 'Gagal memperbarui data petani.');
        }
        $stmt->close();
        redirect($admin_url . '?page=petani');
    }
}

// Handle DELETE
if ($action === 'delete' && $id > 0) {
    // Cascade delete: hapus data alokasi dan penyaluran yang terkait dengan petani ini
    $conn->query("DELETE FROM alokasi WHERE id_petani = $id");
    
    // Hapus foto bukti penyaluran dari server dan kembalikan stok gudang (rollback)
    $res_dist = $conn->query("SELECT id_pupuk, jumlah, bukti FROM penyaluran WHERE id_petani = $id");
    $upload_dir_bukti = dirname(__DIR__) . '/uploads/bukti';
    while ($row_dist = $res_dist->fetch_assoc()) {
        // Kembalikan stok pupuk
        $conn->query("UPDATE pupuk SET stok = stok + {$row_dist['jumlah']} WHERE id = {$row_dist['id_pupuk']}");
        
        // Hapus file bukti
        if (!empty($row_dist['bukti'])) {
            $bukti_file = $upload_dir_bukti . '/' . $row_dist['bukti'];
            if (file_exists($bukti_file)) {
                @unlink($bukti_file);
            }
        }
    }
    // Hapus data penyaluran
    $conn->query("DELETE FROM penyaluran WHERE id_petani = $id");

    // Jika petani ini adalah ketua kelompok, kosongkan ketua kelompoknya
    $conn->query("UPDATE kelompok_tani SET ketua_petani_id = NULL WHERE ketua_petani_id = $id");

    $conn->query("DELETE FROM petani WHERE id = $id");
    setFlash('success', 'Data petani berhasil dihapus.');
    redirect($admin_url . '?page=petani');
}

// Fetch data
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

$total_query = $conn->query("SELECT COUNT(*) as count FROM petani pt JOIN kelompok_tani kt ON pt.id_kelompok = kt.id $where");
$total_rows = $total_query->fetch_assoc()['count'];
$total_pages = ceil($total_rows / $limit);

$petani_list = $conn->query("
    SELECT pt.*, kt.nama_kelompok 
    FROM petani pt 
    JOIN kelompok_tani kt ON pt.id_kelompok = kt.id 
    $where
    ORDER BY pt.nama_petani ASC
    LIMIT $limit OFFSET $offset
");

$kelompok_list = $conn->query("SELECT * FROM kelompok_tani ORDER BY nama_kelompok ASC");

// For edit
$edit_data = null;
if ($action === 'edit' && $id > 0) {
    $edit_data = $conn->query("SELECT * FROM petani WHERE id = $id")->fetch_assoc();
}
