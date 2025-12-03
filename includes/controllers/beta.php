<?php
/**
 * Controller: Beta Program Entry Point
 * Decide si mostrar login o redirigir al dashboard
 */

$db = Database::getInstance();

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
