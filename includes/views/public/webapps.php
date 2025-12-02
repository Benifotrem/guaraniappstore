<section class="section">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="mb-2">Descubre Nuestras Aplicaciones</h1>
            <p class="text-muted" style="max-width: 600px; margin: 0 auto;">
                Explora las mejores aplicaciones web diseñadas para PYMEs latinoamericanas
            </p>
        </div>

        <!-- Filters -->
        <div class="webapps-filters mb-5">
            <form method="GET" action="<?php echo get_url('webapps'); ?>"
                  class="flex gap-3"
                  style="max-width: 900px; margin: 0 auto;">
                <input type="text"
                       name="search"
                       placeholder="🔍 Buscar aplicaciones..."
                       value="<?php echo e($search); ?>"
                       class="form-input"
                       style="flex: 1; font-size: 1rem; padding: 0.75rem 1rem;">

                <select name="category" class="form-select" style="min-width: 200px; padding: 0.75rem 1rem;">
                    <option value="">📁 Todas las categorías</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo e($cat['category']); ?>"
                                <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                            <?php echo e($cat['category']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                    Buscar
                </button>
            </form>
        </div>

        <?php if (empty($webapps)): ?>
            <div class="text-center py-5">
                <div style="font-size: 4rem; opacity: 0.3; margin-bottom: 1rem;">📱</div>
                <h3>No se encontraron aplicaciones</h3>
                <p class="text-muted">Intenta con otros filtros de búsqueda</p>
            </div>
        <?php else: ?>
            <!-- Results count -->
            <div class="mb-4 text-muted" style="font-size: 0.9rem;">
                Mostrando <?php echo count($webapps); ?> aplicaciones
                <?php if ($search || $category): ?>
                    <a href="<?php echo get_url('webapps'); ?>" class="ml-2" style="text-decoration: underline;">
                        Limpiar filtros
                    </a>
                <?php endif; ?>
            </div>

            <!-- Webapps Grid -->
            <div class="grid grid-cols-3" style="gap: 2rem;">
                <?php foreach ($webapps as $webapp): ?>
                    <div class="webapp-card">
                        <!-- Cover Image with Featured Badge -->
                        <div class="webapp-card-cover">
                            <?php
                            // Prioridad: screenshots[0] > cover_image_url > placeholder
                            $screenshots = json_decode($webapp['screenshots'] ?? '[]', true);
                            $display_image = null;

                            if (is_array($screenshots) && !empty($screenshots)) {
                                // Usar el primer screenshot si existe
                                $display_image = $screenshots[0];
                            } elseif (!empty($webapp['cover_image_url'])) {
                                // Fallback a cover_image_url
                                $display_image = $webapp['cover_image_url'];
                            }

                            if ($display_image):
                            ?>
                                <img src="<?php echo e($display_image); ?>"
                                     alt="<?php echo e($webapp['title']); ?>"
                                     loading="lazy"
                                     onerror="this.src='<?php echo ASSETS_URL; ?>/images/placeholder-webapp.jpg'">
                            <?php else: ?>
                                <div class="webapp-card-cover-placeholder">
                                    <svg width="64" height="64" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                                    </svg>
                                </div>
                            <?php endif; ?>

                            <?php if ($webapp['is_featured']): ?>
                                <span class="webapp-card-badge">⭐ Destacada</span>
                            <?php endif; ?>
                        </div>

                        <!-- Card Body -->
                        <div class="webapp-card-body">
                            <div class="webapp-card-header">
                                <?php if ($webapp['logo_url']): ?>
                                    <img src="<?php echo e($webapp['logo_url']); ?>"
                                         alt="<?php echo e($webapp['title']); ?>"
                                         class="webapp-card-logo"
                                         loading="lazy"
                                         onerror="this.style.display='none'">
                                <?php endif; ?>

                                <div class="webapp-card-title-group">
                                    <h3 class="webapp-card-title">
                                        <a href="<?php echo get_url('webapp/' . $webapp['slug']); ?>">
                                            <?php echo e($webapp['title']); ?>
                                        </a>
                                    </h3>

                                    <?php if ($webapp['category']): ?>
                                        <span class="webapp-card-category">
                                            <?php echo e($webapp['category']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <p class="webapp-card-description">
                                <?php echo e($webapp['short_description']); ?>
                            </p>

                            <!-- Tech Stack Pills -->
                            <?php
                            $tech_stack = json_decode($webapp['tech_stack'] ?? '[]', true);
                            if (!empty($tech_stack) && is_array($tech_stack)):
                            ?>
                                <div class="webapp-card-tech">
                                    <?php foreach (array_slice($tech_stack, 0, 3) as $tech): ?>
                                        <span class="tech-pill"><?php echo e($tech); ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($tech_stack) > 3): ?>
                                        <span class="tech-pill-more">+<?php echo count($tech_stack) - 3; ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Card Footer -->
                        <div class="webapp-card-footer">
                            <div class="webapp-card-stats">
                                <span>👁 <?php echo format_number($webapp['view_count']); ?></span>
                            </div>
                            <div class="webapp-card-actions">
                                <?php if (!empty($webapp['app_url'])): ?>
                                    <a href="<?php echo e($webapp['app_url']); ?>"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="btn-webapp-launch"
                                       onclick="trackWebappClick(<?php echo $webapp['id']; ?>)"
                                       title="Abrir aplicación">
                                        🚀 Abrir App
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo get_url('webapp/' . $webapp['slug']); ?>"
                                   class="btn-webapp-view">
                                    Detalles →
                                </a>
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
                        $url = get_url('webapps?' . implode('&', $params));
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
