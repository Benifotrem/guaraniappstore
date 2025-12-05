<!-- Widget de Feedback Flotante -->
<div id="feedback-widget" class="feedback-widget">
    <button id="feedback-btn" class="feedback-btn" title="Enviar feedback">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <span>Feedback</span>
    </button>
</div>

<!-- Modal de Feedback -->
<div id="feedback-modal" class="feedback-modal" style="display: none;">
    <div class="feedback-modal-overlay"></div>
    <div class="feedback-modal-content">
        <div class="feedback-modal-header">
            <h3>Enviar Feedback</h3>
            <button class="feedback-modal-close" id="feedback-close-btn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="feedback-modal-body">
            <!-- Selección de tipo de feedback -->
            <div class="feedback-type-selector">
                <button type="button" class="feedback-type-btn active" data-type="bug">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="8" y="6" width="8" height="15" rx="1"></rect>
                        <path d="M9 9h6"></path>
                        <path d="M9 13h6"></path>
                        <path d="M9 17h6"></path>
                        <path d="M12 2v4"></path>
                        <path d="M8 6h8"></path>
                    </svg>
                    <span>Reportar Bug</span>
                </button>
                <button type="button" class="feedback-type-btn" data-type="feature">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                    <span>Sugerir Feature</span>
                </button>
                <button type="button" class="feedback-type-btn" data-type="review">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                    <span>Dejar Review</span>
                </button>
            </div>

            <!-- Formulario de feedback -->
            <form id="feedback-form" class="feedback-form">
                <input type="hidden" name="webapp_id" value="<?php echo isset($webapp['id']) ? htmlspecialchars($webapp['id']) : ''; ?>">
                <input type="hidden" name="type" id="feedback-type" value="bug">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                <!-- Información de beta tester (opcional) -->
                <div class="feedback-field">
                    <label for="feedback-email">Tu email (opcional para seguimiento)</label>
                    <input type="email" id="feedback-email" name="email" placeholder="tu@email.com">
                    <small>Si eres beta tester, usa el email con el que te registraste</small>
                </div>

                <!-- Título -->
                <div class="feedback-field">
                    <label for="feedback-title">Título *</label>
                    <input type="text" id="feedback-title" name="title" required maxlength="255" placeholder="Breve descripción del problema o sugerencia">
                </div>

                <!-- Descripción -->
                <div class="feedback-field">
                    <label for="feedback-description">Descripción detallada *</label>
                    <textarea id="feedback-description" name="description" required rows="5" placeholder="Describe con el mayor detalle posible..."></textarea>
                    <small class="char-counter">Mínimo 20 caracteres</small>
                </div>

                <!-- Severidad (solo para bugs) -->
                <div class="feedback-field" id="severity-field" style="display: none;">
                    <label for="feedback-severity">Severidad *</label>
                    <select id="feedback-severity" name="severity">
                        <option value="low">Baja - Problema menor</option>
                        <option value="medium" selected>Media - Afecta funcionalidad</option>
                        <option value="high">Alta - Impide usar la app</option>
                        <option value="critical">Crítica - La app no funciona</option>
                    </select>
                </div>

                <!-- Rating (solo para reviews) -->
                <div class="feedback-field" id="rating-field" style="display: none;">
                    <label>Tu calificación *</label>
                    <div class="rating-stars">
                        <input type="radio" name="rating" value="5" id="star5">
                        <label for="star5">★</label>
                        <input type="radio" name="rating" value="4" id="star4">
                        <label for="star4">★</label>
                        <input type="radio" name="rating" value="3" id="star3" checked>
                        <label for="star3">★</label>
                        <input type="radio" name="rating" value="2" id="star2">
                        <label for="star2">★</label>
                        <input type="radio" name="rating" value="1" id="star1">
                        <label for="star1">★</label>
                    </div>
                </div>

                <!-- Screenshot (opcional) -->
                <div class="feedback-field">
                    <label for="feedback-screenshot">Screenshot (opcional)</label>
                    <input type="file" id="feedback-screenshot" name="screenshot" accept="image/*">
                    <small>Ayúdanos con una captura de pantalla si aplica</small>
                </div>

                <!-- Mensajes -->
                <div id="feedback-message" class="feedback-message" style="display: none;"></div>

                <!-- Botones -->
                <div class="feedback-actions">
                    <button type="button" class="btn-secondary" id="feedback-cancel-btn">Cancelar</button>
                    <button type="submit" class="btn-primary" id="feedback-submit-btn">
                        <span class="btn-text">Enviar Feedback</span>
                        <span class="btn-loader" style="display: none;">Enviando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Widget flotante */
.feedback-widget {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 999;
}

.feedback-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: var(--guarani-primary, #10b981);
    color: white;
    border: none;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    transition: all 0.3s ease;
}

.feedback-btn:hover {
    background: var(--guarani-primary-dark, #059669);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.5);
}

/* Modal */
.feedback-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.feedback-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(4px);
}

.feedback-modal-content {
    position: relative;
    background: white;
    border-radius: 16px;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.feedback-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px;
    border-bottom: 1px solid #e5e7eb;
}

.feedback-modal-header h3 {
    margin: 0;
    font-size: 20px;
    color: #111827;
}

.feedback-modal-close {
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;
    color: #6b7280;
    transition: color 0.2s;
}

.feedback-modal-close:hover {
    color: #111827;
}

.feedback-modal-body {
    padding: 24px;
}

/* Selector de tipo */
.feedback-type-selector {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}

