<?php
/**
 * Ubah Password Controller
 * SIPUPUK - Sistem Informasi Stok dan Distribusi Pupuk
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_id = $_SESSION['user_id'] ?? 0;

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        setFlash('danger', 'Semua field password harus diisi.');
        redirect($admin_url . '?page=ubah_password');
    }

    if ($new_password !== $confirm_password) {
        setFlash('danger', 'Password baru dan konfirmasi password tidak cocok.');
        redirect($admin_url . '?page=ubah_password');
    }

    if (strlen($new_password) < 6) {
        setFlash('danger', 'Password baru minimal harus 6 karakter.');
        redirect($admin_url . '?page=ubah_password');
    }

    // Verify current password
    $stmt = $conn->prepare("SELECT password FROM admin WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($current_password, $user['password'])) {
        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $update_stmt = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
        $update_stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($update_stmt->execute()) {
            setFlash('success', 'Password Anda berhasil diperbarui.');
        } else {
            setFlash('danger', 'Gagal memperbarui password di database.');
        }
        $update_stmt->close();
    } else {
        setFlash('danger', 'Password lama yang Anda masukkan salah.');
    }
    
    redirect($admin_url . '?page=ubah_password');
}
