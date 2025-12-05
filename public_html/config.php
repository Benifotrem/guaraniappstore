<?php
/**
 * ================================================
 * GUARANI APP STORE - CONFIGURATION FILE
 * ================================================
 * Configuración para producción
 */

// Prevenir acceso directo
if (!defined('APP_LOADED')) {
    die('Direct access not permitted');
}

// ================================================
// CONFIGURACIÓN DE BASE DE DATOS
// ================================================
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'u489458217_Central');
define('DB_USER', 'u489458217_Cesar');
define('DB_PASS', 'TU_PASSWORD_REAL_AQUI');  // ⚠️ CAMBIAR POR TU PASSWORD
define('DB_CHARSET', 'utf8mb4');

// ================================================
// CONFIGURACIÓN GENERAL DEL SITIO
// ================================================
define('SITE_NAME', 'Guarani App Store');
define('SITE_URL', 'https://guaraniappstore.com');
define('SITE_EMAIL', 'hola@guaraniappstore.com');

// ================================================
// PATHS DEL SISTEMA
// ================================================
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('ASSETS_PATH', PUBLIC_PATH . '/assets');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');

// ================================================
// URLs DEL SISTEMA
// ================================================
define('ASSETS_URL', SITE_URL . '/assets');
define('UPLOADS_URL', SITE_URL . '/uploads');

// ================================================
// CONFIGURACIÓN DE SEGURIDAD
// ================================================
define('SECURITY_SALT', 'i8wC1fmgrBBSAZePbpptiByVe1r9CX6Uc2Rckm26b8JdZx7mgKpG/kEJm/JyBKqQje1nPcJkJ4ZCvtdRPMXzDg==');
define('SESSION_NAME', 'guarani_admin_session');
define('SESSION_LIFETIME', 7200);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 1800);

// ================================================
// CONFIGURACIÓN DE 2FA
// ================================================
define('TWO_FA_ISSUER', 'Guarani App Store');
define('TWO_FA_ENABLED_BY_DEFAULT', false);

// ================================================
// CONFIGURACIÓN DE EMAILS
// ================================================
define('SMTP_ENABLED', false);
define('SMTP_HOST', 'smtp.hostinger.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_ENCRYPTION', 'tls');
define('SMTP_FROM_EMAIL', 'noreply@guaraniappstore.com');
define('SMTP_FROM_NAME', 'Guarani App Store');

// ================================================
// CONFIGURACIÓN DEL BLOG
// ================================================
define('BLOG_POSTS_PER_PAGE', 12);
define('BLOG_AUTO_GENERATION_ENABLED', true);
define('BLOG_GENERATION_INTERVAL_DAYS', 2);
define('BLOG_AUTHOR_NAME', 'César Ruzafa');

// ================================================
// CONFIGURACIÓN DE APIS EXTERNAS
// ================================================
define('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1/chat/completions');
define('DEEPSEEK_MODEL', 'deepseek/deepseek-r1');

// ================================================
// CONFIGURACIÓN DE WEBAPPS
// ================================================
define('WEBAPPS_PER_PAGE', 12);
define('MAX_UPLOAD_SIZE', 10485760);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// ================================================
// CONFIGURACIÓN DE TIMEZONE
// ================================================
date_default_timezone_set('America/Asuncion');

// ================================================
// CONFIGURACIÓN DE ERRORES
// ================================================
define('DEBUG_MODE', false);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ================================================
// CONFIGURACIÓN DE LOGS
// ================================================
define('LOG_PATH', ROOT_PATH . '/logs');
define('ERROR_LOG_FILE', LOG_PATH . '/error.log');
define('ACCESS_LOG_FILE', LOG_PATH . '/access.log');

// ================================================
// RUTAS DE LA APLICACIÓN
// ================================================
$APP_ROUTES = [
    // Rutas públicas
    '' => 'home',
    'home' => 'home',
    'webapps' => 'webapps',
    'webapp' => 'webapp_detail',
    'blog' => 'blog',
    'blog/article' => 'blog_article',
    'subscribe' => 'subscribe',
    'verify-subscription' => 'verify_subscription',
    'unsubscribe' => 'unsubscribe',

    // Rutas de administración
    'admin' => 'admin_login',
    'admin/login' => 'admin_login',
    'admin/logout' => 'admin_logout',
    'admin/dashboard' => 'admin_dashboard',
    'admin/webapps' => 'admin_webapps',
    'admin/webapps/create' => 'admin_webapp_create',
    'admin/webapps/edit' => 'admin_webapp_edit',
    'admin/webapps/delete' => 'admin_webapp_delete',
    'admin/blog' => 'admin_blog',
    'admin/blog/create' => 'admin_blog_create',
    'admin/blog/edit' => 'admin_blog_edit',
    'admin/blog/delete' => 'admin_blog_delete',
    'admin/blog/generate' => 'admin_blog_generate',
    'admin/subscribers' => 'admin_subscribers',
    'admin/beta-testers' => 'admin_beta_testers',
    'admin/beta-testers/approve' => 'admin_beta_testers_approve',
    'admin/beta-testers/change-level' => 'admin_beta_testers_change_level',
    'admin/feedback' => 'admin_feedback',
    'admin/feedback/view' => 'admin_feedback_view',
    'admin/settings' => 'admin_settings',
    'admin/profile' => 'admin_profile',

    // APIs internas
    'ajax/blog-generate' => 'ajax_blog_generate',
    'api/webapp/view' => 'api_webapp_view',
    'api/webapp/click' => 'api_webapp_click',
    'api/blog/view' => 'api_blog_view',
];

// ================================================
// CONSTANTES ADICIONALES
// ================================================
define('APP_VERSION', '1.0.0');
define('MIN_PHP_VERSION', '7.4');

// Verificar versión de PHP
if (version_compare(PHP_VERSION, MIN_PHP_VERSION, '<')) {
    die('Este sistema requiere PHP ' . MIN_PHP_VERSION . ' o superior. Tu versión: ' . PHP_VERSION);
}

// ================================================
// AUTOLOADER SIMPLE
// ================================================
spl_autoload_register(function ($class) {
    $file = INCLUDES_PATH . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
