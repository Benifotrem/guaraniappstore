<?php
/**
 * API: Registrar vista de artículo del blog
 */

header('Content-Type: application/json');

$db = Database::getInstance();
$article_id = (int)($_POST['article_id'] ?? $_GET['article_id'] ?? 0);

if (!$article_id) {
    json_response(['success' => false, 'message' => 'Invalid article_id'], 400);
}

try {
    $db->insert('blog_article_analytics', [
        'article_id' => $article_id,
        'event_type' => 'view',
        'ip_address' => get_client_ip(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'referrer' => $_SERVER['HTTP_REFERER'] ?? ''
    ]);

    json_response(['success' => true]);
} catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Error'], 500);
}
