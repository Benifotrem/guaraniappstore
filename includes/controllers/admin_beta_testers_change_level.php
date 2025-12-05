<?php
/**
 * Controller: Cambiar Nivel de Beta Tester
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
$level = $_POST['level'] ?? '';

$valid_levels = ['bronze', 'silver', 'gold', 'platinum'];

if (!$id || !in_array($level, $valid_levels)) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$db = Database::getInstance();

// Obtener nivel anterior
$tester = $db->fetchOne("SELECT * FROM beta_testers WHERE id = ?", [$id]);
$old_level = $tester['contribution_level'];

// Actualizar nivel
$db->query("UPDATE beta_testers SET contribution_level = ? WHERE id = ?", [$level, $id]);

// Obtener datos actualizados
$tester = $db->fetchOne("SELECT * FROM beta_testers WHERE id = ?", [$id]);

// Enviar notificaciones
send_level_change_email($tester, $old_level, $level);

if ($tester['telegram_id']) {
    send_telegram_level_notification($tester['telegram_id'], $tester, $old_level, $level);
}

log_error("Nivel cambiado de $old_level a $level para beta tester ID: $id por admin");

echo json_encode(['success' => true, 'message' => 'Nivel actualizado y notificaciones enviadas']);
