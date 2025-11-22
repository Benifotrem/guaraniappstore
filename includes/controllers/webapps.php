<?php
/**
 * Controlador: Webapps - Listado público
 */

$db = Database::getInstance();

// Paginación
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = WEBAPPS_PER_PAGE;
$offset = ($page - 1) * $per_page;

// Filtros
$category = isset($_GET['category']) ? sanitize_input($_GET['category']) : '';
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Construir query
$where = ["status = 'published'"];
$params = [];

if ($category) {
    $where[] = "category = ?";
    $params[] = $category;
}

if ($search) {
    $where[] = "(title LIKE ? OR short_description LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$whereClause = implode(' AND ', $where);

// Total de webapps
$total = $db->fetchColumn(
    "SELECT COUNT(*) FROM webapps WHERE {$whereClause}",
    $params
);

// Obtener webapps
$webapps = $db->fetchAll(
    "SELECT * FROM webapps
     WHERE {$whereClause}
     ORDER BY sort_order ASC, published_at DESC
     LIMIT {$per_page} OFFSET {$offset}",
    $params
);

// Categorías para filtro
$categories = $db->fetchAll(
    "SELECT DISTINCT category FROM webapps
     WHERE status = 'published' AND category IS NOT NULL
     ORDER BY category"
);

// Paginación
$total_pages = ceil($total / $per_page);

$page_title = 'Nuestras Aplicaciones - Guarani App Store';
include INCLUDES_PATH . '/views/landing/header.php';
include INCLUDES_PATH . '/views/public/webapps.php';
include INCLUDES_PATH . '/views/landing/footer.php';
