<section class="section">
    <div class="container">
        <!-- Featured Article -->
        <?php if ($featured_article && $page === 1): ?>
            <div class="featured-article card mb-4">
                <?php if ($featured_article['featured_image_url']): ?>
                    <img src="<?php echo e($featured_article['featured_image_url']); ?>"
                         alt="<?php echo e($featured_article['title']); ?>"
                         class="featured-article-image">
                <?php endif; ?>
                <div style="padding: 2rem;">
                    <span class="badge badge-info">Destacado</span>
                    <h2 class="mt-3">
                        <a href="<?php echo get_url('blog/article/' . $featured_article['slug']); ?>">
                            <?php echo e($featured_article['title']); ?>
                        </a>
                    </h2>
                    <div class="article-meta mt-2 mb-3">
                        <span>Por <?php echo e($featured_article['author_name']); ?></span>
                        <span>•</span>
                        <span><?php echo format_date_es($featured_article['published_at'], 'short'); ?></span>
                        <span>•</span>
                        <span>👁 <?php echo format_number($featured_article['view_count']); ?></span>
                    </div>
                    <p class="mb-3"><?php echo e($featured_article['excerpt']); ?></p>
                    <a href="<?php echo get_url('blog/article/' . $featured_article['slug']); ?>"
                       class="btn btn-primary">
                        Leer más →
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="blog-filters mb-4">
            <form method="GET" action="<?php echo get_url('blog'); ?>" class="flex gap-2">
                <input type="text"
                       name="search"
                       placeholder="Buscar artículos..."
                       value="<?php echo e($search); ?>"
                       class="form-input"
                       style="flex: 1;">

                <select name="category" class="form-select">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo e($cat['category']); ?>"
                                <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                            <?php echo e($cat['category']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
        </div>

        <?php if (empty($articles)): ?>
            <div class="text-center py-4">
                <h3>No se encontraron artículos</h3>
                <p class="opacity-75">Intenta con otros filtros de búsqueda</p>
            </div>
        <?php else: ?>
            <!-- Articles Grid -->
            <div class="grid grid-cols-3">
                <?php foreach ($articles as $article): ?>
                    <?php if ($page === 1 && $article['id'] === ($featured_article['id'] ?? 0)) continue; ?>

                    <div class="card hover-lift">
                        <?php if ($article['featured_image_url']): ?>
                            <img src="<?php echo e($article['featured_image_url']); ?>"
                                 alt="<?php echo e($article['title']); ?>"
                                 class="article-image">
                        <?php endif; ?>

                        <div class="card-body">
                            <?php if ($article['category']): ?>
                                <span class="badge badge-info mb-2"><?php echo e($article['category']); ?></span>
                            <?php endif; ?>

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

                            <div class="flex justify-between items-center mt-3">
                                <a href="<?php echo get_url('blog/article/' . $article['slug']); ?>"
                                   class="read-more">
                                    Leer más →
                                </a>
                                <span style="font-size: 0.875rem; opacity: 0.7;">
                                    👁 <?php echo format_number($article['view_count']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination mt-4">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php
                        $params = [];
                        if ($category) $params[] = 'category=' . urlencode($category);
                        if ($search) $params[] = 'search=' . urlencode($search);
                        $params[] = 'page=' . $i;
                        $url = get_url('blog?' . implode('&', $params));
                        ?>
                        <a href="<?php echo $url; ?>"
                           class="pagination-link <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<style>
.featured-article-image {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: var(--radius) var(--radius) 0 0;
}
.pagination {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}
.pagination-link {
    padding: 0.5rem 1rem;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    text-decoration: none;
    color: var(--foreground);
    transition: all 0.2s ease;
}
.pagination-link:hover,
.pagination-link.active {
    background: var(--guarani-primary);
    color: white;
    border-color: var(--guarani-primary);
}
</style>

<?php include INCLUDES_PATH . '/views/landing/footer.php'; ?>
