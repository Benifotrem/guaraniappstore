<div class="mb-4">
    <a href="<?php echo get_url('admin/blog'); ?>" class="btn btn-secondary">
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

            <!-- Categoría -->
            <div class="form-group">
                <label for="category" class="form-label">Categoría</label>
                <input type="text"
                       id="category"
                       name="category"
                       class="form-input"
                       value="<?php echo e($form_data['category'] ?? ''); ?>"
                       placeholder="ej: Transformación Digital, IA para PYMEs">
            </div>

            <!-- Autor -->
            <div class="form-group">
                <label for="author_name" class="form-label">Autor</label>
                <input type="text"
                       id="author_name"
                       name="author_name"
                       class="form-input"
                       value="<?php echo e($form_data['author_name'] ?? 'César Ruzafa'); ?>">
            </div>

            <!-- Excerpt -->
            <div class="form-group admin-form-grid-full">
                <label for="excerpt" class="form-label">Extracto</label>
                <textarea id="excerpt"
                          name="excerpt"
                          class="form-textarea"
                          rows="2"
                          data-max-length="255"><?php echo e($form_data['excerpt'] ?? ''); ?></textarea>
                <small class="form-help">Resumen breve del artículo (150-200 caracteres)</small>
            </div>

            <!-- Contenido -->
            <div class="form-group admin-form-grid-full">
                <label for="content" class="form-label">Contenido (HTML) *</label>
                <textarea id="content"
                          name="content"
                          class="form-textarea"
                          rows="15"
                          required><?php echo e($form_data['content'] ?? ''); ?></textarea>
                <small class="form-help">Puedes usar HTML: &lt;h2&gt;, &lt;h3&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;strong&gt;, etc.</small>
            </div>

            <!-- Imagen destacada -->
            <div class="form-group">
                <label for="featured_image_url" class="form-label">URL Imagen Destacada</label>
                <input type="url"
                       id="featured_image_url"
                       name="featured_image_url"
                       class="form-input"
                       value="<?php echo e($form_data['featured_image_url'] ?? ''); ?>"
                       placeholder="https://...">
            </div>

            <!-- Tags -->
            <div class="form-group">
                <label for="tags" class="form-label">Tags</label>
                <input type="text"
                       id="tags"
                       name="tags"
                       class="form-input"
                       value="<?php echo e(implode(', ', json_decode($form_data['tags'] ?? '[]', true))); ?>"
                       placeholder="IA, PYMEs, Tecnología">
                <small class="form-help">Separados por coma</small>
            </div>

            <!-- SEO Title -->
            <div class="form-group">
                <label for="seo_title" class="form-label">SEO Title</label>
                <input type="text"
                       id="seo_title"
                       name="seo_title"
                       class="form-input"
                       value="<?php echo e($form_data['seo_title'] ?? ''); ?>"
                       placeholder="Título optimizado para buscadores">
            </div>

            <!-- SEO Description -->
            <div class="form-group">
                <label for="seo_description" class="form-label">SEO Description</label>
                <textarea id="seo_description"
                          name="seo_description"
                          class="form-textarea"
                          rows="2"
                          data-max-length="160"><?php echo e($form_data['seo_description'] ?? ''); ?></textarea>
                <small class="form-help">Máximo 160 caracteres</small>
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
        </div>

        <div class="flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <?php echo isset($article) ? 'Actualizar' : 'Crear'; ?> Artículo
            </button>
            <a href="<?php echo get_url('admin/blog'); ?>" class="btn btn-secondary">
                Cancelar
            </a>
        </div>
    </form>
</div>
