<?php
/**
 * Controlador: Blog Article - Detalle de artículo
 */

$db = Database::getInstance();
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    redirect(get_url('blog'));
}

// Obtener artículo
$article = $db->fetchOne("
    SELECT * FROM blog_articles
    WHERE slug = ? AND status = 'published'
", [$slug]);

if (!$article) {
    http_response_code(404);
    include INCLUDES_PATH . '/controllers/404.php';
    exit;
}

// Incrementar contador de vistas
$db->query("UPDATE blog_articles SET view_count = view_count + 1 WHERE id = ?", [$article['id']]);

// Registrar analítica
$db->insert('blog_article_analytics', [
    'article_id' => $article['id'],
    'event_type' => 'view',
    'ip_address' => get_client_ip(),
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'referrer' => $_SERVER['HTTP_REFERER'] ?? ''
]);

// Parsear tags
$article['tags'] = json_decode($article['tags'] ?? '[]', true);

// Webapp relacionada
$related_webapp = null;
if ($article['related_webapp_id']) {
    $related_webapp = $db->fetchOne("
        SELECT * FROM webapps
        WHERE id = ? AND status = 'published'
    ", [$article['related_webapp_id']]);
}

// Artículos relacionados (misma categoría)
$related_articles = [];
if ($article['category']) {
    $related_articles = $db->fetchAll("
        SELECT id, title, slug, excerpt, featured_image_url, author_name, published_at
        FROM blog_articles
        WHERE category = ? AND id != ? AND status = 'published'
        ORDER BY published_at DESC
        LIMIT 3
    ", [$article['category'], $article['id']]);
}

$page_title = $article['seo_title'] ?? $article['title'];
include INCLUDES_PATH . '/views/landing/header.php';
include INCLUDES_PATH . '/views/public/blog_article.php';
include INCLUDES_PATH . '/views/landing/footer.php';
