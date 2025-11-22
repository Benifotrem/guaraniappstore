<?php
/**
 * Controlador: Admin Profile - Perfil del usuario y 2FA
 */

require_admin_auth();

$db = Database::getInstance();
$auth = new Auth();
$user_id = $_SESSION['admin_user_id'];
$success = null;
$error = null;
$qr_code_url = null;
$two_fa_secret = null;

// Obtener datos del usuario
$user = $db->fetchOne("SELECT * FROM admin_users WHERE id = ?", [$user_id]);

// Cambiar contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token de seguridad inválido';
    } else {
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
            $error = 'Todos los campos son requeridos';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Las contraseñas nuevas no coinciden';
        } elseif (strlen($new_password) < 8) {
            $error = 'La contraseña debe tener al menos 8 caracteres';
        } else {
            $result = $auth->changePassword($user_id, $old_password, $new_password);
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        }
    }
}

// Activar 2FA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enable_2fa'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token de seguridad inválido';
    } else {
        $result = $auth->enable2FA($user_id);
        if ($result['success']) {
            $two_fa_secret = $result['secret'];
            $qr_code_url = $result['qr_code_url'];
            $success = 'Escanea el código QR con tu app de autenticación';
        }
    }
}

// Confirmar 2FA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_2fa'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token de seguridad inválido';
    } else {
        $code = $_POST['2fa_code'] ?? '';
        $result = $auth->confirm2FA($user_id, $code);
        if ($result['success']) {
            $success = $result['message'];
            $user = $db->fetchOne("SELECT * FROM admin_users WHERE id = ?", [$user_id]);
        } else {
            $error = $result['message'];
        }
    }
}

// Desactivar 2FA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['disable_2fa'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token de seguridad inválido';
    } else {
        $result = $auth->disable2FA($user_id);
        if ($result['success']) {
            $success = $result['message'];
            $user = $db->fetchOne("SELECT * FROM admin_users WHERE id = ?", [$user_id]);
        }
    }
}

$page_title = 'Mi Perfil';
include INCLUDES_PATH . '/views/admin/layout/header.php';
?>

<h2 class="mb-4">Mi Perfil</h2>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo e($success); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo e($error); ?></div>
<?php endif; ?>

<div class="grid grid-cols-2 gap-4">
    <!-- Información del Usuario -->
    <div class="card" style="padding: 2rem;">
        <h3 class="mb-3">Información del Usuario</h3>
        <div class="mb-3">
            <strong>Usuario:</strong> <?php echo e($user['username']); ?>
        </div>
        <div class="mb-3">
            <strong>Email:</strong> <?php echo e($user['email']); ?>
        </div>
        <div class="mb-3">
            <strong>Nombre:</strong> <?php echo e($user['full_name'] ?? '-'); ?>
        </div>
        <div class="mb-3">
            <strong>Último login:</strong> <?php echo $user['last_login'] ? format_date_es($user['last_login'], 'long') : 'Nunca'; ?>
        </div>
    </div>

    <!-- Cambiar Contraseña -->
    <div class="card" style="padding: 2rem;">
        <h3 class="mb-3">Cambiar Contraseña</h3>
        <form method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="change_password" value="1">

            <div class="form-group">
                <label class="form-label">Contraseña Actual</label>
                <input type="password" name="old_password" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nueva Contraseña</label>
                <input type="password" name="new_password" class="form-input" required minlength="8">
            </div>

            <div class="form-group">
                <label class="form-label">Confirmar Nueva Contraseña</label>
                <input type="password" name="confirm_password" class="form-input" required minlength="8">
            </div>

            <button type="submit" class="btn btn-primary">
                Cambiar Contraseña
            </button>
        </form>
    </div>
</div>

<!-- Autenticación de Dos Factores -->
<div class="card mt-4" style="padding: 2rem;">
    <h3 class="mb-3">Autenticación de Dos Factores (2FA)</h3>

    <?php if ($user['two_fa_enabled']): ?>
        <div class="alert alert-success mb-3">
            ✅ 2FA está actualmente <strong>activado</strong>
        </div>
        <p>Tu cuenta está protegida con autenticación de dos factores.</p>
        <form method="POST" class="mt-3">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="disable_2fa" value="1">
            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de desactivar 2FA?')">
                Desactivar 2FA
            </button>
        </form>
    <?php elseif ($qr_code_url): ?>
        <div class="text-center">
            <p class="mb-3">Escanea este código QR con Google Authenticator o Authy:</p>
            <img src="<?php echo e($qr_code_url); ?>" alt="QR Code" style="max-width: 250px; margin-bottom: 1rem;">
            <p class="mb-3">
                <strong>Clave secreta (manual):</strong><br>
                <code style="font-size: 1.1rem; padding: 0.5rem; background: #f5f5f5; display: inline-block;">
                    <?php echo e($two_fa_secret); ?>
                </code>
            </p>
            <form method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="confirm_2fa" value="1">
                <div class="form-group" style="max-width: 300px; margin: 0 auto;">
                    <label class="form-label">Ingresa el código de 6 dígitos:</label>
                    <input type="text"
                           name="2fa_code"
                           class="form-input"
                           maxlength="6"
                           pattern="[0-9]{6}"
                           required
                           style="text-align: center; font-size: 1.5rem;">
                </div>
                <button type="submit" class="btn btn-success mt-3">
                    Confirmar y Activar 2FA
                </button>
            </form>
        </div>
    <?php else: ?>
        <div class="alert alert-warning mb-3">
            ⚠️ 2FA está actualmente <strong>desactivado</strong>
        </div>
        <p>Recomendamos activar la autenticación de dos factores para mayor seguridad.</p>
        <form method="POST" class="mt-3">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="enable_2fa" value="1">
            <button type="submit" class="btn btn-success">
                Activar 2FA
            </button>
        </form>
    <?php endif; ?>
</div>

<?php include INCLUDES_PATH . '/views/admin/layout/footer.php'; ?>
