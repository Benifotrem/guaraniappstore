<?php
/**
 * Controller: Beta Program Entry Point
 * Decide si mostrar login o redirigir al dashboard
 */

$db = Database::getInstance();

// Verificar cookie "recordar sesión" si no hay sesión activa
if (!isset($_SESSION['beta_user_id']) && isset($_COOKIE['beta_remember_token'])) {
    $token = $_COOKIE['beta_remember_token'];
    $beta_user = $db->fetchOne("
        SELECT * FROM beta_testers 
        WHERE access_token = ? AND status = 'active'
    ", [$token]);
    
    if ($beta_user) {
        // Recrear sesión desde cookie
        $_SESSION['beta_user_id'] = $beta_user['id'];
        $_SESSION['beta_user_name'] = $beta_user['name'];
        $_SESSION['beta_user_email'] = $beta_user['email'];
        $_SESSION['beta_token'] = $token;
        redirect('beta/dashboard');
    }
}

// Verificar si ya tiene sesión activa
if (isset($_SESSION['beta_user_id'])) {
    $beta_user = $db->fetchOne("
        SELECT * FROM beta_testers 
        WHERE id = ? AND status = 'active'
    ", [$_SESSION['beta_user_id']]);
    
    if ($beta_user) {
        // Ya está logueado, redirigir al dashboard
        redirect('beta/dashboard');
    }
}

// No está logueado, mostrar página de login
$page_title = 'Acceso Beta Tester | Guarani App Store';
include INCLUDES_PATH . '/views/beta/login.php';
