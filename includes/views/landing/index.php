<?php include INCLUDES_PATH . '/views/landing/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section hero-with-video">
    <!-- Video de fondo -->
    <video class="hero-video-bg" autoplay muted loop playsinline>
        <source src="<?php echo ASSETS_URL; ?>/videos/hero-background.mp4" type="video/mp4">
        <source src="<?php echo ASSETS_URL; ?>/videos/hero-background.webm" type="video/webm">
    </video>

    <!-- Overlay para mejorar legibilidad del texto -->
    <div class="hero-video-overlay"></div>

    <div class="container hero-container-relative">
        <div class="hero-content">
            <div class="hero-text animate-fade-in-up">
                <h1 class="hero-title">Descubre Nuestras Aplicaciones Web en Producción</h1>
                <p class="hero-description">
                    Soluciones innovadoras desarrolladas con las últimas tecnologías.
                    Explora nuestro portafolio de aplicaciones web y mantente informado
                    con nuestro blog sobre IA aplicada a PYMEs.
                </p>
                <div class="hero-buttons">
                    <a href="<?php echo get_url('webapps'); ?>" class="btn btn-primary btn-lg">
                        Ver Aplicaciones
                    </a>
                    <a href="<?php echo get_url('blog'); ?>" class="btn btn-outline btn-lg">
                        Leer Blog
                    </a>
                </div>
            </div>
            <?php if (file_exists(PUBLIC_PATH . '/assets/images/hero-illustration.svg')): ?>
            <div class="hero-image animate-slide-in-right">
                <img src="<?php echo ASSETS_URL; ?>/images/hero-illustration.svg"
                     alt="Aplicaciones Web"
                     class="hero-img">
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* Hero con video de fondo */
.hero-with-video {
    position: relative;
    overflow: hidden;
    background: #000; /* Fallback mientras carga el video */
}

.hero-video-bg {
    position: absolute;
    top: 50%;
    left: 50%;
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    transform: translate(-50%, -50%);
    z-index: 0;
    object-fit: cover;
}

.hero-video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(11, 94, 215, 0.45), rgba(0, 200, 83, 0.45));
    z-index: 1;
}

.hero-container-relative {
    position: relative;
    z-index: 2;
}

.hero-with-video .hero-content {
    position: relative;
    z-index: 2;
}

.hero-with-video .hero-title,
.hero-with-video .hero-description {
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    color: #fff;
}

