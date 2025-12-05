<?php include INCLUDES_PATH . '/views/landing/header.php'; ?>

<style>
    .faq-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4rem 0 3rem;
        text-align: center;
    }
    .faq-hero h1 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    .faq-container {
        max-width: 900px;
        margin: 3rem auto;
        padding: 0 2rem;
    }
    .faq-section {
        margin-bottom: 3rem;
    }
    .faq-section h2 {
        color: #667eea;
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 3px solid #667eea;
    }
    .faq-item {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .faq-question {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1rem;
        display: flex;
        align-items: start;
    }
    .faq-question::before {
        content: "Q:";
        background: #667eea;
        color: white;
        border-radius: 6px;
        padding: 0.25rem 0.5rem;
        margin-right: 0.75rem;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .faq-answer {
        color: #4a5568;
        line-height: 1.7;
        padding-left: 2.5rem;
    }
    .faq-answer ul, .faq-answer ol {
        margin: 1rem 0;
        padding-left: 1.5rem;
    }
    .faq-answer li {
        margin-bottom: 0.5rem;
    }
    .faq-code {
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 1rem;
        font-family: monospace;
        font-size: 0.9rem;
        margin: 1rem 0;
        overflow-x: auto;
    }
    .faq-highlight {
        background: #fef9c3;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        font-weight: 500;
    }
    .faq-cta {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2.5rem;
        border-radius: 12px;
        text-align: center;
        margin-top: 3rem;
    }
    .faq-cta h3 {
        font-size: 1.8rem;
        margin-bottom: 1rem;
    }
    .faq-cta-button {
        display: inline-block;
        background: white;
        color: #667eea;
        padding: 1rem 2rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        margin: 0.5rem;
        transition: transform 0.2s;
    }
    .faq-cta-button:hover {
        transform: translateY(-2px);
    }
</style>

<div class="faq-hero">
    <div class="container">
        <h1>❓ Preguntas Frecuentes</h1>
        <p style="font-size: 1.2rem; opacity: 0.95;">Todo lo que necesitas saber sobre el Programa Beta Tester</p>
    </div>
</div>

<div class="faq-container">
    
    <!-- SECCIÓN 1: REGISTRO -->
    <div class="faq-section">
        <h2>📝 Registro y Cuenta</h2>
        
        <div class="faq-item">
            <div class="faq-question">¿Cómo me registro como Beta Tester?</div>
            <div class="faq-answer">
                <ol>
                    <li>Ve a <a href="<?php echo get_url('beta/join'); ?>">Programa Beta Tester</a></li>
                    <li>Completa el formulario con tus datos</li>
                    <li>Recibirás un email de bienvenida con tu token de acceso</li>
                    <li>Tu cuenta estará en estado <span class="faq-highlight">pending</span> hasta ser activada</li>
                </ol>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Cuánto tarda la activación de mi cuenta?</div>
            <div class="faq-answer">
                Generalmente entre 24-48 horas. Recibirás un email cuando tu cuenta sea activada y podrás acceder a tu dashboard.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Qué es el token de acceso?</div>
            <div class="faq-answer">
                Es un código único de 64 caracteres que te permite acceder a tu dashboard personal. Lo recibirás en el email de bienvenida. <strong>Guárdalo en un lugar seguro.</strong>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 2: DASHBOARD -->
    <div class="faq-section">
        <h2>📊 Dashboard Personal</h2>
        
        <div class="faq-item">
            <div class="faq-question">¿Cómo accedo a mi dashboard?</div>
            <div class="faq-answer">
                Usa el link que recibiste en tu email de bienvenida, o accede directamente con tu token:
                <div class="faq-code">
                    <?php echo SITE_URL; ?>/beta/dashboard?token=TU_TOKEN_AQUI
                </div>
                <strong>Importante:</strong> Tu cuenta debe estar en estado <span class="faq-highlight">active</span> para acceder al dashboard.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Qué puedo ver en mi dashboard?</div>
            <div class="faq-answer">
                <ul>
                    <li>📊 Tus estadísticas personales (bugs reportados, sugerencias aceptadas)</li>
                    <li>🏅 Tu nivel actual (Bronze, Silver, Gold, Platinum)</li>
                    <li>📈 Progreso al siguiente nivel</li>
                    <li>🏆 Leaderboard (ranking de la comunidad)</li>
                    <li>🚀 Apps disponibles para testear</li>
                    <li>📝 Tu historial de feedback</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 3: TELEGRAM -->
    <div class="faq-section">
        <h2>🤖 Bot de Telegram</h2>
        
        <div class="faq-item">
            <div class="faq-question">¿Cómo conecto mi cuenta con Telegram?</div>
            <div class="faq-answer">
                <ol>
                    <li>Abre Telegram en tu móvil o escritorio</li>
                    <li>Busca el bot: <strong>@guaraniappstore_bot</strong></li>
                    <li>Inicia conversación y envía: <div class="faq-code">/start</div></li>
                    <li>El bot te reconocerá automáticamente si tu username de Telegram coincide con el que registraste</li>
                    <li>Si no coincide, el bot te dará instrucciones para vincular tu cuenta</li>
                </ol>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Qué comandos tiene el bot de Telegram?</div>
            <div class="faq-answer">
                <ul>
                    <li><code>/start</code> - Registrarte o ver tu perfil</li>
                    <li><code>/apps</code> - Ver apps disponibles para testear</li>
                    <li><code>/bug</code> - Reportar un bug</li>
                    <li><code>/feature</code> - Sugerir una feature</li>
                    <li><code>/stats</code> - Ver tus estadísticas personales</li>
                    <li><code>/leaderboard</code> - Ver ranking de beta testers</li>
                    <li><code>/help</code> - Ver ayuda completa</li>
                </ul>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">El bot no me reconoce, ¿qué hago?</div>
            <div class="faq-answer">
                Asegúrate de que:
                <ul>
                    <li>Tu cuenta esté <span class="faq-highlight">activada</span> (estado active)</li>
                    <li>Hayas registrado tu username de Telegram correctamente (sin el @)</li>
                    <li>Tu username de Telegram sea público (visible en tu perfil)</li>
                </ul>
                Si el problema persiste, contacta a: <strong><?php echo SITE_EMAIL; ?></strong>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Recibiré notificaciones por Telegram?</div>
            <div class="faq-answer">
                Sí, una vez vinculada tu cuenta recibirás:
                <ul>
                    <li>🚀 Notificaciones cuando se publiquen nuevas apps</li>
                    <li>✅ Confirmación cuando tu feedback sea revisado</li>
                    <li>🎉 Avisos cuando subas de nivel</li>
                    <li>📢 Anuncios importantes del programa</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 4: FEEDBACK -->
    <div class="faq-section">
        <h2>💬 Enviar Feedback</h2>
        
        <div class="faq-item">
            <div class="faq-question">¿Cómo reporto un bug o sugiero una feature?</div>
            <div class="faq-answer">
                Hay 2 formas:
                <ol>
                    <li><strong>Desde el sitio web:</strong>
                        <ul>
                            <li>Ve a <a href="<?php echo get_url('webapps'); ?>">Apps</a></li>
                            <li>Haz click en el botón flotante de feedback (esquina inferior derecha)</li>
                            <li>Selecciona el tipo (Bug / Feature / Review)</li>
                            <li>Completa el formulario</li>
                        </ul>
                    </li>
                    <li><strong>Desde Telegram:</strong>
                        <ul>
                            <li>Envía <code>/bug</code> o <code>/feature</code> al bot</li>
                            <li>Selecciona la app</li>
                            <li>Sigue las instrucciones</li>
                        </ul>
                    </li>
                </ol>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Qué tipos de feedback puedo enviar?</div>
            <div class="faq-answer">
                <ul>
                    <li>🐛 <strong>Bug:</strong> Errores o problemas técnicos</li>
                    <li>💡 <strong>Feature:</strong> Sugerencias de nuevas funcionalidades</li>
                    <li>⭐ <strong>Review:</strong> Opiniones generales sobre la app</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 5: GAMIFICACIÓN -->
    <div class="faq-section">
        <h2>🏆 Niveles y Gamificación</h2>
        
        <div class="faq-item">
            <div class="faq-question">¿Cómo funcionan los niveles?</div>
            <div class="faq-answer">
                Tu nivel depende de tus contribuciones totales (bugs + sugerencias aceptadas):
                <ul>
                    <li>🥉 <strong>Bronze:</strong> 0-9 contribuciones</li>
                    <li>🥈 <strong>Silver:</strong> 10-24 contribuciones</li>
                    <li>🥇 <strong>Gold:</strong> 25-49 contribuciones</li>
                    <li>💎 <strong>Platinum:</strong> 50+ contribuciones</li>
                </ul>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Qué beneficios obtengo al subir de nivel?</div>
            <div class="faq-answer">
                <ul>
                    <li>🎁 Acceso gratuito de por vida a todas las apps</li>
                    <li>👑 Features premium sin costo</li>
                    <li>🏆 Reconocimiento en los créditos de las apps</li>
                    <li>💬 Línea directa con desarrolladores</li>
                    <li>🎯 Acceso anticipado a nuevas features</li>
                </ul>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Cómo subo en el leaderboard?</div>
            <div class="faq-answer">
                El ranking se basa en:
                <ol>
                    <li>Total de contribuciones (bugs + sugerencias aceptadas)</li>
                    <li>En caso de empate, se considera la fecha de registro (primero en registrarse = mejor posición)</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 6: PROBLEMAS -->
    <div class="faq-section">
        <h2>⚠️ Solución de Problemas</h2>
        
        <div class="faq-item">
            <div class="faq-question">No recibí el email de bienvenida</div>
            <div class="faq-answer">
                <ul>
                    <li>Revisa tu carpeta de <strong>Spam/Correo no deseado</strong></li>
                    <li>Verifica que el email registrado sea correcto</li>
                    <li>Espera unos minutos (puede haber delay)</li>
                    <li>Si no llega, contacta a: <strong><?php echo SITE_EMAIL; ?></strong></li>
                </ul>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">El dashboard me redirige al formulario de registro</div>
            <div class="faq-answer">
                Esto sucede porque tu cuenta está en estado <span class="faq-highlight">pending</span>. Debes esperar a que sea activada por el equipo (24-48 horas).
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Olvidé mi token de acceso</div>
            <div class="faq-answer">
                Busca en tu email el mensaje de bienvenida de <strong>noreply@guaraniappstore.com</strong>. Si no lo encuentras, contacta a: <strong><?php echo SITE_EMAIL; ?></strong> con tu email de registro.
            </div>
        </div>
    </div>

    <!-- CTA FINAL -->
    <div class="faq-cta">
        <h3>¿Listo para comenzar?</h3>
        <p style="font-size: 1.1rem; margin-bottom: 2rem; opacity: 0.95;">Únete a nuestro programa y sé parte de la comunidad</p>
        <a href="<?php echo get_url('beta/join'); ?>" class="faq-cta-button">
            🚀 Registrarme como Beta Tester
        </a>
        <a href="https://t.me/guaraniappstore_bot" class="faq-cta-button">
            🤖 Abrir Bot de Telegram
        </a>
    </div>

</div>

<?php include INCLUDES_PATH . '/views/landing/footer.php'; ?>
