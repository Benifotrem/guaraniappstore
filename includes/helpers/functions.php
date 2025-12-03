<?php
/**
 * FUNCIONES AUXILIARES GLOBALES
 */

/**
 * Sanitizar input de usuario
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validar email
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generar token aleatorio seguro
 */
function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Hash de contraseña
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verificar contraseña
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generar slug amigable para URLs
 */
function generate_slug($text) {
    // Convertir a minúsculas
    $text = strtolower($text);

    // Reemplazar caracteres especiales
    $replacements = [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'ñ' => 'n', 'ü' => 'u',
        'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
    ];
    $text = strtr($text, $replacements);

    // Eliminar caracteres no alfanuméricos
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);

    // Reemplazar espacios y guiones múltiples
    $text = preg_replace('/[\s-]+/', '-', $text);

    // Eliminar guiones del principio y final
    $text = trim($text, '-');

    return $text;
}

/**
 * Formatear fecha en español
 */
function format_date_es($date, $format = 'long') {
    $timestamp = is_numeric($date) ? $date : strtotime($date);

    $months = [
        1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    $days = [
        0 => 'Domingo', 'Lunes', 'Martes', 'Miércoles',
        'Jueves', 'Viernes', 'Sábado'
    ];

    $day = date('j', $timestamp);
    $month = $months[(int)date('n', $timestamp)];
    $year = date('Y', $timestamp);
    $dayName = $days[(int)date('w', $timestamp)];

    if ($format === 'long') {
        return "{$dayName}, {$day} de {$month} de {$year}";
    } elseif ($format === 'short') {
        return "{$day} de {$month} de {$year}";
    } else {
        return "{$day}/{$month}/{$year}";
    }
}

/**
 * Calcular tiempo transcurrido (ej: "hace 2 horas")
 */
function time_ago($datetime) {
    $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
    $difference = time() - $timestamp;

    if ($difference < 60) {
        return 'Hace unos segundos';
    } elseif ($difference < 3600) {
        $mins = floor($difference / 60);
        return "Hace {$mins} " . ($mins == 1 ? 'minuto' : 'minutos');
    } elseif ($difference < 86400) {
        $hours = floor($difference / 3600);
        return "Hace {$hours} " . ($hours == 1 ? 'hora' : 'horas');
    } elseif ($difference < 604800) {
        $days = floor($difference / 86400);
        return "Hace {$days} " . ($days == 1 ? 'día' : 'días');
    } else {
        return format_date_es($timestamp, 'short');
    }
}

/**
 * Truncar texto
 */
function truncate_text($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Obtener IP del cliente
 */
function get_client_ip() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
                'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED',
                'REMOTE_ADDR'];

    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }

    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Redirigir a una URL
 */
function redirect($url, $permanent = false) {
    if (!preg_match('/^https?:\/\//', $url)) {
        $url = get_url($url);
    }
    $status_code = $permanent ? 301 : 302;
    header("Location: {$url}", true, $status_code);
    exit;
}
/**
 * Obtener URL base del sitio
 */
function get_base_url() {
    return SITE_URL;
}

/**
 * Obtener URL de ruta
 */
function get_url($route = '') {
    return SITE_URL . '/' . ltrim($route, '/');
}

/**
 * Incluir vista
 */
function render_view($view, $data = []) {
    extract($data);
    $view_file = INCLUDES_PATH . '/views/' . $view . '.php';

    if (file_exists($view_file)) {
        include $view_file;
    } else {
        die("Vista no encontrada: {$view}");
    }
}

/**
 * JSON response
 */
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Verificar si está logueado como admin
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Requerir autenticación admin
 */
function require_admin_auth() {
    if (!is_admin_logged_in()) {
        redirect(get_url('admin/login'));
    }
}

/**
 * Log de errores personalizado
 */
function log_error($message, $context = []) {
    if (!file_exists(LOG_PATH)) {
        mkdir(LOG_PATH, 0755, true);
    }

    $log_message = '[' . date('Y-m-d H:i:s') . '] ' . $message;
    if (!empty($context)) {
        $log_message .= ' | Context: ' . json_encode($context);
    }
    $log_message .= PHP_EOL;

    file_put_contents(ERROR_LOG_FILE, $log_message, FILE_APPEND);
}

/**
 * Escapar HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Obtener configuración de la BD
 */
function get_setting($key, $default = null) {
    static $settings = null;

    if ($settings === null) {
        $db = Database::getInstance();
        $results = $db->fetchAll("SELECT setting_key, setting_value, setting_type FROM site_settings");
        $settings = [];
        foreach ($results as $row) {
            $value = $row['setting_value'];

            // Convertir según el tipo
            if ($row['setting_type'] === 'number') {
                $value = (int)$value;
            } elseif ($row['setting_type'] === 'boolean') {
                $value = (bool)$value;
            } elseif ($row['setting_type'] === 'json') {
                $value = json_decode($value, true);
            }

            $settings[$row['setting_key']] = $value;
        }
    }

    return $settings[$key] ?? $default;
}

