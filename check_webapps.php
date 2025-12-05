<?php
/**
 * Script de diagnóstico - Verificar webapps
 */

$configFile = file_get_contents(__DIR__ . '/public_html/config.php');

preg_match("/define\('DB_HOST',\s*'([^']+)'/", $configFile, $host);
preg_match("/define\('DB_NAME',\s*'([^']+)'/", $configFile, $name);
preg_match("/define\('DB_USER',\s*'([^']+)'/", $configFile, $user);
preg_match("/define\('DB_PASS',\s*'([^']+)'/", $configFile, $pass);

if (!isset($host[1]) || !isset($name[1]) || !isset($user[1]) || !isset($pass[1])) {
    die("Error: No se pudieron extraer las credenciales\n");
}

try {
    $pdo = new PDO(
        "mysql:host={$host[1]};dbname={$name[1]};charset=utf8mb4",
        $user[1],
        $pass[1],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "=== WEBAPPS EN LA BASE DE DATOS ===\n\n";

    $stmt = $pdo->query("
        SELECT id, title, slug, status, app_url, logo_url, cover_image_url
        FROM webapps
        ORDER BY id DESC
        LIMIT 10
    ");

    $webapps = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($webapps)) {
        echo "No hay webapps en la base de datos.\n";
    } else {
        foreach ($webapps as $i => $w) {
            echo "--- Webapp " . ($i + 1) . " ---\n";
            echo "ID: " . $w['id'] . "\n";
            echo "Título: " . $w['title'] . "\n";
            echo "Slug: " . $w['slug'] . "\n";
            echo "Estado: " . $w['status'] . "\n";
            echo "URL pública: https://guaraniappstore.com/webapp/{$w['slug']}\n";
            echo "App URL: " . ($w['app_url'] ?: 'N/A') . "\n";
            echo "Logo: " . ($w['logo_url'] ?: 'N/A') . "\n";
            echo "Cover: " . ($w['cover_image_url'] ?: 'N/A') . "\n";
            echo "\n";
        }
    }

    echo "=== TOTAL ===\n";
    $total = $pdo->query("SELECT COUNT(*) FROM webapps")->fetchColumn();
    echo "Total de webapps: {$total}\n\n";

    echo "=== POR ESTADO ===\n";
    $stmt = $pdo->query("
        SELECT status, COUNT(*) as count
        FROM webapps
        GROUP BY status
    ");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo "{$row['status']}: {$row['count']}\n";
    }

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage() . "\n");
}
