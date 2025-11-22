<?php
/**
 * Controlador: Admin Webapps - Eliminar
 */

require_admin_auth();

$db = Database::getInstance();
$webapp_id = (int)($_GET['id'] ?? 0);

if (!$webapp_id) {
    redirect(get_url('admin/webapps'));
}

$webapp = $db->fetchOne("SELECT * FROM webapps WHERE id = ?", [$webapp_id]);

if (!$webapp) {
    $_SESSION['error'] = 'Webapp no encontrada';
    redirect(get_url('admin/webapps'));
}

// Eliminar webapp y sus analíticas (CASCADE)
$db->delete('webapps', 'id = ?', [$webapp_id]);

$_SESSION['success'] = 'Webapp eliminada exitosamente';
redirect(get_url('admin/webapps'));
