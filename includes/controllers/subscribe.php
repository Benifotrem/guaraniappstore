<?php
/**
 * Controlador: Subscribe - Suscripción al blog
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(get_url());
}

// Verificar CSRF
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['error'] = 'Token de seguridad inválido';
    redirect(get_url());
}

$email = sanitize_input($_POST['email'] ?? '');

if (!is_valid_email($email)) {
    $_SESSION['error'] = 'Por favor ingresa un email válido';
    redirect(get_url());
}

$db = Database::getInstance();

// Verificar si ya está suscrito
$existing = $db->fetchOne(
    "SELECT * FROM blog_subscribers WHERE email = ?",
    [$email]
);

if ($existing) {
    if ($existing['status'] === 'active') {
        $_SESSION['info'] = 'Este email ya está suscrito';
    } else {
        $_SESSION['info'] = 'Este email ya existe. Por favor verifica tu correo para activar la suscripción';
    }
    redirect(get_url());
}

// Crear nueva suscripción
$verification_token = generate_token(32);

$db->insert('blog_subscribers', [
    'email' => $email,
    'status' => 'pending',
    'verification_token' => $verification_token,
    'subscription_source' => 'landing_page'
]);

// TODO: Enviar email de confirmación
// send_email($email, 'Confirma tu suscripción', $verification_link);

$_SESSION['success'] = '¡Gracias por suscribirte! Te hemos enviado un email de confirmación.';
redirect(get_url());
