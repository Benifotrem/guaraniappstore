<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Guarani App Store - Showcase de aplicaciones web en producción. Descubre nuestras apps y blog sobre IA aplicada a PYMEs.">
    <meta name="keywords" content="aplicaciones web, desarrollo web, Paraguay, IA, PYMEs, transformación digital">
    <title><?php echo $page_title ?? 'Guarani App Store - Apps en Producción'; ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/styles.css?v=<?php echo filemtime(PUBLIC_PATH . '/assets/css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/landing.css?v=<?php echo filemtime(PUBLIC_PATH . '/assets/css/landing.css'); ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/images/favicon.png">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL; ?>">
    <meta property="og:title" content="Guarani App Store - Apps en Producción">
    <meta property="og:description" content="Showcase de aplicaciones web en producción y blog sobre IA aplicada a PYMEs">
    <meta property="og:image" content="<?php echo ASSETS_URL; ?>/images/og-image.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo SITE_URL; ?>">
    <meta property="twitter:title" content="Guarani App Store - Apps en Producción">
    <meta property="twitter:description" content="Showcase de aplicaciones web en producción y blog sobre IA aplicada a PYMEs">
    <meta property="twitter:image" content="<?php echo ASSETS_URL; ?>/images/og-image.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <?php if (isset($additional_head_content)) echo $additional_head_content; ?>
</head>
<body>
    <!-- Header / Navigation -->
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <!-- Logo -->
                <div class="logo">
                    <a href="<?php echo get_url(); ?>">
                        <img src="<?php echo ASSETS_URL; ?>/images/logo.png"
                             alt="Guarani App Store"
                             class="logo-img">
                        <span class="logo-text">Guarani App Store</span>
                    </a>
                </div>

                <!-- Navigation -->
        <nav class="main-nav">
            <a href="<?php echo get_url(); ?>" class="nav-link">Inicio</a>
            <a href="<?php echo get_url('webapps'); ?>" class="nav-link">Apps</a>
            <a href="<?php echo get_url('blog'); ?>" class="nav-link">Blog</a>
            <a href="<?php echo get_url('beta'); ?>" class="nav-link nav-link-beta">🚀 Beta Tester</a>
            <a href="<?php echo get_url('faq'); ?>" class="nav-link">FAQ</a>
            <a href="#contacto" class="nav-link">Contacto</a>
        </nav>
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" id="mobile-menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>

                <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobile-menu">
            <a href="<?php echo get_url(); ?>" class="mobile-nav-link">Inicio</a>
            <a href="<?php echo get_url('webapps'); ?>" class="mobile-nav-link">Apps</a>
            <a href="<?php echo get_url('blog'); ?>" class="mobile-nav-link">Blog</a>
            <a href="<?php echo get_url('beta'); ?>" class="mobile-nav-link">🚀 Beta Tester</a>
            <a href="<?php echo get_url('faq'); ?>" class="mobile-nav-link">FAQ</a>
            <a href="#contacto" class="mobile-nav-link">Contacto</a>
        </div>
    </header>
