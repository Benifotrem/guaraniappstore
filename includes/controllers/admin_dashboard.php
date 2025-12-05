<?php
/**
 * Controlador: Admin Dashboard
 */

require_admin_auth();

$db = Database::getInstance();

// Obtener estadísticas
$stats = [
    'total_webapps' => $db->fetchColumn("SELECT COUNT(*) FROM webapps WHERE status = 'published'"),
    'total_articles' => $db->fetchColumn("SELECT COUNT(*) FROM blog_articles WHERE status = 'published'"),
    'total_subscribers' => $db->fetchColumn("SELECT COUNT(*) FROM blog_subscribers WHERE status = 'active'"),
    'total_webapp_views' => $db->fetchColumn("SELECT SUM(view_count) FROM webapps") ?? 0,
    'total_article_views' => $db->fetchColumn("SELECT SUM(view_count) FROM blog_articles") ?? 0,
    'total_beta_testers' => $db->fetchColumn("SELECT COUNT(*) FROM beta_testers") ?? 0,
    'active_beta_testers' => $db->fetchColumn("SELECT COUNT(*) FROM beta_testers WHERE status = 'active'") ?? 0,
    'pending_beta_testers' => $db->fetchColumn("SELECT COUNT(*) FROM beta_testers WHERE status = 'pending'") ?? 0,
    'total_feedback' => $db->fetchColumn("SELECT COUNT(*) FROM feedback_reports") ?? 0,
    'pending_feedback' => $db->fetchColumn("SELECT COUNT(*) FROM feedback_reports WHERE status = 'new'") ?? 0,
    'bugs_reported' => $db->fetchColumn("SELECT COUNT(*) FROM feedback_reports WHERE type = 'bug'") ?? 0,
    'features_requested' => $db->fetchColumn("SELECT COUNT(*) FROM feedback_reports WHERE type = 'feature'") ?? 0,
];

// Webapps recientes
$recent_webapps = $db->fetchAll("
    SELECT id, title, slug, status, view_count, created_at
    FROM webapps
    ORDER BY created_at DESC
    LIMIT 5
");

// Artículos recientes
$recent_articles = $db->fetchAll("
    SELECT id, title, slug, status, view_count, published_at
    FROM blog_articles
    ORDER BY created_at DESC
    LIMIT 5
");

// Suscriptores recientes
$recent_subscribers = $db->fetchAll("
    SELECT email, status, created_at
    FROM blog_subscribers
    ORDER BY created_at DESC
    LIMIT 5
");

$page_title = 'Dashboard - Panel de Administración';
include INCLUDES_PATH . '/views/admin/layout/header.php';
include INCLUDES_PATH . '/views/admin/dashboard.php';
include INCLUDES_PATH . '/views/admin/layout/footer.php';
