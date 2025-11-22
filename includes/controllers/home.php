<?php
/**
 * Controlador: Home / Landing Page
 */

// Obtener webapps publicadas destacadas
$db = Database::getInstance();

$featured_webapps = $db->fetchAll("
    SELECT * FROM webapps
    WHERE status = 'published' AND is_featured = 1
    ORDER BY sort_order ASC, published_at DESC
    LIMIT 6
");

// Obtener últimos artículos del blog
$recent_articles = $db->fetchAll("
    SELECT id, title, slug, excerpt, featured_image_url, author_name, published_at, view_count
    FROM blog_articles
    WHERE status = 'published'
    ORDER BY published_at DESC
    LIMIT 3
");

// Datos de la empresa (similar a mockData.js)
$company_data = [
    'contact' => [
        'phone' => get_setting('site_phone', '(+595) 981-123456'),
        'email' => get_setting('site_email', 'hola@guaraniappstore.com.py'),
        'location' => 'Asunción, Paraguay 🇵🇾',
        'whatsapp' => get_setting('contact_whatsapp', '595981123456')
    ],
    'social' => [
        'facebook' => get_setting('social_facebook', ''),
        'instagram' => get_setting('social_instagram', ''),
        'youtube' => get_setting('social_youtube', '')
    ]
];

// Incluir la vista de la landing page
include INCLUDES_PATH . '/views/landing/index.php';
