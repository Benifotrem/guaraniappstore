<?php
/**
 * Guarani App Store - Telegram Bot
 * Bot para gestionar beta testers y feedback
 *
 * Configuración:
 * 1. Crear bot con @BotFather en Telegram
 * 2. Obtener el token del bot
 * 3. Configurar webhook: https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://guaraniappstore.com/telegram_bot.php
 *
 * Comandos disponibles:
 * /start - Registrarse como beta tester
 * /apps - Ver apps disponibles
 * /bug - Reportar un bug
 * /feature - Sugerir una feature
 * /stats - Ver tus estadísticas
 * /leaderboard - Ver ranking de beta testers
 * /help - Ver ayuda
 */

// Cargar configuración
define('APP_LOADED', true);
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/classes/Database.php';

// Inicializar base de datos
$db = Database::getInstance();

// Obtener token del bot desde configuración o variable de entorno
$bot_token = getenv('TELEGRAM_BOT_TOKEN') ?: '8507170288:AAEEIvm4WjStUHBi6AqckcZWfr6a8UpYlJ8';

// Obtener update de Telegram
$content = file_get_contents("php://input");
$update = json_decode($content, true);

// Logs para debugging
file_put_contents(__DIR__ . '/../logs/telegram_bot.log', date('Y-m-d H:i:s') . " - " . $content . "\n", FILE_APPEND);
if (!$update) {
    http_response_code(200);
    exit;
}

// Procesar mensaje
if (isset($update['message'])) {
    $message = $update['message'];
    $chat_id = $message['chat']['id'];
    $text = $message['text'] ?? '';
    $user = $message['from'];

    $telegram_username = $user['username'] ?? null;
    $telegram_first_name = $user['first_name'] ?? '';
    $telegram_last_name = $user['last_name'] ?? '';
    $telegram_id = $user['id'];

    // Procesar comandos
    if (substr($text, 0, 1) === '/') {
        $command = strtolower(explode(' ', $text)[0]);

        switch ($command) {
            case '/start':
                handleStart($chat_id, $telegram_id, $telegram_username, $telegram_first_name, $telegram_last_name);
                break;

            case '/apps':
                handleApps($chat_id);
                break;

            case '/bug':
                handleBugReport($chat_id, $telegram_id);
                break;

            case '/feature':
                handleFeatureRequest($chat_id, $telegram_id);
                break;

            case '/stats':
                handleStats($chat_id, $telegram_id);
                break;

            case '/leaderboard':
                handleLeaderboard($chat_id);
                break;

            case '/help':
                handleHelp($chat_id);
                break;

            case '/dao':
                handleDAO($chat_id);
                break;

            case '/join':
                handleJoin($chat_id);
                break;

            default:
                sendMessage($chat_id, "Comando no reconocido. Usa /help para ver los comandos disponibles.");
        }
    }
}

// Procesar callback queries (botones inline)
if (isset($update['callback_query'])) {
    $callback = $update['callback_query'];
    $chat_id = $callback['message']['chat']['id'];
    $message_id = $callback['message']['message_id'];
    $data = $callback['data'];
    $telegram_id = $callback['from']['id'];

    handleCallback($chat_id, $message_id, $data, $telegram_id);
}

http_response_code(200);

/**
 * ==========================================
 * HANDLERS DE COMANDOS
 * ==========================================
 */

/**
 * Comando /start - Registro de beta tester
 */
