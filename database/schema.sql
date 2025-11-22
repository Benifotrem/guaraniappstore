-- ================================================
-- GUARANI APP STORE - DATABASE SCHEMA
-- Compatible con MySQL 5.7+ / MariaDB 10.2+
-- ================================================

-- Eliminar tablas si existen (para desarrollo)
DROP TABLE IF EXISTS `blog_article_analytics`;
DROP TABLE IF EXISTS `blog_subscribers`;
DROP TABLE IF EXISTS `blog_articles`;
DROP TABLE IF EXISTS `webapp_analytics`;
DROP TABLE IF EXISTS `webapps`;
DROP TABLE IF EXISTS `admin_sessions`;
DROP TABLE IF EXISTS `admin_users`;
DROP TABLE IF EXISTS `site_settings`;

-- ================================================
-- TABLA: admin_users
-- Gestión de usuarios administradores con 2FA
-- ================================================
CREATE TABLE `admin_users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100),
  `two_fa_enabled` BOOLEAN DEFAULT FALSE,
  `two_fa_secret` VARCHAR(100),
  `two_fa_method` ENUM('none', 'google_authenticator', 'authy') DEFAULT 'none',
  `last_login` DATETIME,
  `login_attempts` INT DEFAULT 0,
  `locked_until` DATETIME,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: admin_sessions
-- Gestión de sesiones de administradores
-- ================================================
CREATE TABLE `admin_sessions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `admin_user_id` INT UNSIGNED NOT NULL,
  `session_token` VARCHAR(255) UNIQUE NOT NULL,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `expires_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users`(`id`) ON DELETE CASCADE,
  INDEX `idx_session_token` (`session_token`),
  INDEX `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: site_settings
-- Configuración general del sitio y API keys
-- ================================================
CREATE TABLE `site_settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) UNIQUE NOT NULL,
  `setting_value` TEXT,
  `setting_type` ENUM('string', 'number', 'boolean', 'json', 'encrypted') DEFAULT 'string',
  `description` VARCHAR(255),
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: webapps
-- Almacena las webapps publicadas en el showcase
-- ================================================
CREATE TABLE `webapps` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(200) UNIQUE NOT NULL,
  `short_description` VARCHAR(255),
  `full_description` TEXT,
  `logo_url` VARCHAR(500),
  `cover_image_url` VARCHAR(500),
  `screenshots` JSON,
  `app_url` VARCHAR(500),
  `category` VARCHAR(100),
  `tags` JSON,
  `tech_stack` JSON,
  `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
  `is_featured` BOOLEAN DEFAULT FALSE,
  `view_count` INT UNSIGNED DEFAULT 0,
  `click_count` INT UNSIGNED DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  `published_at` DATETIME,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_status` (`status`),
  INDEX `idx_is_featured` (`is_featured`),
  INDEX `idx_sort_order` (`sort_order`),
  FULLTEXT INDEX `idx_fulltext_search` (`title`, `short_description`, `full_description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: webapp_analytics
-- Seguimiento de vistas y clics de webapps
-- ================================================
CREATE TABLE `webapp_analytics` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `webapp_id` INT UNSIGNED NOT NULL,
  `event_type` ENUM('view', 'click') NOT NULL,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `referrer` VARCHAR(500),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`webapp_id`) REFERENCES `webapps`(`id`) ON DELETE CASCADE,
  INDEX `idx_webapp_id` (`webapp_id`),
  INDEX `idx_event_type` (`event_type`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: blog_articles
-- Artículos del blog generados automáticamente
-- ================================================
CREATE TABLE `blog_articles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  `excerpt` TEXT,
  `content` LONGTEXT NOT NULL,
  `featured_image_url` VARCHAR(500),
  `author_name` VARCHAR(100) DEFAULT 'César Ruzafa',
  `category` VARCHAR(100),
  `tags` JSON,
  `related_webapp_id` INT UNSIGNED,
  `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
  `seo_title` VARCHAR(255),
  `seo_description` VARCHAR(255),
  `seo_keywords` TEXT,
  `view_count` INT UNSIGNED DEFAULT 0,
  `generation_prompt` TEXT,
  `google_trends_data` JSON,
  `is_auto_generated` BOOLEAN DEFAULT TRUE,
  `published_at` DATETIME,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`related_webapp_id`) REFERENCES `webapps`(`id`) ON DELETE SET NULL,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_status` (`status`),
  INDEX `idx_published_at` (`published_at`),
  INDEX `idx_category` (`category`),
  FULLTEXT INDEX `idx_fulltext_content` (`title`, `excerpt`, `content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: blog_subscribers
-- Suscriptores del blog
-- ================================================
CREATE TABLE `blog_subscribers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `name` VARCHAR(100),
  `status` ENUM('pending', 'active', 'unsubscribed') DEFAULT 'pending',
  `verification_token` VARCHAR(100),
  `verified_at` DATETIME,
  `unsubscribed_at` DATETIME,
  `subscription_source` VARCHAR(100) DEFAULT 'landing_page',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_status` (`status`),
  INDEX `idx_verification_token` (`verification_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: blog_article_analytics
-- Analíticas de artículos del blog
-- ================================================
CREATE TABLE `blog_article_analytics` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `article_id` INT UNSIGNED NOT NULL,
  `event_type` ENUM('view', 'share', 'comment') NOT NULL,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `referrer` VARCHAR(500),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`article_id`) REFERENCES `blog_articles`(`id`) ON DELETE CASCADE,
  INDEX `idx_article_id` (`article_id`),
  INDEX `idx_event_type` (`event_type`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- INSERTAR DATOS INICIALES
-- ================================================

-- Usuario administrador por defecto
-- Usuario: admin
-- Contraseña: Admin123! (debe cambiarse en primer login)
-- Hash generado con password_hash('Admin123!', PASSWORD_DEFAULT)
INSERT INTO `admin_users` (`username`, `email`, `password_hash`, `full_name`, `two_fa_enabled`, `two_fa_method`) VALUES
('admin', 'admin@guaraniappstore.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', FALSE, 'none');

-- Configuraciones iniciales del sitio
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('site_name', 'Guarani App Store', 'string', 'Nombre del sitio'),
('site_email', 'hola@guaraniappstore.com.py', 'string', 'Email de contacto principal'),
('site_phone', '(+595) 981-123456', 'string', 'Teléfono de contacto'),
('openrouter_api_key', '', 'encrypted', 'API Key de OpenRouter'),
('deepseek_model', 'deepseek/deepseek-r1', 'string', 'Modelo de DeepSeek R1 a usar'),
('blog_auto_generation_enabled', '1', 'boolean', 'Activar generación automática de artículos'),
('blog_generation_interval_days', '2', 'number', 'Días entre generación automática de artículos'),
('blog_author_name', 'César Ruzafa', 'string', 'Nombre del autor de los artículos'),
('blog_author_bio', 'Especialista en IA aplicada a PYMEs', 'string', 'Biografía del autor'),
('google_trends_region', 'AR-BR-PY-UY', 'string', 'Regiones para Google Trends (separadas por guión)'),
('blog_topics', 'IA para PYMES,Inteligencia Artificial en negocios,Automatización empresarial,Transformación digital', 'string', 'Temas principales del blog (separados por coma)'),
('webapps_per_page', '12', 'number', 'Webapps por página en el showcase'),
('contact_whatsapp', '595981123456', 'string', 'Número de WhatsApp'),
('social_facebook', 'https://facebook.com/guaraniappstore', 'string', 'URL Facebook'),
('social_instagram', 'https://instagram.com/guaraniappstore', 'string', 'URL Instagram'),
('social_youtube', 'https://youtube.com/@guaraniappstore', 'string', 'URL YouTube'),
('smtp_enabled', '0', 'boolean', 'Activar envío de emails'),
('smtp_host', '', 'string', 'Host SMTP'),
('smtp_port', '587', 'number', 'Puerto SMTP'),
('smtp_username', '', 'string', 'Usuario SMTP'),
('smtp_password', '', 'encrypted', 'Contraseña SMTP'),
('smtp_from_email', 'noreply@guaraniappstore.com.py', 'string', 'Email remitente'),
('smtp_from_name', 'Guarani App Store', 'string', 'Nombre remitente'),
('2fa_issuer_name', 'Guarani App Store', 'string', 'Nombre del emisor para 2FA'),
('maintenance_mode', '0', 'boolean', 'Modo mantenimiento'),
('analytics_enabled', '1', 'boolean', 'Activar analíticas');

-- Webapp de ejemplo
INSERT INTO `webapps` (`title`, `slug`, `short_description`, `full_description`, `app_url`, `category`, `status`, `is_featured`, `published_at`) VALUES
('Guarani App Store Platform', 'guarani-app-store', 'Plataforma de showcase para aplicaciones web en producción', 'Una plataforma moderna para mostrar y gestionar aplicaciones web en producción, con panel de administración completo y blog automatizado con IA.', 'https://guaraniappstore.com', 'Plataformas Web', 'published', TRUE, NOW());

-- Artículo de bienvenida al blog
INSERT INTO `blog_articles` (`title`, `slug`, `excerpt`, `content`, `author_name`, `category`, `status`, `is_auto_generated`, `published_at`) VALUES
('Bienvenidos a Guarani App Store', 'bienvenidos-guarani-app-store', 'Descubre cómo la inteligencia artificial está transformando las PYMEs en Latinoamérica',
'<h2>El Futuro Digital de las PYMEs</h2>
<p>En Guarani App Store, creemos firmemente que la tecnología y la inteligencia artificial deben estar al alcance de todas las empresas, sin importar su tamaño. Este blog será tu guía para entender cómo las herramientas de IA pueden revolucionar tu negocio.</p>

<h3>¿Por qué IA para PYMEs?</h3>
<p>Las pequeñas y medianas empresas representan el corazón de la economía latinoamericana. Sin embargo, muchas veces enfrentan barreras para acceder a tecnología de punta. La inteligencia artificial ha democratizado el acceso a herramientas que antes solo estaban disponibles para grandes corporaciones.</p>

<h3>Qué encontrarás en este blog</h3>
<ul>
<li><strong>Casos de uso reales:</strong> Aplicaciones prácticas de IA en diferentes sectores</li>
<li><strong>Guías y tutoriales:</strong> Cómo implementar soluciones de IA en tu negocio</li>
<li><strong>Tendencias del mercado:</strong> Lo último en tecnología para empresas</li>
<li><strong>Historias de éxito:</strong> Casos reales de PYMEs que transformaron su negocio con IA</li>
</ul>

<h3>Nuestro Compromiso</h3>
<p>Nos comprometemos a publicar contenido de calidad cada dos días, siempre enfocado en brindarte valor real y aplicable a tu negocio. Cada artículo está pensado para que puedas implementar mejoras concretas en tu empresa.</p>

<p>¡Bienvenido a esta comunidad de empresarios visionarios que están adoptando el futuro!</p>',
'César Ruzafa', 'Transformación Digital', 'published', FALSE, NOW());

-- ================================================
-- VISTAS ÚTILES
-- ================================================

-- Vista de estadísticas de webapps
CREATE OR REPLACE VIEW `webapp_stats` AS
SELECT
    w.id,
    w.title,
    w.slug,
    w.status,
    w.view_count,
    w.click_count,
    COUNT(DISTINCT DATE(wa.created_at)) as days_with_activity,
    MAX(wa.created_at) as last_activity
FROM webapps w
LEFT JOIN webapp_analytics wa ON w.id = wa.webapp_id
GROUP BY w.id;

-- Vista de estadísticas de artículos
CREATE OR REPLACE VIEW `article_stats` AS
SELECT
    ba.id,
    ba.title,
    ba.slug,
    ba.status,
    ba.published_at,
    ba.view_count,
    COUNT(baa.id) as total_interactions,
    MAX(baa.created_at) as last_interaction
FROM blog_articles ba
LEFT JOIN blog_article_analytics baa ON ba.id = baa.article_id
GROUP BY ba.id;

-- ================================================
-- PROCEDIMIENTOS ALMACENADOS
-- ================================================

DELIMITER //

-- Procedimiento para limpiar sesiones expiradas
CREATE PROCEDURE `clean_expired_sessions`()
BEGIN
    DELETE FROM admin_sessions WHERE expires_at < NOW();
END //

-- Procedimiento para obtener estadísticas generales
CREATE PROCEDURE `get_dashboard_stats`()
BEGIN
    SELECT
        (SELECT COUNT(*) FROM webapps WHERE status = 'published') as total_webapps,
        (SELECT COUNT(*) FROM blog_articles WHERE status = 'published') as total_articles,
        (SELECT COUNT(*) FROM blog_subscribers WHERE status = 'active') as total_subscribers,
        (SELECT SUM(view_count) FROM webapps) as total_webapp_views,
        (SELECT SUM(view_count) FROM blog_articles) as total_article_views;
END //

-- Procedimiento para incrementar contador de vistas de webapp
CREATE PROCEDURE `increment_webapp_view`(IN webapp_id_param INT)
BEGIN
    UPDATE webapps SET view_count = view_count + 1 WHERE id = webapp_id_param;
END //

-- Procedimiento para incrementar contador de vistas de artículo
CREATE PROCEDURE `increment_article_view`(IN article_id_param INT)
BEGIN
    UPDATE blog_articles SET view_count = view_count + 1 WHERE id = article_id_param;
END //

DELIMITER ;

-- ================================================
-- EVENTOS PROGRAMADOS (OPCIONAL)
-- ================================================

-- Evento para limpiar sesiones expiradas cada hora
-- SET GLOBAL event_scheduler = ON;
-- CREATE EVENT `cleanup_sessions`
-- ON SCHEDULE EVERY 1 HOUR
-- DO CALL clean_expired_sessions();

-- ================================================
-- FIN DEL SCHEMA
-- ================================================
