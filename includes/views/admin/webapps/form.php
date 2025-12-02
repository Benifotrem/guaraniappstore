<div class="mb-4">
    <a href="<?php echo get_url('admin/webapps'); ?>" class="btn btn-secondary">
        ← Volver
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul style="margin: 0; padding-left: 1.5rem;">
            <?php foreach ($errors as $error): ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="admin-form-container">
    <form method="POST" class="admin-form">
        <?php echo csrf_field(); ?>

        <div class="admin-form-grid">
            <!-- Título -->
            <div class="form-group">
                <label for="title" class="form-label">Título *</label>
                <input type="text"
                       id="title"
                       name="title"
                       class="form-input"
                       value="<?php echo e($form_data['title'] ?? ''); ?>"
                       required>
            </div>

            <!-- Slug -->
            <div class="form-group">
                <label for="slug" class="form-label">Slug</label>
                <input type="text"
                       id="slug"
                       name="slug"
                       class="form-input"
                       value="<?php echo e($form_data['slug'] ?? ''); ?>"
                       data-auto-generate="true">
                <small class="form-help">Se genera automáticamente del título</small>
            </div>

            <!-- URL de la App -->
            <div class="form-group">
                <label for="app_url" class="form-label">URL de la Aplicación</label>
                <input type="url"
                       id="app_url"
                       name="app_url"
                       class="form-input"
                       value="<?php echo e($form_data['app_url'] ?? ''); ?>"
                       placeholder="https://...">
            </div>

            <!-- Categoría -->
            <div class="form-group">
                <label for="category" class="form-label">Categoría</label>
                <input type="text"
                       id="category"
                       name="category"
                       class="form-input"
                       value="<?php echo e($form_data['category'] ?? ''); ?>"
                       placeholder="ej: Plataformas Web, E-commerce, etc">
            </div>

            <!-- Descripción corta -->
            <div class="form-group admin-form-grid-full">
                <label for="short_description" class="form-label">Descripción Corta</label>
                <textarea id="short_description"
                          name="short_description"
                          class="form-textarea"
                          rows="2"
                          data-max-length="255"><?php echo e($form_data['short_description'] ?? ''); ?></textarea>
            </div>

            <!-- Descripción completa -->
            <div class="form-group admin-form-grid-full">
                <label for="full_description" class="form-label">Descripción Completa</label>
                <textarea id="full_description"
                          name="full_description"
                          class="form-textarea"
                          rows="6"><?php echo e($form_data['full_description'] ?? ''); ?></textarea>
            </div>

            <!-- Logo URL -->
            <div class="form-group">
                <label for="logo_url" class="form-label">URL del Logo</label>
                <input type="url"
                       id="logo_url"
                       name="logo_url"
                       class="form-input"
                       value="<?php echo e($form_data['logo_url'] ?? ''); ?>"
                       placeholder="https://...">
            </div>

            <!-- Cover Image URL -->
            <div class="form-group">
                <label for="cover_image_url" class="form-label">URL de Imagen de Portada</label>
                <input type="url"
                       id="cover_image_url"
                       name="cover_image_url"
                       class="form-input"
                       value="<?php echo e($form_data['cover_image_url'] ?? ''); ?>"
                       placeholder="https://...">
            </div>

            <!-- Screenshots -->
            <div class="form-group admin-form-grid-full">
                <label for="screenshots" class="form-label">Screenshots (Capturas de Pantalla)</label>
                <textarea id="screenshots"
                          name="screenshots"
                          class="form-textarea"
                          rows="4"
                          placeholder="Una URL por línea:&#10;https://ejemplo.com/screenshot1.jpg&#10;https://ejemplo.com/screenshot2.jpg&#10;https://ejemplo.com/screenshot3.jpg"><?php
                    $screenshots = json_decode($form_data['screenshots'] ?? '[]', true);
                    if (is_array($screenshots) && !empty($screenshots)) {
                        echo e(implode("\n", $screenshots));
                    }
                ?></textarea>
                <small class="form-help">Una URL por línea. Se descargarán y almacenarán localmente. Se mostrará la primera en las tarjetas.</small>
            </div>

            <!-- Tags -->
            <div class="form-group">
                <label for="tags" class="form-label">Tags</label>
                <input type="text"
                       id="tags"
                       name="tags"
                       class="form-input"
                       value="<?php echo e(implode(', ', json_decode($form_data['tags'] ?? '[]', true))); ?>"
                       placeholder="tag1, tag2, tag3">
                <small class="form-help">Separados por coma</small>
            </div>

            <!-- Tech Stack -->
            <div class="form-group">
                <label for="tech_stack" class="form-label">Stack Tecnológico</label>
                <input type="text"
                       id="tech_stack"
                       name="tech_stack"
                       class="form-input"
                       value="<?php echo e(implode(', ', json_decode($form_data['tech_stack'] ?? '[]', true))); ?>"
                       placeholder="PHP, MySQL, JavaScript">
                <small class="form-help">Separados por coma</small>
            </div>

            <!-- Estado -->
            <div class="form-group">
                <label for="status" class="form-label">Estado</label>
                <select id="status" name="status" class="form-select">
                    <option value="draft" <?php echo ($form_data['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>
                        Borrador
                    </option>
                    <option value="published" <?php echo ($form_data['status'] ?? '') === 'published' ? 'selected' : ''; ?>>
                        Publicado
                    </option>
                    <option value="archived" <?php echo ($form_data['status'] ?? '') === 'archived' ? 'selected' : ''; ?>>
                        Archivado
                    </option>
                </select>
            </div>

            <!-- Orden -->
            <div class="form-group">
                <label for="sort_order" class="form-label">Orden de visualización</label>
                <input type="number"
                       id="sort_order"
                       name="sort_order"
                       class="form-input"
                       value="<?php echo e($form_data['sort_order'] ?? 0); ?>"
                       min="0">
                <small class="form-help">Menor número = mayor prioridad</small>
            </div>

            <!-- Destacada -->
            <div class="form-group admin-form-grid-full">
                <label class="flex items-center gap-2">
                    <input type="checkbox"
                           name="is_featured"
                           <?php echo ($form_data['is_featured'] ?? false) ? 'checked' : ''; ?>>
                    <span>Marcar como destacada (aparecerá en la home)</span>
                </label>
            </div>
        </div>

        <div class="flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <?php echo isset($webapp) ? 'Actualizar' : 'Crear'; ?> Webapp
            </button>
            <a href="<?php echo get_url('admin/webapps'); ?>" class="btn btn-secondary">
                Cancelar
            </a>
        </div>
    </form>
</div>
