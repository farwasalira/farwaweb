<?php
/**
 * Pupuk Controller
 * CRUD operations for fertilizer data
 */

$upload_dir = dirname(__DIR__) . '/uploads/pupuk';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'store') {
        $nama = sanitize($_POST['nama_pupuk']);
        $berat_kemasan = (int)$_POST['berat_kemasan_kg'];
        $harga_sak = (float)$_POST['harga_per_sak'];
        $deskripsi = sanitize($_POST['deskripsi']);
        $foto = null;

        // Validasi nama pupuk unik
        $nama_check = $conn->query("SELECT id FROM pupuk WHERE nama_pupuk = '" . $conn->real_escape_string($nama) . "' LIMIT 1");
        if ($nama_check && $nama_check->num_rows > 0) {
            setFlash('danger', 'Jenis pupuk "' . htmlspecialchars($nama) . '" sudah ada. Nama pupuk harus unik.');
            redirect($admin_url . '?page=pupuk');
        }

        // Validasi berat kemasan > 0
        if ($berat_kemasan <= 0) {
            setFlash('danger', 'Berat kemasan harus lebih dari 0 kg.');
            redirect($admin_url . '?page=pupuk');
        }

        // Validasi harga > 0
        if ($harga_sak <= 0) {
            setFlash('danger', 'Harga per sak harus lebih dari 0.');
            redirect($admin_url . '?page=pupuk');
        }

        // Auto-hitung ukuran kemasan
        $ukuran = $berat_kemasan . ' kg';

        if (!empty($_FILES['foto']['name'])) {
            $result = uploadFile($_FILES['foto'], $upload_dir);
            if ($result['success']) $foto = $result['filename'];
        }

        $stmt = $conn->prepare("INSERT INTO pupuk (nama_pupuk, foto, ukuran_kemasan, berat_kemasan_kg, harga_per_sak, deskripsi) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssids", $nama, $foto, $ukuran, $berat_kemasan, $harga_sak, $deskripsi);

        if ($stmt->execute()) {
            setFlash('success', 'Data pupuk berhasil ditambahkan.');
        } else {
            setFlash('danger', 'Gagal menambahkan data pupuk.');
        }
        $stmt->close();
        redirect($admin_url . '?page=pupuk');
    }

    if ($action === 'update' && $id > 0) {
        $nama = sanitize($_POST['nama_pupuk']);
        $berat_kemasan = (int)$_POST['berat_kemasan_kg'];
        $harga_sak = (float)$_POST['harga_per_sak'];
        $deskripsi = sanitize($_POST['deskripsi']);

        // Validasi nama pupuk unik (kecuali dirinya sendiri)
        $nama_check = $conn->query("SELECT id FROM pupuk WHERE nama_pupuk = '" . $conn->real_escape_string($nama) . "' AND id != $id LIMIT 1");
        if ($nama_check && $nama_check->num_rows > 0) {
            setFlash('danger', 'Jenis pupuk "' . htmlspecialchars($nama) . '" sudah ada.');
            redirect($admin_url . '?page=pupuk&action=edit&id=' . $id);
        }

        // Validasi berat kemasan > 0
        if ($berat_kemasan <= 0) {
            setFlash('danger', 'Berat kemasan harus lebih dari 0 kg.');
            redirect($admin_url . '?page=pupuk&action=edit&id=' . $id);
        }

        // Validasi harga > 0
        if ($harga_sak <= 0) {
            setFlash('danger', 'Harga per sak harus lebih dari 0.');
            redirect($admin_url . '?page=pupuk&action=edit&id=' . $id);
        }

        // Auto-hitung ukuran kemasan
        $ukuran = $berat_kemasan . ' kg';

        if (!empty($_FILES['foto']['name'])) {
            $result = uploadFile($_FILES['foto'], $upload_dir);
            if ($result['success']) {
                // Delete old photo
                $old = $conn->query("SELECT foto FROM pupuk WHERE id = $id")->fetch_assoc();
                if ($old['foto'] && file_exists($upload_dir . '/' . $old['foto'])) {
                    unlink($upload_dir . '/' . $old['foto']);
                }
                $foto = $result['filename'];
                $stmt = $conn->prepare("UPDATE pupuk SET nama_pupuk=?, foto=?, ukuran_kemasan=?, berat_kemasan_kg=?, harga_per_sak=?, deskripsi=? WHERE id=?");
                $stmt->bind_param("sssidsi", $nama, $foto, $ukuran, $berat_kemasan, $harga_sak, $deskripsi, $id);
            }
        }

        if (!isset($stmt)) {
            $stmt = $conn->prepare("UPDATE pupuk SET nama_pupuk=?, ukuran_kemasan=?, berat_kemasan_kg=?, harga_per_sak=?, deskripsi=? WHERE id=?");
            $stmt->bind_param("ssidsi", $nama, $ukuran, $berat_kemasan, $harga_sak, $deskripsi, $id);
        }

        if ($stmt->execute()) {
            setFlash('success', 'Data pupuk berhasil diperbarui.');
        } else {
            setFlash('danger', 'Gagal memperbarui data pupuk.');
        }
        $stmt->close();
        redirect($admin_url . '?page=pupuk');
    }
}

