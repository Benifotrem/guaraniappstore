<?php
/**
 * Controlador: Blog - Listado público
 */

$db = Database::getInstance();

// Paginación
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = BLOG_POSTS_PER_PAGE;
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
    $where[] = "(title LIKE ? OR excerpt LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$whereClause = implode(' AND ', $where);

// Total de artículos
$total = $db->fetchColumn(
    "SELECT COUNT(*) FROM blog_articles WHERE {$whereClause}",
    $params
);

// Obtener artículos
$articles = $db->fetchAll(
    "SELECT id, title, slug, excerpt, featured_image_url, author_name, category, published_at, view_count
     FROM blog_articles
     WHERE {$whereClause}
     ORDER BY published_at DESC
     LIMIT {$per_page} OFFSET {$offset}",
    $params
);

// Categorías para filtro
$categories = $db->fetchAll(
    "SELECT DISTINCT category FROM blog_articles
     WHERE status = 'published' AND category IS NOT NULL
     ORDER BY category"
);

// Artículo destacado (el más reciente)
$featured_article = $articles[0] ?? null;

// Paginación
$total_pages = ceil($total / $per_page);

$page_title = 'Blog - Guarani App Store';
include INCLUDES_PATH . '/views/landing/header.php';
include INCLUDES_PATH . '/views/public/blog.php';
include INCLUDES_PATH . '/views/landing/footer.php';
