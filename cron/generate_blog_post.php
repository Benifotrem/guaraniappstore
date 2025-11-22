<?php
/**
 * CRON JOB: Generar artículo de blog automáticamente
 *
 * Ejecutar cada 2 días con cron:
 * 0 10 ASTERISK/2 * * php /ruta/completa/a/cron/generate_blog_post.php
 * Nota: Reemplazar ASTERISK con el símbolo *
 */

// Definir que la app está cargada
define('APP_LOADED', true);

// Cargar configuración
require_once dirname(__DIR__) . '/public_html/config.php';
require_once INCLUDES_PATH . '/helpers/functions.php';

// Log de inicio
$logMessage = "\n" . str_repeat('=', 50) . "\n";
$logMessage .= "CRON JOB: Generación de artículo de blog\n";
$logMessage .= "Fecha: " . date('Y-m-d H:i:s') . "\n";
$logMessage .= str_repeat('=', 50) . "\n";

echo $logMessage;
log_error($logMessage);

try {
    // Verificar si la generación automática está activada
    if (!get_setting('blog_auto_generation_enabled', false)) {
        $msg = "Generación automática desactivada. Abortando.\n";
        echo $msg;
        log_error($msg);
        exit(0);
    }

    // Verificar última generación
    $db = Database::getInstance();
    $lastArticle = $db->fetchOne("
        SELECT published_at
        FROM blog_articles
        WHERE is_auto_generated = 1
        ORDER BY published_at DESC
        LIMIT 1
    ");

    $intervalDays = get_setting('blog_generation_interval_days', 2);

    if ($lastArticle) {
        $daysSinceLastArticle = (time() - strtotime($lastArticle['published_at'])) / 86400;

        if ($daysSinceLastArticle < $intervalDays) {
            $msg = "Último artículo generado hace {$daysSinceLastArticle} días. Esperando {$intervalDays} días. Abortando.\n";
            echo $msg;
            log_error($msg);
            exit(0);
        }
    }

    // Generar artículo
    echo "Iniciando generación de artículo...\n";
    $generator = new BlogGenerator();

    $result = $generator->generateArticle();

    if ($result['success']) {
        echo "✓ Artículo generado exitosamente!\n";
        echo "  ID: {$result['article_id']}\n";
        echo "  Título: {$result['title']}\n";

        log_error("Artículo generado exitosamente. ID: {$result['article_id']}, Título: {$result['title']}");

        // Notificar suscriptores
        echo "Notificando a suscriptores...\n";
        $generator->notifySubscribers($result['article_id']);
        echo "✓ Notificaciones enviadas\n";
    } else {
        echo "✗ Error al generar artículo\n";
        log_error("Error al generar artículo");
        exit(1);
    }

} catch (Exception $e) {
    $error = "ERROR: " . $e->getMessage() . "\n";
    $error .= "Trace: " . $e->getTraceAsString() . "\n";
    echo $error;
    log_error($error);
    exit(1);
}

echo "\nProceso completado exitosamente\n";
echo str_repeat('=', 50) . "\n";
exit(0);