if ($action === 'delete' && $id > 0) {
    // Proteksi: jangan hapus pupuk yang sudah ada di transaksi
    $has_stok = $conn->query("SELECT COUNT(*) as c FROM stok WHERE id_pupuk = $id")->fetch_assoc()['c'];
    $has_alokasi = $conn->query("SELECT COUNT(*) as c FROM alokasi WHERE id_pupuk = $id")->fetch_assoc()['c'];
    $has_penyaluran = $conn->query("SELECT COUNT(*) as c FROM penyaluran WHERE id_pupuk = $id")->fetch_assoc()['c'];

    $total_refs = $has_stok + $has_alokasi + $has_penyaluran;
    if ($total_refs > 0) {
        $detail = [];
        if ($has_stok > 0) $detail[] = "{$has_stok} catatan stok";
        if ($has_alokasi > 0) $detail[] = "{$has_alokasi} alokasi";
        if ($has_penyaluran > 0) $detail[] = "{$has_penyaluran} penyaluran";
        setFlash('danger', 'Pupuk tidak dapat dihapus karena sudah digunakan di: ' . implode(', ', $detail) . '. Data pupuk yang sudah memiliki transaksi tidak boleh dihapus demi integritas histori.');
        redirect($admin_url . '?page=pupuk');
    }

    // Aman dihapus
    $old = $conn->query("SELECT foto FROM pupuk WHERE id = $id")->fetch_assoc();
    if ($old && $old['foto'] && file_exists($upload_dir . '/' . $old['foto'])) {
        unlink($upload_dir . '/' . $old['foto']);
    }
    $conn->query("DELETE FROM pupuk WHERE id = $id");
    setFlash('success', 'Data pupuk berhasil dihapus.');
    redirect($admin_url . '?page=pupuk');
}

// Fetch data
$search = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$where = "";
if ($search !== '') {
    $where = " WHERE nama_pupuk LIKE '%" . $conn->real_escape_string($search) . "%' ";
}
$limit = 10;
$page_num = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($page_num < 1) $page_num = 1;
$offset = ($page_num - 1) * $limit;

$total_query = $conn->query("SELECT COUNT(*) as count FROM pupuk $where");
$total_rows = $total_query->fetch_assoc()['count'];
$total_pages = ceil($total_rows / $limit);

$pupuk_list = $conn->query("SELECT * FROM pupuk $where ORDER BY FIELD(nama_pupuk, 'UREA', 'NPK PHONSKA', 'NPK PELANGI', 'ORGANIK', 'ZA') ASC LIMIT $limit OFFSET $offset");

$edit_data = null;
if ($action === 'edit' && $id > 0) {
    $edit_data = $conn->query("SELECT * FROM pupuk WHERE id = $id")->fetch_assoc();
}
