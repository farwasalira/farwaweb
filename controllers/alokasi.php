<?php
/**
 * Alokasi Controller
 */

// Get fertilizer IDs by name dynamically
$pupuk_map = [];
$res = $conn->query("SELECT id, nama_pupuk FROM pupuk");
while ($row = $res->fetch_assoc()) {
    $name = strtoupper(trim($row['nama_pupuk']));
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
    if ($action === 'store') {
        $id_petani = (int)$_POST['id_petani'];
        $periode = '2026';

        // Delete existing allocations for this farmer and period first
        $conn->query("DELETE FROM alokasi WHERE id_petani = $id_petani AND periode = '$periode'");

        $fertilizers = ['urea', 'phonska', 'sp36', 'za', 'organik'];
        $has_saved = false;

        foreach ($fertilizers as $key) {
            if (isset($_POST[$key]) && (int)$_POST[$key] > 0) {
                $jumlah = (int)$_POST[$key];
                $id_pupuk = $pupuk_map[$key] ?? null;
                if ($id_pupuk) {
                    $stmt = $conn->prepare("INSERT INTO alokasi (id_petani, id_pupuk, jumlah, periode) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("iiis", $id_petani, $id_pupuk, $jumlah, $periode);
                    if ($stmt->execute()) {
                        $has_saved = true;
                    }
                    $stmt->close();
                }
            }
        }

        if ($has_saved) {
            setFlash('success', 'Alokasi berhasil ditambahkan.');
        } else {
            setFlash('danger', 'Gagal menambahkan alokasi (Jumlah pupuk harus lebih dari 0).');
        }
        redirect($admin_url . '?page=alokasi');
    }

    if ($action === 'update') {
        $id_petani_old = (int)$_GET['id_petani'];
        $periode_old = '2026';
        
        $id_petani_new = (int)$_POST['id_petani'];
        $periode_new = '2026';

        // Delete old allocations for this farmer and period first
        $conn->query("DELETE FROM alokasi WHERE id_petani = $id_petani_old AND periode = '$periode_old'");

        $fertilizers = ['urea', 'phonska', 'sp36', 'za', 'organik'];
        $has_saved = false;

        foreach ($fertilizers as $key) {
            if (isset($_POST[$key]) && (int)$_POST[$key] > 0) {
                $jumlah = (int)$_POST[$key];
                $id_pupuk = $pupuk_map[$key] ?? null;
                if ($id_pupuk) {
                    $stmt = $conn->prepare("INSERT INTO alokasi (id_petani, id_pupuk, jumlah, periode) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("iiis", $id_petani_new, $id_pupuk, $jumlah, $periode_new);
                    if ($stmt->execute()) {
                        $has_saved = true;
                    }
                    $stmt->close();
                }
            }
        }

        if ($has_saved) {
            setFlash('success', 'Alokasi berhasil diperbarui.');
        } else {
            setFlash('danger', 'Gagal memperbarui alokasi.');
        }
        redirect($admin_url . '?page=alokasi');
    }
}

if ($action === 'delete' && isset($_GET['id_petani'])) {
    $id_petani = (int)$_GET['id_petani'];
    $periode = '2026';
    $conn->query("DELETE FROM alokasi WHERE id_petani = $id_petani AND periode = '$periode'");
    setFlash('success', 'Alokasi berhasil dihapus.');
    redirect($admin_url . '?page=alokasi');
}

// Fetch data - grouped by petani with pivoted pupuk columns
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

$total_query = $conn->query("SELECT COUNT(DISTINCT CONCAT(pt.id, a.periode)) as count FROM alokasi a JOIN petani pt ON a.id_petani = pt.id $where");
$total_rows = $total_query->fetch_assoc()['count'];
$total_pages = ceil($total_rows / $limit);

$alokasi_list = $conn->query("
    SELECT 
        pt.id AS id_petani,
        pt.nama_petani,
        pt.nik,
        kt.nama_kelompok,
        COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'UREA' THEN a.jumlah END), 0) as urea,
        COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'NPK PHONSKA' THEN a.jumlah END), 0) as phonska,
        COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'NPK PELANGI' THEN a.jumlah END), 0) as sp36,
        COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'ZA' THEN a.jumlah END), 0) as za,
        COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'ORGANIK' THEN a.jumlah END), 0) as organik,
        COALESCE(SUM(a.jumlah), 0) as total,
        a.periode
    FROM alokasi a
    JOIN petani pt ON a.id_petani = pt.id
    JOIN kelompok_tani kt ON pt.id_kelompok = kt.id
    JOIN pupuk pu ON a.id_pupuk = pu.id
    $where
    GROUP BY pt.id, a.periode
    ORDER BY pt.nama_petani ASC
    LIMIT $limit OFFSET $offset
");

$petani_options = $conn->query("SELECT pt.id, pt.nama_petani, pt.nik, kt.nama_kelompok FROM petani pt JOIN kelompok_tani kt ON pt.id_kelompok = kt.id ORDER BY pt.nama_petani ASC");

$edit_data = null;
if ($action === 'edit' && isset($_GET['id_petani'])) {
    $id_petani = (int)$_GET['id_petani'];
    $periode = '2026';

    $allocs = $conn->query("
        SELECT 
            pt.id AS id_petani, pt.nama_petani, kt.nama_kelompok,
            COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'UREA' THEN a.jumlah END), 0) as urea,
            COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'NPK PHONSKA' THEN a.jumlah END), 0) as phonska,
            COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'NPK PELANGI' THEN a.jumlah END), 0) as sp36,
            COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'ZA' THEN a.jumlah END), 0) as za,
            COALESCE(SUM(CASE WHEN pu.nama_pupuk = 'ORGANIK' THEN a.jumlah END), 0) as organik
        FROM petani pt
        LEFT JOIN kelompok_tani kt ON pt.id_kelompok = kt.id
        LEFT JOIN alokasi a ON pt.id = a.id_petani AND a.periode = '$periode'
        LEFT JOIN pupuk pu ON a.id_pupuk = pu.id
        WHERE pt.id = $id_petani
        GROUP BY pt.id
    ");
    if ($allocs->num_rows > 0) {
        $edit_data = $allocs->fetch_assoc();
        $edit_data['periode'] = $periode;
    }
}
