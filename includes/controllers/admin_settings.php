<?php
/**
 * Controlador: Admin Settings - Configuración
 */

require_admin_auth();

$db = Database::getInstance();
$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token de seguridad inválido';
    } else {
        // Actualizar configuraciones
        $settings = [
            'site_name' => sanitize_input($_POST['site_name'] ?? ''),
            'site_email' => sanitize_input($_POST['site_email'] ?? ''),
            'site_phone' => sanitize_input($_POST['site_phone'] ?? ''),
            'contact_whatsapp' => sanitize_input($_POST['contact_whatsapp'] ?? ''),
            'openrouter_api_key' => sanitize_input($_POST['openrouter_api_key'] ?? ''),
            'openai_api_key' => sanitize_input($_POST['openai_api_key'] ?? ''),
            'deepseek_model' => sanitize_input($_POST['deepseek_model'] ?? ''),
            'image_generation_model' => sanitize_input($_POST['image_generation_model'] ?? ''),
            'blog_auto_generation_enabled' => isset($_POST['blog_auto_generation_enabled']) ? '1' : '0',
            'blog_generation_interval_days' => (int)($_POST['blog_generation_interval_days'] ?? 2),
            'blog_author_name' => sanitize_input($_POST['blog_author_name'] ?? ''),
        ];

        foreach ($settings as $key => $value) {
            update_setting($key, $value);
        }

        $success = 'Configuración actualizada exitosamente';
    }
}

// Obtener configuraciones actuales
$current_settings = [];
$results = $db->fetchAll("SELECT setting_key, setting_value FROM site_settings");
foreach ($results as $row) {
    $current_settings[$row['setting_key']] = $row['setting_value'];
}

$page_title = 'Configuración';
include INCLUDES_PATH . '/views/admin/layout/header.php';
?>

<h2 class="mb-4">Configuración del Sitio</h2>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo e($success); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo e($error); ?></div>
<?php endif; ?>

<div class="admin-form-container">
    <form method="POST">
        <?php echo csrf_field(); ?>

        <h3 class="mb-3">Información General</h3>
        <div class="admin-form-grid mb-4">
            <div class="form-group">
                <label class="form-label">Nombre del Sitio</label>
                <input type="text" name="site_name" class="form-input"
                       value="<?php echo e($current_settings['site_name'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Email de Contacto</label>
                <input type="email" name="site_email" class="form-input"
                       value="<?php echo e($current_settings['site_email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Teléfono</label>
                <input type="text" name="site_phone" class="form-input"
                       value="<?php echo e($current_settings['site_phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">WhatsApp (sin +)</label>
                <input type="text" name="contact_whatsapp" class="form-input"
                       value="<?php echo e($current_settings['contact_whatsapp'] ?? ''); ?>"
                       placeholder="595981123456">
            </div>
        </div>

        <h3 class="mb-3">Configuración de IA</h3>
        <div class="admin-form-grid mb-4">
            <div class="form-group admin-form-grid-full">
                <label class="form-label">OpenRouter API Key</label>
                <input type="password" name="openrouter_api_key" class="form-input"
                       value="<?php echo e($current_settings['openrouter_api_key'] ?? ''); ?>"
                       placeholder="sk-or-v1-...">
                <small class="form-help">Obtén tu API key en <a href="https://openrouter.ai" target="_blank">openrouter.ai</a></small>
            </div>

            <div class="form-group">
                <label class="form-label">API Key de OpenAI (Directo)</label>
                <input type="password" name="openai_api_key" class="form-input"
                       value="<?php echo e($current_settings['openai_api_key'] ?? ''); ?>"
                       placeholder="sk-...">
                <small class="form-help">Para generar imágenes directamente con OpenAI (GPT-5 Image Mini). Opcional.</small>
            </div>

            <div class="form-group">
                <label class="form-label">Modelo DeepSeek</label>
                <input type="text" name="deepseek_model" class="form-input"
                       value="<?php echo e($current_settings['deepseek_model'] ?? 'deepseek/deepseek-r1'); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Modelo de Generación de Imágenes</label>
                <input type="text" name="image_generation_model" class="form-input"
                       value="<?php echo e($current_settings['image_generation_model'] ?? 'openai/gpt-5-image-mini'); ?>"
                       placeholder="openai/gpt-5-image-mini">
                <small class="form-help">Modelo para generar imágenes. Ejemplos: openai/gpt-5-image-mini, google/gemini-2.5-flash-image</small>
            </div>
        </div>

        <h3 class="mb-3">Configuración del Blog</h3>
        <div class="admin-form-grid mb-4">
            <div class="form-group">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="blog_auto_generation_enabled"
                           <?php echo ($current_settings['blog_auto_generation_enabled'] ?? '0') === '1' ? 'checked' : ''; ?>>
                    <span>Activar generación automática de artículos</span>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label">Intervalo de generación (días)</label>
                <input type="number" name="blog_generation_interval_days" class="form-input"
                       value="<?php echo e($current_settings['blog_generation_interval_days'] ?? 2); ?>"
                       min="1" max="30">
            </div>

            <div class="form-group">
                <label class="form-label">Nombre del Autor</label>
                <input type="text" name="blog_author_name" class="form-input"
                       value="<?php echo e($current_settings['blog_author_name'] ?? 'César Ruzafa'); ?>">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">
            Guardar Configuración
        </button>
    </form>
</div>

<?php include INCLUDES_PATH . '/views/admin/layout/footer.php'; ?>
