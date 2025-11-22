<?php
/**
 * Controlador: Admin Webapps - Listado
 */

require_admin_auth();

$db = Database::getInstance();

// Obtener webapps
$webapps = $db->fetchAll("
    SELECT id, title, slug, status, category, is_featured, view_count, click_count, created_at
    FROM webapps
    ORDER BY created_at DESC
");

$page_title = 'Gestión de Webapps';
include INCLUDES_PATH . '/views/admin/layout/header.php';
include INCLUDES_PATH . '/views/admin/webapps/index.php';
include INCLUDES_PATH . '/views/admin/layout/footer.php';
