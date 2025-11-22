<div class="flex justify-between items-center mb-4">
    <h2>Blog</h2>
    <div class="flex gap-2">
        <a href="<?php echo get_url('admin/blog/generate'); ?>" class="btn btn-success btn-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Generar con IA
        </a>
        <a href="<?php echo get_url('admin/blog/create'); ?>" class="btn btn-primary btn-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Artículo
        </a>
    </div>
</div>

<?php if (empty($articles)): ?>
    <div class="card text-center" style="padding: 3rem;">
        <h3>No hay artículos</h3>
        <p class="mb-3">Crea un artículo manualmente o genera uno con IA</p>
        <div class="flex gap-2 justify-center">
            <a href="<?php echo get_url('admin/blog/create'); ?>" class="btn btn-primary">
                + Crear Artículo
            </a>
            <a href="<?php echo get_url('admin/blog/generate'); ?>" class="btn btn-success">
                🤖 Generar con IA
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Estado</th>
                    <th>Categoría</th>
                    <th>Tipo</th>
                    <th>Visitas</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td>
                            <strong><?php echo e($article['title']); ?></strong>
                            <div style="font-size: 0.875rem; opacity: 0.7;">
                                /<?php echo e($article['slug']); ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-<?php
                                echo $article['status'] === 'published' ? 'success' :
                                     ($article['status'] === 'draft' ? 'warning' : 'danger');
                            ?>">
                                <?php echo ucfirst($article['status']); ?>
                            </span>
                        </td>
                        <td><?php echo e($article['category'] ?? '-'); ?></td>
                        <td>
                            <?php if ($article['is_auto_generated']): ?>
                                <span class="badge badge-info">🤖 IA</span>
                            <?php else: ?>
                                <span class="badge badge-success">✍️ Manual</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo format_number($article['view_count']); ?></td>
                        <td><?php echo format_date_es($article['published_at'] ?? $article['created_at'], 'short'); ?></td>
                        <td>
                            <div class="flex gap-2">
                                <a href="<?php echo get_url('blog/article/' . $article['slug']); ?>"
                                   target="_blank"
                                   class="btn btn-sm btn-secondary"
                                   title="Ver">
                                    👁
                                </a>
                                <a href="<?php echo get_url('admin/blog/edit?id=' . $article['id']); ?>"
                                   class="btn btn-sm btn-primary"
                                   title="Editar">
                                    ✏️
                                </a>
                                <a href="<?php echo get_url('admin/blog/delete?id=' . $article['id']); ?>"
                                   class="btn btn-sm btn-danger"
                                   data-confirm-delete="¿Eliminar este artículo?"
                                   title="Eliminar">
                                    🗑️
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
