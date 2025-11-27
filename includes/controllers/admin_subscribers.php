<?php
/**
 * Controlador: Admin Subscribers - Gestión de suscriptores del blog
 *
 * Funcionalidades:
 * - Listado paginado de suscriptores
 * - Búsqueda por email/nombre
 * - Filtros por estado
 * - Estadísticas de suscriptores
 */

require_admin_auth();

$db = Database::getInstance();

// ==================================================
// PARÁMETROS DE BÚSQUEDA Y FILTROS
// ==================================================
$searchQuery = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$currentPage = max(1, intval($_GET['page'] ?? 1));
$limit = max(10, min(500, intval($_GET['limit'] ?? 25))); // Entre 10 y 500
$offset = ($currentPage - 1) * $limit;

// ==================================================
// OBTENER ESTADÍSTICAS
// ==================================================
$stats = [
    'total' => $db->fetchColumn("SELECT COUNT(*) FROM blog_subscribers"),
    'active' => $db->fetchColumn("SELECT COUNT(*) FROM blog_subscribers WHERE status = 'active'"),
    'pending' => $db->fetchColumn("SELECT COUNT(*) FROM blog_subscribers WHERE status = 'pending'"),
    'unsubscribed' => $db->fetchColumn("SELECT COUNT(*) FROM blog_subscribers WHERE status = 'unsubscribed'"),
];

// ==================================================
// CONSTRUIR QUERY CON FILTROS
// ==================================================
$whereClauses = [];
$params = [];

// Búsqueda por email o nombre
if ($searchQuery) {
    $whereClauses[] = "(email LIKE ? OR name LIKE ?)";
    $searchTerm = "%{$searchQuery}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// Filtro por estado
if ($statusFilter && in_array($statusFilter, ['active', 'pending', 'unsubscribed'])) {
    $whereClauses[] = "status = ?";
    $params[] = $statusFilter;
}

// SQL base
$whereSQL = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// ==================================================
// OBTENER TOTAL DE RESULTADOS (PARA PAGINACIÓN)
// ==================================================
$totalSubscribers = $db->fetchColumn(
    "SELECT COUNT(*) FROM blog_subscribers {$whereSQL}",
    $params
);

$totalPages = max(1, ceil($totalSubscribers / $limit));

// Ajustar página actual si está fuera de rango
if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
    $offset = ($currentPage - 1) * $limit;
}

// ==================================================
// OBTENER SUSCRIPTORES PAGINADOS
// ==================================================
$subscribers = $db->fetchAll(
    "SELECT *
     FROM blog_subscribers
     {$whereSQL}
     ORDER BY created_at DESC
     LIMIT ? OFFSET ?",
    array_merge($params, [$limit, $offset])
);

// ==================================================
// PREPARAR DATOS PARA LA VISTA
// ==================================================
$page_title = 'Gestión de Suscriptores';

// ==================================================
// RENDERIZAR VISTA
// ==================================================
include INCLUDES_PATH . '/views/admin/subscribers.php';
