<?php
/**
 * Controller: Beta Recover Token
 * Permite al usuario recuperar su token de acceso por email
 */

$db = Database::getInstance();
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Por favor ingresa tu email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email inválido';
    } else {
        // Buscar beta tester por email
        $tester = $db->fetchOne("SELECT * FROM beta_testers WHERE email = ?", [$email]);
        
        if ($tester) {
            // Enviar email con el token
            $subject = 'Recuperación de Token - Guarani App Store';
            $dashboard_url = SITE_URL . '/beta/dashboard?token=' . $tester['access_token'];
            
            $message = '
            <html>
            <body style="font-family: Arial, sans-serif; line-height: 1.6;">
                <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
                        <h1>🔑 Tu Token de Acceso</h1>
                    </div>
                    <div style="background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px;">
                        <p>Hola <strong>' . htmlspecialchars($tester['name']) . '</strong>,</p>
                        <p>Has solicitado recuperar tu token de acceso al Beta Dashboard.</p>
                        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea;">
                            <p style="margin: 0; font-size: 0.9em; color: #666;">Tu token de acceso es:</p>
                            <p style="font-family: monospace; font-size: 14px; background: #f1f1f1; padding: 12px; border-radius: 6px; word-break: break-all; margin: 10px 0;">
                                ' . htmlspecialchars($tester['access_token']) . '
                            </p>
                        </div>
                        <p><strong>O accede directamente:</strong></p>
                        <div style="text-align: center; margin: 30px 0;">
                            <a href="' . $dashboard_url . '" style="display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                                Acceder a Mi Dashboard
                            </a>
                        </div>
                        <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">
                        <p style="font-size: 0.85em; color: #666;">
                            <strong>Consejo:</strong> Guarda este email para futuros accesos o marca la opción "Recordar mi sesión" al hacer login.
                        </p>
                    </div>
                </div>
            </body>
            </html>
            ';
            
            $email_sent = send_email($tester['email'], $subject, $message);
            
            if ($email_sent) {
                log_notification(
                    $tester['id'],
                    'registration',
                    'email',
                    $tester['email'],
                    $subject,
                    'sent'
                );
                $success = 'Te hemos enviado un email con tu token de acceso. Revisa tu bandeja de entrada.';
            } else {
                $error = 'Hubo un error al enviar el email. Por favor intenta nuevamente.';
            }
        } else {
            // Por seguridad, no revelar si el email existe o no
            $success = 'Si el email está registrado, recibirás un mensaje con tu token de acceso.';
        }
    }
}

// Renderizar vista
require_once INCLUDES_PATH . '/views/beta/recover-token.php';
