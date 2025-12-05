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

// Enviar email de confirmación con Brevo
if (EMAIL_ENABLED && BREVO_API_KEY) {
    require_once INCLUDES_PATH . '/classes/BrevoMailer.php';

    $mailer = new BrevoMailer(BREVO_API_KEY, EMAIL_FROM_EMAIL, EMAIL_FROM_NAME);
    $result = $mailer->sendVerificationEmail($email, '', $verification_token);

    if ($result['success']) {
        $_SESSION['success'] = '¡Gracias por suscribirte! Te hemos enviado un email de confirmación.';
    } else {
        $_SESSION['warning'] = '¡Gracias por suscribirte! Sin embargo, hubo un problema al enviar el email de confirmación. Contacta al administrador.';
        error_log('Failed to send verification email: ' . $result['message']);
    }
} else {
    $_SESSION['success'] = '¡Gracias por suscribirte! Tu suscripción está pendiente de activación.';
}

redirect(get_url());
