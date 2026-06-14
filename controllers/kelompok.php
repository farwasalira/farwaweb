<?php
/**
 * Kelompok Tani Controller
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'store') {
        $kode = strtoupper(sanitize($_POST['kode_kelompok']));
        $nama = strtoupper(sanitize($_POST['nama_kelompok']));
        $ketua_petani_id = isset($_POST['ketua_petani_id']) && $_POST['ketua_petani_id'] !== '' ? (int)$_POST['ketua_petani_id'] : null;

        // Validasi kode kelompok unik
        $kode_check = $conn->query("SELECT id FROM kelompok_tani WHERE kode_kelompok = '$kode' LIMIT 1");
        if ($kode_check && $kode_check->num_rows > 0) {
            setFlash('danger', 'ID Poktan "' . htmlspecialchars($kode) . '" sudah ada. Gunakan ID Poktan yang berbeda.');
            redirect($admin_url . '?page=kelompok');
        }

        // Validasi nama kelompok unik
        $nama_check = $conn->query("SELECT id FROM kelompok_tani WHERE nama_kelompok = '$nama' LIMIT 1");
        if ($nama_check && $nama_check->num_rows > 0) {
            setFlash('danger', 'Nama kelompok tani "' . htmlspecialchars($nama) . '" sudah ada. Gunakan nama yang berbeda.');
            redirect($admin_url . '?page=kelompok');
        }

        // Validasi: satu petani tidak boleh menjadi ketua 2 kelompok
        if ($ketua_petani_id) {
            $ketua_check = $conn->query("SELECT id FROM kelompok_tani WHERE ketua_petani_id = $ketua_petani_id LIMIT 1");
            if ($ketua_check && $ketua_check->num_rows > 0) {
                setFlash('danger', 'Petani yang dipilih sudah menjadi ketua kelompok lain. Satu orang hanya boleh menjadi ketua satu kelompok.');
                redirect($admin_url . '?page=kelompok');
            }
        }

        $ketua_nama = '';
        $ketua_nik = '';
        if ($ketua_petani_id) {
            $p_stmt = $conn->prepare("SELECT nama_petani, nik FROM petani WHERE id = ?");
            $p_stmt->bind_param("i", $ketua_petani_id);
            $p_stmt->execute();
            $p_res = $p_stmt->get_result()->fetch_assoc();
            $p_stmt->close();
            if ($p_res) {
                $ketua_nama = $p_res['nama_petani'];
                $ketua_nik = $p_res['nik'];
            }
        }

        $jumlah_anggota = isset($_POST['jumlah_anggota']) ? (int)$_POST['jumlah_anggota'] : 0;
        $stmt = $conn->prepare("INSERT INTO kelompok_tani (kode_kelompok, nama_kelompok, ketua_petani_id, ketua_kelompok, jumlah_anggota) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisi", $kode, $nama, $ketua_petani_id, $ketua_nama, $jumlah_anggota);

        if ($stmt->execute()) {
            $kelompok_id = $conn->insert_id;
            
            // Sync farmer's id_kelompok
            if ($ketua_petani_id) {
                $conn->query("UPDATE petani SET id_kelompok = $kelompok_id WHERE id = $ketua_petani_id");
            }

            setFlash('success', 'Kelompok tani berhasil ditambahkan.');
        } else {
            setFlash('danger', 'Gagal menambahkan kelompok tani.');
        }
        redirect($admin_url . '?page=kelompok');
    }

    if ($action === 'update' && $id > 0) {
        $kode = strtoupper(sanitize($_POST['kode_kelompok']));
        $nama = strtoupper(sanitize($_POST['nama_kelompok']));
        $ketua_petani_id = isset($_POST['ketua_petani_id']) && $_POST['ketua_petani_id'] !== '' ? (int)$_POST['ketua_petani_id'] : null;

        // Validasi kode kelompok unik (kecuali kelompok yg sedang diedit)
        $kode_check = $conn->query("SELECT id FROM kelompok_tani WHERE kode_kelompok = '$kode' AND id != $id LIMIT 1");
        if ($kode_check && $kode_check->num_rows > 0) {
            setFlash('danger', 'ID Poktan "' . htmlspecialchars($kode) . '" sudah digunakan. Gunakan ID Poktan yang berbeda.');
            redirect($admin_url . '?page=kelompok&action=edit&id=' . $id);
        }

        // Validasi nama kelompok unik (kecuali kelompok yg sedang diedit)
        $nama_check = $conn->query("SELECT id FROM kelompok_tani WHERE nama_kelompok = '$nama' AND id != $id LIMIT 1");
        if ($nama_check && $nama_check->num_rows > 0) {
            setFlash('danger', 'Nama kelompok tani "' . htmlspecialchars($nama) . '" sudah digunakan. Gunakan nama yang berbeda.');
            redirect($admin_url . '?page=kelompok&action=edit&id=' . $id);
        }

        // Validasi: satu petani tidak boleh menjadi ketua 2 kelompok (kecuali kelompok ini sendiri)
        if ($ketua_petani_id) {
            $ketua_check = $conn->query("SELECT id FROM kelompok_tani WHERE ketua_petani_id = $ketua_petani_id AND id != $id LIMIT 1");
            if ($ketua_check && $ketua_check->num_rows > 0) {
                setFlash('danger', 'Petani yang dipilih sudah menjadi ketua kelompok lain. Satu orang hanya boleh menjadi ketua satu kelompok.');
                redirect($admin_url . '?page=kelompok&action=edit&id=' . $id);
            }
        }

        // Get farmer details if selected
        $ketua_nama = '';
        $ketua_nik = '';
        if ($ketua_petani_id) {
            $p_stmt = $conn->prepare("SELECT nama_petani, nik FROM petani WHERE id = ?");
            $p_stmt->bind_param("i", $ketua_petani_id);
            $p_stmt->execute();
            $p_res = $p_stmt->get_result()->fetch_assoc();
            $p_stmt->close();
            if ($p_res) {
                $ketua_nama = $p_res['nama_petani'];
                $ketua_nik = $p_res['nik'];
            }
        }

        $jumlah_anggota = isset($_POST['jumlah_anggota']) ? (int)$_POST['jumlah_anggota'] : 0;
        $stmt = $conn->prepare("UPDATE kelompok_tani SET kode_kelompok=?, nama_kelompok=?, ketua_petani_id=?, ketua_kelompok=?, jumlah_anggota=? WHERE id=?");
        $stmt->bind_param("ssisii", $kode, $nama, $ketua_petani_id, $ketua_nama, $jumlah_anggota, $id);

        if ($stmt->execute()) {
            // Sync farmer's id_kelompok
            if ($ketua_petani_id) {
                $conn->query("UPDATE petani SET id_kelompok = $id WHERE id = $ketua_petani_id");
            }

            setFlash('success', 'Kelompok tani berhasil diperbarui.');
        } else {
            setFlash('danger', 'Gagal memperbarui kelompok tani.');
        }
        redirect($admin_url . '?page=kelompok');
    }
}

if ($action === 'delete' && $id > 0) {
    // Proteksi: jangan hapus kelompok yang masih punya anggota
    $member_count = $conn->query("SELECT COUNT(*) as c FROM petani WHERE id_kelompok = $id")->fetch_assoc()['c'];
    if ($member_count > 0) {
        setFlash('danger', 'Kelompok tani tidak dapat dihapus karena masih memiliki ' . $member_count . ' anggota. Pindahkan atau hapus semua anggota terlebih dahulu.');
        redirect($admin_url . '?page=kelompok');
    }

    $conn->query("DELETE FROM kelompok_tani WHERE id = $id");
    setFlash('success', 'Kelompok tani berhasil dihapus.');
    redirect($admin_url . '?page=kelompok');
}

$search = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$where = "";
if ($search !== '') {
    $q = $conn->real_escape_string($search);
    $where = " WHERE k.nama_kelompok LIKE '%$q%' ";
}

$kelompok_list = $conn->query("
    SELECT k.* 
    FROM kelompok_tani k 
    $where
    ORDER BY k.nama_kelompok ASC
");


$edit_data = null;
if ($action === 'edit' && $id > 0) {
    $edit_data = $conn->query("SELECT * FROM kelompok_tani WHERE id = $id")->fetch_assoc();
}
