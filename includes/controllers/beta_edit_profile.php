<?php
/**
 * Controller: Beta Edit Profile
 * Permite editar nombre y telegram username
 */

// Verificar autenticación
if (!isset($_SESSION['beta_user_id'])) {
    redirect('beta');
}

$db = Database::getInstance();
$error = null;
$success = null;

// Obtener datos actuales
$tester = $db->fetchOne("SELECT * FROM beta_testers WHERE id = ?", [$_SESSION['beta_user_id']]);

if (!$tester) {
    redirect('beta');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $telegram_username = trim($_POST['telegram_username'] ?? '');
    
    if (empty($name)) {
        $error = 'El nombre es obligatorio';
    } elseif (strlen($name) < 3) {
        $error = 'El nombre debe tener al menos 3 caracteres';
    } else {
        // Validar telegram username si se proporciona
        if (!empty($telegram_username) && !preg_match('/^[a-zA-Z0-9_]{5,32}$/', $telegram_username)) {
            $error = 'Username de Telegram inválido (5-32 caracteres, solo letras, números y guion bajo)';
        } else {
            // Actualizar datos
            $db->query("
                UPDATE beta_testers 
                SET name = ?, telegram_username = ?
                WHERE id = ?
            ", [$name, $telegram_username ?: null, $_SESSION['beta_user_id']]);
            
            // Actualizar sesión
            $_SESSION['beta_user_name'] = $name;
            
            log_error("Beta tester actualizó perfil: $name (ID: {$_SESSION['beta_user_id']})");
            
            $success = 'Perfil actualizado correctamente';
            
            // Recargar datos
            $tester = $db->fetchOne("SELECT * FROM beta_testers WHERE id = ?", [$_SESSION['beta_user_id']]);
        }
    }
}

// Renderizar vista
require_once INCLUDES_PATH . '/views/beta/edit-profile.php';
