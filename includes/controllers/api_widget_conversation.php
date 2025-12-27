<?php
/**
 * API Endpoint: Widget Conversation
 * Maneja las conversaciones del widget web y las guarda en la BD
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Método no permitido'], 405);
}

// Leer JSON del body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    json_response(['error' => 'Datos inválidos'], 400);
}

// Validar campos requeridos
$required_fields = ['action', 'session_id'];
if (!isset($data['action']) || !isset($data['session_id'])) {
    json_response(['error' => 'Faltan campos requeridos: action, session_id'], 400);
}

try {
    require_once INCLUDES_PATH . '/classes/LeadManager.php';
    $leadManager = new LeadManager();

    $session_id = sanitize($data['session_id']);
    $action = sanitize($data['action']);
    $email = isset($data['email']) ? sanitize($data['email']) : null;
    $name = isset($data['name']) ? sanitize($data['name']) : null;
    $phone = isset($data['phone']) ? sanitize($data['phone']) : null;
    $message = isset($data['message']) ? sanitize($data['message']) : null;

    // Detectar interés basado en la acción
    $interest_mapping = [
        'dao' => 'dao_governance',
        'benefits' => 'become_shareholder',
        'join' => 'beta_tester',
        'telegram' => 'contact',
    ];

    $interest = isset($interest_mapping[$action]) ? $interest_mapping[$action] : 'general_inquiry';

    // Construir descripción del proyecto basada en las interacciones
    $project_desc_mapping = [
        'dao' => 'Interesado en saber qué es una DAO y cómo funciona la gobernanza descentralizada',
        'benefits' => 'Preguntó sobre los beneficios de convertirse en propietario/accionista de la DAO',
        'join' => 'Mostró interés en unirse al programa Beta Tester para acceso prioritario al accionariado',
        'telegram' => 'Quiso conectar con el bot de Telegram para más información',
    ];

    $project_description = isset($project_desc_mapping[$action]) ? $project_desc_mapping[$action] : $message;

    // Preparar datos del lead
    $lead_data = [
        'web_session_id' => $session_id,
        'source' => 'web_widget',
        'interest' => $interest,
        'project_description' => $project_description,
    ];

    // Agregar datos adicionales si están disponibles
    if ($email) $lead_data['email'] = $email;
    if ($name) $lead_data['name'] = $name;
    if ($phone) $lead_data['phone'] = $phone;

    // Guardar o actualizar lead
    $lead_id = $leadManager->saveOrUpdateLead($lead_data);

    if (!$lead_id) {
        json_response(['error' => 'Error al guardar lead'], 500);
    }

    // Guardar conversación
    $user_message = isset($data['user_message']) ? $data['user_message'] : null;
    $bot_response = isset($data['bot_response']) ? $data['bot_response'] : null;

    if ($user_message) {
        $leadManager->saveMessage($lead_id, 'user', $user_message, $session_id);
    }

    if ($bot_response) {
        $leadManager->saveMessage($lead_id, 'bot', $bot_response, $session_id);
    }

    // Recalcular score
    $score = $leadManager->calculateAndUpdateLeadScore($lead_id);

    // Responder con éxito
    json_response([
        'success' => true,
        'lead_id' => $lead_id,
        'score' => $score,
        'message' => 'Conversación guardada exitosamente'
    ], 200);

} catch (Exception $e) {
    error_log('Widget API Error: ' . $e->getMessage());
    json_response(['error' => 'Error interno del servidor', 'details' => $e->getMessage()], 500);
}
