<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelar Cuenta - Beta Tester | Guarani App Store</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 600px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
        }
        h1 {
            color: #dc2626;
            font-size: 28px;
            margin-bottom: 8px;
        }
        .subtitle {
            color: #6b7280;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .warning {
            background: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        .warning h3 {
            color: #991b1b;
            font-size: 16px;
            margin-bottom: 8px;
        }
        .warning ul {
            margin-left: 20px;
            color: #7f1d1d;
        }
        .warning li {
            margin-bottom: 6px;
        }
        .info {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        .info h3 {
            color: #065f46;
            font-size: 16px;
            margin-bottom: 8px;
        }
        .checkbox-group {
            margin: 24px 0;
        }
        .checkbox-group label {
            display: flex;
            align-items: start;
            gap: 12px;
            cursor: pointer;
        }
        .checkbox-group input[type="checkbox"] {
            margin-top: 4px;
            width: 20px;
            height: 20px;
            cursor: pointer;
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
        .btn-danger {
            background: #dc2626;
            color: white;
        }
        .btn-danger:hover:not(:disabled) {
            background: #b91c1c;
            transform: translateY(-2px);
        }
        .btn-danger:disabled {
            opacity: 0.5;
            cursor: not-allowed;
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
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚠️ Cancelar Cuenta</h1>
        <p class="subtitle">¿Estás seguro de que deseas cancelar tu cuenta de Beta Tester?</p>

        <div class="warning">
            <h3>📋 Al cancelar tu cuenta:</h3>
            <ul>
                <li>Perderás acceso al dashboard</li>
                <li>No recibirás más notificaciones</li>
                <li>Tu progreso y contribuciones se mantendrán en el historial</li>
                <li>Podrás volver a registrarte en el futuro si lo deseas</li>
            </ul>
        </div>

        <div class="info">
            <h3>💡 Alternativas</h3>
            <p>Si tienes algún problema o sugerencia, escríbenos a <strong>cesarruzafa@gmail.com</strong> antes de cancelar tu cuenta.</p>
        </div>

        <form method="POST" action="<?php echo get_url('beta/unsubscribe'); ?>" id="unsubscribeForm">
            <div class="checkbox-group">
                <label>
                    <input type="checkbox" id="confirmCheckbox" required>
                    <span>Confirmo que deseo cancelar mi cuenta de Beta Tester y entiendo que perderé el acceso al dashboard.</span>
                </label>
            </div>

            <div class="actions">
                <button type="submit" name="confirm_unsubscribe" value="1" class="btn btn-danger" id="submitBtn" disabled>
                    🗑️ Sí, Cancelar Mi Cuenta
                </button>
                <a href="<?php echo get_url('beta/dashboard'); ?>" class="btn btn-secondary">Volver al Dashboard</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('confirmCheckbox').addEventListener('change', function() {
            document.getElementById('submitBtn').disabled = !this.checked;
        });
    </script>
</body>
</html>
