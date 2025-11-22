<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación 2FA - Panel de Administración</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-login-container">
        <div class="admin-login-box">
            <div class="admin-login-header">
                <img src="<?php echo ASSETS_URL; ?>/images/logo.png"
                     alt="Logo" class="admin-login-logo">
                <h1>Verificación 2FA</h1>
                <p>Ingresa el código de tu aplicación de autenticación</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo e($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo get_url('admin/login'); ?>" class="admin-login-form">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="verify_2fa" value="1">

                <div class="form-group">
                    <label for="code" class="form-label">Código de 6 dígitos</label>
                    <input type="text"
                           id="code"
                           name="code"
                           class="form-input"
                           maxlength="6"
                           pattern="[0-9]{6}"
                           required
                           autofocus
                           autocomplete="off"
                           style="text-align: center; font-size: 1.5rem; letter-spacing: 0.5rem;">
                </div>

                <button type="submit" class="btn btn-primary w-full btn-lg">
                    Verificar
                </button>
            </form>

            <div class="admin-login-footer">
                <a href="<?php echo get_url('admin/logout'); ?>">← Cancelar</a>
            </div>
        </div>
    </div>
</body>
</html>
