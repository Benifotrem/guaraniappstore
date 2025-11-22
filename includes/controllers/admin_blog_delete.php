<?php
/**
 * Controlador: Admin Blog - Eliminar artículo
 */

require_admin_auth();

$db = Database::getInstance();
$article_id = (int)($_GET['id'] ?? 0);

if (!$article_id) {
    redirect(get_url('admin/blog'));
}

$article = $db->fetchOne("SELECT * FROM blog_articles WHERE id = ?", [$article_id]);

if (!$article) {
    $_SESSION['error'] = 'Artículo no encontrado';
    redirect(get_url('admin/blog'));
}

// Eliminar artículo y sus analíticas (CASCADE)
$db->delete('blog_articles', 'id = ?', [$article_id]);

$_SESSION['success'] = 'Artículo eliminado exitosamente';
redirect(get_url('admin/blog'));
