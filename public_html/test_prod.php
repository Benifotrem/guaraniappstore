<?php
/**
 * Test para verificar el entorno
 */
echo "<h1>Test de Entorno</h1>";
echo "<p><strong>Hostname:</strong> " . gethostname() . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</p>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Current File:</strong> " . __FILE__ . "</p>";

// Test database connection
define('APP_LOADED', true);
require_once __DIR__ . '/config.php';

echo "<h2>Test de Base de Datos</h2>";

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM webapps");
    $result = $stmt->fetch();

    echo "<p style='color: green;'><strong>✓ Conexión exitosa!</strong></p>";
    echo "<p>Webapps en DB: " . $result['count'] . "</p>";

    echo "<h3>Información de conexión:</h3>";
    echo "<p>DB_HOST: " . DB_HOST . "</p>";
    echo "<p>DB_NAME: " . DB_NAME . "</p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>✗ Error de conexión:</strong></p>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";

    echo "<h3>Información de debug:</h3>";
    echo "<p>DB_HOST: " . DB_HOST . "</p>";
    echo "<p>DB_NAME: " . DB_NAME . "</p>";
    echo "<p>Error Code: " . $e->getCode() . "</p>";
}

echo "<hr><p><small>Elimina este archivo después de verificar</small></p>";
