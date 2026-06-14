<?php
/**
 * Auth Controller
 * Login and Logout for Admin
 */

$auth_action = isset($_GET['action']) ? sanitize($_GET['action']) : 'login';

// Handle login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $auth_action === 'login') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $redirect_url = isset($_POST['redirect']) ? $_POST['redirect'] : '';

    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = 'Username dan password harus diisi.';
        $back_url = $admin_url . '?page=login';
        if (!empty($redirect_url)) {
            $back_url .= '&redirect=' . urlencode($redirect_url);
        }
        redirect($back_url);
    }

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nama'] = $user['nama_lengkap'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['user_role'] = 'admin';

            // Backwards compatibility for Admin
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_nama'] = $user['nama_lengkap'];
            $_SESSION['admin_username'] = $user['username'];

            // Redirect handler
            if (!empty($redirect_url)) {
                redirect($redirect_url);
            } else {
                redirect($admin_url . '?page=dashboard');
            }
        } else {
            $_SESSION['login_error'] = 'Password yang Anda masukkan salah.';
            $back_url = $admin_url . '?page=login';
            if (!empty($redirect_url)) {
                $back_url .= '&redirect=' . urlencode($redirect_url);
            }
            redirect($back_url);
        }
    } else {
        $_SESSION['login_error'] = 'Username tidak ditemukan.';
        $back_url = $admin_url . '?page=login';
        if (!empty($redirect_url)) {
            $back_url .= '&redirect=' . urlencode($redirect_url);
        }
        redirect($back_url);
    }
}

// Handle logout
if ($auth_action === 'logout') {
    session_destroy();
    redirect($public_url);
}
