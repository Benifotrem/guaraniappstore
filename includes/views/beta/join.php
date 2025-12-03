<?php
$page_title = 'Únete como Beta Tester - Guarani App Store';
include INCLUDES_PATH . '/views/landing/header.php';
?>

<style>
/* Beta Landing Page Styles */
.beta-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 6rem 0 4rem;
    position: relative;
    overflow: hidden;
}

.beta-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="2" height="2" fill="white" fill-opacity="0.1"/></svg>');
    opacity: 0.3;
}

.beta-hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: white;
}

.beta-hero h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: white;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.beta-hero-subtitle {
    font-size: 1.25rem;
    margin-bottom: 2rem;
    opacity: 0.95;
}

.beta-badge {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 0.5rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    margin-bottom: 2rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.beta-benefits {
    padding: 4rem 0;
    background: #f8f9fa;
}

.benefit-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 100%;
}

.benefit-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}

.benefit-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: block;
}

.benefit-card h3 {
    font-size: 1.25rem;
    margin-bottom: 1rem;
    color: #2d3748;
}

.benefit-card p {
    color: #718096;
    margin-bottom: 0;
}

.beta-form-section {
    padding: 4rem 0;
}

.form-container {
    max-width: 600px;
    margin: 0 auto;
    background: white;
    padding: 3rem;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

.form-container h2 {
    text-align: center;
    margin-bottom: 2rem;
    color: #2d3748;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #2d3748;
}

.form-group label .required {
    color: #e53e3e;
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.2s ease;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-textarea {
    min-height: 120px;
    resize: vertical;
}

.form-help {
    font-size: 0.875rem;
    color: #718096;
    margin-top: 0.25rem;
}

.btn-beta-submit {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1.125rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-beta-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

@media (max-width: 768px) {
    .beta-hero h1 {
        font-size: 2rem;
    }

    .beta-hero-subtitle {
        font-size: 1rem;
    }

    .form-container {
        padding: 2rem 1rem;
    }
}
</style>

<!-- Hero Section -->
<section class="beta-hero">
    <div class="container">
        <div class="beta-hero-content">
            <div class="beta-badge">
                🚀 Plazas Limitadas
            </div>
            <h1 class="animate-fade-in-up">
                Sé Parte del Futuro:<br>Beta Tester Exclusivo
            </h1>
            <p class="beta-hero-subtitle">
                Ayúdanos a crear las mejores aplicaciones para PYMEs<br>
                y obtén beneficios exclusivos de por vida
            </p>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="beta-benefits">
    <div class="container">
        <div class="text-center mb-5">
            <h2>¿Qué Ganas Como Beta Tester?</h2>
            <p class="text-muted">Beneficios exclusivos que solo nuestros testers reciben</p>
        </div>

        <div class="grid grid-cols-3" style="gap: 2rem;">
            <div class="benefit-card">
                <span class="benefit-icon">🎁</span>
                <h3>Acceso Gratuito de por Vida</h3>
                <p>Usa todas nuestras aplicaciones sin costo alguno, incluso cuando salgan de beta</p>
            </div>

            <div class="benefit-card">
                <span class="benefit-icon">👑</span>
                <h3>Features Premium Gratis</h3>
                <p>Acceso a todas las características premium sin pagar nada extra</p>
            </div>

            <div class="benefit-card">
                <span class="benefit-icon">🏆</span>
                <h3>Reconocimiento Público</h3>
                <p>Tu nombre en los créditos de la app si contribuyes significativamente</p>
            </div>

            <div class="benefit-card">
                <span class="benefit-icon">💬</span>
                <h3>Línea Directa con Devs</h3>
                <p>Canal privado de Discord/Telegram con el equipo de desarrollo</p>
            </div>

            <div class="benefit-card">
                <span class="benefit-icon">🚀</span>
                <h3>Acceso Anticipado</h3>
                <p>Prueba nuevas features antes que nadie y da tu opinión</p>
            </div>

            <div class="benefit-card">
                <span class="benefit-icon">🎯</span>
                <h3>Influencia Real</h3>
                <p>Tus sugerencias tienen peso en las decisiones de producto</p>
            </div>
        </div>
    </div>
</section>

<!-- Registration Form -->
<section class="beta-form-section">
    <div class="container">
        <div class="form-container">
            <h2>Solicita tu Acceso</h2>

            <?php if (isset($_SESSION['beta_success'])): ?>
                <div style="padding: 1rem; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; margin-bottom: 2rem; color: #155724;">
                    <strong>¡Registro exitoso!</strong><br>
                    Revisa tu email para confirmar tu registro y recibir las instrucciones de acceso.
                </div>
                <?php unset($_SESSION['beta_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['beta_error'])): ?>
                <div style="padding: 1rem; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; margin-bottom: 2rem; color: #721c24;">
                    <strong>Error:</strong> <?php echo e($_SESSION['beta_error']); ?>
                </div>
                <?php unset($_SESSION['beta_error']); ?>
            <?php endif; ?>

            <form method="POST" action="<?php echo get_url('beta/register'); ?>">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label for="name">
                        Nombre Completo <span class="required">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           class="form-input"
                           required
                           placeholder="Tu nombre">
                </div>

                <div class="form-group">
                    <label for="email">
                        Email <span class="required">*</span>
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="form-input"
                           required
                           placeholder="tu@email.com">
                    <p class="form-help">Te enviaremos las credenciales de acceso a este email</p>
                </div>

                <div class="form-group">
                    <label for="country">País</label>
                    <input type="text"
                           id="country"
                           name="country"
                           class="form-input"
                           placeholder="Paraguay, Argentina, etc.">
                </div>

                <div class="form-group">
                    <label for="company">Empresa/Negocio (opcional)</label>
                    <input type="text"
                           id="company"
                           name="company"
                           class="form-input"
                           placeholder="Nombre de tu empresa o PYME">
                </div>

                <div class="form-group">
                    <label for="interested_app">
                        ¿Qué aplicación te interesa? <span class="required">*</span>
                    </label>
                    <select id="interested_app"
                            name="interested_app"
                            class="form-select"
                            required>
                        <option value="">Selecciona una app...</option>
                        <option value="all">Todas las aplicaciones</option>
                        <option value="dataflow">Dataflow - Gestión de Datos</option>
                        <option value="autodiagnosis">Auto Diagnosis - IA para Vehículos</option>
                        <option value="guaraniappstore">Guarani App Store Platform</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="problems_to_solve">
                        ¿Qué problemas buscas resolver? <span class="required">*</span>
                    </label>
                    <textarea id="problems_to_solve"
                              name="problems_to_solve"
                              class="form-textarea"
                              required
                              placeholder="Cuéntanos qué desafíos enfrentas en tu negocio y cómo esperamos que nuestra app te ayude..."></textarea>
                    <p class="form-help">Esto nos ayuda a priorizar features que realmente necesitas</p>
                </div>

                <div class="form-group">
                    <label for="technical_level">
                        Nivel Técnico <span class="required">*</span>
                    </label>
                    <select id="technical_level"
                            name="technical_level"
                            class="form-select"
                            required>
                        <option value="">Selecciona tu nivel...</option>
                        <option value="user">Usuario Final - Solo quiero usar la app</option>
                        <option value="advanced">Usuario Avanzado - Conozco de tecnología</option>
                        <option value="developer">Desarrollador - Puedo reportar bugs técnicos</option>
                    </select>
                    <p class="form-help">Esto nos ayuda a adaptar la comunicación y el tipo de feedback que necesitamos</p>
                </div>

                <button type="submit" class="btn-beta-submit">
                    🚀 Solicitar Acceso Beta
                </button>
            </form>

            <p style="text-align: center; margin-top: 2rem; color: #718096; font-size: 0.875rem;">
                Al registrarte, aceptas compartir feedback honesto y constructivo sobre las aplicaciones
            </p>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/views/landing/footer.php'; ?>
