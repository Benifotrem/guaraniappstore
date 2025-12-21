    <!-- Footer -->
    <footer class="site-footer bg-guarani-dark text-white" style="color: #ffffff;">
        <div class="container">
            <div class="footer-content">
                <!-- Company Info -->
                <div class="footer-column">
                    <h3 class="footer-title">Guarani App Store</h3>
                    <p class="footer-text">
                        Showcase de aplicaciones web en fase Beta o producción. Descubre nuestras soluciones
                        y mantente al día con las últimas tendencias en IA para PYMEs.
                    </p>
                    <div class="social-links">
                        <?php
                        $facebook = get_setting('social_facebook', '');
                        $instagram = get_setting('social_instagram', '');
                        $youtube = get_setting('social_youtube', '');
                        ?>
                        <?php if (!empty($facebook)): ?>
                            <a href="<?php echo e($facebook); ?>"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="social-link">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($instagram)): ?>
                            <a href="<?php echo e($instagram); ?>"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="social-link">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($youtube)): ?>
                            <a href="<?php echo e($youtube); ?>"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="social-link">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-column">
                    <h4 class="footer-title">Enlaces Rápidos</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo get_url(); ?>">Inicio</a></li>
                        <li><a href="<?php echo get_url('webapps'); ?>">Nuestras Apps</a></li>
                        <li><a href="<?php echo get_url('blog'); ?>">Blog</a></li>
                        <li><a href="#contacto">Contacto</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-column">
                    <h4 class="footer-title">Contacto</h4>
                    <ul class="footer-contact">
                        <li>
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            <?php echo e(get_setting('site_email', 'hola@guaraniappstore.com.py')); ?>
                        </li>
                        <li>
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                            </svg>
                            <?php echo e(get_setting('site_phone', '(+595) 981-123456')); ?>
                        </li>
                        <li>
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                            Asunción, Paraguay 🇵🇾
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div class="footer-column">
                    <h4 class="footer-title">Suscríbete al Blog</h4>
                    <p class="footer-text mb-3">
                        Recibe los últimos artículos sobre IA aplicada a PYMEs
                    </p>
                    <form action="<?php echo get_url('subscribe'); ?>" method="POST" class="newsletter-form">
                        <?php echo csrf_field(); ?>
                        <input type="email"
                               name="email"
                               placeholder="Tu email"
                               required
                               class="newsletter-input">
                        <button type="submit" class="newsletter-btn">
                            Suscribirse
                        </button>
                    </form>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Guarani App Store. Todos los derechos reservados.</p>
                <p class="footer-tech">
                    Desarrollado con <span style="color: #e74c3c;">♥</span> en Paraguay
                </p>
            </div>
        </div>
    </footer>

    <!-- Guarani Assistant Widget -->
    <div id="guarani-widget" class="guarani-widget">
        <!-- Floating Button -->
        <button id="widget-toggle" class="widget-toggle" aria-label="Abrir asistente">
            <div class="widget-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                </svg>
                <span class="pulse-ring"></span>
            </div>
            <span class="widget-close-icon">×</span>
        </button>

        <!-- Chat Window -->
        <div id="widget-window" class="widget-window">
            <div class="widget-header">
                <div class="widget-header-content">
                    <div class="widget-avatar">
                        <img src="<?php echo ASSETS_URL; ?>/images/logo.png" alt="Guarani">
                    </div>
                    <div class="widget-header-text">
                        <h3>Guarani Assistant</h3>
                        <p class="widget-status"><span class="status-dot"></span> En línea</p>
                    </div>
                </div>
            </div>

            <div class="widget-messages" id="widget-messages">
                <div class="message bot-message">
                    <div class="message-content">
                        <p>¡Che angirū! 👋 Soy tu asistente de Guarani App Store.</p>
                    </div>
                </div>
                <div class="message bot-message">
                    <div class="message-content">
                        <p>Estamos construyendo algo especial: una plataforma colaborativa para PYMEs. <strong>Y estamos considerando convertirnos en DAO</strong> (Organización Autónoma Descentralizada). 🌟</p>
                    </div>
                </div>
                <div class="message bot-message">
                    <div class="message-content">
                        <p><strong>¿Qué significa esto para vos?</strong><br>
                        Si te unís como Beta Tester ahora, cuando nos convirtamos en DAO tendrás <strong>prioridad para entrar al accionariado</strong> 💎</p>
                    </div>
                </div>
                <div class="widget-options">
                    <button class="widget-option" data-action="dao">🤔 ¿Qué es una DAO?</button>
                    <button class="widget-option" data-action="benefits">💰 ¿Qué beneficios tiene?</button>
                    <button class="widget-option" data-action="join">🚀 Quiero ser Beta Tester</button>
                    <button class="widget-option" data-action="telegram">💬 Hablar con el bot</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>
    <?php if (isset($additional_scripts)) echo $additional_scripts; ?>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-toggle').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('active');
            this.classList.toggle('active');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobile-menu');
            const toggle = document.getElementById('mobile-menu-toggle');

            if (!mobileMenu.contains(event.target) && !toggle.contains(event.target)) {
                mobileMenu.classList.remove('active');
                toggle.classList.remove('active');
            }
        });

        // Guarani Widget functionality
        (function() {
            const toggle = document.getElementById('widget-toggle');
            const window = document.getElementById('widget-window');
            const messagesContainer = document.getElementById('widget-messages');

            // Toggle widget
            toggle.addEventListener('click', function() {
                this.classList.toggle('active');
                window.classList.toggle('active');
            });

            // Widget responses
            const responses = {
                dao: {
                    user: '🤔 ¿Qué es una DAO?',
                    bot: '<p><strong>DAO</strong> significa <strong>Decentralized Autonomous Organization</strong> (Organización Autónoma Descentralizada).</p><p>Es una organización dirigida por <strong>reglas codificadas en smart contracts</strong>, donde las decisiones las toman los miembros mediante votación. No hay jefes ni directores tradicionales.</p><p>En nuestro caso, los <strong>miembros de la DAO tendrían voz y voto</strong> en decisiones importantes del negocio: nuevas features, inversiones, dirección estratégica, etc. 🗳️</p>'
                },
                benefits: {
                    user: '💰 ¿Qué beneficios tiene?',
                    bot: '<p><strong>Beneficios de entrar como propietario:</strong></p><p>🎯 <strong>Tokens de Gobernanza:</strong> Tu voto cuenta en decisiones importantes<br>💵 <strong>Participación en Ganancias:</strong> Como accionista, recibís dividendos según beneficios<br>📈 <strong>Crecimiento del valor:</strong> Si la empresa crece, tu participación vale más<br>🚀 <strong>Prioridad en nuevos productos:</strong> Acceso VIP permanente<br>🤝 <strong>Networking:</strong> Sos parte de una comunidad de innovadores</p><p><strong>Los Beta Testers tendrán prioridad</strong> cuando hagamos la transición a DAO. Es tu oportunidad de entrar temprano. 💎</p>'
                },
                join: {
                    user: '🚀 Quiero ser Beta Tester',
                    bot: '<p>¡Excelente decisión! 🎉</p><p>Registrate ahora y comenzá a disfrutar de:</p><p>✅ Acceso <strong>GRATIS de por vida</strong> a todas las apps<br>✅ Todas las funciones premium<br>✅ Tu nombre en los créditos<br>✅ <strong>Prioridad para entrar al accionariado cuando seamos DAO</strong></p><p><a href="<?php echo get_url("beta/join"); ?>" style="display: inline-block; background: #00a884; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 0.5rem;">📝 Registrarme Ahora</a></p>'
                },
                telegram: {
                    user: '💬 Hablar con el bot',
                    bot: '<p>¡Perfecto! Nuestro bot de Telegram <strong>@guaraniappstore_bot</strong> puede ayudarte con:</p><p>✅ Registrarte como Beta Tester<br>✅ Reportar bugs<br>✅ Sugerir mejoras<br>✅ Ver tus estadísticas<br>✅ Explicarte sobre DAOs y gobernanza</p><p><a href="https://t.me/guaraniappstore_bot" target="_blank" style="display: inline-block; background: #0088cc; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 0.5rem;">💬 Abrir en Telegram</a></p>'
                }
            };

            // Handle option clicks
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('widget-option') || e.target.closest('.widget-option')) {
                    const button = e.target.classList.contains('widget-option') ? e.target : e.target.closest('.widget-option');
                    const action = button.getAttribute('data-action');

                    if (responses[action]) {
                        // Add user message
                        const userMsg = document.createElement('div');
                        userMsg.className = 'message user-message';
                        userMsg.innerHTML = '<div class="message-content"><p>' + responses[action].user + '</p></div>';
                        messagesContainer.appendChild(userMsg);

                        // Remove options
                        const optionsDiv = messagesContainer.querySelector('.widget-options');
                        if (optionsDiv) optionsDiv.remove();

                        // Add bot response after delay
                        setTimeout(function() {
                            const botMsg = document.createElement('div');
                            botMsg.className = 'message bot-message';
                            botMsg.innerHTML = '<div class="message-content">' + responses[action].bot + '</div>';
                            messagesContainer.appendChild(botMsg);

                            // Scroll to bottom
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;

                            // Add back to start button
                            setTimeout(function() {
                                const backButton = document.createElement('button');
                                backButton.className = 'widget-option';
                                backButton.innerHTML = '↩️ Volver al inicio';
                                backButton.onclick = function() {
                                    location.reload();
                                };
                                messagesContainer.appendChild(backButton);
                                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                            }, 500);
                        }, 800);

                        // Scroll to bottom
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                }
            });
        })();
    </script>
</body>
</html>
