<?php
/**
 * Controller: Beta Login Process
 * Procesa el login con token de acceso
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('beta');
}

// Verificar CSRF
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['login_error'] = 'Token de seguridad inválido';
    redirect('beta');
}

$token = trim($_POST['token'] ?? '');

// Validar formato del token
if (empty($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    $_SESSION['login_error'] = 'Token inválido. Debe tener 64 caracteres hexadecimales.';
    redirect('beta');
}

// Buscar beta tester por token
$db = Database::getInstance();
$beta_user = $db->fetchOne("
    SELECT * FROM beta_testers 
    WHERE access_token = ?
", [$token]);

if (!$beta_user) {
    $_SESSION['login_error'] = 'Token no encontrado. Verifica que sea correcto.';
    redirect('beta');
}

// Verificar estado
if ($beta_user['status'] !== 'active') {
    $_SESSION['login_error'] = 'Tu cuenta aún no está activada. Espera la confirmación por email (24-48 horas).';
    redirect('beta');
}

// Login exitoso - crear sesión
$_SESSION['beta_user_id'] = $beta_user['id'];
$_SESSION['beta_user_name'] = $beta_user['name'];
$_SESSION['beta_user_email'] = $beta_user['email'];
$_SESSION['beta_token'] = $token;

// Log de login
log_error("Beta tester login exitoso: {$beta_user['name']} ({$beta_user['email']}) - ID: {$beta_user['id']}");

// Redirigir al dashboard
redirect('beta/dashboard');