function handleStart($chat_id, $telegram_id, $telegram_username, $first_name, $last_name) {
    global $db;

    // Verificar si ya está registrado
    $existing = $db->fetchOne("
        SELECT id, name, access_token, status, contribution_level
        FROM beta_testers
        WHERE telegram_username = ? OR telegram_id = ?
    ", [$telegram_username, $telegram_id]);

    if ($existing) {
        $status_emoji = $existing['status'] === 'active' ? '✅' : '⏳';
        $level_emoji = [
            'bronze' => '🥉',
            'silver' => '🥈',
            'gold' => '🥇',
            'platinum' => '💎'
        ][$existing['contribution_level']] ?? '🆕';

        $message = "👋 ¡Hola de nuevo, {$existing['name']}!\n\n";
        $message .= "Estado: $status_emoji " . ucfirst($existing['status']) . "\n";
        $message .= "Nivel: $level_emoji " . ucfirst($existing['contribution_level']) . "\n\n";
        $message .= "🔗 Accede a tu dashboard:\n";
        $message .= SITE_URL . "/beta/dashboard?token={$existing['access_token']}\n\n";
        $message .= "Usa /help para ver todos los comandos disponibles.";

        sendMessage($chat_id, $message);
        return;
    }

    // Nuevo usuario
    $message = "🎉 ¡Bienvenido al Programa Beta de Guarani App Store!\n\n";
    $message .= "Soy el bot oficial para beta testers. Aquí podrás:\n\n";
    $message .= "🐛 Reportar bugs\n";
    $message .= "💡 Sugerir features\n";
    $message .= "📊 Ver tus estadísticas\n";
    $message .= "🏆 Competir en el leaderboard\n";
    $message .= "🚀 Recibir notificaciones de nuevas apps\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "🌟 *IMPORTANTE:*\n";
    $message .= "Estamos considerando convertirnos en *DAO*. Los Beta Testers tendrán *prioridad de entrada al accionariado* con tokens de gobernanza.\n\n";
    $message .= "¿Qué es una DAO? Usa /dao para aprender más.\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "Para completar tu registro, visita:\n";
    $message .= SITE_URL . "/beta/join\n\n";
    $message .= "Después de registrarte, vuelve aquí y usa /start para vincular tu cuenta.";

    // Actualizar telegram_id si existe por username
    if ($telegram_username) {
        $db->query("UPDATE beta_testers SET telegram_id = ? WHERE telegram_username = ?", [$telegram_id, $telegram_username]);
    }

    sendMessage($chat_id, $message);
}

/**
 * Comando /apps - Listar apps disponibles
 */
function handleApps($chat_id) {
    global $db;

    $apps = $db->fetchAll("
        SELECT id, title, short_description, app_url, category
        FROM webapps
        WHERE status = 'published'
        ORDER BY created_at DESC
        LIMIT 5
    ");

    if (empty($apps)) {
        sendMessage($chat_id, "📭 No hay apps disponibles en este momento.");
        return;
    }

    $message = "🚀 *Apps Disponibles para Testear:*\n\n";

    foreach ($apps as $app) {
        $message .= "━━━━━━━━━━━━━━━━\n";
        $message .= "📱 *{$app['title']}*\n";
        $message .= "📁 {$app['category']}\n";
        $message .= "📝 {$app['short_description']}\n";
        $message .= "🔗 {$app['app_url']}\n\n";
    }

    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "Ver todas: " . SITE_URL . "/webapps";

    sendMessage($chat_id, $message, 'Markdown');
}

/**
 * Comando /bug - Reportar bug
 */
function handleBugReport($chat_id, $telegram_id) {
    global $db;

    // Verificar que esté registrado
    $tester = getBetaTester($telegram_id);
    if (!$tester) {
        sendMessage($chat_id, "⚠️ Primero debes registrarte. Usa /start para más información.");
        return;
    }

    // Obtener apps
    $apps = $db->fetchAll("SELECT id, title FROM webapps WHERE status = 'published' LIMIT 10");

    if (empty($apps)) {
        sendMessage($chat_id, "📭 No hay apps disponibles para reportar bugs.");
        return;
    }

    $message = "🐛 *Reportar Bug*\n\n";
    $message .= "Selecciona la app donde encontraste el bug:";

    // Crear botones inline
    $keyboard = [];
    foreach ($apps as $app) {
        $keyboard[] = [[
            'text' => $app['title'],
            'callback_data' => "bug_{$app['id']}"
        ]];
    }

    sendMessageWithKeyboard($chat_id, $message, $keyboard, 'Markdown');
}

/**
 * Comando /feature - Sugerir feature
 */
function handleFeatureRequest($chat_id, $telegram_id) {
    global $db;

    // Verificar que esté registrado
    $tester = getBetaTester($telegram_id);
    if (!$tester) {
        sendMessage($chat_id, "⚠️ Primero debes registrarte. Usa /start para más información.");
        return;
    }

    // Obtener apps
    $apps = $db->fetchAll("SELECT id, title FROM webapps WHERE status = 'published' LIMIT 10");

    if (empty($apps)) {
        sendMessage($chat_id, "📭 No hay apps disponibles.");
        return;
    }

    $message = "💡 *Sugerir Feature*\n\n";
    $message .= "Selecciona la app para la cual quieres sugerir una feature:";

    // Crear botones inline
    $keyboard = [];
    foreach ($apps as $app) {
        $keyboard[] = [[
            'text' => $app['title'],
            'callback_data' => "feature_{$app['id']}"
        ]];
    }

    sendMessageWithKeyboard($chat_id, $message, $keyboard, 'Markdown');
}

/**
 * Comando /stats - Ver estadísticas personales
 */
function handleStats($chat_id, $telegram_id) {
    $tester = getBetaTester($telegram_id);

    if (!$tester) {
        sendMessage($chat_id, "⚠️ Primero debes registrarte. Usa /start para más información.");
        return;
    }

    $level_emoji = [
        'bronze' => '🥉',
        'silver' => '🥈',
        'gold' => '🥇',
        'platinum' => '💎'
    ][$tester['contribution_level']] ?? '🆕';

    $total = $tester['bugs_reported'] + $tester['suggestions_accepted'];

    $message = "📊 *Tus Estadísticas*\n\n";
    $message .= "👤 {$tester['name']}\n";
    $message .= "📧 {$tester['email']}\n";
    $message .= "🏅 Nivel: $level_emoji " . ucfirst($tester['contribution_level']) . "\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "🐛 Bugs Reportados: {$tester['bugs_reported']}\n";
    $message .= "💡 Sugerencias Aceptadas: {$tester['suggestions_accepted']}\n";
    $message .= "🎯 Total Contribuciones: $total\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";

    // Calcular progreso al siguiente nivel
    $level_thresholds = ['bronze' => 10, 'silver' => 25, 'gold' => 50, 'platinum' => 999];
    $next_threshold = $level_thresholds[$tester['contribution_level']] ?? 999;

    if ($next_threshold < 999) {
        $remaining = $next_threshold - $total;
        $next_level = ['bronze' => 'Silver', 'silver' => 'Gold', 'gold' => 'Platinum'][$tester['contribution_level']];
        $message .= "🎯 Progreso a $next_level: $remaining contribuciones restantes\n\n";
    } else {
        $message .= "👑 ¡Has alcanzado el nivel máximo!\n\n";
    }

    $message .= "🔗 Dashboard completo:\n";
    $message .= SITE_URL . "/beta/dashboard?token={$tester['access_token']}";

    sendMessage($chat_id, $message, 'Markdown');
}

/**
 * Comando /leaderboard - Ver ranking
 */
function handleLeaderboard($chat_id) {
    global $db;

    $leaderboard = $db->fetchAll("
        SELECT
            name,
            contribution_level,
            bugs_reported,
            suggestions_accepted,
            (bugs_reported + suggestions_accepted) as total
        FROM beta_testers
        WHERE status = 'active'
        ORDER BY total DESC, created_at ASC
        LIMIT 10
    ");

    if (empty($leaderboard)) {
        sendMessage($chat_id, "📭 El leaderboard está vacío.");
        return;
    }

    $message = "🏆 *Top 10 Beta Testers*\n\n";

    $medals = ['🥇', '🥈', '🥉'];

    foreach ($leaderboard as $index => $tester) {
        $position = $index + 1;
        $medal = $index < 3 ? $medals[$index] : "$position.";

        $level_emoji = [
            'bronze' => '🥉',
            'silver' => '🥈',
            'gold' => '🥇',
            'platinum' => '💎'
        ][$tester['contribution_level']] ?? '🆕';

        $message .= "$medal *{$tester['name']}* $level_emoji\n";
        $message .= "   🎯 {$tester['total']} contribuciones";
        $message .= " (🐛 {$tester['bugs_reported']} | 💡 {$tester['suggestions_accepted']})\n\n";
    }

    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "¿Quieres subir en el ranking?\n";
    $message .= "Usa /apps para ver apps y /bug o /feature para contribuir.";

    sendMessage($chat_id, $message, 'Markdown');
}

/**
 * Comando /help - Ayuda
 */
function handleHelp($chat_id) {
    $message = "🤖 *Comandos Disponibles*\n\n";
    $message .= "/start - Registrarte o ver tu perfil\n";
    $message .= "/join - Unirte al Programa Beta\n";
    $message .= "/apps - Ver apps disponibles\n";
    $message .= "/bug - Reportar un bug\n";
    $message .= "/feature - Sugerir una feature\n";
    $message .= "/stats - Ver tus estadísticas\n";
    $message .= "/leaderboard - Ver ranking de testers\n";
    $message .= "/dao - ¿Qué es una DAO?\n";
    $message .= "/help - Ver esta ayuda\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "🌟 *Estamos considerando convertirnos en DAO*\n";
    $message .= "Los Beta Testers tendrán *prioridad de entrada al accionariado*\n\n";
    $message .= "💬 *Comunidad:*\n";
    $message .= "• Discord: [Próximamente]\n";
    $message .= "• Web: " . SITE_URL . "\n\n";
    $message .= "¿Preguntas? Contacta: " . SITE_EMAIL;

    sendMessage($chat_id, $message, 'Markdown');
}

/**
 * Comando /dao - Explicar qué es una DAO
 */
function handleDAO($chat_id) {
    $message = "🌟 *¿Qué es una DAO?*\n\n";
    $message .= "*DAO* significa *Decentralized Autonomous Organization* (Organización Autónoma Descentralizada).\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "🎯 *Características principales:*\n\n";
    $message .= "• *Sin jefes tradicionales:* Las decisiones se toman mediante votación de los miembros\n";
    $message .= "• *Reglas en código:* Todo está automatizado en smart contracts\n";
    $message .= "• *Transparente:* Todas las decisiones y transacciones son públicas\n";
    $message .= "• *Democrático:* Tu voto cuenta según tu participación\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "🎁 *Para Guarani App Store:*\n\n";
    $message .= "Estamos *considerando convertirnos en DAO* para que nuestra comunidad participe activamente en las decisiones del negocio.\n\n";
    $message .= "Los miembros de la DAO podrían:\n";
    $message .= "• 🗳️ Votar en decisiones estratégicas\n";
    $message .= "• 💰 Recibir dividendos según participación\n";
    $message .= "• 🚀 Proponer nuevos productos\n";
    $message .= "• 📈 Beneficiarse del crecimiento de la empresa\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "💎 *¿Cómo entrar?*\n\n";
    $message .= "Los *Beta Testers tendrán prioridad* cuando hagamos la transición a DAO.\n\n";
    $message .= "Si aún no eres Beta Tester, usa /join para unirte ahora y asegurar tu lugar.";

    sendMessage($chat_id, $message, 'Markdown');
}

/**
 * Comando /join - Invitar a unirse al programa beta
 */
function handleJoin($chat_id) {
    $message = "🚀 *Únete al Programa Beta Tester*\n\n";
    $message .= "¿Por qué ser Beta Tester?\n\n";
    $message .= "✅ Acceso *GRATIS de por vida* a todas las apps\n";
    $message .= "✅ Todas las funciones premium sin pagar\n";
    $message .= "✅ Tu nombre en los créditos\n";
    $message .= "✅ Voz directa con los desarrolladores\n";
    $message .= "✅ Probás nuevas features antes que nadie\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "💎 *BONUS ESPECIAL:*\n\n";
    $message .= "Estamos sopesando convertirnos en *DAO* (Organización Autónoma Descentralizada).\n\n";
    $message .= "Cuando eso pase, los *Beta Testers tendrán prioridad de entrada al accionariado* con tokens de gobernanza.\n\n";
    $message .= "Esto significa:\n";
    $message .= "• 🗳️ Voto en decisiones del negocio\n";
    $message .= "• 💵 Participación en ganancias\n";
    $message .= "• 📈 Crecimiento del valor de tu participación\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "📝 *Cómo registrarte:*\n\n";
    $message .= "1️⃣ Visita: " . SITE_URL . "/beta/join\n";
    $message .= "2️⃣ Completa el formulario\n";
    $message .= "3️⃣ Recibís tu token de acceso por email\n";
    $message .= "4️⃣ Te activamos en 24-48 horas\n";
    $message .= "5️⃣ ¡Listo! Ya podés empezar a testear\n\n";
    $message .= "Usa /dao para aprender más sobre DAOs y tokens de gobernanza.\n\n";
    $message .= "¿Preguntas? Visita: " . SITE_URL . "/faq";

    sendMessage($chat_id, $message, 'Markdown');
}

/**
 * Manejar callbacks de botones inline
 */
function handleCallback($chat_id, $message_id, $data, $telegram_id) {
    global $db;

    // Parsear callback data
    $parts = explode('_', $data);
    $action = $parts[0];
    $app_id = $parts[1] ?? null;

    $tester = getBetaTester($telegram_id);

    if (!$tester) {
        sendMessage($chat_id, "⚠️ Error: No se encontró tu registro.");
        return;
    }

    if ($action === 'bug' || $action === 'feature') {
        $type = $action;
        $type_label = $type === 'bug' ? 'Bug' : 'Feature';

        $app = $db->fetchOne("SELECT title FROM webapps WHERE id = ?", [$app_id]);

        if (!$app) {
            sendMessage($chat_id, "⚠️ App no encontrada.");
            return;
        }

        $message = "✅ App seleccionada: *{$app['title']}*\n\n";
        $message .= "Para completar el reporte de $type_label, ve al sitio web:\n\n";
        $message .= SITE_URL . "/webapps\n\n";
        $message .= "O envíame un mensaje con el siguiente formato:\n\n";
        $message .= "`Título breve del $type_label`\n\n";
        $message .= "_(Próximamente podrás reportar directamente desde Telegram)_";

        sendMessage($chat_id, $message, 'Markdown');
    }
}

/**
 * ==========================================
 * FUNCIONES AUXILIARES
 * ==========================================
 */

/**
 * Obtener beta tester por telegram_id
 */
function getBetaTester($telegram_id) {
    global $db;

    return $db->fetchOne("
        SELECT *
        FROM beta_testers
        WHERE telegram_id = ? AND status = 'active'
    ", [$telegram_id]);
}

/**
 * Enviar mensaje simple
 */
function sendMessage($chat_id, $text, $parse_mode = null) {
    global $bot_token;

    $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";

    $data = [
        'chat_id' => $chat_id,
        'text' => $text
    ];

    if ($parse_mode) {
        $data['parse_mode'] = $parse_mode;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

/**
 * Enviar mensaje con teclado inline
 */
function sendMessageWithKeyboard($chat_id, $text, $keyboard, $parse_mode = null) {
    global $bot_token;

    $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";

    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
    ];

    if ($parse_mode) {
        $data['parse_mode'] = $parse_mode;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

/**
 * Enviar notificación a todos los beta testers activos
 * Uso: Notificar cuando se publica una nueva app
 */
function notifyAllTesters($message) {
    global $db;

    $testers = $db->fetchAll("
        SELECT telegram_id
        FROM beta_testers
        WHERE status = 'active' AND telegram_id IS NOT NULL
    ");

    foreach ($testers as $tester) {
        sendMessage($tester['telegram_id'], $message, 'Markdown');
        usleep(100000); // 100ms delay para evitar rate limiting
    }
}
