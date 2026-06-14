<?php
/**
 * Informasi Controller
 * Manages information under 'Informasi' menu for the admin panel
 */

// Handle Information Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'store_informasi') {
        $judul = sanitize($_POST['judul']);
        $isi = sanitize($_POST['isi']);
        $tanggal = sanitize($_POST['tanggal']);
        $aktif = isset($_POST['aktif']) ? 1 : 0;

        $stmt = $conn->prepare("INSERT INTO informasi (judul, isi, tanggal, aktif) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $judul, $isi, $tanggal, $aktif);

        if ($stmt->execute()) {
            setFlash('success', 'Data informasi berhasil ditambahkan.');
        } else {
            setFlash('danger', 'Gagal menambahkan data informasi.');
        }
        $stmt->close();
        redirect($admin_url . '?page=informasi');
    }

    if ($action === 'update_informasi' && $id > 0) {
        $judul = sanitize($_POST['judul']);
        $isi = sanitize($_POST['isi']);
        $tanggal = sanitize($_POST['tanggal']);
        $aktif = isset($_POST['aktif']) ? 1 : 0;

        $stmt = $conn->prepare("UPDATE informasi SET judul=?, isi=?, tanggal=?, aktif=? WHERE id=?");
        $stmt->bind_param("sssii", $judul, $isi, $tanggal, $aktif, $id);

        if ($stmt->execute()) {
            setFlash('success', 'Data informasi berhasil diperbarui.');
        } else {
            setFlash('danger', 'Gagal memperbarui data informasi.');
        }
        $stmt->close();
        redirect($admin_url . '?page=informasi');
    }
}

if ($action === 'delete_informasi' && $id > 0) {
    $conn->query("DELETE FROM informasi WHERE id = $id");
    setFlash('success', 'Data informasi berhasil dihapus.');
    redirect($admin_url . '?page=informasi');
}

// Fetch information list
$informasi_list = $conn->query("SELECT * FROM informasi ORDER BY tanggal DESC, id DESC");

$edit_informasi = null;
if ($action === 'edit_informasi' && $id > 0) {
    $edit_informasi = $conn->query("SELECT * FROM informasi WHERE id = $id")->fetch_assoc();
}

