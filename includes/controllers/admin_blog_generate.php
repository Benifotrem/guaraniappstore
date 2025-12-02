<?php
/**
 * Controlador: Admin Blog - Generar artículo con IA
 */

require_admin_auth();

$page_title = 'Generar Artículo con IA';
include INCLUDES_PATH . '/views/admin/layout/header.php';
?>

<div class="mb-4">
    <a href="<?php echo get_url('admin/blog'); ?>" class="btn btn-secondary">
        ← Volver
    </a>
</div>

<div id="error-message" class="alert alert-error" style="display: none;"></div>

<div class="card" style="padding: 3rem; text-align: center;">
    <div style="font-size: 4rem; margin-bottom: 1rem;">🤖</div>
    <h2>Generar Artículo con Inteligencia Artificial</h2>
    <p style="max-width: 600px; margin: 1rem auto 2rem; opacity: 0.8;">
        El sistema generará automáticamente un artículo sobre IA aplicada a PYMEs,
        analizando tendencias en Latinoamérica y dando preferencia a las apps publicadas.
    </p>

    <!-- Botón de generación -->
    <div id="generate-button-container">
        <button id="generate-btn" class="btn btn-success btn-lg">
            ⚡ Generar Artículo Ahora
        </button>

        <div class="mt-4" style="font-size: 0.875rem; opacity: 0.7;">
            <p><strong>Configuración actual:</strong></p>
            <p>Modelo de texto: DeepSeek R1</p>
            <p>Modelo de imágenes: <?php echo e(get_setting('image_generation_model', 'openai/gpt-5-image-mini')); ?></p>
            <p>Autor: <?php echo e(get_setting('blog_author_name', 'César Ruzafa')); ?></p>
            <p>Longitud: 1200-1800 palabras</p>
        </div>
    </div>

    <!-- Barra de progreso -->
    <div id="progress-container" style="display: none; max-width: 600px; margin: 0 auto;">
        <div class="progress-steps mb-4">
            <div class="progress-step" data-step="1">
                <div class="step-icon">📊</div>
                <div class="step-text">Analizando tendencias</div>
            </div>
            <div class="progress-step" data-step="2">
                <div class="step-icon">📱</div>
                <div class="step-text">Obteniendo webapps</div>
            </div>
            <div class="progress-step" data-step="3">
                <div class="step-icon">✍️</div>
                <div class="step-text">Generando contenido</div>
            </div>
            <div class="progress-step" data-step="4">
                <div class="step-icon">💾</div>
                <div class="step-text">Guardando artículo</div>
            </div>
            <div class="progress-step" data-step="5">
                <div class="step-icon">🎨</div>
                <div class="step-text">Creando imagen</div>
            </div>
        </div>

        <div class="progress-bar-container">
            <div class="progress-bar" id="progress-bar"></div>
        </div>
        <p class="progress-text mt-2" id="progress-text">Iniciando...</p>
    </div>
</div>

<style>
.progress-steps {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.progress-step {
    flex: 1;
    text-align: center;
    opacity: 0.3;
    transition: opacity 0.3s ease;
}

.progress-step.active {
    opacity: 1;
}

.progress-step.completed {
    opacity: 0.6;
}

.step-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.step-text {
    font-size: 0.875rem;
    font-weight: 500;
}

.progress-bar-container {
    width: 100%;
    height: 8px;
    background: var(--border);
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--guarani-primary), var(--guarani-secondary));
    width: 0%;
    transition: width 0.5s ease;
}

.progress-text {
    font-size: 0.875rem;
    opacity: 0.8;
}
</style>

<script>
document.getElementById('generate-btn').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;

    // Ocultar botón y mostrar progreso
    document.getElementById('generate-button-container').style.display = 'none';
    document.getElementById('progress-container').style.display = 'block';

    // Definir pasos y duraciones estimadas (en ms)
    const steps = [
        { step: 1, text: 'Analizando tendencias de Google...', duration: 2000 },
        { step: 2, text: 'Obteniendo webapps publicadas...', duration: 2000 },
        { step: 3, text: 'Generando contenido con IA... (puede tomar 30-60 segundos)', duration: 35000 },
        { step: 4, text: 'Generando imagen con IA... (puede tomar 15-30 segundos)', duration: 20000 },
        { step: 5, text: 'Guardando artículo en la base de datos...', duration: 3000 }
    ];

    let currentStep = 0;
    let totalDuration = steps.reduce((sum, s) => sum + s.duration, 0);
    let elapsedDuration = 0;

    function updateProgress() {
        if (currentStep >= steps.length) return;

        const step = steps[currentStep];

        // Actualizar texto
        const progressText = document.getElementById('progress-text');
        if (progressText) {
            progressText.textContent = step.text;
        }

        // Marcar paso como activo
        const stepElements = document.querySelectorAll('.progress-step');
        stepElements.forEach((el, idx) => {
            el.classList.remove('active');
            if (idx < step.step - 1) {
                el.classList.add('completed');
            }
            if (idx === step.step - 1) {
                el.classList.add('active');
            }
        });

        // Actualizar barra de progreso
        elapsedDuration += (currentStep > 0 ? steps[currentStep - 1].duration : 0);
        const progressPercent = (elapsedDuration / totalDuration) * 100;
        const progressBar = document.getElementById('progress-bar');
        if (progressBar) {
            progressBar.style.width = progressPercent + '%';
        }

        currentStep++;

        if (currentStep < steps.length) {
            setTimeout(updateProgress, step.duration);
        }
    }

    // Iniciar animación de progreso
    updateProgress();

    // Hacer llamada AJAX
    fetch('<?php echo get_url("ajax/blog-generate"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'csrf_token=<?php echo generate_csrf_token(); ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Completar barra de progreso
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');

            if (progressBar) {
                progressBar.style.width = '100%';
            }
            if (progressText) {
                progressText.textContent = '¡Artículo generado exitosamente!';
            }

            // Marcar todos los pasos como completados
            document.querySelectorAll('.progress-step').forEach(el => {
                el.classList.remove('active');
                el.classList.add('completed');
            });

            // Redirigir después de 1 segundo
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } else {
            // Mostrar error
            const errorMsg = document.getElementById('error-message');
            const progressContainer = document.getElementById('progress-container');
            const buttonContainer = document.getElementById('generate-button-container');

            if (errorMsg) {
                errorMsg.textContent = 'Error: ' + (data.error || 'Error desconocido');
                errorMsg.style.display = 'block';
            }
            if (progressContainer) {
                progressContainer.style.display = 'none';
            }
            if (buttonContainer) {
                buttonContainer.style.display = 'block';
            }
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const errorMsg = document.getElementById('error-message');
        const progressContainer = document.getElementById('progress-container');
        const buttonContainer = document.getElementById('generate-button-container');

        if (errorMsg) {
            errorMsg.textContent = 'Error de conexión: ' + error.message;
            errorMsg.style.display = 'block';
        }
        if (progressContainer) {
            progressContainer.style.display = 'none';
        }
        if (buttonContainer) {
            buttonContainer.style.display = 'block';
        }
        btn.disabled = false;
    });
});
</script>

<?php include INCLUDES_PATH . '/views/admin/layout/footer.php'; ?>
