<div class="flex justify-between items-center mb-4">
    <h2>Webapps</h2>
    <a href="<?php echo get_url('admin/webapps/create'); ?>" class="btn btn-primary btn-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva Webapp
    </a>
</div>

<?php if (empty($webapps)): ?>
    <div class="card text-center" style="padding: 3rem;">
        <h3>No hay webapps registradas</h3>
        <p class="mb-3">Comienza creando tu primera webapp</p>
        <a href="<?php echo get_url('admin/webapps/create'); ?>" class="btn btn-primary">
            + Crear Webapp
        </a>
    </div>
<?php else: ?>
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Estado</th>
                    <th>Categoría</th>
                    <th>Destacada</th>
                    <th>Visitas</th>
                    <th>Clics</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($webapps as $webapp): ?>
                    <tr>
                        <td>
                            <strong><?php echo e($webapp['title']); ?></strong>
                            <div style="font-size: 0.875rem; opacity: 0.7;">
                                /<?php echo e($webapp['slug']); ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-<?php
                                echo $webapp['status'] === 'published' ? 'success' :
                                     ($webapp['status'] === 'draft' ? 'warning' : 'danger');
                            ?>">
                                <?php echo ucfirst($webapp['status']); ?>
                            </span>
                        </td>
                        <td><?php echo e($webapp['category'] ?? '-'); ?></td>
                        <td>
                            <?php if ($webapp['is_featured']): ?>
                                <span class="badge badge-info">⭐ Sí</span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?php echo format_number($webapp['view_count']); ?></td>
                        <td><?php echo format_number($webapp['click_count']); ?></td>
                        <td><?php echo format_date_es($webapp['created_at'], 'short'); ?></td>
                        <td>
                            <div class="flex gap-2">
                                <a href="<?php echo get_url('webapp/' . $webapp['slug']); ?>"
                                   target="_blank"
                                   class="btn btn-sm btn-secondary"
                                   title="Ver">
                                    👁
                                </a>
                                <a href="<?php echo get_url('admin/webapps/edit?id=' . $webapp['id']); ?>"
                                   class="btn btn-sm btn-primary"
                                   title="Editar">
                                    ✏️
                                </a>
                                <a href="<?php echo get_url('admin/webapps/delete?id=' . $webapp['id']); ?>"
                                   class="btn btn-sm btn-danger"
                                   data-confirm-delete="¿Eliminar esta webapp?"
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
