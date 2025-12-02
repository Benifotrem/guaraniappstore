<?php
/**
 * Controlador: Admin Webapps - Editar
 */

require_admin_auth();

$db = Database::getInstance();
$webapp_id = (int)($_GET['id'] ?? 0);

if (!$webapp_id) {
    redirect(get_url('admin/webapps'));
}

$webapp = $db->fetchOne("SELECT * FROM webapps WHERE id = ?", [$webapp_id]);

if (!$webapp) {
    $_SESSION['error'] = 'Webapp no encontrada';
    redirect(get_url('admin/webapps'));
}

$errors = [];
$form_data = $webapp;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de seguridad inválido';
    } else {
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

        if (empty($form_data['title'])) {
            $errors[] = 'El título es requerido';
        }

        if (empty($form_data['slug'])) {
            $form_data['slug'] = generate_slug($form_data['title']);
        }

        // Verificar slug único (excepto este registro)
        $existing = $db->fetchOne("SELECT id FROM webapps WHERE slug = ? AND id != ?",
            [$form_data['slug'], $webapp_id]);
        if ($existing) {
            $errors[] = 'El slug ya existe';
        }

        $tags = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));
        $tech_stack = array_filter(array_map('trim', explode(',', $_POST['tech_stack'] ?? '')));

        $form_data['tags'] = json_encode($tags);
        $form_data['tech_stack'] = json_encode($tech_stack);

        // URLs de imágenes - Descargar y re-hostear automáticamente
        $logo_url = sanitize_input($_POST['logo_url'] ?? '');
        $cover_url = sanitize_input($_POST['cover_image_url'] ?? '');

        // Solo re-hostear si la URL cambió
        if (!empty($logo_url) && $logo_url !== $webapp['logo_url']) {
            $rehosted_logo = download_and_rehost_image($logo_url, 'webapps/logos');
            $form_data['logo_url'] = $rehosted_logo ?? $logo_url;
        } else {
            $form_data['logo_url'] = $logo_url;
        }

        if (!empty($cover_url) && $cover_url !== $webapp['cover_image_url']) {
            $rehosted_cover = download_and_rehost_image($cover_url, 'webapps/covers');
            $form_data['cover_image_url'] = $rehosted_cover ?? $cover_url;
        } else {
            $form_data['cover_image_url'] = $cover_url;
        }

        // Procesar screenshots (múltiples URLs, una por línea)
        $screenshots_input = trim($_POST['screenshots'] ?? '');
        $screenshots_array = [];
        if (!empty($screenshots_input)) {
            $screenshot_urls = array_filter(array_map('trim', explode("\n", $screenshots_input)));

            // Comparar con screenshots actuales para solo descargar los nuevos
            $existing_screenshots = json_decode($webapp['screenshots'] ?? '[]', true);
            $existing_screenshots = is_array($existing_screenshots) ? $existing_screenshots : [];

            foreach ($screenshot_urls as $screenshot_url) {
                // Si ya está en la lista de existentes y es una URL local, mantenerla
                if (in_array($screenshot_url, $existing_screenshots) &&
                    (strpos($screenshot_url, SITE_URL) === 0 || strpos($screenshot_url, ASSETS_URL) === 0)) {
                    $screenshots_array[] = $screenshot_url;
                } else {
                    // Descargar y re-hostear screenshot nueva o externa
                    $rehosted_screenshot = download_and_rehost_image($screenshot_url, 'webapps/screenshots');
                    if ($rehosted_screenshot) {
                        $screenshots_array[] = $rehosted_screenshot;
                    }
                }
            }
        }
        $form_data['screenshots'] = json_encode($screenshots_array);

        if (empty($errors)) {
            if ($form_data['status'] === 'published' && empty($webapp['published_at'])) {
                $form_data['published_at'] = date('Y-m-d H:i:s');
            }

            $db->update('webapps', $form_data, 'id = ?', [$webapp_id]);

            $_SESSION['success'] = 'Webapp actualizada exitosamente';
            redirect(get_url('admin/webapps'));
        }
    }
}

$page_title = 'Editar Webapp';
include INCLUDES_PATH . '/views/admin/layout/header.php';
include INCLUDES_PATH . '/views/admin/webapps/form.php';
include INCLUDES_PATH . '/views/admin/layout/footer.php';
