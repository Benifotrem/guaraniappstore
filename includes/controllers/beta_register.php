<?php
/**
 * Controller: Beta Tester Registration
 * Procesa el registro de nuevos beta testers
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('beta/join');
}

// Verificar CSRF token
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['beta_error'] = 'Token de seguridad inválido';
    redirect('beta/join');
}

// Validar y sanitizar inputs
$name = sanitize_input($_POST['name'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$country = sanitize_input($_POST['country'] ?? '');
$company = sanitize_input($_POST['company'] ?? '');
$interested_app = sanitize_input($_POST['interested_app'] ?? '');
$problems_to_solve = sanitize_input($_POST['problems_to_solve'] ?? '');
$technical_level = sanitize_input($_POST['technical_level'] ?? '');

// Validaciones
$errors = [];

if (empty($name)) {
    $errors[] = 'El nombre es requerido';
}

if (!$email) {
    $errors[] = 'Email inválido';
}

if (empty($interested_app)) {
    $errors[] = 'Debes seleccionar una aplicación de interés';
}

if (empty($problems_to_solve) || strlen($problems_to_solve) < 20) {
    $errors[] = 'Por favor describe los problemas que buscas resolver (mínimo 20 caracteres)';
}

if (!in_array($technical_level, ['user', 'advanced', 'developer'])) {
    $errors[] = 'Nivel técnico inválido';
}

if (!empty($errors)) {
    $_SESSION['beta_error'] = implode('. ', $errors);
    redirect('beta/join');
}

// Verificar si el email ya está registrado
$db = Database::getInstance();
$existing = $db->fetchOne("SELECT id FROM beta_testers WHERE email = ?", [$email]);

if ($existing) {
    $_SESSION['beta_error'] = 'Este email ya está registrado como beta tester';
    redirect('beta/join');
}

// Generar token de acceso único
$access_token = bin2hex(random_bytes(32));

// Insertar en la base de datos
try {
    $beta_tester_id = $db->insert('beta_testers', [
        'name' => $name,
        'email' => $email,
        'country' => $country,
        'company' => $company,
        'interested_app' => $interested_app,
        'problems_to_solve' => $problems_to_solve,
        'technical_level' => $technical_level,
        'status' => 'pending',
        'access_token' => $access_token
    ]);

    // Enviar email de bienvenida
    $welcome_email_sent = send_beta_welcome_email($name, $email, $access_token);

    // Log del registro
    log_error("Nuevo Beta Tester registrado: $name ($email) - ID: $beta_tester_id", [
        'beta_tester_id' => $beta_tester_id,
        'interested_app' => $interested_app,
        'technical_level' => $technical_level
    ]);

    $_SESSION['beta_success'] = true;
    redirect('beta/join');

} catch (Exception $e) {
    log_error("Error al registrar beta tester: " . $e->getMessage());
    $_SESSION['beta_error'] = 'Hubo un error al procesar tu registro. Por favor intenta nuevamente.';
    redirect('beta/join');
}

/**
 * Enviar email de bienvenida al nuevo beta tester
 */
function send_beta_welcome_email($name, $email, $access_token) {
    $dashboard_url = SITE_URL . '/beta/dashboard?token=' . $access_token;
    $discord_invite = get_setting('discord_invite_url', '#');
    $telegram_invite = get_setting('telegram_invite_url', '#');

    $subject = '¡Bienvenido al Programa Beta de Guarani App Store! 🚀';

    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
            .btn { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; margin: 10px 5px; }
            .card { background: white; padding: 20px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #667eea; }
            .footer { text-align: center; margin-top: 30px; color: #718096; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🎉 ¡Bienvenido, $name!</h1>
                <p>Ahora eres parte de nuestro equipo de Beta Testers</p>
            </div>

            <div class='content'>
                <h2>¿Qué Sigue Ahora?</h2>

                <div class='card'>
                    <h3>1️⃣ Accede a tu Dashboard</h3>
                    <p>Haz click en el botón para acceder a tu panel exclusivo donde encontrarás todas las apps disponibles para testear:</p>
                    <a href='$dashboard_url' class='btn'>Ir a Mi Dashboard</a>
                </div>

                <div class='card'>
                    <h3>2️⃣ Únete a la Comunidad</h3>
                    <p>Conéctate con otros beta testers y el equipo de desarrollo:</p>
                    <a href='$discord_invite' class='btn'>Unirse a Discord</a>
                    <a href='$telegram_invite' class='btn'>Unirse a Telegram</a>
                </div>

                <div class='card'>
                    <h3>3️⃣ Tus Beneficios</h3>
                    <ul>
                        <li>🎁 <strong>Acceso Gratuito de por Vida</strong> a todas las apps</li>
                        <li>👑 <strong>Features Premium</strong> sin costo</li>
                        <li>🏆 <strong>Reconocimiento</strong> en los créditos de la app</li>
                        <li>💬 <strong>Línea Directa</strong> con desarrolladores</li>
                    </ul>
                </div>

                <div class='card'>
                    <h3>📌 Tu Token de Acceso</h3>
                    <p>Guarda este token en un lugar seguro. Lo necesitarás para acceder:</p>
                    <code style='background: #e2e8f0; padding: 10px; display: block; border-radius: 4px;'>$access_token</code>
                </div>

                <p style='margin-top: 30px;'><strong>¿Cómo Puedes Contribuir?</strong></p>
                <ul>
                    <li>Reporta cualquier bug que encuentres</li>
                    <li>Sugiere mejoras y nuevas features</li>
                    <li>Comparte tu experiencia usando las apps</li>
                    <li>Ayuda a otros testers en la comunidad</li>
                </ul>
            </div>

            <div class='footer'>
                <p>Gracias por ser parte de Guarani App Store</p>
                <p><a href='" . SITE_URL . "'>guaraniappstore.com</a></p>
            </div>
        </div>
    </body>
    </html>
    ";

    return send_email($email, $subject, $message);
}
