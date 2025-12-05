<article class="section">
    <div class="container" style="max-width: 900px;">
        <!-- Article Header -->
        <header class="article-header mb-4">
            <?php if ($article['category']): ?>
                <span class="badge badge-info mb-3"><?php echo e($article['category']); ?></span>
            <?php endif; ?>

            <h1 class="article-detail-title"><?php echo e($article['title']); ?></h1>

            <div class="article-meta-detail">
                <div class="flex items-center gap-3">
                    <div class="author-avatar">
                        <?php echo strtoupper(substr($article['author_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <div class="author-name"><?php echo e($article['author_name']); ?></div>
                        <div class="article-date-detail">
                            <?php echo format_date_es($article['published_at'], 'long'); ?>
                            <span class="mx-2">•</span>
                            <?php echo time_ago($article['published_at']); ?>
                            <span class="mx-2">•</span>
                            👁 <?php echo format_number($article['view_count']); ?> vistas
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Featured Image -->
        <?php if ($article['featured_image_url']): ?>
            <div class="article-featured-image mb-4">
                <img src="<?php echo e($article['featured_image_url']); ?>"
                     alt="<?php echo e($article['title']); ?>">
            </div>
        <?php endif; ?>

        <!-- Article Content -->
        <div class="article-content card" style="padding: 3rem;">
            <?php echo $article['content']; ?>
        </div>

        <!-- Tags -->
        <?php if (!empty($article['tags'])): ?>
            <div class="article-tags mt-4">
                <strong>Tags:</strong>
                <div class="tags-list mt-2">
                    <?php foreach ($article['tags'] as $tag): ?>
                        <span class="tag-badge"><?php echo e($tag); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Related Webapp -->
        <?php if ($related_webapp): ?>
            <div class="related-webapp-box card mt-4" style="padding: 2rem; background: var(--guarani-light);">
                <h3 class="mb-3">🚀 Aplicación Relacionada</h3>
                <div class="flex items-center gap-3">
                    <?php if ($related_webapp['logo_url']): ?>
                        <img src="<?php echo e($related_webapp['logo_url']); ?>"
                             alt="<?php echo e($related_webapp['title']); ?>"
                             style="width: 60px; height: 60px; object-fit: contain;">
                    <?php endif; ?>
                    <div style="flex: 1;">
                        <h4><?php echo e($related_webapp['title']); ?></h4>
                        <p><?php echo e($related_webapp['short_description']); ?></p>
                    </div>
                    <a href="<?php echo get_url('webapp/' . $related_webapp['slug']); ?>"
                       class="btn btn-primary">
                        Ver App
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Related Articles -->
        <?php if (!empty($related_articles)): ?>
            <div class="related-articles mt-4">
                <h2 class="mb-3">Artículos Relacionados</h2>
                <div class="grid grid-cols-3">
                    <?php foreach ($related_articles as $related): ?>
                        <div class="card hover-lift">
                            <?php if ($related['featured_image_url']): ?>
                                <img src="<?php echo e($related['featured_image_url']); ?>"
                                     alt="<?php echo e($related['title']); ?>"
                                     class="article-image">
                            <?php endif; ?>
                            <div class="card-body">
                                <h3><?php echo e($related['title']); ?></h3>
                                <p><?php echo e(truncate_text($related['excerpt'], 100)); ?></p>
                                <div class="mt-3">
                                    <a href="<?php echo get_url('blog/article/' . $related['slug']); ?>"
                                       class="read-more">
                                        Leer más →
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Newsletter CTA -->
        <div class="newsletter-cta card mt-4" style="padding: 3rem; text-align: center; background: var(--gradient-primary); color: white;">
            <h2 style="color: white; margin-bottom: 1rem;">¿Te gustó este artículo?</h2>
            <p style="margin-bottom: 2rem; opacity: 0.95;">
                Suscríbete para recibir más contenido sobre IA aplicada a PYMEs
            </p>
            <form action="<?php echo get_url('subscribe'); ?>" method="POST" class="newsletter-form-inline">
                <?php echo csrf_field(); ?>
                <input type="email"
                       name="email"
                       placeholder="Tu email"
                       required
                       class="newsletter-input-inline">
                <button type="submit" class="btn btn-primary">
                    Suscribirse
                </button>
            </form>
        </div>
    </div>
</article>

<style>
.article-detail-title {
    font-size: 3rem;
    line-height: 1.2;
    margin-bottom: 1.5rem;
}
.article-meta-detail {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border);
}
.author-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--guarani-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
}
.author-name {
    font-weight: 600;
    font-size: 1rem;
}
.article-date-detail {
    font-size: 0.875rem;
    opacity: 0.7;
}
.article-featured-image img {
    width: 100%;
    border-radius: var(--radius);
}
.article-content {
    font-size: 1.125rem;
    line-height: 1.8;
}
.article-content h2 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: var(--guarani-dark);
}
.article-content h3 {
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    color: var(--guarani-dark);
}
.article-content p {
    margin-bottom: 1.5rem;
}
.article-content ul,
.article-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}
.article-content li {
    margin-bottom: 0.5rem;
}
.article-content img {
    max-width: 100%;
    border-radius: var(--radius);
    margin: 2rem 0;
}
.newsletter-form-inline {
    display: flex;
    gap: 1rem;
    max-width: 500px;
    margin: 0 auto;
}
.newsletter-input-inline {
    flex: 1;
    padding: 1rem;
    border: none;
    border-radius: var(--radius);
}
@media (max-width: 768px) {
    .article-detail-title {
        font-size: 2rem;
    }
    .newsletter-form-inline {
        flex-direction: column;
    }
}
</style>
