<?php
/**
 * Sistema de Notificaciones para Beta Testers
 * Maneja emails y notificaciones de Telegram con logs
 */

/**
 * Registrar notificación en logs
 */
function log_notification($beta_tester_id, $type, $channel, $recipient, $subject, $status, $error = null) {
    $db = Database::getInstance();
    $db->insert('notification_logs', [
        'beta_tester_id' => $beta_tester_id,
        'notification_type' => $type,
        'channel' => $channel,
        'recipient' => $recipient,
        'subject' => $subject,
        'status' => $status,
        'error_message' => $error
    ]);
}

/**
 * Enviar email de activación de cuenta
 */
function send_activation_email($tester) {
    $subject = '¡Tu cuenta de Beta Tester ha sido activada! 🎉';
    $dashboard_url = SITE_URL . '/beta/dashboard?token=' . $tester['access_token'];
    
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;'>
                <h1>🎉 ¡Cuenta Activada!</h1>
            </div>
            <div style='background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px;'>
                <p>Hola <strong>{$tester['name']}</strong>,</p>
                <p>¡Excelentes noticias! Tu cuenta de Beta Tester ha sido <strong>activada</strong>.</p>
                <p>Ya puedes acceder a tu dashboard y comenzar a probar nuestras apps:</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='$dashboard_url' style='display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;'>
                        Acceder a Mi Dashboard
                    </a>
                </div>
                <p><strong>También puedes usar nuestro bot de Telegram:</strong></p>
                <p>👉 <a href='https://t.me/guaraniappstore_bot'>@guaraniappstore_bot</a></p>
                <p>Envía /start para vincular tu cuenta.</p>
                <hr style='margin: 20px 0; border: none; border-top: 1px solid #ddd;'>
                <p style='font-size: 0.9em; color: #666;'>¡Gracias por ser parte de nuestra comunidad!</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $result = send_email($tester['email'], $subject, $message);
    
    log_notification(
        $tester['id'],
        'activation',
        'email',
        $tester['email'],
        $subject,
        $result ? 'sent' : 'failed',
        $result ? null : 'Error al enviar email'
    );
    
    return $result;
}

/**
 * Enviar email de cambio de nivel
 */
function send_level_change_email($tester, $old_level, $new_level) {
    $level_names = [
        'bronze' => 'Bronze 🥉',
        'silver' => 'Silver 🥈',
        'gold' => 'Gold 🥇',
        'platinum' => 'Platinum 💎'
    ];
    
    $subject = "¡Has subido a nivel {$level_names[$new_level]}! 🎉";
    $dashboard_url = SITE_URL . '/beta/dashboard?token=' . $tester['access_token'];
    
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;'>
                <h1>🏆 ¡Nuevo Nivel Alcanzado!</h1>
            </div>
            <div style='background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px;'>
                <p>Hola <strong>{$tester['name']}</strong>,</p>
                <p>¡Felicitaciones! Has subido de nivel:</p>
                <div style='text-align: center; margin: 30px 0; padding: 20px; background: white; border-radius: 8px;'>
                    <div style='font-size: 1.2em; color: #666; margin-bottom: 10px;'>
                        {$level_names[$old_level]}
                    </div>
                    <div style='font-size: 2em; margin: 10px 0;'>⬇️</div>
                    <div style='font-size: 1.8em; font-weight: bold; color: #f59e0b;'>
                        {$level_names[$new_level]}
                    </div>
                </div>
                <p><strong>Tus contribuciones hasta ahora:</strong></p>
                <ul>
                    <li>🐛 Bugs reportados: {$tester['bugs_reported']}</li>
                    <li>💡 Sugerencias aceptadas: {$tester['suggestions_accepted']}</li>
                </ul>
                <p>¡Sigue así! Cada aporte mejora nuestras aplicaciones.</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='$dashboard_url' style='display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;'>
                        Ver Mi Dashboard
                    </a>
                </div>
                <hr style='margin: 20px 0; border: none; border-top: 1px solid #ddd;'>
                <p style='font-size: 0.9em; color: #666;'>¡Gracias por tu dedicación! 🚀</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $result = send_email($tester['email'], $subject, $message);
    
    log_notification(
        $tester['id'],
        'level_change',
        'email',
        $tester['email'],
        $subject,
        $result ? 'sent' : 'failed',
        $result ? null : 'Error al enviar email'
    );
    
    return $result;
}

/**
 * Enviar notificación Telegram de cambio de nivel
 */
function send_telegram_level_notification($telegram_id, $tester, $old_level, $new_level) {
    if (!$telegram_id) {
        return false;
    }
    
    $level_icons = [
        'bronze' => '🥉',
        'silver' => '🥈',
        'gold' => '🥇',
        'platinum' => '💎'
    ];
    
    $message = "🎉 *¡Felicitaciones {$tester['name']}!*\n\n";
    $message .= "Has subido de nivel:\n";
    $message .= "{$level_icons[$old_level]} " . ucfirst($old_level) . " → {$level_icons[$new_level]} *" . ucfirst($new_level) . "*\n\n";
    $message .= "📊 *Tus contribuciones:*\n";
    $message .= "🐛 Bugs: {$tester['bugs_reported']}\n";
    $message .= "💡 Sugerencias: {$tester['suggestions_accepted']}\n\n";
    $message .= "¡Sigue así! 🚀";
    
    $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
    $data = [
        'chat_id' => $telegram_id,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $success = $http_code == 200;
    
    log_notification(
        $tester['id'],
        'level_change',
        'telegram',
        $telegram_id,
        'Level change notification',
        $success ? 'sent' : 'failed',
        $success ? null : "HTTP $http_code: $response"
    );
    
    return $success;
}

/**
 * Notificar al admin de nuevo registro
 */
function notify_admin_new_registration($tester) {
    $admin_email = ADMIN_EMAIL ?? 'admin@guaraniappstore.com';
    
    $subject = "🚀 Nuevo Beta Tester registrado: {$tester['name']}";
    
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2>Nuevo Beta Tester</h2>
        <p><strong>Nombre:</strong> {$tester['name']}</p>
        <p><strong>Email:</strong> {$tester['email']}</p>
        <p><strong>Telegram:</strong> @{$tester['telegram_username']}</p>
        <p><strong>País:</strong> {$tester['country']}</p>
        <p><strong>Empresa:</strong> {$tester['company']}</p>
        <hr>
        <p><a href='" . SITE_URL . "/admin/beta-testers'>Ver en Panel Admin</a></p>
    </body>
    </html>
    ";
    
    $result = send_email($admin_email, $subject, $message);
    
    log_notification(
        $tester['id'],
        'admin_alert',
        'email',
        $admin_email,
        $subject,
        $result ? 'sent' : 'failed',
        $result ? null : 'Error al enviar email'
    );
    
    return $result;
}
