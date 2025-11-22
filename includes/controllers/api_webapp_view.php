<?php
/**
 * API: Registrar vista de webapp
 */

header('Content-Type: application/json');

$db = Database::getInstance();
$webapp_id = (int)($_POST['webapp_id'] ?? $_GET['webapp_id'] ?? 0);

if (!$webapp_id) {
    json_response(['success' => false, 'message' => 'Invalid webapp_id'], 400);
}

try {
    $db->insert('webapp_analytics', [
        'webapp_id' => $webapp_id,
        'event_type' => 'view',
        'ip_address' => get_client_ip(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'referrer' => $_SERVER['HTTP_REFERER'] ?? ''
    ]);

    json_response(['success' => true]);
} catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Error'], 500);
}
