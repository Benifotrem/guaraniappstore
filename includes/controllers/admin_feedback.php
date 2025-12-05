<?php
/**
 * Controller: Admin Feedback List
 * Ruta: /admin/feedback
 */

// Verificar autenticación
require_once INCLUDES_PATH . '/auth.php';
check_auth();

// Renderizar vista
render_view('admin/feedback/list');
