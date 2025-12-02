<?php
/**
 * Test Database Connection - Direct credentials
 * Este script no requiere config.php
 */

echo "=== TEST DE CONEXIÓN A BASE DE DATOS ===\n\n";

// Credenciales directas (copiadas de config.php)
define('DB_HOST', 'localhost');
define('DB_NAME', 'u489458217_Central');
define('DB_USER', 'u489458217_Cesar');
define('DB_PASS', '5;vtVURM&X;d');

echo "1. Verificando extensión PDO MySQL...\n";
if (extension_loaded('pdo_mysql')) {
    echo "   ✓ PDO MySQL está cargada\n\n";
} else {
    echo "   ✗ PDO MySQL NO está cargada - ESTE ES EL PROBLEMA\n\n";
    exit(1);
}

echo "2. Intentando conectar con localhost...\n";
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    echo "   ✓ Conexión exitosa con localhost!\n\n";

    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM webapps");
    $result = $stmt->fetch();
    echo "3. Test de query:\n";
    echo "   ✓ Webapps en DB: " . $result['count'] . "\n\n";

} catch (PDOException $e) {
    echo "   ✗ Error con localhost: " . $e->getMessage() . "\n\n";

    // Intentar con 127.0.0.1
    echo "3. Intentando conectar con 127.0.0.1...\n";
    try {
        $pdo = new PDO(
            "mysql:host=127.0.0.1;dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        echo "   ✓ Conexión exitosa con 127.0.0.1!\n";
        echo "   ℹ SOLUCIÓN: Cambiar DB_HOST de 'localhost' a '127.0.0.1' en config.php\n\n";
    } catch (PDOException $e) {
        echo "   ✗ Error con 127.0.0.1: " . $e->getMessage() . "\n\n";

        // Intentar con socket
        echo "4. Intentando con socket...\n";
        try {
            $pdo = new PDO(
                "mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS
            );
            echo "   ✓ Conexión exitosa con socket!\n\n";
        } catch (PDOException $e) {
            echo "   ✗ Error con socket: " . $e->getMessage() . "\n\n";
        }
    }
}

echo "4. Información del entorno PHP:\n";
echo "   PHP Version: " . PHP_VERSION . "\n";
echo "   SAPI: " . php_sapi_name() . "\n";
echo "   PDO Drivers: " . implode(', ', PDO::getAvailableDrivers()) . "\n";
