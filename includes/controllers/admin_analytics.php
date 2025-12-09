<?php
/**
 * Controlador: Admin Analytics Dashboard
 * Dashboard avanzado con métricas y visualizaciones
 */

require_admin_auth();

$db = Database::getInstance();

// Rango de fechas (últimos 30 días por defecto)
$days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
$date_from = date('Y-m-d', strtotime("-{$days} days"));
$date_to = date('Y-m-d');

// ===== MÉTRICAS GENERALES =====
$stats = [
    'total_webapps' => $db->fetchColumn("SELECT COUNT(*) FROM webapps WHERE status = 'published'"),
    'total_views' => $db->fetchColumn("SELECT SUM(view_count) FROM webapps") ?? 0,
    'total_clicks' => $db->fetchColumn("SELECT SUM(click_count) FROM webapps") ?? 0,
    'total_feedback' => $db->fetchColumn("SELECT COUNT(*) FROM feedback_reports"),
    'active_beta_testers' => $db->fetchColumn("SELECT COUNT(*) FROM beta_testers WHERE status = 'active'") ?? 0,
];

// Comparación con período anterior
$previous_date_from = date('Y-m-d', strtotime("-" . ($days * 2) . " days"));
$previous_date_to = $date_from;

$previous_views = $db->fetchColumn("
    SELECT COUNT(*) FROM webapp_analytics
    WHERE event_type = 'view'
    AND DATE(created_at) BETWEEN ? AND ?
", [$previous_date_from, $previous_date_to]) ?? 0;

$current_views = $db->fetchColumn("
    SELECT COUNT(*) FROM webapp_analytics
    WHERE event_type = 'view'
    AND DATE(created_at) BETWEEN ? AND ?
", [$date_from, $date_to]) ?? 0;

$views_change = $previous_views > 0 ? (($current_views - $previous_views) / $previous_views) * 100 : 0;

// ===== TOP 10 WEBAPPS MÁS VISITADAS =====
$top_webapps = $db->fetchAll("
    SELECT
        title,
        view_count,
        click_count,
        ROUND((click_count / NULLIF(view_count, 0)) * 100, 2) as ctr
    FROM webapps
    WHERE status = 'published'
    ORDER BY view_count DESC
    LIMIT 10
");

// ===== VISITAS POR DÍA (ÚLTIMOS 30 DÍAS) =====
$daily_views = $db->fetchAll("
    SELECT
        DATE(created_at) as date,
        COUNT(*) as views
    FROM webapp_analytics
    WHERE event_type = 'view'
    AND DATE(created_at) BETWEEN ? AND ?
    GROUP BY DATE(created_at)
    ORDER BY date ASC
", [$date_from, $date_to]);

// Rellenar días faltantes con 0
$daily_views_map = [];
foreach ($daily_views as $row) {
    $daily_views_map[$row['date']] = $row['views'];
}

$daily_views_complete = [];
for ($i = $days - 1; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $daily_views_complete[] = [
        'date' => $date,
        'views' => $daily_views_map[$date] ?? 0
    ];
}

// ===== DISTRIBUCIÓN DE FEEDBACK POR TIPO =====
$feedback_by_type = $db->fetchAll("
    SELECT
        type,
        COUNT(*) as count
    FROM feedback_reports
    GROUP BY type
    ORDER BY count DESC
");

// ===== BETA TESTERS POR NIVEL =====
$beta_testers_by_level = $db->fetchAll("
    SELECT
        contribution_level as level,
        COUNT(*) as count
    FROM beta_testers
    WHERE status = 'active'
    GROUP BY contribution_level
    ORDER BY
        CASE contribution_level
            WHEN 'platinum' THEN 1
            WHEN 'gold' THEN 2
            WHEN 'silver' THEN 3
            WHEN 'bronze' THEN 4
        END
");

// ===== ACTIVIDAD RECIENTE =====
$recent_feedback = $db->fetchAll("
    SELECT
        type,
        title,
        status,
        created_at
    FROM feedback_reports
    ORDER BY created_at DESC
    LIMIT 5
");

$recent_beta_signups = $db->fetchAll("
    SELECT
        name,
        email,
        status,
        created_at
    FROM beta_testers
    ORDER BY created_at DESC
    LIMIT 5
");

// ===== CONVERSIÓN (VIEWS -> CLICKS) =====
$conversion_rate = $stats['total_views'] > 0
    ? round(($stats['total_clicks'] / $stats['total_views']) * 100, 2)
    : 0;

$page_title = 'Analytics - Panel de Administración';
include INCLUDES_PATH . '/views/admin/layout/header.php';
include INCLUDES_PATH . '/views/admin/analytics.php';
include INCLUDES_PATH . '/views/admin/layout/footer.php';
