<?php
/**
 * AJAX Controller: Generar artículo con IA
 */

// Asegurar que es una petición AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    http_response_code(403);
    exit('Access denied');
}

require_admin_auth();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    echo json_encode(['error' => 'Token de seguridad inválido']);
    exit;
}

try {
    $generator = new BlogGenerator();
    $result = $generator->generateArticle();

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'article_id' => $result['article_id'],
            'title' => $result['title'],
            'featured_image' => $result['featured_image'] ?? null,
            'redirect' => get_url('admin/blog/edit?id=' . $result['article_id'])
        ]);
    } else {
        throw new Exception('Error al generar artículo');
    }

} catch (Exception $e) {
    log_error("Error generando artículo vía AJAX: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
