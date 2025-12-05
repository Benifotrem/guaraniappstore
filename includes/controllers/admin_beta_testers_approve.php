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

// Enviar email de activación con el nuevo sistema
send_activation_email($tester);

log_error("Beta tester activado: {$tester['name']} (ID: $id) por admin");

echo json_encode(['success' => true, 'message' => 'Beta tester activado exitosamente']);
