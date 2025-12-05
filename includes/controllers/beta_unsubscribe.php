<?php
/**
 * Controller: Beta Unsubscribe
 * Permite al usuario darse de baja del programa beta
 */

// Verificar autenticación
if (!isset($_SESSION['beta_user_id'])) {
    redirect('beta');
}

$db = Database::getInstance();
$tester = $db->fetchOne("SELECT * FROM beta_testers WHERE id = ?", [$_SESSION['beta_user_id']]);

if (!$tester) {
    redirect('beta');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_unsubscribe'])) {
    // Cambiar estado a inactive
    $db->query("UPDATE beta_testers SET status = 'inactive' WHERE id = ?", [$_SESSION['beta_user_id']]);
    
    // Enviar email de confirmación
    $subject = 'Has cancelado tu cuenta Beta Tester - Guarani App Store';
    $message = '
    <html>
    <body style="font-family: Arial, sans-serif;">
        <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
            <h2>👋 Cuenta Cancelada</h2>
            <p>Hola <strong>' . htmlspecialchars($tester['name']) . '</strong>,</p>
            <p>Tu cuenta de Beta Tester ha sido desactivada exitosamente.</p>
            <p>Si en algún momento deseas volver, puedes registrarte nuevamente en:</p>
            <p><a href="' . SITE_URL . '/beta/join">Unirme al Programa Beta</a></p>
            <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">
            <p style="font-size: 0.85em; color: #666;">Gracias por haber sido parte de nuestra comunidad.</p>
        </div>
    </body>
    </html>
    ';
    
    send_email($tester['email'], $subject, $message);
    
    log_error("Beta tester se dio de baja: {$tester['name']} (ID: {$_SESSION['beta_user_id']})");
    
    // Cerrar sesión
    unset($_SESSION['beta_user_id']);
    unset($_SESSION['beta_user_name']);
    unset($_SESSION['beta_user_email']);
    unset($_SESSION['beta_token']);
    
    // Borrar cookie si existe
    if (isset($_COOKIE['beta_remember_token'])) {
        setcookie('beta_remember_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    
    $_SESSION['unsubscribe_success'] = 'Tu cuenta ha sido desactivada correctamente. ¡Gracias por participar!';
    redirect('beta');
}

// Renderizar vista
require_once INCLUDES_PATH . '/views/beta/unsubscribe.php';
