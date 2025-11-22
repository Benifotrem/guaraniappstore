<?php
/**
 * Controlador: Admin Blog - Crear artículo
 */

require_admin_auth();

$db = Database::getInstance();
$errors = [];
$form_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de seguridad inválido';
    } else {
        $form_data = [
            'title' => sanitize_input($_POST['title'] ?? ''),
            'slug' => sanitize_input($_POST['slug'] ?? ''),
            'excerpt' => sanitize_input($_POST['excerpt'] ?? ''),
            'content' => $_POST['content'] ?? '', // No sanitizar HTML
            'author_name' => sanitize_input($_POST['author_name'] ?? get_setting('blog_author_name', 'César Ruzafa')),
            'category' => sanitize_input($_POST['category'] ?? ''),
            'status' => sanitize_input($_POST['status'] ?? 'draft'),
            'featured_image_url' => sanitize_input($_POST['featured_image_url'] ?? ''),
            'seo_title' => sanitize_input($_POST['seo_title'] ?? ''),
            'seo_description' => sanitize_input($_POST['seo_description'] ?? ''),
            'is_auto_generated' => 0,
        ];

        if (empty($form_data['title'])) {
            $errors[] = 'El título es requerido';
        }

        if (empty($form_data['slug'])) {
            $form_data['slug'] = generate_slug($form_data['title']);
        }

        // Verificar slug único
        $existing = $db->fetchOne("SELECT id FROM blog_articles WHERE slug = ?", [$form_data['slug']]);
        if ($existing) {
            $errors[] = 'El slug ya existe';
        }

        if (empty($form_data['content'])) {
            $errors[] = 'El contenido es requerido';
        }

        // Tags
        $tags = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));
        $form_data['tags'] = json_encode($tags);

        if (!empty($form_data['seo_title'])) {
            $form_data['seo_title'] = $form_data['title'];
        }

        if (empty($errors)) {
            if ($form_data['status'] === 'published') {
                $form_data['published_at'] = date('Y-m-d H:i:s');
            }

            $article_id = $db->insert('blog_articles', $form_data);

            $_SESSION['success'] = 'Artículo creado exitosamente';
            redirect(get_url('admin/blog'));
        }
    }
}

$page_title = 'Nuevo Artículo';
include INCLUDES_PATH . '/views/admin/layout/header.php';
include INCLUDES_PATH . '/views/admin/blog/form.php';
include INCLUDES_PATH . '/views/admin/layout/footer.php';