/* Asegurar que los botones sean visibles */
.hero-with-video .btn {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

/* Responsive */
@media (max-width: 768px) {
    .hero-video-bg {
        min-width: auto;
        width: 100%;
    }
}
</style>

<!-- Featured Apps Section -->
<?php if (!empty($featured_webapps)): ?>
<section class="section apps-section">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">Aplicaciones Destacadas</h2>
            <p class="section-description">
                Conoce las aplicaciones web que hemos desarrollado y que están en producción
            </p>
        </div>

        <div class="grid grid-cols-3">
            <?php foreach ($featured_webapps as $webapp): ?>
                <div class="card hover-lift">
                    <?php if ($webapp['cover_image_url']): ?>
                        <img src="<?php echo e($webapp['cover_image_url']); ?>"
                             alt="<?php echo e($webapp['title']); ?>"
                             class="webapp-cover">
                    <?php endif; ?>

                    <div class="card-body">
                        <?php if ($webapp['logo_url']): ?>
                            <img src="<?php echo e($webapp['logo_url']); ?>"
                                 alt="<?php echo e($webapp['title']); ?>"
                                 class="webapp-logo">
                        <?php endif; ?>

                        <h3 class="webapp-title"><?php echo e($webapp['title']); ?></h3>
                        <p class="webapp-description">
                            <?php echo e($webapp['short_description']); ?>
                        </p>

                        <?php if ($webapp['category']): ?>
                            <span class="webapp-category"><?php echo e($webapp['category']); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer">
                        <a href="<?php echo get_url('webapp/' . $webapp['slug']); ?>"
                           class="btn btn-secondary w-full">
                            Ver Detalles
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="<?php echo get_url('webapps'); ?>" class="btn btn-primary">
                Ver Todas las Aplicaciones
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- About Section -->
<section class="section bg-guarani-light">
    <div class="container">
        <div class="grid grid-cols-2">
            <div>
                <h2 class="text-guarani-dark mb-3">Nuestro Propósito</h2>
                <p class="mb-3">
                    En Guarani App Store, nuestro objetivo es hacer que la tecnología sea
                    <strong>mba'e porã</strong> (algo bueno) para todos. Desarrollamos
                    aplicaciones web innovadoras y compartimos conocimiento sobre cómo
                    la inteligencia artificial puede transformar las PYMEs.
                </p>
                <p>
                    Cada aplicación que publicamos representa nuestro compromiso con la
                    excelencia y la innovación, siempre pensando en el impacto real
                    que puede generar en los negocios.
                </p>
            </div>
            <div>
                <h2 class="text-guarani-dark mb-3">Nuestra Visión</h2>
                <p class="mb-3">
                    Soñamos con ser referentes en innovación tecnológica, llevando
                    soluciones de calidad a toda Sudamérica. Queremos que cada
                    emprendedor, cada profesional, cada negocio pueda contar su
                    historia en el mundo digital.
                </p>
                <p>
                    A través de nuestras aplicaciones y contenido educativo, buscamos
                    democratizar el acceso a tecnología de punta.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Blog Section -->
<?php if (!empty($recent_articles)): ?>
<section class="section blog-section">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">Últimos Artículos del Blog</h2>
            <p class="section-description">
                Mantente informado sobre IA aplicada a PYMEs y transformación digital
            </p>
        </div>

        <div class="grid grid-cols-3">
            <?php foreach ($recent_articles as $article): ?>
                <div class="card hover-lift">
                    <?php if ($article['featured_image_url']): ?>
                        <img src="<?php echo e($article['featured_image_url']); ?>"
                             alt="<?php echo e($article['title']); ?>"
                             class="article-image">
                    <?php endif; ?>

                    <div class="card-body">
                        <div class="article-meta">
                            <span class="article-author"><?php echo e($article['author_name']); ?></span>
                            <span class="article-date">
                                <?php echo format_date_es($article['published_at'], 'short'); ?>
                            </span>
                        </div>

                        <h3 class="article-title">
                            <a href="<?php echo get_url('blog/article/' . $article['slug']); ?>">
                                <?php echo e($article['title']); ?>
                            </a>
                        </h3>

                        <p class="article-excerpt">
                            <?php echo e(truncate_text($article['excerpt'], 120)); ?>
                        </p>

                        <a href="<?php echo get_url('blog/article/' . $article['slug']); ?>"
                           class="read-more">
                            Leer más →
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="<?php echo get_url('blog'); ?>" class="btn btn-primary">
                Ver Todos los Artículos
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="section cta-section bg-guarani-gradient text-white">
    <div class="container text-center">
        <h2 class="cta-title">¿Quieres Saber Más?</h2>
        <p class="cta-description">
            Contáctanos para conocer más sobre nuestras aplicaciones o
            suscríbete a nuestro blog para recibir los últimos artículos
        </p>
        <div class="cta-buttons">
            <?php if (!empty($company_data['contact']['whatsapp'])): ?>
                <a href="https://api.whatsapp.com/send?phone=<?php echo e($company_data['contact']['whatsapp']); ?>&text=¡Hola!%20Quiero%20más%20información"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn btn-primary btn-lg">
                    Contactar por WhatsApp
                </a>
            <?php endif; ?>
            <a href="#footer" class="btn btn-outline btn-lg">
                Suscribirse al Blog
            </a>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/views/landing/footer.php'; ?>
