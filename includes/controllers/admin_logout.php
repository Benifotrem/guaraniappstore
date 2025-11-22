<?php
/**
 * Controlador: Admin Logout
 */

$auth = new Auth();
$auth->logout();

$_SESSION['success'] = 'Sesión cerrada exitosamente';
redirect(get_url('admin/login'));
