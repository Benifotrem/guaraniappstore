<?php
/**
 * Controlador: Admin Login
 */

// Si ya está logueado, redirigir al dashboard
if (is_admin_logged_in()) {
    redirect(get_url('admin/dashboard'));
}

$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token de seguridad inválido';
    } else {
        $username = sanitize_input($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Por favor completa todos los campos';
        } else {
            $auth = new Auth();
            $result = $auth->login($username, $password);

            if ($result['success']) {
                redirect(get_url('admin/dashboard'));
            } elseif ($result['requires_2fa'] ?? false) {
                // Mostrar formulario 2FA
                include INCLUDES_PATH . '/views/admin/2fa_verify.php';
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

// Mostrar formulario de login
include INCLUDES_PATH . '/views/admin/login.php';
