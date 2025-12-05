<?php
/**
 * Script: Notificar nueva webapp a beta testers
 * Uso: php scripts/notify_new_webapp.php <webapp_id>
 *
 * Envía notificación por Telegram a todos los beta testers activos
 * cuando se publica una nueva webapp.
 */

// Cargar configuración
define('APP_LOADED', true);
require_once __DIR__ . '/../public_html/config.php';
require_once INCLUDES_PATH . '/classes/Database.php';
require_once __DIR__ . '/../telegram_bot.php';

// Obtener webapp_id del argumento
$webapp_id = $argv[1] ?? null;

if (!$webapp_id) {
    echo "❌ Error: Debes proporcionar el ID de la webapp\n";
    echo "Uso: php scripts/notify_new_webapp.php <webapp_id>\n";
    exit(1);
}

// Inicializar base de datos
$db = new Database();

// Obtener información de la webapp
$webapp = $db->fetchOne("
    SELECT id, title, short_description, category, app_url
    FROM webapps
    WHERE id = ? AND status = 'published'
", [$webapp_id]);

if (!$webapp) {
    echo "❌ Error: Webapp no encontrada o no está publicada\n";
    exit(1);
}

// Construir mensaje
$message = "🚀 *¡Nueva App Disponible para Testear!*\n\n";
$message .= "📱 *{$webapp['title']}*\n";
$message .= "📁 {$webapp['category']}\n\n";
$message .= "📝 {$webapp['short_description']}\n\n";
$message .= "━━━━━━━━━━━━━━━━\n\n";
$message .= "¿Quieres ser el primero en encontrar bugs?\n";
$message .= "🐛 Usa /bug para reportar\n";
$message .= "💡 Usa /feature para sugerir mejoras\n\n";
$message .= "🎯 Cada contribución suma puntos para tu nivel!\n\n";
$message .= "🔗 Acceder a la app:\n{$webapp['app_url']}\n\n";
$message .= "Ver todas las apps:\n" . SITE_URL . "/webapps";

echo "📤 Enviando notificación a beta testers...\n\n";
echo "Webapp: {$webapp['title']}\n";

// Obtener beta testers activos con Telegram
$testers = $db->fetchAll("
    SELECT id, name, telegram_id
    FROM beta_testers
    WHERE status = 'active' AND telegram_id IS NOT NULL
");

$total = count($testers);
echo "Beta testers encontrados: $total\n\n";

if ($total === 0) {
    echo "⚠️ No hay beta testers con Telegram vinculado\n";
    exit(0);
}

// Enviar notificación
$sent = 0;
$failed = 0;

foreach ($testers as $tester) {
    echo "Enviando a {$tester['name']}... ";

    $result = sendMessage($tester['telegram_id'], $message, 'Markdown');

    if ($result && isset($result['ok']) && $result['ok']) {
        echo "✅\n";
        $sent++;
    } else {
        echo "❌\n";
        $failed++;

        // Log error
        $error = json_encode($result);
        file_put_contents(
            __DIR__ . '/../logs/telegram_notify_errors.log',
            date('Y-m-d H:i:s') . " - Failed to send to {$tester['name']} ({$tester['telegram_id']}): $error\n",
            FILE_APPEND
        );
    }

    // Delay para evitar rate limiting
    usleep(100000); // 100ms
}

echo "\n━━━━━━━━━━━━━━━━\n";
echo "✅ Enviadas: $sent\n";
echo "❌ Fallidas: $failed\n";
echo "📊 Total: $total\n";
echo "━━━━━━━━━━━━━━━━\n\n";

if ($failed > 0) {
    echo "⚠️ Revisa logs/telegram_notify_errors.log para ver detalles de errores\n";
}

echo "✅ Proceso completado!\n";
