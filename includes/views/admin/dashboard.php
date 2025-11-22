<!-- Dashboard Stats -->
<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-value"><?php echo format_number($stats['total_webapps']); ?></div>
                <div class="stat-card-label">Webapps Publicadas</div>
            </div>
            <div class="stat-card-icon primary">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                </svg>
            </div>
        </div>
        <a href="<?php echo get_url('admin/webapps'); ?>" class="text-guarani-primary">
            Ver todas →
        </a>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-value"><?php echo format_number($stats['total_articles']); ?></div>
                <div class="stat-card-label">Artículos Publicados</div>
            </div>
            <div class="stat-card-icon info">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
        </div>
        <a href="<?php echo get_url('admin/blog'); ?>" class="text-guarani-primary">
            Ver todos →
        </a>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-value"><?php echo format_number($stats['total_subscribers']); ?></div>
                <div class="stat-card-label">Suscriptores Activos</div>
            </div>
            <div class="stat-card-icon success">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
        <a href="<?php echo get_url('admin/subscribers'); ?>" class="text-guarani-primary">
            Ver todos →
        </a>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-card-value"><?php echo format_number($stats['total_webapp_views'] + $stats['total_article_views']); ?></div>
                <div class="stat-card-label">Visitas Totales</div>
            </div>
            <div class="stat-card-icon warning">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-2 gap-4 mb-4">
    <div class="card">
        <h3 class="mb-3">Acciones Rápidas</h3>
        <div class="flex flex-col gap-2">
            <a href="<?php echo get_url('admin/webapps/create'); ?>" class="btn btn-primary">
                + Nueva Webapp
            </a>
            <a href="<?php echo get_url('admin/blog/create'); ?>" class="btn btn-primary">
                + Nuevo Artículo
            </a>
            <a href="<?php echo get_url('admin/blog/generate'); ?>" class="btn btn-success">
                🤖 Generar Artículo con IA
            </a>
        </div>
    </div>

    <div class="card">
        <h3 class="mb-3">Estado del Sistema</h3>
        <div class="flex flex-col gap-2">
            <div class="flex justify-between">
                <span>PHP Version:</span>
                <strong><?php echo PHP_VERSION; ?></strong>
            </div>
            <div class="flex justify-between">
                <span>Blog Automatizado:</span>
                <strong class="text-success">
                    <?php echo get_setting('blog_auto_generation_enabled') ? 'Activo' : 'Inactivo'; ?>
                </strong>
            </div>
            <div class="flex justify-between">
                <span>2FA:</span>
                <strong>
                    <?php echo $_SESSION['admin_user_2fa'] ?? false ? 'Activado' : 'Desactivado'; ?>
                </strong>
            </div>
        </div>
    </div>
</div>

<!-- Recent Webapps -->
<?php if (!empty($recent_webapps)): ?>
<div class="admin-table-container mb-4">
    <div style="padding: 1.5rem; border-bottom: 1px solid var(--border);">
        <h3>Webapps Recientes</h3>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Título</th>
                <th>Estado</th>
                <th>Visitas</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_webapps as $webapp): ?>
                <tr>
                    <td><strong><?php echo e($webapp['title']); ?></strong></td>
                    <td>
                        <span class="badge badge-<?php echo $webapp['status'] === 'published' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($webapp['status']); ?>
                        </span>
                    </td>
                    <td><?php echo format_number($webapp['view_count']); ?></td>
                    <td><?php echo format_date_es($webapp['created_at'], 'short'); ?></td>
                    <td>
                        <a href="<?php echo get_url('admin/webapps/edit?id=' . $webapp['id']); ?>" class="text-guarani-primary">
                            Editar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Recent Articles -->
<?php if (!empty($recent_articles)): ?>
<div class="admin-table-container mb-4">
    <div style="padding: 1.5rem; border-bottom: 1px solid var(--border);">
        <h3>Artículos Recientes</h3>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Título</th>
                <th>Estado</th>
                <th>Visitas</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_articles as $article): ?>
                <tr>
                    <td><strong><?php echo e($article['title']); ?></strong></td>
                    <td>
                        <span class="badge badge-<?php echo $article['status'] === 'published' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($article['status']); ?>
                        </span>
                    </td>
                    <td><?php echo format_number($article['view_count']); ?></td>
                    <td><?php echo format_date_es($article['published_at'], 'short'); ?></td>
                    <td>
                        <a href="<?php echo get_url('admin/blog/edit?id=' . $article['id']); ?>" class="text-guarani-primary">
                            Editar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