/**
 * Actualizar configuración en la BD
 */
function update_setting($key, $value) {
    $db = Database::getInstance();
    return $db->update('site_settings',
        ['setting_value' => $value],
        'setting_key = ?',
        [$key]
    );
}

/**
 * Enviar email
 */
function send_email($to, $subject, $message, $isHTML = true) {
    if (!EMAIL_ENABLED) {
        log_error("Email no enviado - EMAIL_ENABLED = false: $to");
        return false;
    }
    
    $url = 'https://api.brevo.com/v3/smtp/email';
    
    $data = [
        'sender' => [
            'name' => EMAIL_FROM_NAME,
            'email' => EMAIL_FROM_EMAIL
        ],
        'to' => [
            ['email' => $to]
        ],
        'subject' => $subject,
        'htmlContent' => $isHTML ? $message : nl2br($message)
    ];
    
    $headers = [
        'accept: application/json',
        'api-key: ' . BREVO_API_KEY,
        'content-type: application/json'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    log_error("Brevo response - HTTP $http_code - Body: $response - To: $to");    

    if ($http_code >= 200 && $http_code < 300) {
        log_error("✅ Email enviado exitosamente a: $to");
        return true;
    } else {
        log_error("❌ Error enviando email a $to - HTTP $http_code - Response: $response");
        return false;
    }
}
/**
 * Formatear número con separadores
 */
function format_number($number) {
    return number_format($number, 0, ',', '.');
}

/**
 * Validar CSRF token
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generar CSRF token
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generate_token(32);
    }
    return $_SESSION['csrf_token'];
}

/**
 * Obtener token CSRF como input hidden
 */
function csrf_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Descargar imagen de URL externa y re-hostearla localmente
 * Soluciona problemas de hotlinking bloqueado
 * VERSIÓN MEJORADA: Con reintentos, múltiples user-agents y validación robusta
 *
 * @param string $imageUrl URL de la imagen externa
 * @param string $subfolder Subcarpeta donde guardar (ej: 'webapps', 'blog')
 * @param int $maxRetries Número máximo de reintentos
 * @return string|null URL local de la imagen o null si falla
 */
function download_and_rehost_image($imageUrl, $subfolder = 'webapps', $maxRetries = 3) {
    if (empty($imageUrl) || !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        return null;
    }

    // Si ya es una URL local, devolverla tal cual
    if (strpos($imageUrl, SITE_URL) === 0 || strpos($imageUrl, ASSETS_URL) === 0) {
        return $imageUrl;
    }

    // Lista de User-Agents para rotar (algunos servicios bloquean por User-Agent)
    $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    ];

    $attempt = 0;
    $lastError = '';

    while ($attempt < $maxRetries) {
        $attempt++;

        try {
            // Rotar User-Agent en cada intento
            $userAgent = $userAgents[($attempt - 1) % count($userAgents)];

            // Configuración más permisiva en intentos posteriores
            $sslVerify = ($attempt === 1); // Solo verificar SSL en el primer intento

            $ch = curl_init($imageUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_SSL_VERIFYPEER => $sslVerify,
                CURLOPT_SSL_VERIFYHOST => $sslVerify ? 2 : 0,
                CURLOPT_USERAGENT => $userAgent,
                CURLOPT_REFERER => $imageUrl,
                CURLOPT_ENCODING => '', // Aceptar cualquier encoding
                CURLOPT_MAXREDIRS => 5,
            ]);

            $imageData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Validar respuesta HTTP
            if ($httpCode !== 200) {
                $lastError = "HTTP $httpCode";
                if (!empty($curlError)) {
                    $lastError .= " - $curlError";
                }

                if ($attempt < $maxRetries) {
                    sleep(1); // Esperar 1 segundo antes de reintentar
                    continue;
                }
                log_error("No se pudo descargar imagen después de $maxRetries intentos: $lastError - $imageUrl");
                return null;
            }

            // Validar que haya datos
            if (empty($imageData)) {
                $lastError = "Datos vacíos";
                if ($attempt < $maxRetries) {
                    sleep(1);
                    continue;
                }
                log_error("Imagen descargada está vacía: $imageUrl");
                return null;
            }

            // Validación robusta: verificar que sea realmente una imagen usando getimagesizefromstring
            $imageInfo = @getimagesizefromstring($imageData);
            if ($imageInfo === false) {
                $lastError = "Datos no son una imagen válida (getimagesizefromstring falló)";
                if ($attempt < $maxRetries) {
                    sleep(1);
                    continue;
                }
                log_error("Datos descargados no son una imagen válida: $imageUrl");
                return null;
            }

            // Determinar extensión desde los datos reales de la imagen
            $mimeType = $imageInfo['mime'] ?? '';
            $extension = 'jpg'; // Por defecto

            switch ($mimeType) {
                case 'image/jpeg':
                    $extension = 'jpg';
                    break;
                case 'image/png':
                    $extension = 'png';
                    break;
                case 'image/gif':
                    $extension = 'gif';
                    break;
                case 'image/webp':
                    $extension = 'webp';
                    break;
                default:
                    // Intentar desde Content-Type como fallback
                    if (preg_match('/image\/(jpeg|jpg|png|gif|webp)/i', $contentType, $matches)) {
                        $extension = strtolower($matches[1]);
                        if ($extension === 'jpeg') $extension = 'jpg';
                    }
            }

            // Crear directorio si no existe
            $uploadDir = PUBLIC_PATH . '/assets/images/' . $subfolder;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generar nombre único
            $filename = uniqid('img_', true) . '.' . $extension;
            $filepath = $uploadDir . '/' . $filename;

            // Guardar imagen
            if (file_put_contents($filepath, $imageData) === false) {
                $lastError = "No se pudo escribir archivo en disco";
                if ($attempt < $maxRetries) {
                    sleep(1);
                    continue;
                }
                log_error("No se pudo guardar imagen localmente: $filepath");
                return null;
            }

            // Optimizar imagen automáticamente
            optimize_webapp_image($filepath, 1200, 800, 85);

            // Devolver URL local
            $localUrl = ASSETS_URL . '/images/' . $subfolder . '/' . $filename;

            // Log de éxito (solo si hubo más de un intento)
            if ($attempt > 1) {
                log_error("Imagen re-hosteada exitosamente en intento $attempt: $imageUrl -> $localUrl");
            }

            return $localUrl;

        } catch (Exception $e) {
            $lastError = $e->getMessage();
            if ($attempt < $maxRetries) {
                sleep(1);
                continue;
            }
            log_error("Error descargando imagen después de $maxRetries intentos: " . $e->getMessage() . " - $imageUrl");
            return null;
        }
    }

    // Si llegamos aquí, todos los intentos fallaron
    log_error("Falló descarga de imagen después de $maxRetries intentos. Último error: $lastError - $imageUrl");
    return null;
}

