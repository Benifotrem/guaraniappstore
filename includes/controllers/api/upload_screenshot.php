<?php
/**
 * API Endpoint: Upload Screenshot
 * Maneja el upload directo de screenshots desde el formulario de admin
 */

// Solo admin puede usar este endpoint
if (!is_admin_logged_in()) {
    http_response_code(403);
    json_response(['error' => 'No autorizado'], 403);
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Método no permitido'], 405);
}

// Validar que se haya enviado un archivo
if (!isset($_FILES['screenshot']) || $_FILES['screenshot']['error'] !== UPLOAD_ERR_OK) {
    $error_msg = 'No se recibió ningún archivo';
    if (isset($_FILES['screenshot']['error'])) {
        switch ($_FILES['screenshot']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error_msg = 'El archivo es demasiado grande';
                break;
            case UPLOAD_ERR_PARTIAL:
                $error_msg = 'El archivo se subió parcialmente';
                break;
            case UPLOAD_ERR_NO_FILE:
                $error_msg = 'No se seleccionó ningún archivo';
                break;
            default:
                $error_msg = 'Error al subir el archivo';
        }
    }
    json_response(['error' => $error_msg], 400);
}

$file = $_FILES['screenshot'];

// Validar tamaño (máximo 10MB)
$max_size = 10 * 1024 * 1024; // 10MB
if ($file['size'] > $max_size) {
    json_response(['error' => 'El archivo no puede superar 10MB'], 400);
}

// Validar tipo de archivo usando getimagesize
$image_info = @getimagesize($file['tmp_name']);
if ($image_info === false) {
    json_response(['error' => 'El archivo no es una imagen válida'], 400);
}

// Validar MIME type
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($image_info['mime'], $allowed_types)) {
    json_response(['error' => 'Tipo de imagen no permitido. Use JPG, PNG, GIF o WEBP'], 400);
}

// Determinar extensión
$extension = 'jpg';
switch ($image_info['mime']) {
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
}

// Crear directorio si no existe
$upload_dir = PUBLIC_PATH . '/assets/images/webapps/screenshots';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Generar nombre único
$filename = uniqid('screenshot_', true) . '.' . $extension;
$filepath = $upload_dir . '/' . $filename;

// Mover archivo subido
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    json_response(['error' => 'Error al guardar el archivo'], 500);
}

// Optimizar imagen automáticamente (redimensionar y comprimir)
$original_size = filesize($filepath);
$optimized = optimize_webapp_image($filepath, 1200, 800, 85);

// Obtener tamaño optimizado
$optimized_size = filesize($filepath);
$size_reduction = $optimized ? round((($original_size - $optimized_size) / $original_size) * 100, 1) : 0;

// Generar URL
$url = ASSETS_URL . '/images/webapps/screenshots/' . $filename;

// Responder con la URL
json_response([
    'success' => true,
    'url' => $url,
    'filename' => $filename,
    'original_size' => $original_size,
    'optimized_size' => $optimized_size,
    'size_reduction_percent' => $size_reduction,
    'type' => $image_info['mime'],
    'optimized' => $optimized
], 200);
