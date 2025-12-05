<?php
/**
 * Controller: Procesar envío de feedback
 * Ruta: /feedback/submit
 */

// Solo aceptar POST y AJAX
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Headers para JSON
header('Content-Type: application/json');

// Validar CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
    exit;
}

// Recoger datos del formulario
$webapp_id = filter_input(INPUT_POST, 'webapp_id', FILTER_VALIDATE_INT);
$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$severity = $_POST['severity'] ?? null;
$rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);

// Validaciones
$errors = [];

if (!$webapp_id) {
    $errors[] = 'ID de webapp inválido';
}

if (!in_array($type, ['bug', 'feature', 'review'])) {
    $errors[] = 'Tipo de feedback inválido';
}

if (empty($title) || strlen($title) > 255) {
    $errors[] = 'El título es requerido y debe tener máximo 255 caracteres';
}

if (empty($description) || strlen($description) < 20) {
    $errors[] = 'La descripción debe tener al menos 20 caracteres';
}

// Validaciones específicas por tipo
if ($type === 'bug') {
    if (!in_array($severity, ['low', 'medium', 'high', 'critical'])) {
        $severity = 'medium';  // Default
    }
}

if ($type === 'review') {
    if (!$rating || $rating < 1 || $rating > 5) {
        $errors[] = 'La calificación debe estar entre 1 y 5';
    }
}

if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => implode('. ', $errors)
    ]);
    exit;
}

try {
    // Verificar que la webapp existe
    $webapp = $db->fetchOne("SELECT id, title FROM webapps WHERE id = ?", [$webapp_id]);

    if (!$webapp) {
        echo json_encode([
            'success' => false,
            'message' => 'La aplicación especificada no existe'
        ]);
        exit;
    }

    // Buscar beta tester por email (si se proporcionó)
    $beta_tester_id = null;
    if ($email) {
        $beta_tester = $db->fetchOne("SELECT id, name FROM beta_testers WHERE email = ? AND status = 'active'", [$email]);
        if ($beta_tester) {
            $beta_tester_id = $beta_tester['id'];
        }
    }

    // Recoger información del navegador
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $browser_info = get_browser_info($user_agent);

    // Manejar screenshot si se subió
    $screenshot_url = null;
    if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
        $screenshot_url = handle_screenshot_upload($_FILES['screenshot'], $webapp_id);
    }

    // Insertar feedback en la base de datos
    $feedback_data = [
        'webapp_id' => $webapp_id,
        'beta_tester_id' => $beta_tester_id,
        'type' => $type,
        'title' => $title,
        'description' => $description,
        'severity' => $type === 'bug' ? $severity : null,
        'rating' => $type === 'review' ? $rating : null,
        'status' => 'new',
        'user_agent' => $user_agent,
        'browser_info' => $browser_info,
        'screenshot_url' => $screenshot_url
    ];

    $feedback_id = $db->insert('feedback_reports', $feedback_data);

    if ($feedback_id) {
        // Si es de un beta tester activo, incrementar contador de bugs reportados
        if ($beta_tester_id && $type === 'bug') {
            $db->query("UPDATE beta_testers SET bugs_reported = bugs_reported + 1 WHERE id = ?", [$beta_tester_id]);

            // Actualizar nivel de contribución si corresponde
            update_contribution_level($beta_tester_id);
        }

        // Enviar notificación a admin (opcional)
        if (defined('ADMIN_EMAIL') && ADMIN_EMAIL) {
            send_feedback_notification_to_admin($feedback_id, $feedback_data, $webapp['title']);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Feedback enviado exitosamente. ¡Gracias por tu contribución!',
            'feedback_id' => $feedback_id
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al guardar el feedback. Por favor intenta de nuevo.'
        ]);
    }

} catch (Exception $e) {
    error_log("Error al procesar feedback: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor. Por favor intenta más tarde.'
    ]);
}

/**
 * Obtener información básica del navegador
 */
function get_browser_info($user_agent) {
    if (!$user_agent) return null;

    $browser = 'Unknown';
    $version = '';

    if (preg_match('/MSIE|Trident/i', $user_agent)) {
        $browser = 'Internet Explorer';
    } elseif (preg_match('/Edge/i', $user_agent)) {
        $browser = 'Microsoft Edge';
    } elseif (preg_match('/Chrome/i', $user_agent)) {
        $browser = 'Google Chrome';
    } elseif (preg_match('/Safari/i', $user_agent)) {
        $browser = 'Safari';
    } elseif (preg_match('/Firefox/i', $user_agent)) {
        $browser = 'Mozilla Firefox';
    } elseif (preg_match('/Opera|OPR/i', $user_agent)) {
        $browser = 'Opera';
    }

    return $browser;
}

