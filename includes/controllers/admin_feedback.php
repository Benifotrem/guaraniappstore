<?php
/**
 * Controller: Admin Feedback List
 * Ruta: /admin/feedback
 */

// Verificar autenticación
require_admin_auth();

// Renderizar vista
render_view('admin/feedback/list');
