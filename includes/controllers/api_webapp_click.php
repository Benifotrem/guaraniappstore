<?php
/**
 * API: Registrar clic en webapp
 */

header('Content-Type: application/json');

$db = Database::getInstance();
$input = json_decode(file_get_contents('php://input'), true);
$webapp_id = (int)($input['webapp_id'] ?? 0);

if (!$webapp_id) {
    json_response(['success' => false, 'message' => 'Invalid webapp_id'], 400);
}

try {
    // Incrementar contador de clics
    $db->query("UPDATE webapps SET click_count = click_count + 1 WHERE id = ?", [$webapp_id]);

    // Registrar analítica
    $db->insert('webapp_analytics', [
        'webapp_id' => $webapp_id,
        'event_type' => 'click',
        'ip_address' => get_client_ip(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'referrer' => $_SERVER['HTTP_REFERER'] ?? ''
    ]);

    json_response(['success' => true]);
} catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Error'], 500);
}
