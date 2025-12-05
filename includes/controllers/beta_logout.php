<?php
/**
 * Controller: Beta Logout
 * Cierra la sesión del beta tester
 */

// Limpiar variables de sesión de beta tester
unset($_SESSION['beta_user_id']);
unset($_SESSION['beta_user_name']);
unset($_SESSION['beta_user_email']);
unset($_SESSION['beta_token']);

// Borrar cookie "recordar sesión" si existe
if (isset($_COOKIE['beta_remember_token'])) {
    setcookie('beta_remember_token', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

// Regenerar ID de sesión para prevenir session fixation
session_regenerate_id(true);

// Mensaje de éxito
$_SESSION['logout_success'] = 'Has cerrado sesión correctamente.';

// Redirigir al login
redirect('beta');