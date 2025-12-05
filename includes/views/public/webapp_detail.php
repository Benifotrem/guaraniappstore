<section class="section">
    <div class="container">
        <div class="webapp-detail">
            <!-- Header -->
            <div class="webapp-detail-header">
                <div class="flex items-center gap-4 mb-4">
                    <?php if ($webapp['logo_url']): ?>
                        <img src="<?php echo e($webapp['logo_url']); ?>"
                             alt="<?php echo e($webapp['title']); ?>"
                             class="webapp-detail-logo">
                    <?php endif; ?>
                    <div>
                        <h1 class="webapp-detail-title"><?php echo e($webapp['title']); ?></h1>
                        <?php if ($webapp['category']): ?>
                            <span class="webapp-category"><?php echo e($webapp['category']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($webapp['short_description']): ?>
                    <p class="webapp-detail-subtitle"><?php echo e($webapp['short_description']); ?></p>
                <?php endif; ?>

                <div class="webapp-detail-stats">
                    <span>👁 <?php echo format_number($webapp['view_count']); ?> vistas</span>
                    <span>🔗 <?php echo format_number($webapp['click_count']); ?> clics</span>
                    <?php if ($webapp['published_at']): ?>
                        <span>📅 Publicado <?php echo format_date_es($webapp['published_at'], 'short'); ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($webapp['app_url']): ?>
                    <div class="mt-4">
                        <a href="<?php echo e($webapp['app_url']); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-primary btn-lg"
                           onclick="trackWebappClick(<?php echo $webapp['id']; ?>)">
                            🚀 Visitar Aplicación
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cover Image -->
            <?php if ($webapp['cover_image_url']): ?>
                <div class="webapp-detail-cover mb-4">
                    <img src="<?php echo e($webapp['cover_image_url']); ?>"
                         alt="<?php echo e($webapp['title']); ?>">
                </div>
            <?php endif; ?>

            <!-- Description -->
            <?php if ($webapp['full_description']): ?>
                <div class="webapp-detail-description card mb-4">
                    <h2>Descripción</h2>
                    <div class="mt-3">
                        <?php echo nl2br(e($webapp['full_description'])); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Screenshots -->
            <?php if (!empty($webapp['screenshots'])): ?>
                <div class="webapp-detail-screenshots card mb-4">
                    <h2>Capturas de Pantalla</h2>
                    <div class="screenshots-grid mt-3">
                        <?php foreach ($webapp['screenshots'] as $screenshot): ?>
                            <img src="<?php echo e($screenshot); ?>"
                                 alt="Screenshot"
                                 class="screenshot-img">
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tech Stack -->
            <?php if (!empty($webapp['tech_stack'])): ?>
                <div class="webapp-detail-tech card mb-4">
                    <h2>Stack Tecnológico</h2>
                    <div class="tech-stack-list mt-3">
                        <?php foreach ($webapp['tech_stack'] as $tech): ?>
                            <span class="tech-badge"><?php echo e($tech); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tags -->
            <?php if (!empty($webapp['tags'])): ?>
                <div class="webapp-detail-tags mb-4">
                    <h3>Tags:</h3>
                    <div class="tags-list mt-2">
                        <?php foreach ($webapp['tags'] as $tag): ?>
                            <span class="tag-badge"><?php echo e($tag); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Related Webapps -->
            <?php if (!empty($related_webapps)): ?>
                <div class="related-webapps mt-4">
                    <h2>Aplicaciones Relacionadas</h2>
                    <div class="grid grid-cols-3 mt-3">
                        <?php foreach ($related_webapps as $related): ?>
                            <div class="card hover-lift">
                                <?php if ($related['logo_url']): ?>
                                    <img src="<?php echo e($related['logo_url']); ?>"
                                         alt="<?php echo e($related['title']); ?>"
                                         class="webapp-logo">
                                <?php endif; ?>
                                <h3><?php echo e($related['title']); ?></h3>
                                <p><?php echo e(truncate_text($related['short_description'], 100)); ?></p>
                                <a href="<?php echo get_url('webapp/' . $related['slug']); ?>"
                                   class="btn btn-secondary w-full mt-3">
                                    Ver Detalles
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Widget de Feedback para Beta Testers -->
<?php include INCLUDES_PATH . '/views/feedback/widget.php'; ?>

<script>
function trackWebappClick(webappId) {
    fetch('<?php echo get_url("api/webapp/click"); ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({webapp_id: webappId})
    });
}
</script>

<style>
.webapp-detail-logo {
    width: 100px;
    height: 100px;
    object-fit: contain;
}
.webapp-detail-title {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}
.webapp-detail-subtitle {
    font-size: 1.25rem;
    opacity: 0.8;
    margin-bottom: 1rem;
}
.webapp-detail-stats {
    display: flex;
    gap: 2rem;
    font-size: 0.875rem;
    opacity: 0.7;
}
.webapp-detail-cover img {
    width: 100%;
    border-radius: var(--radius);
}
.webapp-detail-description,
.webapp-detail-screenshots,
.webapp-detail-tech {
    padding: 2rem;
}
.screenshots-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}
.screenshot-img {
    width: 100%;
    border-radius: var(--radius);
    cursor: pointer;
    transition: transform 0.3s ease;
}
.screenshot-img:hover {
    transform: scale(1.05);
}
.tech-stack-list,
.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.tech-badge,
.tag-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    background: var(--guarani-light);
    color: var(--guarani-dark);
    border-radius: var(--radius);
    font-size: 0.875rem;
    font-weight: 600;
}
</style>
