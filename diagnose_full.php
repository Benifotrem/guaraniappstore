<?php
/**
 * Diagnóstico completo de conexión MySQL
 */

echo "=== DIAGNÓSTICO COMPLETO DE MYSQL ===\n\n";

// 1. Información PHP
echo "1. INFORMACIÓN PHP:\n";
echo "   PHP Version: " . PHP_VERSION . "\n";
echo "   SAPI: " . php_sapi_name() . "\n";
echo "   PDO Drivers: " . implode(', ', PDO::getAvailableDrivers()) . "\n";
echo "   PDO MySQL extension: " . (extension_loaded('pdo_mysql') ? 'SÍ' : 'NO') . "\n\n";

// 2. Credenciales desde config.php
echo "2. CREDENCIALES DE CONFIG.PHP:\n";
$configFile = file_get_contents(__DIR__ . '/public_html/config.php');
preg_match("/define\('DB_HOST',\s*'([^']+)'/", $configFile, $host);
preg_match("/define\('DB_NAME',\s*'([^']+)'/", $configFile, $name);
preg_match("/define\('DB_USER',\s*'([^']+)'/", $configFile, $user);
preg_match("/define\('DB_PASS',\s*'([^']+)'/", $configFile, $pass);

if (!isset($host[1]) || !isset($name[1]) || !isset($user[1]) || !isset($pass[1])) {
    die("   ✗ Error: No se pudieron extraer las credenciales\n");
}

echo "   DB_HOST: " . $host[1] . "\n";
echo "   DB_NAME: " . $name[1] . "\n";
echo "   DB_USER: " . $user[1] . "\n";
echo "   DB_PASS: " . str_repeat('*', strlen($pass[1])) . "\n\n";

// 3. Test con mysqladmin (si está disponible)
echo "3. TEST CON MYSQLADMIN:\n";
$mysqladmin_test = shell_exec("mysqladmin -u{$user[1]} -p'{$pass[1]}' ping 2>&1");
if ($mysqladmin_test) {
    echo "   " . trim($mysqladmin_test) . "\n\n";
} else {
    echo "   mysqladmin no disponible\n\n";
}

// 4. Intentos de conexión PDO
$connection_methods = [
    'localhost' => "mysql:host=localhost;dbname={$name[1]};charset=utf8mb4",
    '127.0.0.1' => "mysql:host=127.0.0.1;dbname={$name[1]};charset=utf8mb4",
    'localhost con puerto' => "mysql:host=localhost;port=3306;dbname={$name[1]};charset=utf8mb4",
    '127.0.0.1 con puerto' => "mysql:host=127.0.0.1;port=3306;dbname={$name[1]};charset=utf8mb4",
];

// Intentar encontrar socket común en Hostinger
$possible_sockets = [
    '/var/run/mysqld/mysqld.sock',
    '/tmp/mysql.sock',
    '/var/lib/mysql/mysql.sock',
    '/usr/local/mysql/var/mysql.sock',
    getenv('HOME') . '/mysql.sock',
];

foreach ($possible_sockets as $socket) {
    if (file_exists($socket)) {
        $connection_methods["socket ($socket)"] = "mysql:unix_socket=$socket;dbname={$name[1]};charset=utf8mb4";
        break;
    }
}

echo "4. INTENTOS DE CONEXIÓN PDO:\n";
$success = false;
$working_dsn = null;

foreach ($connection_methods as $method => $dsn) {
    echo "   Intentando $method...\n";
    try {
        $pdo = new PDO($dsn, $user[1], $pass[1], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        // Test query
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM webapps");
        $result = $stmt->fetch();

        echo "   ✓ ÉXITO! Webapps en DB: " . $result['count'] . "\n";
        $success = true;
        $working_dsn = $dsn;
        break;
    } catch (PDOException $e) {
        echo "   ✗ " . $e->getMessage() . "\n";
    }
}

echo "\n";

// 5. Resultado final
echo "5. RESULTADO:\n";
if ($success) {
    echo "   ✓ Conexión exitosa!\n";
    echo "   DSN que funciona: $working_dsn\n\n";

    // Mostrar solución si no es localhost estándar
    if (strpos($working_dsn, 'localhost') === false || strpos($working_dsn, 'unix_socket') !== false || strpos($working_dsn, 'port=3306') !== false) {
        echo "6. SOLUCIÓN:\n";
        echo "   Actualiza config.php con:\n";
        if (strpos($working_dsn, 'unix_socket') !== false) {
            preg_match('/unix_socket=([^;]+)/', $working_dsn, $socket_match);
            echo "   define('DB_SOCKET', '{$socket_match[1]}');\n";
            echo "   Y en Database.php usa: mysql:unix_socket=" . DB_SOCKET . "\n";
        } else {
            preg_match('/host=([^;]+)/', $working_dsn, $host_match);
            echo "   define('DB_HOST', '{$host_match[1]}');\n";
        }
    }
} else {
    echo "   ✗ No se pudo establecer conexión con ningún método\n";
    echo "   POSIBLES CAUSAS:\n";
    echo "   - MySQL no está corriendo\n";
    echo "   - Credenciales incorrectas\n";
    echo "   - Host de base de datos es remoto (no localhost)\n";
    echo "   - Restricciones de firewall\n\n";

    echo "6. INFORMACIÓN ADICIONAL:\n";
    echo "   Buscar el host correcto en el panel de control de Hostinger:\n";
    echo "   1. Ve a hPanel > Bases de Datos\n";
    echo "   2. Busca 'Información de conexión' o 'Hostname'\n";
    echo "   3. El hostname podría ser algo como: mysql123.hostinger.com\n";
}
