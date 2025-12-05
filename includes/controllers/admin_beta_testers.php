<?php
/**
 * Controller: Admin Beta Testers List
 * Gestión de beta testers
 */

// Verificar autenticación admin
// Autenticación manejada en index.php
require_admin_auth();

$db = Database::getInstance();

// Obtener filtros
$filter_status = $_GET['status'] ?? '';
$filter_level = $_GET['level'] ?? '';
$search = $_GET['search'] ?? '';

// Construir query
$where = ['1=1'];
$params = [];

if ($filter_status) {
    $where[] = "status = ?";
    $params[] = $filter_status;
}

if ($filter_level) {
    $where[] = "contribution_level = ?";
    $params[] = $filter_level;
}

if ($search) {
    $where[] = "(name LIKE ? OR email LIKE ? OR telegram_username LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_sql = implode(' AND ', $where);

// Obtener beta testers
$beta_testers = $db->fetchAll("
    SELECT 
        id,
        name,
        email,
        telegram_username,
        telegram_id,
        status,
        contribution_level,
        bugs_reported,
        suggestions_accepted,
        (bugs_reported + suggestions_accepted) as total_contributions,
        created_at
    FROM beta_testers
    WHERE $where_sql
    ORDER BY created_at DESC
", $params);

// Estadísticas globales
$stats = $db->fetchOne("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
        SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive,
        SUM(bugs_reported) as total_bugs,
        SUM(suggestions_accepted) as total_suggestions
    FROM beta_testers
");

// Renderizar vista
include INCLUDES_PATH . '/views/admin/beta-testers/list.php';
