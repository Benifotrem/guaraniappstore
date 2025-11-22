<?php
/**
 * Controlador: Webapp Detail - Detalle de webapp
 */

$db = Database::getInstance();
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    redirect(get_url('webapps'));
}

// Obtener webapp
$webapp = $db->fetchOne("
    SELECT * FROM webapps
    WHERE slug = ? AND status = 'published'
", [$slug]);

if (!$webapp) {
    http_response_code(404);
    include INCLUDES_PATH . '/controllers/404.php';
    exit;
}

// Incrementar contador de vistas
$db->query("UPDATE webapps SET view_count = view_count + 1 WHERE id = ?", [$webapp['id']]);

// Registrar analítica
$db->insert('webapp_analytics', [
    'webapp_id' => $webapp['id'],
    'event_type' => 'view',
    'ip_address' => get_client_ip(),
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'referrer' => $_SERVER['HTTP_REFERER'] ?? ''
]);

// Parsear tags y tech_stack
$webapp['tags'] = json_decode($webapp['tags'] ?? '[]', true);
$webapp['tech_stack'] = json_decode($webapp['tech_stack'] ?? '[]', true);
$webapp['screenshots'] = json_decode($webapp['screenshots'] ?? '[]', true);

// Webapps relacionadas (misma categoría)
$related_webapps = [];
if ($webapp['category']) {
    $related_webapps = $db->fetchAll("
        SELECT id, title, slug, short_description, logo_url, category
        FROM webapps
        WHERE category = ? AND id != ? AND status = 'published'
        ORDER BY RAND()
        LIMIT 3
    ", [$webapp['category'], $webapp['id']]);
}

$page_title = $webapp['title'] . ' - Guarani App Store';
include INCLUDES_PATH . '/views/landing/header.php';
include INCLUDES_PATH . '/views/public/webapp_detail.php';
include INCLUDES_PATH . '/views/landing/footer.php';
