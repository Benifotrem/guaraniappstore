#!/usr/bin/env php
<?php
/**
 * Script para actualizar las URLs de screenshots en la base de datos
 * con los nombres correctos de los archivos que existen en el servidor
 */

// Simular variables de servidor
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'guaraniappstore.com';
$_SERVER['REQUEST_URI'] = '/';

ob_start();
require_once __DIR__ . '/public_html/index.php';
ob_end_clean();

$db = Database::getInstance();

// Mapeo correcto: slug => nueva URL de screenshot
$updates = [
    'auto-diagnosis-de-vehiculos' => 'https://guaraniappstore.com/assets/images/webapps/screenshots/screenshot_6938056a2e8ed6.49348456.jpg',
    'guarani-app-store' => 'https://guaraniappstore.com/assets/images/webapps/screenshots/screenshot_693805d2db2578.43557026.jpg',
    'dataflow-plataforma-gestion-analisis-datos' => 'https://guaraniappstore.com/assets/images/webapps/screenshots/screenshot_693805ad263267.73745994.png'
];

echo "=== ACTUALIZANDO SCREENSHOTS EN BASE DE DATOS ===\n\n";

foreach ($updates as $slug => $new_screenshot_url) {
    echo "Procesando: $slug\n";

    // Obtener el registro actual
    $webapp = $db->fetchOne("SELECT id, slug, screenshots FROM webapps WHERE slug = ?", [$slug]);

    if (!$webapp) {
        echo "  ❌ No encontrado\n";
        continue;
    }

    // Crear array con el nuevo screenshot
    $screenshots_array = [$new_screenshot_url];
    $screenshots_json = json_encode($screenshots_array);

    // Actualizar en BD
    $db->query("UPDATE webapps SET screenshots = ? WHERE slug = ?", [
        $screenshots_json,
        $slug
    ]);

    echo "  ✅ Actualizado: $new_screenshot_url\n";
}

echo "\n=== VERIFICACIÓN FINAL ===\n";

// Verificar que se actualizaron correctamente
$result = $db->fetchAll("
    SELECT slug, screenshots
    FROM webapps
    WHERE slug IN ('auto-diagnosis-de-vehiculos', 'guarani-app-store', 'dataflow-plataforma-gestion-analisis-datos')
    ORDER BY slug
");

foreach ($result as $webapp) {
    $screenshots = json_decode($webapp['screenshots'], true);
    echo "\n{$webapp['slug']}:\n";
    if ($screenshots) {
        foreach ($screenshots as $url) {
            echo "  - $url\n";
        }
    }
}

echo "\n✅ Actualización completada\n";
