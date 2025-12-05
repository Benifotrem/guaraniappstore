<?php
/**
 * Script de diagnóstico - Verificar últimos artículos generados
 */

// Leer credenciales del config.php sin ejecutarlo
$configFile = file_get_contents(__DIR__ . '/public_html/config.php');

// Extraer credenciales de DB
preg_match("/define\('DB_HOST',\s*'([^']+)'/", $configFile, $host);
preg_match("/define\('DB_NAME',\s*'([^']+)'/", $configFile, $name);
preg_match("/define\('DB_USER',\s*'([^']+)'/", $configFile, $user);
preg_match("/define\('DB_PASS',\s*'([^']+)'/", $configFile, $pass);

if (!isset($host[1]) || !isset($name[1]) || !isset($user[1]) || !isset($pass[1])) {
    die("Error: No se pudieron extraer las credenciales de la base de datos\n");
}

try {
    $pdo = new PDO(
        "mysql:host={$host[1]};dbname={$name[1]};charset=utf8mb4",
        $user[1],
        $pass[1],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "=== ÚLTIMOS 3 ARTÍCULOS EN LA BASE DE DATOS ===\n\n";

    $stmt = $pdo->query("
        SELECT id, title, status, author_name,
               is_auto_generated,
               DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at
        FROM blog_articles
        ORDER BY created_at DESC
        LIMIT 3
    ");

    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($articles)) {
        echo "No hay artículos en la base de datos.\n";
    } else {
        foreach ($articles as $i => $article) {
            echo "--- Artículo " . ($i + 1) . " ---\n";
            echo "ID: " . $article['id'] . "\n";
            echo "Título: " . $article['title'] . "\n";
            echo "Estado: " . $article['status'] . "\n";
            echo "Autor: " . $article['author_name'] . "\n";
            echo "Auto-generado: " . ($article['is_auto_generated'] ? 'Sí' : 'No') . "\n";
            echo "Creado: " . $article['created_at'] . "\n";
            echo "\n";
        }
    }

    echo "=== TOTAL DE ARTÍCULOS ===\n";
    $total = $pdo->query("SELECT COUNT(*) FROM blog_articles")->fetchColumn();
    echo "Total de artículos: {$total}\n\n";

    echo "=== ARTÍCULOS AUTO-GENERADOS ===\n";
    $autoGen = $pdo->query("SELECT COUNT(*) FROM blog_articles WHERE is_auto_generated = 1")->fetchColumn();
    echo "Total auto-generados: {$autoGen}\n\n";

    echo "=== ARTÍCULOS POR ESTADO ===\n";
    $stmt = $pdo->query("
        SELECT status, COUNT(*) as count
        FROM blog_articles
        GROUP BY status
    ");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo "{$row['status']}: {$row['count']}\n";
    }

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage() . "\n");
}
