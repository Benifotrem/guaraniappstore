#!/usr/bin/env php
<?php
/**
 * Actualizar logo de autodiagnosis para usar URL de aplicación nativa
 */

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'guaraniappstore.com';
$_SERVER['REQUEST_URI'] = '/';

ob_start();
require_once __DIR__ . '/public_html/index.php';
ob_end_clean();

$db = Database::getInstance();

echo "=== ACTUALIZANDO LOGO DE AUTODIAGNOSIS ===\n\n";

// URL del logo de la aplicación nativa
$new_logo_url = 'https://autodiagnosis.com.py/images/logo.png';

// Actualizar
$db->query("
    UPDATE webapps
    SET logo_url = ?
    WHERE slug = 'auto-diagnosis-de-vehiculos'
", [$new_logo_url]);

echo "✅ Logo actualizado\n";
echo "   URL: $new_logo_url\n\n";

// Verificar
$webapp = $db->fetchOne("
    SELECT slug, title, logo_url
    FROM webapps
    WHERE slug = 'auto-diagnosis-de-vehiculos'
");

echo "=== VERIFICACIÓN ===\n";
echo "Webapp: {$webapp['title']}\n";
echo "Logo: {$webapp['logo_url']}\n";

echo "\n✅ Actualización completada\n";
