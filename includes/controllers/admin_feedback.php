<?php
/**
 * Controller: Admin Feedback List
 * Ruta: /admin/feedback
 */

// Verificar autenticación
require_admin_auth();

$db = Database::getInstance();

// Obtener filtros
$filter_status = $_GET['status'] ?? '';
$filter_type = $_GET['type'] ?? '';
$search = $_GET['search'] ?? '';

// Construir query
$where = ['1=1'];
$params = [];

if ($filter_status) {
    $where[] = "status = ?";
    $params[] = $filter_status;
}

if ($filter_type) {
    $where[] = "type = ?";
    $params[] = $filter_type;
}

if ($search) {
    $where[] = "(title LIKE ? OR description LIKE ? OR user_email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_sql = implode(' AND ', $where);

// Obtener feedback reports
$feedback_reports = $db->fetchAll("
    SELECT
        id,
        type,
        title,
        description,
        priority,
        status,
        user_name,
        user_email,
        created_at,
        updated_at
    FROM feedback_reports
    WHERE $where_sql
    ORDER BY
        CASE status
            WHEN 'new' THEN 1
            WHEN 'in_progress' THEN 2
            WHEN 'resolved' THEN 3
            WHEN 'closed' THEN 4
        END,
        created_at DESC
", $params);

// Obtener estadísticas
$stats = [
    'total' => $db->fetchColumn("SELECT COUNT(*) FROM feedback_reports") ?? 0,
    'new' => $db->fetchColumn("SELECT COUNT(*) FROM feedback_reports WHERE status = 'new'") ?? 0,
    'in_progress' => $db->fetchColumn("SELECT COUNT(*) FROM feedback_reports WHERE status = 'in_progress'") ?? 0,
    'bugs' => $db->fetchColumn("SELECT COUNT(*) FROM feedback_reports WHERE type = 'bug'") ?? 0,
    'features' => $db->fetchColumn("SELECT COUNT(*) FROM feedback_reports WHERE type = 'feature'") ?? 0,
];

// Renderizar vista
render_view('admin/feedback/list');
