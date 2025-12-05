<?php
/**
 * Controlador: Admin Webapps - Crear
 */

require_admin_auth();

$db = Database::getInstance();
$errors = [];
$form_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de seguridad inválido';
    } else {
        // Validar datos
        $form_data = [
            'title' => sanitize_input($_POST['title'] ?? ''),
            'slug' => sanitize_input($_POST['slug'] ?? ''),
            'short_description' => sanitize_input($_POST['short_description'] ?? ''),
            'full_description' => sanitize_input($_POST['full_description'] ?? ''),
            'app_url' => sanitize_input($_POST['app_url'] ?? ''),
            'category' => sanitize_input($_POST['category'] ?? ''),
            'status' => sanitize_input($_POST['status'] ?? 'draft'),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ];

        // Validaciones
        if (empty($form_data['title'])) {
            $errors[] = 'El título es requerido';
        }

        if (empty($form_data['slug'])) {
            $form_data['slug'] = generate_slug($form_data['title']);
        }

        // Verificar slug único
        $existing = $db->fetchOne("SELECT id FROM webapps WHERE slug = ?", [$form_data['slug']]);
        if ($existing) {
            $errors[] = 'El slug ya existe';
        }

        // Tags y tech stack
        $tags = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));
        $tech_stack = array_filter(array_map('trim', explode(',', $_POST['tech_stack'] ?? '')));

        $form_data['tags'] = json_encode($tags);
        $form_data['tech_stack'] = json_encode($tech_stack);

        // URLs de imágenes - Descargar y re-hostear automáticamente
        $logo_url = sanitize_input($_POST['logo_url'] ?? '');
        $cover_url = sanitize_input($_POST['cover_image_url'] ?? '');

        // Descargar y guardar logo localmente si es URL externa
        if (!empty($logo_url)) {
            $rehosted_logo = download_and_rehost_image($logo_url, 'webapps/logos');
            $form_data['logo_url'] = $rehosted_logo ?? $logo_url;
        } else {
            $form_data['logo_url'] = '';
        }

        // Descargar y guardar cover image localmente si es URL externa
        if (!empty($cover_url)) {
            $rehosted_cover = download_and_rehost_image($cover_url, 'webapps/covers');
            $form_data['cover_image_url'] = $rehosted_cover ?? $cover_url;
        } else {
            $form_data['cover_image_url'] = '';
        }

        // Procesar screenshots (múltiples URLs, una por línea)
        $screenshots_input = trim($_POST['screenshots'] ?? '');
        $screenshots_array = [];
        if (!empty($screenshots_input)) {
            $screenshot_urls = array_filter(array_map('trim', explode("\n", $screenshots_input)));
            foreach ($screenshot_urls as $screenshot_url) {
                // Descargar y re-hostear cada screenshot
                $rehosted_screenshot = download_and_rehost_image($screenshot_url, 'webapps/screenshots');
                if ($rehosted_screenshot) {
                    $screenshots_array[] = $rehosted_screenshot;
                }
            }
        }
        $form_data['screenshots'] = json_encode($screenshots_array);

        if (empty($errors)) {
            if ($form_data['status'] === 'published' && empty($form_data['published_at'])) {
                $form_data['published_at'] = date('Y-m-d H:i:s');
            }

            $webapp_id = $db->insert('webapps', $form_data);

            $_SESSION['success'] = 'Webapp creada exitosamente';
            redirect(get_url('admin/webapps'));
        }
    }
}

$page_title = 'Nueva Webapp';
include INCLUDES_PATH . '/views/admin/layout/header.php';
include INCLUDES_PATH . '/views/admin/webapps/form.php';
include INCLUDES_PATH . '/views/admin/layout/footer.php';
