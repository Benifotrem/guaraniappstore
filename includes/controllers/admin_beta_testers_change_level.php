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

// Actualizar nivel
$db->query("UPDATE beta_testers SET contribution_level = ? WHERE id = ?", [$level, $id]);

log_error("Nivel cambiado a $level para beta tester ID: $id por admin");

echo json_encode(['success' => true, 'message' => 'Nivel actualizado exitosamente']);
