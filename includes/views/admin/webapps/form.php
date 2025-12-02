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

                <!-- Upload Zone -->
                <div class="screenshot-upload-zone" id="screenshotUploadZone">
                    <div class="upload-zone-content">
                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <h3>Arrastra imágenes aquí</h3>
                        <p>o haz clic para seleccionar</p>
                        <input type="file"
                               id="screenshotFileInput"
                               accept="image/jpeg,image/png,image/gif,image/webp"
                               multiple
                               style="display: none;">
                        <button type="button" class="btn btn-secondary mt-2" onclick="document.getElementById('screenshotFileInput').click()">
                            Seleccionar Imágenes
                        </button>
                        <small class="mt-2" style="display: block; opacity: 0.7;">JPG, PNG, GIF, WEBP • Máx 10MB por imagen</small>
                    </div>
                    <div class="upload-zone-progress" id="uploadProgress" style="display: none;">
                        <div class="progress-bar">
                            <div class="progress-fill" id="progressFill"></div>
                        </div>
                        <p id="progressText">Subiendo...</p>
                    </div>
                </div>

                <!-- Preview Zone -->
                <div class="screenshot-preview-zone" id="screenshotPreviewZone"></div>

                <!-- Textarea con URLs (mantener compatibilidad) -->
                <div class="mt-3">
                    <label for="screenshots" class="form-label" style="font-size: 0.875rem; opacity: 0.8;">URLs de Screenshots (una por línea)</label>
                    <textarea id="screenshots"
                              name="screenshots"
                              class="form-textarea"
                              rows="4"
                              placeholder="Una URL por línea:&#10;https://ejemplo.com/screenshot1.jpg&#10;https://ejemplo.com/screenshot2.jpg"><?php
                        $screenshots = json_decode($form_data['screenshots'] ?? '[]', true);
                        if (is_array($screenshots) && !empty($screenshots)) {
                            echo e(implode("\n", $screenshots));
                        }
                    ?></textarea>
                    <small class="form-help">Puedes subir imágenes arriba o pegar URLs aquí. Las imágenes se almacenarán localmente.</small>
                </div>
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

<script>
(function() {
    const uploadZone = document.getElementById('screenshotUploadZone');
    const fileInput = document.getElementById('screenshotFileInput');
    const previewZone = document.getElementById('screenshotPreviewZone');
    const screenshotsTextarea = document.getElementById('screenshots');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');

    // Drag & Drop handlers
    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('drag-over');
    });

    uploadZone.addEventListener('dragleave', () => {
        uploadZone.classList.remove('drag-over');
    });

    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('drag-over');
        const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
        if (files.length > 0) {
            handleFiles(files);
        }
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        if (files.length > 0) {
            handleFiles(files);
        }
    });

    // Handle file uploads
    async function handleFiles(files) {
        const validFiles = files.filter(f => {
            if (!f.type.match(/^image\/(jpeg|png|gif|webp)$/)) {
                alert(`El archivo ${f.name} no es un formato válido (JPG, PNG, GIF, WEBP)`);
                return false;
            }
            if (f.size > 10 * 1024 * 1024) {
                alert(`El archivo ${f.name} es demasiado grande (máx 10MB)`);
                return false;
            }
            return true;
        });

        if (validFiles.length === 0) return;

        // Show progress
        uploadProgress.style.display = 'block';
        progressText.textContent = `Subiendo ${validFiles.length} imagen(es)...`;

        let uploaded = 0;
        const totalFiles = validFiles.length;

        for (const file of validFiles) {
            try {
                const url = await uploadFile(file);
                addScreenshotUrl(url);
                addPreview(url, file.name);
                uploaded++;
                const percent = Math.round((uploaded / totalFiles) * 100);
                progressFill.style.width = percent + '%';
                progressText.textContent = `${uploaded}/${totalFiles} subidas`;
            } catch (error) {
                alert(`Error al subir ${file.name}: ${error.message}`);
            }
        }

        // Hide progress after 2 seconds
        setTimeout(() => {
            uploadProgress.style.display = 'none';
            progressFill.style.width = '0%';
        }, 2000);

        // Clear file input
        fileInput.value = '';
    }

    // Upload single file
    function uploadFile(file) {
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('screenshot', file);

            fetch('<?php echo get_url('api/upload-screenshot'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resolve(data.url);
                } else {
                    reject(new Error(data.error || 'Error al subir'));
                }
            })
            .catch(error => reject(error));
        });
    }

    // Add URL to textarea
    function addScreenshotUrl(url) {
        const currentUrls = screenshotsTextarea.value.trim();
        const urls = currentUrls ? currentUrls.split('\n') : [];
        if (!urls.includes(url)) {
            urls.push(url);
            screenshotsTextarea.value = urls.join('\n');
        }
    }

    // Add preview
    function addPreview(url, name) {
        const preview = document.createElement('div');
        preview.className = 'screenshot-preview-item';
        preview.innerHTML = `
            <img src="${url}" alt="${name}">
            <div class="preview-overlay">
                <button type="button" class="preview-remove" onclick="removeScreenshot('${url}', this)" title="Eliminar">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
            <span class="preview-name">${name}</span>
        `;
        previewZone.appendChild(preview);
    }

    // Remove screenshot (global function)
    window.removeScreenshot = function(url, button) {
        // Remove from textarea
        const urls = screenshotsTextarea.value.split('\n').filter(u => u.trim() !== url);
        screenshotsTextarea.value = urls.join('\n');

        // Remove preview
        const previewItem = button.closest('.screenshot-preview-item');
        if (previewItem) {
            previewItem.remove();
        }
    };

    // Load existing screenshots on page load
    function loadExistingScreenshots() {
        const urls = screenshotsTextarea.value.trim().split('\n').filter(u => u.trim());
        urls.forEach(url => {
            const filename = url.split('/').pop();
            addPreview(url, filename);
        });
    }

    // Initialize
    loadExistingScreenshots();
})();
</script>

<style>
.screenshot-upload-zone {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    background: #fafafa;
    transition: all 0.3s ease;
    cursor: pointer;
}

.screenshot-upload-zone.drag-over {
    border-color: var(--primary-color, #4f46e5);
    background: #f0f0ff;
}

.upload-zone-content svg {
    margin: 0 auto 1rem;
    opacity: 0.5;
}

.upload-zone-content h3 {
    margin: 0 0 0.5rem;
    font-size: 1.125rem;
    color: #333;
}

.upload-zone-content p {
    margin: 0;
    color: #666;
}

.upload-zone-progress {
    padding: 1rem 0;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e0e0e0;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-fill {
    height: 100%;
    background: var(--primary-color, #4f46e5);
    width: 0%;
    transition: width 0.3s ease;
}

#progressText {
    margin: 0;
    font-size: 0.875rem;
    color: #666;
}

.screenshot-preview-zone {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.screenshot-preview-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #ddd;
    background: white;
}

.screenshot-preview-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    display: block;
}

.preview-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.screenshot-preview-item:hover .preview-overlay {
    opacity: 1;
}

.preview-remove {
    background: white;
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #dc2626;
    transition: transform 0.2s ease;
}

.preview-remove:hover {
    transform: scale(1.1);
}

.preview-name {
    display: block;
    padding: 0.5rem;
    font-size: 0.75rem;
    color: #666;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
