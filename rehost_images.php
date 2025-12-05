<?php
/**
 * Script para re-hostear todas las imágenes externas de webapps
 */

$configFile = file_get_contents(__DIR__ . '/public_html/config.php');

preg_match("/define\('DB_HOST',\s*'([^']+)'/", $configFile, $host);
preg_match("/define\('DB_NAME',\s*'([^']+)'/", $configFile, $name);
preg_match("/define\('DB_USER',\s*'([^']+)'/", $configFile, $user);
preg_match("/define\('DB_PASS',\s*'([^']+)'/", $configFile, $pass);
preg_match("/define\('ASSETS_URL',\s*'([^']+)'/", $configFile, $assetsUrl);
preg_match("/define\('SITE_URL',\s*'([^']+)'/", $configFile, $siteUrl);

if (!isset($host[1]) || !isset($name[1]) || !isset($user[1]) || !isset($pass[1])) {
    die("Error: No se pudieron extraer las credenciales\n");
}

$ASSETS_URL = $assetsUrl[1] ?? 'https://guaraniappstore.com/assets';
$SITE_URL = $siteUrl[1] ?? 'https://guaraniappstore.com';

// Función para descargar y re-hostear imagen
function download_and_rehost_image($imageUrl, $subfolder = 'webapps') {
    global $ASSETS_URL, $SITE_URL;

    if (empty($imageUrl) || !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        return null;
    }

    // Si ya es una URL local, devolverla tal cual
    if (strpos($imageUrl, $SITE_URL) === 0 || strpos($imageUrl, $ASSETS_URL) === 0) {
        return $imageUrl;
    }

    try {
        echo "  Descargando: $imageUrl ... ";

        // Descargar imagen
        $ch = curl_init($imageUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            CURLOPT_REFERER => $imageUrl,
        ]);

        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($httpCode !== 200 || empty($imageData)) {
            echo "FAIL (HTTP $httpCode)\n";
            return null;
        }

        // Validar que sea una imagen
        if (!preg_match('/^image\//i', $contentType)) {
            echo "FAIL (no es imagen: $contentType)\n";
            return null;
        }

        // Determinar extensión
        $extension = 'jpg';
        if (preg_match('/image\/(jpeg|jpg|png|gif|webp)/i', $contentType, $matches)) {
            $extension = strtolower($matches[1]);
            if ($extension === 'jpeg') $extension = 'jpg';
        }

        // Crear directorio si no existe
        $uploadDir = __DIR__ . '/public_html/assets/images/' . $subfolder;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generar nombre único
        $filename = uniqid('img_', true) . '.' . $extension;
        $filepath = $uploadDir . '/' . $filename;

        // Guardar imagen
        if (file_put_contents($filepath, $imageData) === false) {
            echo "FAIL (no se pudo guardar)\n";
            return null;
        }

        // Devolver URL local
        $localUrl = $ASSETS_URL . '/images/' . $subfolder . '/' . $filename;
        echo "OK → $localUrl\n";

        return $localUrl;

    } catch (Exception $e) {
        echo "FAIL (" . $e->getMessage() . ")\n";
        return null;
    }
}

try {
    $pdo = new PDO(
        "mysql:host={$host[1]};dbname={$name[1]};charset=utf8mb4",
        $user[1],
        $pass[1],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "=== RE-HOSTEANDO IMÁGENES EXTERNAS ===\n\n";

    $stmt = $pdo->query("
        SELECT id, title, logo_url, cover_image_url
        FROM webapps
        WHERE logo_url IS NOT NULL OR cover_image_url IS NOT NULL
    ");

    $webapps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $updated = 0;

    foreach ($webapps as $webapp) {
        echo "Webapp: {$webapp['title']}\n";

        $changes = [];

        // Procesar logo
        if (!empty($webapp['logo_url'])) {
            $newLogo = download_and_rehost_image($webapp['logo_url'], 'webapps/logos');
            if ($newLogo && $newLogo !== $webapp['logo_url']) {
                $changes['logo_url'] = $newLogo;
            }
        }

        // Procesar cover
        if (!empty($webapp['cover_image_url'])) {
            $newCover = download_and_rehost_image($webapp['cover_image_url'], 'webapps/covers');
            if ($newCover && $newCover !== $webapp['cover_image_url']) {
                $changes['cover_image_url'] = $newCover;
            }
        }

        // Actualizar en DB si hay cambios
        if (!empty($changes)) {
            $sets = [];
            $params = [];
            foreach ($changes as $field => $value) {
                $sets[] = "$field = ?";
                $params[] = $value;
            }
            $params[] = $webapp['id'];

            $sql = "UPDATE webapps SET " . implode(', ', $sets) . " WHERE id = ?";
            $pdo->prepare($sql)->execute($params);

            echo "  ✓ Actualizado en base de datos\n";
            $updated++;
        }

        echo "\n";
    }

    echo "=== RESUMEN ===\n";
    echo "Webapps procesadas: " . count($webapps) . "\n";
    echo "Webapps actualizadas: $updated\n";

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage() . "\n");
}