/**
 * Optimiza y redimensiona una imagen para las tarjetas de webapp
 *
 * @param string $imagePath Ruta del archivo de imagen
 * @param int $maxWidth Ancho máximo
 * @param int $maxHeight Alto máximo
 * @param int $quality Calidad de compresión (1-100)
 * @return bool True si se optimizó correctamente
 */
function optimize_webapp_image($imagePath, $maxWidth = 1200, $maxHeight = 800, $quality = 85) {
    if (!file_exists($imagePath)) {
        return false;
    }

    // Obtener información de la imagen
    $imageInfo = @getimagesize($imagePath);
    if ($imageInfo === false) {
        return false;
    }

    list($width, $height, $type) = $imageInfo;

    // Si la imagen ya es más pequeña que el máximo, solo optimizar calidad
    if ($width <= $maxWidth && $height <= $maxHeight) {
        // Solo recomprimir si es JPEG o PNG grande
        $fileSize = filesize($imagePath);
        if ($fileSize < 500000) { // Menos de 500KB
            return true; // Ya está optimizada
        }
    }

    // Calcular nuevas dimensiones manteniendo aspect ratio
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    if ($ratio >= 1) {
        $ratio = 1; // No agrandar imágenes pequeñas
    }

    $newWidth = round($width * $ratio);
    $newHeight = round($height * $ratio);

    // Crear imagen desde el archivo original
    $source = null;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = @imagecreatefromjpeg($imagePath);
            break;
        case IMAGETYPE_PNG:
            $source = @imagecreatefrompng($imagePath);
            break;
        case IMAGETYPE_GIF:
            $source = @imagecreatefromgif($imagePath);
            break;
        case IMAGETYPE_WEBP:
            if (function_exists('imagecreatefromwebp')) {
                $source = @imagecreatefromwebp($imagePath);
            }
            break;
    }

    if ($source === null || $source === false) {
        return false;
    }

    // Crear imagen redimensionada
    $destination = imagecreatetruecolor($newWidth, $newHeight);

    // Preservar transparencia para PNG y GIF
    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
        imagealphablending($destination, false);
        imagesavealpha($destination, true);
        $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
        imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
    }

    // Redimensionar con alta calidad
    imagecopyresampled(
        $destination, $source,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        $width, $height
    );

    // Guardar imagen optimizada
    $success = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $success = imagejpeg($destination, $imagePath, $quality);
            break;
        case IMAGETYPE_PNG:
            // PNG quality is 0-9, convertir de 0-100
            $pngQuality = round((100 - $quality) / 11.111);
            $success = imagepng($destination, $imagePath, $pngQuality);
            break;
        case IMAGETYPE_GIF:
            $success = imagegif($destination, $imagePath);
            break;
        case IMAGETYPE_WEBP:
            if (function_exists('imagewebp')) {
                $success = imagewebp($destination, $imagePath, $quality);
            }
            break;
    }

    // Liberar memoria
    imagedestroy($source);
    imagedestroy($destination);

    return $success;
}
