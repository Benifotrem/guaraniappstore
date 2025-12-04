<?php
/**
 * Controller: Aprobar Beta Tester
 * Cambia estado de pending a active y envía email
 */

header('Content-Type: application/json');

// Autenticación
require_admin_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

$db = Database::getInstance();

// Obtener beta tester
$tester = $db->fetchOne("SELECT * FROM beta_testers WHERE id = ?", [$id]);

if (!$tester) {
    echo json_encode(['success' => false, 'message' => 'Beta tester no encontrado']);
    exit;
}

if ($tester['status'] !== 'pending') {
    echo json_encode(['success' => false, 'message' => 'Este beta tester ya no está pendiente']);
    exit;
}

// Actualizar a activo
$db->query("UPDATE beta_testers SET status = 'active' WHERE id = ?", [$id]);

// Enviar email de activación
$subject = '¡Tu cuenta de Beta Tester ha sido activada! 🎉';
$dashboard_url = SITE_URL . '/beta/dashboard?token=' . $tester['access_token'];

$message = "
<html>
<body style='font-family: Arial, sans-serif; line-height: 1.6;'>
    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
        <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;'>
            <h1>🎉 ¡Cuenta Activada!</h1>
        </div>
        <div style='background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px;'>
            <p>Hola <strong>{$tester['name']}</strong>,</p>
            <p>¡Excelentes noticias! Tu cuenta de Beta Tester ha sido <strong>activada</strong>.</p>
            <p>Ya puedes acceder a tu dashboard y comenzar a probar nuestras apps:</p>
            <div style='text-align: center; margin: 30px 0;'>
                <a href='$dashboard_url' style='display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;'>
                    Acceder a Mi Dashboard
                </a>
            </div>
            <p><strong>También puedes usar nuestro bot de Telegram:</strong></p>
            <p>👉 <a href='https://t.me/guaraniappstore_bot'>@guaraniappstore_bot</a></p>
            <p>¡Gracias por ser parte de nuestra comunidad!</p>
        </div>
    </div>
</body>
</html>
";

send_email($tester['email'], $subject, $message);

log_error("Beta tester activado: {$tester['name']} (ID: $id) por admin");

echo json_encode(['success' => true, 'message' => 'Beta tester activado exitosamente']);
