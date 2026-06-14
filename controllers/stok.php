<?php
/**
 * Stok Controller
 * Stok Masuk & Stok Keluar
 */

$tab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'masuk';

// Get fertilizer IDs dynamically
$pupuk_map = [];
$res = $conn->query("SELECT id, nama_pupuk, ukuran_kemasan FROM pupuk");
$pupuk_kemasan = [];
while ($row = $res->fetch_assoc()) {
    $name = strtoupper(trim($row['nama_pupuk']));
    $pupuk_kemasan[$row['id']] = (int) filter_var($row['ukuran_kemasan'], FILTER_SANITIZE_NUMBER_INT);
    if (strpos($name, 'UREA') !== false) {
        $pupuk_map['urea'] = $row['id'];
    } elseif (strpos($name, 'PHONSKA') !== false) {
        $pupuk_map['phonska'] = $row['id'];
    } elseif (strpos($name, 'PELANGI') !== false) {
        $pupuk_map['sp36'] = $row['id'];
    } elseif (strpos($name, 'ZA') !== false) {
        $pupuk_map['za'] = $row['id'];
    } elseif (strpos($name, 'ORGANIK') !== false) {
        $pupuk_map['organik'] = $row['id'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'store_masuk') {
        $tanggal = sanitize($_POST['tanggal']);
        $nama_sopir = sanitize($_POST['nama_sopir']);
        $bukti = null;

        // Validasi: tanggal tidak boleh masa depan
        if ($tanggal > date('Y-m-d')) {
            setFlash('danger', 'Tanggal stok masuk tidak boleh melebihi hari ini (' . date('d/m/Y') . ').');
            redirect($admin_url . '?page=stok&tab=masuk');
        }
        if (!empty($_FILES['bukti']['name'])) {
            $upload_dir_bukti = dirname(__DIR__) . '/uploads/bukti';
            $result = uploadFile($_FILES['bukti'], $upload_dir_bukti);
            if ($result['success']) {
                $bukti = $result['filename'];
            }
        }

        $fertilizers = ['urea', 'phonska', 'sp36', 'za', 'organik'];
        $has_saved = false;

        foreach ($fertilizers as $key) {
            if (isset($_POST[$key]) && (int)$_POST[$key] > 0) {
                $id_pupuk = $pupuk_map[$key] ?? null;
                $kemasan = $id_pupuk ? $pupuk_kemasan[$id_pupuk] : 50;
                $jumlah_kg = (int)$_POST[$key] * $kemasan;
                
                if ($id_pupuk) {
                    $stmt = $conn->prepare("INSERT INTO stok (tanggal, id_pupuk, jumlah, keterangan, bukti) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("siiss", $tanggal, $id_pupuk, $jumlah_kg, $nama_sopir, $bukti);
                    if ($stmt->execute()) {
                        $conn->query("UPDATE pupuk SET stok = stok + $jumlah_kg WHERE id = $id_pupuk");
                        $has_saved = true;
                    }
                    $stmt->close();
                }
            }
        }

        if ($has_saved) {
            setFlash('success', 'Catatan stok masuk berhasil disimpan.');
        } else {
            setFlash('danger', 'Gagal menyimpan catatan stok masuk (Isi setidaknya satu jenis pupuk).');
        }
        redirect($admin_url . '?page=stok&tab=masuk');
    }

    if ($action === 'update_masuk') {
        $tanggal_old = $_GET['tanggal_old'] ?? '';
        $sopir_old = $_GET['sopir_old'] ?? '';
        $bukti_old = $_GET['bukti_old'] ?? '';

        $tanggal_new = sanitize($_POST['tanggal']);
        $nama_sopir_new = sanitize($_POST['nama_sopir']);
        $bukti_new = $bukti_old;

        // Validasi: tanggal tidak boleh masa depan
        if ($tanggal_new > date('Y-m-d')) {
            setFlash('danger', 'Tanggal stok masuk tidak boleh melebihi hari ini (' . date('d/m/Y') . ').');
            redirect($admin_url . '?page=stok&tab=masuk');
        }

        if (!empty($_FILES['bukti']['name'])) {
            $upload_dir_bukti = dirname(__DIR__) . '/uploads/bukti';
            $result = uploadFile($_FILES['bukti'], $upload_dir_bukti);
            if ($result['success']) {
                $bukti_new = $result['filename'];
            }
        }

        // 1. Deduct old quantities from inventory
        if ($bukti_old === '') {
            $stmt_old = $conn->prepare("SELECT id_pupuk, jumlah FROM stok WHERE tanggal = ? AND keterangan = ? AND (bukti = ? OR bukti IS NULL)");
            $stmt_old->bind_param("sss", $tanggal_old, $sopir_old, $bukti_old);
        } else {
            $stmt_old = $conn->prepare("SELECT id_pupuk, jumlah FROM stok WHERE tanggal = ? AND keterangan = ? AND bukti = ?");
            $stmt_old->bind_param("sss", $tanggal_old, $sopir_old, $bukti_old);
        }
        $stmt_old->execute();
        $res_old = $stmt_old->get_result();
        while ($row = $res_old->fetch_assoc()) {
            $update_stmt = $conn->prepare("UPDATE pupuk SET stok = stok - ? WHERE id = ?");
            $update_stmt->bind_param("ii", $row['jumlah'], $row['id_pupuk']);
            $update_stmt->execute();
            $update_stmt->close();
        }
        $stmt_old->close();

        // 2. Delete old entries from stok
        if ($bukti_old === '') {
            $del_old = $conn->prepare("DELETE FROM stok WHERE tanggal = ? AND keterangan = ? AND (bukti = ? OR bukti IS NULL)");
            $del_old->bind_param("sss", $tanggal_old, $sopir_old, $bukti_old);
        } else {
            $del_old = $conn->prepare("DELETE FROM stok WHERE tanggal = ? AND keterangan = ? AND bukti = ?");
            $del_old->bind_param("sss", $tanggal_old, $sopir_old, $bukti_old);
        }
        $del_old->execute();
        $del_old->close();

        // 3. Insert new entries and add to inventory
        $fertilizers = ['urea', 'phonska', 'sp36', 'za', 'organik'];
        $has_saved = false;

        foreach ($fertilizers as $key) {
            if (isset($_POST[$key]) && (int)$_POST[$key] > 0) {
                $id_pupuk = $pupuk_map[$key] ?? null;
                $kemasan = $id_pupuk ? $pupuk_kemasan[$id_pupuk] : 50;
                $jumlah_kg = (int)$_POST[$key] * $kemasan;
                
                if ($id_pupuk) {
                    $stmt = $conn->prepare("INSERT INTO stok (tanggal, id_pupuk, jumlah, keterangan, bukti) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("siiss", $tanggal_new, $id_pupuk, $jumlah_kg, $nama_sopir_new, $bukti_new);
                    if ($stmt->execute()) {
                        $conn->query("UPDATE pupuk SET stok = stok + $jumlah_kg WHERE id = $id_pupuk");
                        $has_saved = true;
                    }
                    $stmt->close();
                }
            }
        }

        if ($has_saved) {
            setFlash('success', 'Catatan stok masuk berhasil diperbarui.');
        } else {
            setFlash('danger', 'Gagal memperbarui catatan stok masuk.');
        }
        redirect($admin_url . '?page=stok&tab=masuk');
    }


}

if ($action === 'delete_masuk' && isset($_GET['tanggal'])) {
    $tanggal = $_GET['tanggal'] ?? '';
    $sopir = $_GET['sopir'] ?? '';
    $bukti = $_GET['bukti'] ?? '';

    // Find matching records and deduct from stock
    if ($bukti === '') {
        $stmt = $conn->prepare("SELECT id_pupuk, jumlah FROM stok WHERE tanggal = ? AND keterangan = ? AND (bukti = ? OR bukti IS NULL)");
        $stmt->bind_param("sss", $tanggal, $sopir, $bukti);
    } else {
        $stmt = $conn->prepare("SELECT id_pupuk, jumlah FROM stok WHERE tanggal = ? AND keterangan = ? AND bukti = ?");
        $stmt->bind_param("sss", $tanggal, $sopir, $bukti);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $update_stmt = $conn->prepare("UPDATE pupuk SET stok = stok - ? WHERE id = ?");
        $update_stmt->bind_param("ii", $row['jumlah'], $row['id_pupuk']);
        $update_stmt->execute();
        $update_stmt->close();
    }
    $stmt->close();

    // Delete matching records
    if ($bukti === '') {
        $del_stmt = $conn->prepare("DELETE FROM stok WHERE tanggal = ? AND keterangan = ? AND (bukti = ? OR bukti IS NULL)");
        $del_stmt->bind_param("sss", $tanggal, $sopir, $bukti);
    } else {
        $del_stmt = $conn->prepare("DELETE FROM stok WHERE tanggal = ? AND keterangan = ? AND bukti = ?");
        $del_stmt->bind_param("sss", $tanggal, $sopir, $bukti);
    }
    $del_stmt->execute();
    $del_stmt->close();
    
    setFlash('success', 'Data stok masuk berhasil dihapus.');
    redirect($admin_url . '?page=stok&tab=masuk');
}



// Fetch data
$pupuk_options = $conn->query("SELECT id, nama_pupuk, stok FROM pupuk ORDER BY FIELD(nama_pupuk, 'UREA', 'NPK PHONSKA', 'NPK PELANGI', 'ORGANIK', 'ZA') ASC");

$search_date = isset($_GET['d']) ? sanitize($_GET['d']) : '';
$where = "";
if ($search_date !== '') {
    // Validasi format tanggal YYYY-MM-DD
    $d_obj = DateTime::createFromFormat('Y-m-d', $search_date);
    if ($d_obj && $d_obj->format('Y-m-d') === $search_date) {
        if ($search_date > date('Y-m-d')) {
            setFlash('warning', 'Pencarian gagal: Anda tidak dapat mencari data tanggal di masa depan.');
            $search_date = '';
        } else {
            $d = $conn->real_escape_string($search_date);
            $where = " WHERE sm.tanggal = '$d' ";
        }
    } else {
        $search_date = ''; // Reset jika format tidak valid
    }
}

$limit = 10;
$page_num = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($page_num < 1) $page_num = 1;
$offset = ($page_num - 1) * $limit;

$total_query = $conn->query("SELECT COUNT(DISTINCT CONCAT(tanggal, keterangan, IFNULL(bukti,''))) as count FROM stok sm $where");
$total_rows = $total_query->fetch_assoc()['count'];
$total_pages = ceil($total_rows / $limit);

$stok_list = $conn->query("
    SELECT 
        sm.tanggal,
        sm.keterangan AS nama_sopir,
        sm.bukti AS bukti_foto,
        COALESCE(SUM(CASE WHEN p.nama_pupuk = 'UREA' THEN sm.jumlah / CAST(REPLACE(p.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) AS urea,
        COALESCE(SUM(CASE WHEN p.nama_pupuk = 'NPK PHONSKA' THEN sm.jumlah / CAST(REPLACE(p.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) AS phonska,
        COALESCE(SUM(CASE WHEN p.nama_pupuk = 'NPK PELANGI' THEN sm.jumlah / CAST(REPLACE(p.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) AS sp36,
        COALESCE(SUM(CASE WHEN p.nama_pupuk = 'ZA' THEN sm.jumlah / CAST(REPLACE(p.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) AS za,
        COALESCE(SUM(CASE WHEN p.nama_pupuk = 'ORGANIK' THEN sm.jumlah / CAST(REPLACE(p.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) AS organik,
        COALESCE(SUM(sm.jumlah / CAST(REPLACE(p.ukuran_kemasan, ' kg', '') AS UNSIGNED)), 0) AS total
    FROM stok sm
    JOIN pupuk p ON sm.id_pupuk = p.id
    $where
    GROUP BY sm.tanggal, sm.keterangan, sm.bukti
    ORDER BY sm.tanggal DESC
    LIMIT $limit OFFSET $offset
");

$edit_data = null;
if ($action === 'edit_masuk' && isset($_GET['tanggal'])) {
    $tanggal = sanitize($_GET['tanggal']);
    $sopir = sanitize($_GET['sopir']);
    $bukti = sanitize($_GET['bukti']);

    $res = $conn->query("
        SELECT 
            sm.tanggal,
            sm.keterangan AS nama_sopir,
            sm.bukti AS bukti_foto,
            COALESCE(SUM(CASE WHEN p.nama_pupuk = 'UREA' THEN sm.jumlah / CAST(REPLACE(p.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) AS urea,
            COALESCE(SUM(CASE WHEN p.nama_pupuk = 'NPK PHONSKA' THEN sm.jumlah / CAST(REPLACE(p.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) AS phonska,
            COALESCE(SUM(CASE WHEN p.nama_pupuk = 'NPK PELANGI' THEN sm.jumlah / CAST(REPLACE(p.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) AS sp36,
            COALESCE(SUM(CASE WHEN p.nama_pupuk = 'ZA' THEN sm.jumlah / CAST(REPLACE(p.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) AS za,
            COALESCE(SUM(CASE WHEN p.nama_pupuk = 'ORGANIK' THEN sm.jumlah / CAST(REPLACE(p.ukuran_kemasan, ' kg', '') AS UNSIGNED) END), 0) AS organik
        FROM stok sm
        JOIN pupuk p ON sm.id_pupuk = p.id
        WHERE sm.tanggal = '$tanggal' AND sm.keterangan = '$sopir' AND (sm.bukti = '$bukti' OR (sm.bukti IS NULL AND '$bukti' = ''))
        GROUP BY sm.tanggal, sm.keterangan, sm.bukti
    ");
    if ($res->num_rows > 0) {
        $edit_data = $res->fetch_assoc();
    }
}
