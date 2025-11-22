<?php
/**
 * Controlador: Admin Blog - Editar artículo
 */

require_admin_auth();

$db = Database::getInstance();
$article_id = (int)($_GET['id'] ?? 0);

if (!$article_id) {
    redirect(get_url('admin/blog'));
}

$article = $db->fetchOne("SELECT * FROM blog_articles WHERE id = ?", [$article_id]);

if (!$article) {
    $_SESSION['error'] = 'Artículo no encontrado';
    redirect(get_url('admin/blog'));
}

$errors = [];
$form_data = $article;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de seguridad inválido';
    } else {
        $form_data = [
            'title' => sanitize_input($_POST['title'] ?? ''),
            'slug' => sanitize_input($_POST['slug'] ?? ''),
            'excerpt' => sanitize_input($_POST['excerpt'] ?? ''),
            'content' => $_POST['content'] ?? '',
            'author_name' => sanitize_input($_POST['author_name'] ?? get_setting('blog_author_name', 'César Ruzafa')),
            'category' => sanitize_input($_POST['category'] ?? ''),
            'status' => sanitize_input($_POST['status'] ?? 'draft'),
            'featured_image_url' => sanitize_input($_POST['featured_image_url'] ?? ''),
            'seo_title' => sanitize_input($_POST['seo_title'] ?? ''),
            'seo_description' => sanitize_input($_POST['seo_description'] ?? ''),
        ];

        if (empty($form_data['title'])) {
            $errors[] = 'El título es requerido';
        }

        if (empty($form_data['slug'])) {
            $form_data['slug'] = generate_slug($form_data['title']);
        }

        // Verificar slug único (excepto este registro)
        $existing = $db->fetchOne("SELECT id FROM blog_articles WHERE slug = ? AND id != ?",
            [$form_data['slug'], $article_id]);
        if ($existing) {
            $errors[] = 'El slug ya existe';
        }

        // Tags
        $tags = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));
        $form_data['tags'] = json_encode($tags);

        if (empty($errors)) {
            if ($form_data['status'] === 'published' && empty($article['published_at'])) {
                $form_data['published_at'] = date('Y-m-d H:i:s');
            }

            $db->update('blog_articles', $form_data, 'id = ?', [$article_id]);

            $_SESSION['success'] = 'Artículo actualizado exitosamente';
            redirect(get_url('admin/blog'));
        }
    }
}

$page_title = 'Editar Artículo';
include INCLUDES_PATH . '/views/admin/layout/header.php';
include INCLUDES_PATH . '/views/admin/blog/form.php';
include INCLUDES_PATH . '/views/admin/layout/footer.php';
