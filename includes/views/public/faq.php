<?php include INCLUDES_PATH . '/views/landing/header.php'; ?>

<style>
    @keyframes gradientFlow {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .faq-hero {
        background: linear-gradient(-45deg, #00a884, #008069, #00bfa5, #00695c);
        background-size: 400% 400%;
        animation: gradientFlow 15s ease infinite;
        color: white;
        padding: 5rem 0 4rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .faq-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></svg>');
        opacity: 0.3;
        animation: gradientFlow 20s ease infinite;
    }

    .faq-hero h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: white;
        font-weight: 800;
        position: relative;
        z-index: 1;
        text-shadow: 0 2px 20px rgba(0,0,0,0.2);
    }

    .faq-hero-subtitle {
        font-size: 1.3rem;
        opacity: 0.95;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.6;
        position: relative;
        z-index: 1;
    }

    .faq-container {
        max-width: 900px;
        margin: 3rem auto;
        padding: 0 2rem;
    }

    .faq-intro {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: none;
        border-left: 5px solid #00a884;
        padding: 2.5rem;
        border-radius: 16px;
        margin-bottom: 3rem;
        box-shadow: 0 4px 20px rgba(0,168,132,0.1);
        animation: fadeInUp 0.6s ease-out;
    }

    .faq-intro h3 {
        color: #00695c;
        font-size: 1.6rem;
        margin-bottom: 1rem;
        font-weight: 700;
    }

    .faq-intro p {
        color: #1e4d2b;
        line-height: 1.8;
        margin-bottom: 0.75rem;
    }

    .guarani-word {
        color: #00a884;
        font-weight: 700;
        font-style: italic;
    }

    .faq-section {
        margin-bottom: 3rem;
        animation: fadeInUp 0.6s ease-out;
    }

    .faq-section h2 {
        color: #00695c;
        font-size: 2rem;
        margin-bottom: 2rem;
        padding-bottom: 0.75rem;
        border-bottom: none;
        position: relative;
        font-weight: 700;
    }

    .faq-section h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, #00a884, #00bfa5);
        border-radius: 2px;
    }

    .faq-item {
        background: white;
        border: 2px solid transparent;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .faq-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #00a884, #00bfa5, #4ade80);
        transform: scaleX(0);
        transition: transform 0.4s ease;
        transform-origin: left;
    }

    .faq-item:hover {
        box-shadow: 0 8px 30px rgba(0,168,132,0.15);
        border-color: #00a88420;
        transform: translateY(-4px);
    }

    .faq-item:hover::before {
        transform: scaleX(1);
    }

    .faq-question {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
        display: flex;
        align-items: start;
        gap: 1rem;
    }

    .faq-question::before {
        content: "Q";
        background: linear-gradient(135deg, #00a884, #008069);
        color: white;
        border-radius: 8px;
        padding: 0.4rem 0.7rem;
        font-size: 0.85rem;
        flex-shrink: 0;
        font-weight: 800;
        box-shadow: 0 2px 8px rgba(0,168,132,0.3);
    }

    .faq-answer {
        color: #475569;
        line-height: 1.8;
        padding-left: 3rem;
        font-size: 1.05rem;
    }

    .faq-answer ul, .faq-answer ol {
        margin: 1rem 0;
        padding-left: 1.5rem;
    }

    .faq-answer li {
        margin-bottom: 0.75rem;
    }

    .faq-answer strong {
        color: #1e293b;
        font-weight: 600;
    }

    .faq-answer a {
        color: #00a884;
        text-decoration: none;
        font-weight: 600;
        border-bottom: 2px solid transparent;
        transition: border-color 0.3s ease;
    }

    .faq-answer a:hover {
        border-bottom-color: #00a884;
    }

    .faq-code {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        margin: 1rem 0;
        overflow-x: auto;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
    }

    .faq-highlight {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        padding: 0.3rem 0.6rem;
        border-radius: 6px;
        font-weight: 600;
        color: #92400e;
    }

    .faq-cta {
        background: linear-gradient(-45deg, #00a884, #008069, #00bfa5, #00695c);
        background-size: 400% 400%;
        animation: gradientFlow 15s ease infinite;
        color: white;
        padding: 3.5rem 2.5rem;
        border-radius: 20px;
        text-align: center;
        margin-top: 3rem;
        box-shadow: 0 10px 40px rgba(0,168,132,0.3);
        position: relative;
        overflow: hidden;
    }

    .faq-cta::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: gradientFlow 10s ease infinite;
    }

    .faq-cta h3 {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: white;
        font-weight: 800;
        position: relative;
        z-index: 1;
    }

    .faq-cta p {
        position: relative;
        z-index: 1;
    }

    .faq-cta-button {
        display: inline-block;
        background: white;
        color: #00695c;
        padding: 1.1rem 2.5rem;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        margin: 0.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        position: relative;
        z-index: 1;
        font-size: 1.05rem;
    }

    .faq-cta-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        background: #f0fdf4;
    }

    @media (max-width: 768px) {
        .faq-hero h1 {
            font-size: 2rem;
        }

        .faq-answer {
            padding-left: 0;
        }

        .faq-item {
            padding: 1.5rem;
        }

        .faq-question::before {
            font-size: 0.75rem;
            padding: 0.3rem 0.5rem;
        }
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
            ¡Che angirū! (¡Hola amigo!) Ser <strong>Beta Tester</strong> en Guarani App Store no es solo "probar aplicaciones antes que nadie".
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
            🎯 Probás nuevas funciones antes que nadie<br>
            <strong>💎 BONUS: Prioridad de entrada al accionariado cuando nos convirtamos en DAO</strong>
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

    <!-- SECCIÓN 6: DAO Y FUTURO -->
    <div class="faq-section">
        <h2>🌟 DAO & Accionariado - Tu Futuro en Guarani</h2>

        <div class="faq-item">
            <div class="faq-question">¿Qué es esto de convertirse en DAO?</div>
            <div class="faq-answer">
                <strong>DAO</strong> significa <strong>Decentralized Autonomous Organization</strong> (Organización Autónoma Descentralizada).
                <br><br>
                Estamos <strong>sopesando convertirnos en DAO</strong>, lo que significaría que Guarani App Store sería dirigida por su comunidad
                a través de votaciones y gobernanza compartida. No habría un "jefe" tradicional, sino que <strong>las decisiones importantes las tomarían
                los miembros mediante votación</strong>.
                <br><br>
                Esto es parte de nuestra visión de construir algo <span class="guarani-word">oñondivegua</span> (colaborativo), donde cada voz cuenta.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Qué beneficios tendría entrar como accionista/miembro de la DAO?</div>
            <div class="faq-answer">
                Si nos convertimos en DAO, los miembros tendrían:
                <ul>
                    <li><strong>🗳️ Tokens de Gobernanza:</strong> Tu voto contaría en decisiones estratégicas del negocio (nuevas apps, inversiones, dirección general, etc.)</li>
                    <li><strong>💰 Participación en Ganancias:</strong> Como accionista, recibirías dividendos según los beneficios de la empresa</li>
                    <li><strong>📈 Crecimiento del Valor:</strong> Si la empresa crece, tu participación vale más con el tiempo</li>
                    <li><strong>🚀 Acceso VIP Permanente:</strong> Prioridad en todos los productos y servicios futuros</li>
                    <li><strong>🤝 Networking Premium:</strong> Sos parte de una comunidad de innovadores y emprendedores tech</li>
                    <li><strong>💡 Poder de Propuesta:</strong> Podés proponer nuevos productos, features, o direcciones estratégicas</li>
                </ul>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Cómo entro al accionariado cuando se conviertan en DAO?</div>
            <div class="faq-answer">
                Aquí viene lo bueno: <strong>Los Beta Testers tendrán prioridad de entrada</strong>. 💎
                <br><br>
                Cuando llegue el momento de hacer la transición a DAO, los beta testers activos recibirán:
                <ul>
                    <li>🎫 <strong>Acceso prioritario:</strong> Podrán adquirir tokens/acciones antes que el público general</li>
                    <li>💎 <strong>Precio preferencial:</strong> Descuentos o condiciones especiales de entrada</li>
                    <li>🎁 <strong>Tokens iniciales bonus:</strong> Dependiendo de tu nivel y contribuciones, podrías recibir tokens gratuitos como reconocimiento</li>
                </ul>
                <br>
                <strong>Por eso ahora es el mejor momento para unirte como Beta Tester.</strong> No solo disfrutás de acceso gratis a todas las apps,
                sino que te estás asegurando un lugar preferencial en el futuro accionariado.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Cuándo se van a convertir en DAO?</div>
            <div class="faq-answer">
                Todavía no hay una fecha definida. Estamos <strong>evaluando cuidadosamente</strong> esta posibilidad.
                <br><br>
                Antes de hacerlo, queremos:
                <ul>
                    <li>Crecer nuestra base de usuarios y beta testers</li>
                    <li>Consolidar nuestras aplicaciones actuales</li>
                    <li>Estudiar el marco legal y regulatorio en Paraguay</li>
                    <li>Diseñar un modelo de gobernanza justo y sostenible</li>
                </ul>
                <br>
                Los beta testers serán los <strong>primeros en enterarse</strong> cuando tengamos novedades.
                Si estás registrado y activo, te notificaremos por email y Telegram.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">¿Qué diferencia hay entre tokens de gobernanza y acciones tradicionales?</div>
            <div class="faq-answer">
                <strong>Tokens de Gobernanza (DAO):</strong>
                <ul>
                    <li>Digital, basados en blockchain</li>
                    <li>Transferibles de forma inmediata y global</li>
                    <li>Votación directa en propuestas (sin intermediarios)</li>
                    <li>Transparencia total (todo registrado en blockchain)</li>
                    <li>Más flexibles para crear modelos de distribución creativos</li>
                </ul>
                <br>
                <strong>Acciones Tradicionales:</strong>
                <ul>
                    <li>Reguladas por ley de sociedades anónimas</li>
                    <li>Votación a través de juntas de accionistas</li>
                    <li>Transferencias más burocráticas</li>
                    <li>Marco legal más establecido en Paraguay</li>
                </ul>
                <br>
                Todavía estamos evaluando cuál sería el mejor modelo para nuestra comunidad. Podríamos usar un híbrido o uno puro.
                <strong>Lo que sí es seguro: los beta testers tendrán ventaja en cualquier escenario.</strong>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 7: PROBLEMAS -->
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
            y disfrutá de acceso gratuito para siempre. <strong>Jaha oñondive!</strong> (¡Vamos juntos!)
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
