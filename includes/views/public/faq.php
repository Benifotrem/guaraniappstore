<?php include INCLUDES_PATH . '/views/landing/header.php'; ?>

<style>
    .faq-hero {
        background: var(--gradient-primary);
        color: white;
        padding: 4rem 0 3rem;
        text-align: center;
    }
    .faq-hero h1 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: white;
    }
    .faq-hero-subtitle {
        font-size: 1.2rem;
        opacity: 0.95;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.6;
    }
    .faq-container {
        max-width: 900px;
        margin: 3rem auto;
        padding: 0 2rem;
    }
    .faq-intro {
        background: var(--guarani-light);
        border-left: 4px solid var(--guarani-primary);
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 3rem;
    }
    .faq-intro h3 {
        color: var(--guarani-primary);
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    .faq-intro p {
        color: var(--guarani-dark);
        line-height: 1.7;
        margin-bottom: 0.75rem;
    }
    .guarani-word {
        color: var(--guarani-primary);
        font-weight: 700;
        font-style: italic;
    }
    .faq-section {
        margin-bottom: 3rem;
    }
    .faq-section h2 {
        color: var(--guarani-primary);
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 3px solid var(--guarani-primary);
    }
    .faq-item {
        background: white;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .faq-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-color: var(--guarani-primary-light);
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
        background: var(--guarani-primary);
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
        border: 1px solid var(--border);
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
        background: var(--gradient-primary);
        color: white;
        padding: 2.5rem;
        border-radius: 12px;
        text-align: center;
        margin-top: 3rem;
    }
    .faq-cta h3 {
        font-size: 1.8rem;
        margin-bottom: 1rem;
        color: white;
    }
    .faq-cta-button {
        display: inline-block;
        background: white;
        color: var(--guarani-primary);
        padding: 1rem 2rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        margin: 0.5rem;
        transition: all 0.3s ease;
    }
    .faq-cta-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
</style>

<div class="faq-hero">
    <div class="container">
        <h1>❓ Mba'éichapa oñemba'apo - Preguntas Frecuentes</h1>
        <p class="faq-hero-subtitle">Todo lo que necesitás saber para ser parte de nuestra comunidad Beta Tester</p>
    </div>
</div>

<div class="faq-container">

    <!-- INTRODUCCIÓN Y MISIÓN -->
    <div class="faq-intro">
        <h3>🌿 ¿Qué significa ser Beta Tester en Guarani App Store?</h3>
        <p>
            ¡Che ru! (¡Hola amigo!) Ser <strong>Beta Tester</strong> en Guarani App Store no es solo "probar aplicaciones antes que nadie".
            Es ser <span class="guarani-word">ñe'ẽhára</span> (guardián) de la calidad, un colaborador clave en la construcción de herramientas
            digitales pensadas para PYMEs y emprendedores.
        </p>
        <p>
            Tu misión es simple pero fundamental: <strong>usar nuestras aplicaciones en tu día a día, encontrar errores, sugerir mejoras,
            y ayudarnos a crear productos que realmente resuelvan problemas reales</strong>. No hace falta ser desarrollador ni experto técnico,
            solo tener ganas de <span class="guarani-word">pytyvõ</span> (ayudar) y compartir tu experiencia honesta.
        </p>
        <p style="margin-bottom: 0;">
            <strong>Beneficios que recibís:</strong><br>
            🎁 Acceso gratuito <strong>de por vida</strong> a todas las apps (sí, para siempre)<br>
            👑 Todas las funciones premium sin pagar un peso<br>
            🏆 Tu nombre en los créditos si hacés contribuciones importantes<br>
            💬 Línea directa con los desarrolladores para que te escuchen<br>
            🎯 Probás nuevas funciones antes que nadie
        </p>
    </div>

    <!-- SECCIÓN 1: REGISTRO -->
    <div class="faq-section">
        <h2>📝 Mba'éichapa Amoñepyrũ - Cómo empiezo</h2>

        <div class="faq-item">
            <div class="faq-question">¿Cómo me registro como Beta Tester?</div>
            <div class="faq-answer">
                Registrarte es re fácil:
                <ol>
                    <li>Entrá a <a href="<?php echo get_url('beta/join'); ?>">Unirme al Programa Beta</a></li>
                    <li>Completá el formulario con tus datos (nombre, email, usuario de Telegram)</li>
                    <li>Te llega un email con tu <strong>token de acceso</strong> (un código único de 64 caracteres)</li>
                    <li>Tu cuenta queda en <span class="faq-highlight">pendiente</span> hasta que la activemos (24-48 horas máximo)</li>
                    <li>Cuando esté activa, ¡ya podés acceder a tu dashboard y empezar a testear!</li>
                </ol>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Cuánto tardo en empezar a probar apps?</div>
            <div class="faq-answer">
                Revisamos las solicitudes cada 24-48 horas. Cuando activemos tu cuenta, te llega un email avisándote y podés
                empezar a usar el dashboard de inmediato. Mientras esperás, podés ir familiarizándote con las apps públicas en el sitio.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Qué es ese token que me mandan?</div>
            <div class="faq-answer">
                Es tu <strong>"llave maestra"</strong> para entrar a tu dashboard personal. Es un código único de 64 caracteres que solo vos tenés.
                <strong>Guardalo en un lugar seguro</strong> (anotalo, guardalo en un gestor de contraseñas, lo que prefieras).
                Sin ese token no podés acceder a tu cuenta.
            </div>
        </div>
    </div>

    <!-- SECCIÓN 2: DASHBOARD -->
    <div class="faq-section">
        <h2>📊 Che Dashboard - Mi Espacio Personal</h2>

        <div class="faq-item">
            <div class="faq-question">¿Cómo entro a mi dashboard?</div>
            <div class="faq-answer">
                Tenés dos formas:
                <ul>
                    <li><strong>Por el link del email:</strong> En el email de bienvenida hay un link directo con tu token incluido. Un clic y listo.</li>
                    <li><strong>Manualmente:</strong> Entrá a <a href="<?php echo get_url('beta'); ?>"><?php echo get_url('beta'); ?></a> y pegá tu token</li>
                </ul>
                <strong>Importante:</strong> Tu cuenta tiene que estar en estado <span class="faq-highlight">activa</span> para poder entrar.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Qué veo en mi dashboard?</div>
            <div class="faq-answer">
                Tu dashboard es como tu "tablero de comandos" personal:
                <ul>
                    <li>📊 <strong>Tus estadísticas:</strong> cuántos bugs reportaste, sugerencias aceptadas, contribuciones totales</li>
                    <li>🏅 <strong>Tu nivel actual:</strong> Bronze, Silver, Gold o Platinum (subís reportando y sugiriendo)</li>
                    <li>📈 <strong>Progreso:</strong> cuánto te falta para el próximo nivel</li>
                    <li>🏆 <strong>Leaderboard:</strong> el ranking de la comunidad (competencia sana, ¿no?)</li>
                    <li>🚀 <strong>Apps disponibles:</strong> las aplicaciones que podés testear ahora mismo</li>
                    <li>📝 <strong>Tu historial:</strong> todo el feedback que mandaste y su estado</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 3: TELEGRAM -->
    <div class="faq-section">
        <h2>🤖 Ñe'ẽmondo Bot - Bot de Telegram</h2>

        <div class="faq-item">
            <div class="faq-question">¿Cómo conecto mi cuenta con Telegram?</div>
            <div class="faq-answer">
                Telegram es súper cómodo para reportar rápido:
                <ol>
                    <li>Abrí Telegram en tu celu o compu</li>
                    <li>Buscá el bot: <strong>@guaraniappstore_bot</strong></li>
                    <li>Mandále <code>/start</code></li>
                    <li>Si tu username de Telegram es el mismo que pusiste al registrarte, el bot te reconoce al toque</li>
                    <li>Si no coincide, el bot te da instrucciones para vincular manualmente</li>
                </ol>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Qué comandos puedo usar en el bot?</div>
            <div class="faq-answer">
                <ul>
                    <li><code>/start</code> - Registrarte o ver tu perfil</li>
                    <li><code>/apps</code> - Ver qué apps podés testear</li>
                    <li><code>/bug</code> - Reportar un error que encontraste</li>
                    <li><code>/feature</code> - Sugerir algo que te gustaría que tenga la app</li>
                    <li><code>/stats</code> - Ver tus estadísticas (bugs, sugerencias, nivel)</li>
                    <li><code>/leaderboard</code> - Ver el ranking de beta testers</li>
                    <li><code>/help</code> - Ayuda completa con todos los comandos</li>
                </ul>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">El bot no me reconoce, ¿qué hago?</div>
            <div class="faq-answer">
                Revisá esto:
                <ul>
                    <li>¿Tu cuenta está <span class="faq-highlight">activada</span>? (si está "pending" no funciona todavía)</li>
                    <li>¿Pusiste bien tu username de Telegram al registrarte? (sin la @, solo el nombre)</li>
                    <li>¿Tu username de Telegram es público? (configuralo en Telegram: Settings → Username)</li>
                </ul>
                Si todo está ok y sigue sin andar, escribinos a: <strong><?php echo SITE_EMAIL; ?></strong>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Me van a mandar spam por Telegram?</div>
            <div class="faq-answer">
                ¡Para nada! Solo recibís notificaciones útiles:
                <ul>
                    <li>🚀 Cuando publicamos una nueva app para testear</li>
                    <li>✅ Cuando tu feedback sea revisado o aceptado</li>
                    <li>🎉 Cuando subas de nivel (celebramos juntos)</li>
                    <li>📢 Anuncios importantes (muy de vez en cuando)</li>
                </ul>
                Cero spam. Prometido.
            </div>
        </div>
    </div>

    <!-- SECCIÓN 4: FEEDBACK -->
    <div class="faq-section">
        <h2>💬 Pytyvõ Moinge - Enviar Ayuda y Feedback</h2>

        <div class="faq-item">
            <div class="faq-question">¿Cómo reporto un bug o sugiero una mejora?</div>
            <div class="faq-answer">
                Hay dos formas, elegí la que te quede más cómoda:
                <br><br>
                <strong>1️⃣ Desde el sitio web:</strong>
                <ul>
                    <li>Entrá a cualquier <a href="<?php echo get_url('webapps'); ?>">aplicación</a></li>
                    <li>Vas a ver un botón flotante de "Feedback" abajo a la derecha</li>
                    <li>Hacé clic y elegí qué tipo de feedback querés mandar (Bug / Feature / Review)</li>
                    <li>Completá el formulario con los detalles</li>
                    <li>Si querés, subí una captura de pantalla</li>
                </ul>
                <br>
                <strong>2️⃣ Desde Telegram (más rápido):</strong>
                <ul>
                    <li>Abrí el chat con <strong>@guaraniappstore_bot</strong></li>
                    <li>Mandá <code>/bug</code> para un error o <code>/feature</code> para una sugerencia</li>
                    <li>Elegí la app</li>
                    <li>El bot te va guiando paso a paso</li>
                </ul>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Qué tipos de feedback puedo mandar?</div>
            <div class="faq-answer">
                <ul>
                    <li>🐛 <strong>Bug (Error):</strong> Cuando algo no funciona como debería. Ej: "el botón de guardar no hace nada", "se cuelga al subir una imagen grande"</li>
                    <li>💡 <strong>Feature (Sugerencia):</strong> Ideas para nuevas funcionalidades o mejoras. Ej: "sería genial poder exportar a Excel", "falta un filtro por fecha"</li>
                    <li>⭐ <strong>Review (Opinión general):</strong> Tu experiencia usando la app. Ej: "me encanta, muy intuitiva", "se ve bien pero es un poco lenta"</li>
                </ul>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Cómo hago un buen reporte de bug?</div>
            <div class="faq-answer">
                Cuanto más detalle des, más fácil es para nosotros arreglarlo. Contanos:
                <ul>
                    <li><strong>Qué estabas haciendo:</strong> "estaba cargando un producto nuevo"</li>
                    <li><strong>Qué pasó:</strong> "al hacer clic en Guardar, se quedó cargando para siempre"</li>
                    <li><strong>Qué esperabas que pasara:</strong> "que se guarde y me muestre el mensaje de éxito"</li>
                    <li><strong>Si podés reproducirlo:</strong> "pasa siempre que..." o "pasó una sola vez"</li>
                    <li><strong>Captura de pantalla:</strong> si aplica, súbela (vale oro)</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 5: GAMIFICACIÓN -->
    <div class="faq-section">
        <h2>🏆 Ñemombarete - Niveles y Reconocimiento</h2>

        <div class="faq-item">
            <div class="faq-question">¿Cómo funcionan los niveles?</div>
            <div class="faq-answer">
                Tu nivel sube según tus <strong>contribuciones totales</strong> (bugs reportados + sugerencias aceptadas):
                <ul>
                    <li>🥉 <strong>Bronze:</strong> 0-9 contribuciones (estás arrancando, <span class="guarani-word">ñepyrũ</span>)</li>
                    <li>🥈 <strong>Silver:</strong> 10-24 contribuciones (ya le estás agarrando la mano)</li>
                    <li>🥇 <strong>Gold:</strong> 25-49 contribuciones (sos un crack, <span class="guarani-word">iporãiterei</span>!)</li>
                    <li>💎 <strong>Platinum:</strong> 50+ contribuciones (leyenda de la comunidad)</li>
                </ul>
                <strong>Nota:</strong> Reportar bugs cuenta siempre. Las sugerencias solo cuentan si las aceptamos (para evitar spam de ideas random).
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Qué gano al subir de nivel?</div>
            <div class="faq-answer">
                <strong>Todos los niveles tienen los mismos beneficios principales:</strong>
                <ul>
                    <li>🎁 Acceso <strong>gratuito de por vida</strong> a todas las aplicaciones</li>
                    <li>👑 Todas las funciones premium sin costo</li>
                    <li>💬 Línea directa con los desarrolladores</li>
                    <li>🎯 Acceso anticipado a nuevas funcionalidades</li>
                </ul>
                <strong>Pero los niveles más altos (Gold y Platinum) también ganan:</strong>
                <ul>
                    <li>🏆 Tu nombre en los créditos de las apps (si aportaste mucho)</li>
                    <li>🎤 Voz prioritaria en decisiones de producto</li>
                    <li>🎁 Regalos y sorpresas especiales de vez en cuando</li>
                </ul>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Cómo subo en el ranking (leaderboard)?</div>
            <div class="faq-answer">
                El ranking se ordena así:
                <ol>
                    <li><strong>Por contribuciones totales:</strong> quien más bugs y sugerencias aportó, más arriba</li>
                    <li><strong>En caso de empate:</strong> gana quien se registró primero (recompensa a los early adopters)</li>
                </ol>
                No es una competencia a muerte, pero está bueno ver cómo crece la comunidad y <span class="guarani-word">joapy</span> (ayudarse mutuamente).
            </div>
        </div>
    </div>

    <!-- SECCIÓN 6: PROBLEMAS -->
    <div class="faq-section">
        <h2>⚠️ Ñemyatyrõ - Solución de Problemas</h2>

        <div class="faq-item">
            <div class="faq-question">No me llegó el email de bienvenida</div>
            <div class="faq-answer">
                Revisá:
                <ul>
                    <li>📧 Tu carpeta de <strong>Spam / Correo no deseado</strong> (a veces cae ahí)</li>
                    <li>✉️ Que el email que pusiste esté bien escrito</li>
                    <li>⏳ Esperá 10-15 minutos (a veces hay delay)</li>
                    <li>📩 Si después de 30 min no llegó, escribinos a: <strong><?php echo SITE_EMAIL; ?></strong></li>
                </ul>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">El dashboard me redirige al formulario de registro</div>
            <div class="faq-answer">
                Esto pasa cuando tu cuenta todavía está en estado <span class="faq-highlight">pending</span> (pendiente de activación).
                Tenés que esperar a que la activemos (24-48 horas). Cuando esté lista, te mandamos un email avisándote.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Olvidé mi token de acceso / perdí el email</div>
            <div class="faq-answer">
                Buscá en tu email el mensaje de <strong>noreply@guaraniappstore.com</strong> con asunto "Bienvenido al Programa Beta Tester".
                Si no lo encontrás (borraste el email, cambio de cuenta, etc.), escribinos a <strong><?php echo SITE_EMAIL; ?></strong>
                con el email que usaste para registrarte y te lo reenviamos.
            </div>
        </div>
    </div>

    <!-- CTA FINAL -->
    <div class="faq-cta">
        <h3>¿Listo para ser parte de la comunidad?</h3>
        <p style="font-size: 1.1rem; margin-bottom: 2rem; opacity: 0.95;">
            Sumate al programa, <span style="font-style: italic;">pytyvõ</span> (ayudá) a construir mejores herramientas,
            y disfrutá de acceso gratuito para siempre. <strong>Jajapoja'o!</strong> (¡Vamos juntos!)
        </p>
        <a href="<?php echo get_url('beta/join'); ?>" class="faq-cta-button">
            🚀 Quiero ser Beta Tester
        </a>
        <a href="https://t.me/guaraniappstore_bot" class="faq-cta-button">
            🤖 Abrir Bot de Telegram
        </a>
    </div>

</div>

<?php include INCLUDES_PATH . '/views/landing/footer.php'; ?>
