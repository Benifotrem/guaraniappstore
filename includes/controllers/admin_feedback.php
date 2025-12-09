<?php
/**
 * Controller: Admin Feedback List
 * Ruta: /admin/feedback
 */

// Verificar autenticación
require_admin_auth();

// Inicializar base de datos
$db = Database::getInstance();

// Renderizar vista pasando $db
render_view('admin/feedback/list', ['db' => $db]);
