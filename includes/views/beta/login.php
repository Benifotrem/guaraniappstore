<?php include INCLUDES_PATH . '/views/landing/header.php'; ?>

<style>
    .login-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 0 2rem;
        text-align: center;
    }
    .login-container {
        max-width: 500px;
        margin: 3rem auto;
        padding: 0 2rem;
    }
    .login-card {
        background: white;
        border-radius: 16px;
        padding: 3rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .login-card h2 {
        color: #2d3748;
        margin-bottom: 0.5rem;
        text-align: center;
    }
    .login-card p {
        color: #718096;
        text-align: center;
        margin-bottom: 2rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-group label {
        display: block;
        color: #4a5568;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .form-group input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.2s;
    }
    .form-group input:focus {
        outline: none;
        border-color: #667eea;
    }
    .btn-login {
        width: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 1rem;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .btn-login:hover {
        transform: translateY(-2px);
    }
    .login-links {
        text-align: center;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e2e8f0;
    }
    .login-links a {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
    }
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    .alert-error {
        background: #fee;
        border: 1px solid #fcc;
        color: #c00;
    }
    .alert-success {
        background: #efe;
        border: 1px solid #cfc;
        color: #060;
    }
</style>

<div class="login-hero">
    <div class="container">
        <h1>🔐 Acceso Beta Tester</h1>
        <p style="font-size: 1.1rem; opacity: 0.95;">Ingresa con tu token de acceso</p>
    </div>
</div>

<div class="login-container">
    <div class="login-card">
        <h2>Iniciar Sesión</h2>
        <p>Usa el token que recibiste en tu email de bienvenida</p>

        <?php if (isset($_SESSION['logout_success'])): ?>
            <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #10b981;">
                <?php echo htmlspecialchars($_SESSION['logout_success']); ?>
                <?php unset($_SESSION['logout_success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['unsubscribe_success'])): ?>
            <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #10b981;">
                <?php echo htmlspecialchars($_SESSION['unsubscribe_success']); ?>
                <?php unset($_SESSION['unsubscribe_success']); ?>
            </div>
        <?php endif; ?>




        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_SESSION['login_error']); ?>
                <?php unset($_SESSION['login_error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo get_url('beta/login-process'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="form-group">
                <label for="token">Token de Acceso</label>
                <input 
                    type="text" 
                    id="token" 
                    name="token" 
                    placeholder="Pega tu token aquí (64 caracteres)"
                    required
                    maxlength="64"
                    pattern="[a-f0-9]{64}"
                >
                <small style="color: #718096; font-size: 0.9rem;">
                    Ejemplo: b44033047cfcb3b457b6b269743d57af1cba16f5...
                </small>
            </div>

            <div style="margin: 1rem 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="remember_me" value="1" style="width: auto; cursor: pointer;">
                    <span style="font-size: 0.9rem;">Recordar mi sesión por 30 días</span>
                </label>
            </div>ñ


            <div style="text-align: right; margin-bottom: 1rem;">
                <a href="<?php echo get_url('beta/recover-token'); ?>" style="color: #667eea; text-decoration: none; font-size: 0.9rem;">¿Olvidaste tu token?</a>
            </div>

            <button type="submit" class="btn-login">
                Acceder al Dashboard
            </button>
        </form>

        <div class="login-links">
            <p style="color: #718096; margin-bottom: 1rem;">¿No tienes cuenta?</p>
            <a href="<?php echo get_url('beta/join'); ?>">
                Registrarme como Beta Tester
            </a>
            <br><br>
            <a href="<?php echo get_url('faq'); ?>">
                ¿Olvidaste tu token? Ver FAQ
            </a>
        </div>
    </div>
</div>

<?php include INCLUDES_PATH . '/views/landing/footer.php'; ?>
