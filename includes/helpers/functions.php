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
    if (!SMTP_ENABLED) {
        return false;
    }

    // Aquí se implementaría el envío real con PHPMailer o similar
    // Por ahora retornamos true para desarrollo
    return true;
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
 *
 * @param string $imageUrl URL de la imagen externa
 * @param string $subfolder Subcarpeta donde guardar (ej: 'webapps', 'blog')
 * @return string|null URL local de la imagen o null si falla
 */
function download_and_rehost_image($imageUrl, $subfolder = 'webapps') {
    if (empty($imageUrl) || !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        return null;
    }

    // Si ya es una URL local, devolverla tal cual
    if (strpos($imageUrl, SITE_URL) === 0 || strpos($imageUrl, ASSETS_URL) === 0) {
        return $imageUrl;
    }

    try {
        // Descargar imagen
        $ch = curl_init($imageUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            CURLOPT_REFERER => $imageUrl,  // Algunos servicios requieren referer
        ]);

        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($httpCode !== 200 || empty($imageData)) {
            log_error("No se pudo descargar imagen: HTTP $httpCode - $imageUrl");
            return null;
        }

        // Validar que sea una imagen
        if (!preg_match('/^image\//i', $contentType)) {
            log_error("URL no es una imagen válida: $contentType - $imageUrl");
            return null;
        }

        // Determinar extensión
        $extension = 'jpg';  // Por defecto
        if (preg_match('/image\/(jpeg|jpg|png|gif|webp)/i', $contentType, $matches)) {
            $extension = strtolower($matches[1]);
            if ($extension === 'jpeg') $extension = 'jpg';
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
            log_error("No se pudo guardar imagen localmente: $filepath");
            return null;
        }

        // Devolver URL local
        $localUrl = ASSETS_URL . '/images/' . $subfolder . '/' . $filename;
        log_info("Imagen re-hosteada exitosamente: $imageUrl -> $localUrl");

        return $localUrl;

    } catch (Exception $e) {
        log_error("Error descargando imagen: " . $e->getMessage());
        return null;
    }
}
