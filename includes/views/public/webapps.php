<section class="section">
    <div class="container">
        <!-- Filters -->
        <div class="webapps-filters mb-4">
            <form method="GET" action="<?php echo get_url('webapps'); ?>" class="flex gap-2">
                <input type="text"
                       name="search"
                       placeholder="Buscar aplicaciones..."
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

        <?php if (empty($webapps)): ?>
            <div class="text-center py-4">
                <h3>No se encontraron aplicaciones</h3>
                <p class="opacity-75">Intenta con otros filtros de búsqueda</p>
            </div>
        <?php else: ?>
            <!-- Webapps Grid -->
            <div class="grid grid-cols-3">
                <?php foreach ($webapps as $webapp): ?>
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

                            <h3 class="webapp-title">
                                <a href="<?php echo get_url('webapp/' . $webapp['slug']); ?>">
                                    <?php echo e($webapp['title']); ?>
                                </a>
                            </h3>

                            <p class="webapp-description">
                                <?php echo e($webapp['short_description']); ?>
                            </p>

                            <?php if ($webapp['category']): ?>
                                <span class="webapp-category"><?php echo e($webapp['category']); ?></span>
                            <?php endif; ?>

                            <div class="mt-3 flex gap-2" style="font-size: 0.875rem; opacity: 0.7;">
                                <span>👁 <?php echo format_number($webapp['view_count']); ?> vistas</span>
                            </div>
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