/**
 * Manejar upload de screenshot
 */
function handle_screenshot_upload($file, $webapp_id) {
    $upload_dir = PUBLIC_PATH . '/assets/images/feedback/';

    // Crear directorio si no existe
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Validar tipo de archivo
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        return null;
    }

    // Validar tamaño (máx 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return null;
    }

    // Generar nombre único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'screenshot_' . $webapp_id . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    $filepath = $upload_dir . $filename;

    // Mover archivo
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Optimizar imagen
        optimize_feedback_image($filepath);

        // Retornar URL relativa
        return '/assets/images/feedback/' . $filename;
    }

    return null;
}

/**
 * Optimizar imagen de feedback
 */
function optimize_feedback_image($filepath) {
    $max_width = 1920;
    $max_height = 1080;
    $quality = 80;

    list($width, $height, $type) = getimagesize($filepath);

    // Si es menor que el máximo, no hacer nada
    if ($width <= $max_width && $height <= $max_height) {
        return;
    }

    // Calcular nuevas dimensiones manteniendo aspect ratio
    $ratio = min($max_width / $width, $max_height / $height);
    $new_width = round($width * $ratio);
    $new_height = round($height * $ratio);

    // Crear imagen según tipo
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($filepath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($filepath);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($filepath);
            break;
        default:
            return;
    }

    // Redimensionar
    $thumb = imagecreatetruecolor($new_width, $new_height);

    // Preservar transparencia para PNG
    if ($type === IMAGETYPE_PNG) {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    }

    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Guardar según tipo
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumb, $filepath, $quality);
            break;
        case IMAGETYPE_PNG:
            imagepng($thumb, $filepath, 9);
            break;
        case IMAGETYPE_GIF:
            imagegif($thumb, $filepath);
            break;
    }

    imagedestroy($source);
    imagedestroy($thumb);
}

/**
 * Actualizar nivel de contribución del beta tester
 */
function update_contribution_level($beta_tester_id) {
    global $db;

    $stats = $db->fetchOne("
        SELECT bugs_reported, suggestions_accepted
        FROM beta_testers
        WHERE id = ?
    ", [$beta_tester_id]);

    if (!$stats) return;

    $total_contributions = $stats['bugs_reported'] + $stats['suggestions_accepted'];

    $new_level = 'bronze';
    if ($total_contributions >= 50) {
        $new_level = 'platinum';
    } elseif ($total_contributions >= 25) {
        $new_level = 'gold';
    } elseif ($total_contributions >= 10) {
        $new_level = 'silver';
    }

    $db->query("UPDATE beta_testers SET contribution_level = ? WHERE id = ?", [$new_level, $beta_tester_id]);
}

/**
 * Enviar notificación de nuevo feedback al admin
 */
function send_feedback_notification_to_admin($feedback_id, $data, $webapp_title) {
    if (!function_exists('send_email')) return;

    $type_labels = [
        'bug' => 'Bug Reportado',
        'feature' => 'Feature Sugerida',
        'review' => 'Review Recibida'
    ];

    $type_label = $type_labels[$data['type']] ?? 'Feedback';

    $subject = "[$type_label] {$data['title']} - $webapp_title";

    $message = "
    <h2>Nuevo Feedback Recibido</h2>
    <p><strong>Aplicación:</strong> $webapp_title</p>
    <p><strong>Tipo:</strong> $type_label</p>
    <p><strong>Título:</strong> {$data['title']}</p>
    <p><strong>Descripción:</strong></p>
    <p>{$data['description']}</p>
    ";

    if ($data['type'] === 'bug' && $data['severity']) {
        $message .= "<p><strong>Severidad:</strong> {$data['severity']}</p>";
    }

    if ($data['type'] === 'review' && $data['rating']) {
        $stars = str_repeat('⭐', $data['rating']);
        $message .= "<p><strong>Calificación:</strong> $stars ({$data['rating']}/5)</p>";
    }

    $message .= "<p><a href='" . BASE_URL . "/admin/feedback/view/$feedback_id'>Ver en Admin Panel</a></p>";

    send_email(ADMIN_EMAIL, $subject, $message);
}
