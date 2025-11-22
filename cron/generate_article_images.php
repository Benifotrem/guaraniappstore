<?php
/**
 * Generar imágenes para artículos que no tienen
 * Ejecutar cada 5 minutos vía cron
 */

// Definir constante de aplicación cargada
define('APP_LOADED', true);

// Cargar configuración
require_once __DIR__ . '/../public_html/config.php';
require_once INCLUDES_PATH . '/helpers/functions.php';

// Obtener artículos sin imagen (máximo 1 por ejecución)
$db = Database::getInstance();
$article = $db->fetchOne("
    SELECT id, title, content 
    FROM blog_articles 
    WHERE (featured_image_url IS NULL OR featured_image_url = '')
    AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
    ORDER BY created_at DESC
    LIMIT 1
");

if (!$article) {
    exit("No hay artículos pendientes de imagen\n");
}

echo "Generando imagen para artículo ID: {$article['id']} - {$article['title']}\n";

try {
    // Generar imagen
    $generator = new BlogGenerator();
    $imageUrl = $generator->generateArticleImagePublic([
        'title' => $article['title'],
        'content' => $article['content']
    ]);

    if ($imageUrl) {
        // Actualizar artículo
        $db->update('blog_articles', [
            'featured_image_url' => $imageUrl
        ], ['id' => $article['id']]);
        
        echo "✓ Imagen generada y guardada: $imageUrl\n";
    } else {
        echo "✗ Error: generateArticleImagePublic retornó NULL\n";
    }
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
