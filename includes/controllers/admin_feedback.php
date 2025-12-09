<?php
/**
 * Controller: Admin Feedback List
 * Ruta: /admin/feedback
 */

// Verificar autenticación
require_admin_auth();

// Inicializar base de datos
$db = Database::getInstance();

// Título de página
$page_title = 'Feedback - Panel de Administración';

// Incluir layout con header y footer
include INCLUDES_PATH . '/views/admin/layout/header.php';
include INCLUDES_PATH . '/views/admin/feedback/list.php';
include INCLUDES_PATH . '/views/admin/layout/footer.php';
