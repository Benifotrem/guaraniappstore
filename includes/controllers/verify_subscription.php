<?php
/**
 * Controlador: Verify Subscription - Verificar suscripción
 */

$db = Database::getInstance();
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $_SESSION['error'] = 'Token inválido';
    redirect(get_url());
}

$subscriber = $db->fetchOne("
    SELECT * FROM blog_subscribers
    WHERE verification_token = ? AND status = 'pending'
", [$token]);

if (!$subscriber) {
    $_SESSION['error'] = 'El link de verificación es inválido o ya fue utilizado';
    redirect(get_url());
}

// Activar suscripción
$db->update('blog_subscribers', [
    'status' => 'active',
    'verified_at' => date('Y-m-d H:i:s'),
    'verification_token' => null
], 'id = ?', [$subscriber['id']]);

// Enviar email de bienvenida
if (EMAIL_ENABLED && BREVO_API_KEY) {
    require_once INCLUDES_PATH . '/classes/BrevoMailer.php';

    $mailer = new BrevoMailer(BREVO_API_KEY, EMAIL_FROM_EMAIL, EMAIL_FROM_NAME);
    $mailer->sendWelcomeEmail($subscriber['email'], $subscriber['name']);
}

$_SESSION['success'] = '¡Gracias! Tu suscripción ha sido confirmada. Recibirás nuestros artículos por email.';
redirect(get_url('blog'));
