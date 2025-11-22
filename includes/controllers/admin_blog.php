<?php
/**
 * Controlador: Admin Blog - Listado
 */

require_admin_auth();

$db = Database::getInstance();

$articles = $db->fetchAll("
    SELECT id, title, slug, status, category, is_auto_generated, view_count, published_at, created_at
    FROM blog_articles
    ORDER BY created_at DESC
");

$page_title = 'Gestión de Blog';
include INCLUDES_PATH . '/views/admin/layout/header.php';
include INCLUDES_PATH . '/views/admin/blog/index.php';
include INCLUDES_PATH . '/views/admin/layout/footer.php';
