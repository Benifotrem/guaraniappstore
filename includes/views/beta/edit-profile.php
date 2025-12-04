<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Beta Tester | Guarani App Store</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
        }
        .header {
            margin-bottom: 30px;
        }
        h1 {
            color: #111827;
            font-size: 28px;
            margin-bottom: 8px;
        }
        .subtitle {
            color: #6b7280;
            font-size: 14px;
        }
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #dc2626;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        .form-group {
            margin-bottom: 24px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 500;
            font-size: 14px;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .help-text {
            font-size: 13px;
            color: #6b7280;
            margin-top: 6px;
        }
        .readonly-field {
            background: #f9fafb;
            color: #6b7280;
            cursor: not-allowed;
        }
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
            margin-left: 10px;
        }
        .btn-secondary:hover {
            background: #d1d5db;
        }
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✏️ Editar Perfil</h1>
            <p class="subtitle">Actualiza tu información personal</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo get_url('beta/edit-profile'); ?>">
            <div class="form-group">
                <label for="name">Nombre Completo *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="<?php echo htmlspecialchars($tester['name']); ?>"
                    required
                    minlength="3"
                    maxlength="100"
                >
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="text" 
                    id="email" 
                    value="<?php echo htmlspecialchars($tester['email']); ?>"
                    class="readonly-field"
                    readonly
                >
                <p class="help-text">El email no se puede cambiar</p>
            </div>

            <div class="form-group">
                <label for="telegram_username">Username de Telegram</label>
                <input 
                    type="text" 
                    id="telegram_username" 
                    name="telegram_username" 
                    value="<?php echo htmlspecialchars($tester['telegram_username'] ?? ''); ?>"
                    placeholder="tu_username (sin @)"
                    pattern="[a-zA-Z0-9_]{5,32}"
                >
                <p class="help-text">Para recibir notificaciones por @guaraniappstore_bot</p>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">💾 Guardar Cambios</button>
                <a href="<?php echo get_url('beta/dashboard'); ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>

        <div style='margin-top: 40px; padding-top: 30px; border-top: 2px solid #e5e7eb;'>
            <h3 style='color: #991b1b; margin-bottom: 10px; font-size: 18px;'>⚠️ Zona Peligrosa</h3>
            <p style='color: #6b7280; margin-bottom: 16px; font-size: 14px;'>Si deseas cancelar tu cuenta de Beta Tester:</p>
            <a href="<?php echo get_url('beta/unsubscribe'); ?>" style="padding: 10px 20px; background: #dc2626; color: white; text-decoration: none; border-radius: 8px; font-size: 14px; display: inline-block;">
                🗑️ Cancelar Mi Cuenta
            </a>
        </div>
