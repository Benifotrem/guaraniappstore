<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Panel de Administración</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-login-container">
        <div class="admin-login-box">
            <div class="admin-login-header">
                <img src="<?php echo ASSETS_URL; ?>/images/logo.png"
                     alt="Logo" class="admin-login-logo">
                <h1>Guarani App Store</h1>
                <p>Panel de Administración</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo e($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo e($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="admin-login-form">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label for="username" class="form-label">Usuario</label>
                    <input type="text"
                           id="username"
                           name="username"
                           class="form-input"
                           required
                           autofocus
                           autocomplete="username">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-input"
                           required
                           autocomplete="current-password">
                </div>

                <button type="submit" class="btn btn-primary w-full btn-lg">
                    Iniciar Sesión
                </button>
            </form>

            <div class="admin-login-footer">
                <a href="<?php echo get_url(); ?>">← Volver al sitio</a>
            </div>
        </div>
    </div>
</body>
</html>