.feedback-type-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 16px;
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 13px;
    font-weight: 500;
    color: #6b7280;
}

.feedback-type-btn:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.feedback-type-btn.active {
    background: #ecfdf5;
    border-color: var(--guarani-primary, #10b981);
    color: var(--guarani-primary, #10b981);
}

/* Campos del formulario */
.feedback-field {
    margin-bottom: 20px;
}

.feedback-field label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.feedback-field input[type="text"],
.feedback-field input[type="email"],
.feedback-field textarea,
.feedback-field select {
    width: 100%;
    padding: 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.feedback-field input:focus,
.feedback-field textarea:focus,
.feedback-field select:focus {
    outline: none;
    border-color: var(--guarani-primary, #10b981);
}

.feedback-field small {
    display: block;
    margin-top: 4px;
    font-size: 12px;
    color: #6b7280;
}

/* Rating stars */
.rating-stars {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 8px;
}

.rating-stars input {
    display: none;
}

.rating-stars label {
    font-size: 32px;
    color: #d1d5db;
    cursor: pointer;
    transition: color 0.2s;
}

.rating-stars input:checked ~ label,
.rating-stars label:hover,
.rating-stars label:hover ~ label {
    color: #fbbf24;
}

/* Mensajes */
.feedback-message {
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 16px;
    font-size: 14px;
}

.feedback-message.success {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #10b981;
}

.feedback-message.error {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #ef4444;
}

/* Botones */
.feedback-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 24px;
}

.btn-secondary,
.btn-primary {
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-secondary {
    background: white;
    color: #374151;
    border: 1px solid #d1d5db;
}

.btn-secondary:hover {
    background: #f9fafb;
}

.btn-primary {
    background: var(--guarani-primary, #10b981);
    color: white;
    border: none;
}

.btn-primary:hover {
    background: var(--guarani-primary-dark, #059669);
}

.btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
    .feedback-type-selector {
        grid-template-columns: 1fr;
    }

    .feedback-widget {
        bottom: 20px;
        right: 20px;
    }

    .feedback-btn span {
        display: none;
    }

    .feedback-btn {
        width: 56px;
        height: 56px;
        padding: 0;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const widget = document.getElementById('feedback-widget');
    const modal = document.getElementById('feedback-modal');
    const openBtn = document.getElementById('feedback-btn');
    const closeBtn = document.getElementById('feedback-close-btn');
    const cancelBtn = document.getElementById('feedback-cancel-btn');
    const overlay = modal.querySelector('.feedback-modal-overlay');
    const form = document.getElementById('feedback-form');
    const typeButtons = document.querySelectorAll('.feedback-type-btn');
    const typeInput = document.getElementById('feedback-type');
    const severityField = document.getElementById('severity-field');
    const ratingField = document.getElementById('rating-field');
    const descriptionField = document.getElementById('feedback-description');
    const charCounter = document.querySelector('.char-counter');

    // Abrir modal
    openBtn.addEventListener('click', () => {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    });

    // Cerrar modal
    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        form.reset();
        showMessage('', '');
    }

    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', closeModal);

    // Cambiar tipo de feedback
    typeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            // Actualizar botones activos
            typeButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Actualizar tipo
            const type = btn.dataset.type;
            typeInput.value = type;

            // Mostrar/ocultar campos según tipo
            severityField.style.display = type === 'bug' ? 'block' : 'none';
            ratingField.style.display = type === 'review' ? 'block' : 'none';

            // Actualizar placeholder de descripción
            const placeholders = {
                'bug': '¿Qué problema encontraste? ¿Cuándo ocurre? ¿Pasos para reproducirlo?',
                'feature': '¿Qué funcionalidad te gustaría ver? ¿Cómo te ayudaría?',
                'review': '¿Qué te pareció la aplicación? ¿Qué destacarías?'
            };
            descriptionField.placeholder = placeholders[type];
        });
    });

    // Contador de caracteres
    descriptionField.addEventListener('input', () => {
        const length = descriptionField.value.length;
        charCounter.textContent = length < 20 ? `Mínimo 20 caracteres (${length}/20)` : `${length} caracteres`;
        charCounter.style.color = length < 20 ? '#ef4444' : '#6b7280';
    });

    // Enviar formulario
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Validar descripción
        if (descriptionField.value.length < 20) {
            showMessage('La descripción debe tener al menos 20 caracteres', 'error');
            return;
        }

        // Deshabilitar botón
        const submitBtn = document.getElementById('feedback-submit-btn');
        submitBtn.disabled = true;
        submitBtn.querySelector('.btn-text').style.display = 'none';
        submitBtn.querySelector('.btn-loader').style.display = 'inline';

        try {
            const formData = new FormData(form);

            const response = await fetch('<?php echo BASE_URL; ?>/feedback/submit', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showMessage('¡Gracias por tu feedback! Lo revisaremos pronto.', 'success');
                setTimeout(() => {
                    closeModal();
                }, 2000);
            } else {
                showMessage(result.message || 'Error al enviar el feedback', 'error');
            }
        } catch (error) {
            showMessage('Error de conexión. Por favor intenta de nuevo.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.querySelector('.btn-text').style.display = 'inline';
            submitBtn.querySelector('.btn-loader').style.display = 'none';
        }
    });

    function showMessage(text, type) {
        const messageEl = document.getElementById('feedback-message');
        if (text) {
            messageEl.textContent = text;
            messageEl.className = `feedback-message ${type}`;
            messageEl.style.display = 'block';
        } else {
            messageEl.style.display = 'none';
        }
    }
});
</script>
