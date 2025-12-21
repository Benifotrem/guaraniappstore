<?php
/**
 * Guarani App Store - Bot de Telegram Inteligente
 * Bot conversacional con IA para captación de leads
 *
 * Funcionalidades:
 * - Conversación contextual
 * - Detección de intenciones
 * - Calificación automática de leads
 * - Notificaciones al admin
 * - Captación profesional de clientes potenciales
 */

// Cargar configuración
define('APP_LOADED', true);
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/classes/Database.php';
require_once INCLUDES_PATH . '/classes/LeadManager.php';

// Inicializar
$db = Database::getInstance();
$leadManager = new LeadManager();

// Obtener token del bot
$bot_token = getenv('TELEGRAM_BOT_TOKEN') ?: '8507170288:AAEEIvm4WjStUHBi6AqckcZWfr6a8UpYlJ8';

// Obtener update de Telegram
$content = file_get_contents("php://input");
$update = json_decode($content, true);

// Logs
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

    $telegram_id = $user['id'];
    $telegram_username = $user['username'] ?? null;
    $first_name = $user['first_name'] ?? '';
    $last_name = $user['last_name'] ?? '';
    $full_name = trim($first_name . ' ' . $last_name);

    // Procesar comandos o conversación
    if (substr($text, 0, 1) === '/') {
        handleCommand($chat_id, $telegram_id, $text, $telegram_username, $full_name);
    } else {
        handleConversation($chat_id, $telegram_id, $text, $full_name);
    }
}

http_response_code(200);

/**
 * ==========================================
 * HANDLER DE COMANDOS
 * ==========================================
 */
function handleCommand($chat_id, $telegram_id, $text, $telegram_username, $full_name) {
    global $leadManager;

    $command = strtolower(explode(' ', $text)[0]);

    switch ($command) {
        case '/start':
            handleStart($chat_id, $telegram_id, $telegram_username, $full_name);
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
            // Tratar como conversación
            handleConversation($chat_id, $telegram_id, $text, $full_name);
    }
}

/**
 * ==========================================
 * HANDLER DE CONVERSACIÓN CONTEXTUAL
 * ==========================================
 */
function handleConversation($chat_id, $telegram_id, $text, $full_name) {
    global $leadManager;

    // Detectar intención
    $intent = $leadManager->detectIntent($text);

    // Extraer entidades
    $entities = $leadManager->extractEntities($text);

    // Obtener o crear lead
    $lead = $leadManager->getConversationState($telegram_id);

    if (!$lead) {
        // Crear nuevo lead
        $lead_id = $leadManager->upsertLead([
            'telegram_id' => $telegram_id,
            'telegram_username' => $telegram_username ?? '',
            'name' => $full_name,
            'source' => 'telegram_bot',
            'status' => 'new',
            'lead_type' => 'client'
        ]);
    } else {
        $lead_id = $lead['id'];
    }

    // Guardar mensaje del usuario
    $leadManager->saveMessage($lead_id, $telegram_id, 'user', $text, $intent);

    // Obtener contexto
    $context = $leadManager->getConversationContext($telegram_id, 5);

    // Generar respuesta basada en intención
    $response = generateResponse($intent, $text, $context, $entities, $lead_id);

    // Guardar respuesta del bot
    $leadManager->saveMessage($lead_id, $telegram_id, 'bot', $response['message']);

    // Enviar respuesta
    sendMessage($chat_id, $response['message'], 'Markdown');

    // Actualizar lead con información capturada
    if (!empty($entities)) {
        $update_data = ['telegram_id' => $telegram_id];

        if (isset($entities['email'])) $update_data['email'] = $entities['email'];
        if (isset($entities['phone'])) $update_data['phone'] = $entities['phone'];

        $leadManager->upsertLead($update_data);
    }

    // Si el intent indica alto valor, actualizar y notificar
    if (in_array($intent, ['create_saas', 'consulting', 'partnership'])) {
        $leadManager->upsertLead([
            'telegram_id' => $telegram_id,
            'interest' => $intent,
            'lead_type' => 'client'
        ]);

        // Calcular score
        $score = $leadManager->calculateLeadScore($lead_id);

        // Si score es alto, notificar inmediatamente
        if ($score >= 40 && !$lead['admin_notified']) {
            $leadManager->notifyAdmin($lead_id, 'new_lead');
        }
    }

    // Actualizar descripción del proyecto si es relevante
    if ($intent === 'create_saas' && strlen($text) > 30) {
        $current_desc = $lead['project_description'] ?? '';
        $new_desc = $current_desc . "\n" . $text;

        $leadManager->upsertLead([
            'telegram_id' => $telegram_id,
            'project_description' => trim($new_desc)
        ]);
    }
}

/**
 * Generar respuesta contextual
 */
function generateResponse($intent, $text, $context, $entities, $lead_id) {
    global $leadManager, $db;

    $responses = [
        'create_saas' => [
            'first' => "¡Excelente! Me encantaría ayudarte a crear tu SaaS. 🚀\n\n¿Podrías contarme brevemente qué problema quiere resolver tu aplicación?",
            'follow_up' => "Interesante. Para darte una propuesta personalizada, necesito entender mejor tu visión.\n\n¿Ya identificaste tu mercado objetivo? ¿Quiénes serían tus usuarios?",
            'details' => "Perfecto. ¿Qué funcionalidades clave necesitarías en tu MVP (Producto Mínimo Viable)?",
            'budget' => "Excelente contexto. Para dimensionar el proyecto correctamente:\n\n¿Cuál es tu presupuesto aproximado?\na) < $5,000\nb) $5,000 - $15,000\nc) $15,000 - $30,000\nd) > $30,000",
            'contact' => "¡Genial! Ya tengo suficiente información para preparar una propuesta.\n\n¿Cuál es tu email para enviarte los detalles?"
        ],
        'consulting' => [
            'first' => "Entiendo que necesitás consultoría tecnológica. 💡\n\n¿En qué área específica necesitás orientación?\n- Arquitectura de software\n- Elección de tecnologías\n- Estrategia de producto\n- Otra",
            'follow_up' => "¿Podrías darme más detalles sobre tu situación actual y qué querés lograr?"
        ],
        'partnership' => [
            'first' => "¡Me alegra que quieras explorar una colaboración! 🤝\n\n¿Qué tipo de partnership tenías en mente?\n- Referidos\n- Co-desarrollo\n- Reventa/Rebranding\n- Otro",
            'follow_up' => "Interesante. ¿Podrías contarme un poco sobre tu empresa/proyecto?"
        ],
        'beta_testing' => [
            'first' => "¡Genial que quieras ser Beta Tester! 🎯\n\nComo beta tester tendrás:\n✅ Acceso GRATIS de por vida\n✅ Funciones premium\n✅ Prioridad para el accionariado DAO\n\nPara registrarte: " . SITE_URL . "/beta/join"
        ],
        'pricing' => [
            'first' => "Los precios varían según el proyecto. Para darte una cotización precisa, necesito saber:\n\n¿Qué tipo de aplicación necesitás crear?\n¿Qué funcionalidades principales requiere?",
            'follow_up' => "Basado en eso, puedo prepararte una propuesta detallada. ¿Cuál es tu email?"
        ],
        'contact' => [
            'first' => "¡Perfecto! Podemos agendar una llamada o videollamada para discutir tu proyecto.\n\n¿Cuál es tu email y en qué horario preferís que te contactemos?"
        ],
        'general' => [
            'first' => "Hola! Soy el asistente de Guarani App Store. 👋\n\nPuedo ayudarte con:\n\n🚀 Crear tu aplicación SaaS\n💡 Consultoría tecnológica\n🤝 Partnerships\n🎯 Programa Beta Tester\n📊 Ver nuestros proyectos\n\n¿En qué puedo asistirte?",
            'follow_up' => "Claro, ¿podrías darme más detalles para poder ayudarte mejor?"
        ]
    ];

    // Determinar qué fase de la conversación
    $message_count = count($context);

    if ($message_count <= 2) {
        $phase = 'first';
    } elseif ($message_count <= 4) {
        $phase = 'follow_up';
    } elseif ($message_count <= 6) {
        $phase = 'details';
    } elseif ($message_count <= 8) {
        $phase = 'budget';
    } else {
        $phase = 'contact';
    }

    // Obtener respuesta
    $intent_responses = $responses[$intent] ?? $responses['general'];
    $message = $intent_responses[$phase] ?? $intent_responses['first'] ?? $intent_responses['follow_up'];

    // Si ya tenemos email, agradecer y cerrar
    if (!empty($entities['email'])) {
        $message = "¡Perfecto! Ya tengo tu email: " . $entities['email'] . "\n\n";
        $message .= "Te contactaré en las próximas 24 horas con una propuesta detallada para tu proyecto.\n\n";
        $message .= "Mientras tanto, podés ver algunos de nuestros trabajos aquí:\n" . SITE_URL . "/webapps\n\n";
        $message .= "¿Hay algo más en lo que pueda ayudarte?";
    }

    return ['message' => $message];
}

/**
 * Comando /start
 */
function handleStart($chat_id, $telegram_id, $telegram_username, $full_name) {
    global $leadManager;

    // Crear/actualizar lead
    $lead_id = $leadManager->upsertLead([
        'telegram_id' => $telegram_id,
        'telegram_username' => $telegram_username ?? '',
        'name' => $full_name,
        'source' => 'telegram_bot',
        'lead_type' => 'client'
    ]);

    $message = "¡Hola! Soy el asistente inteligente de *Guarani App Store*. 👋\n\n";
    $message .= "Estoy aquí para ayudarte a:\n\n";
    $message .= "🚀 *Crear tu aplicación SaaS* - Desde la idea hasta producción\n";
    $message .= "💡 *Consultoría tecnológica* - Arquitectura, stack, estrategia\n";
    $message .= "🤝 *Partnerships* - Colaboraciones y alianzas\n";
    $message .= "🎯 *Programa Beta Tester* - Acceso gratis + prioridad DAO\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "💎 *¿Sabías que estamos considerando convertirnos en DAO?*\n";
    $message .= "Los early adopters tendrán prioridad en el accionariado. Usa /dao para saber más.\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "Escribíme lo que necesitás y te voy a guiar paso a paso. 💬";

    sendMessage($chat_id, $message, 'Markdown');
}

/**
 * Comando /help
 */
function handleHelp($chat_id) {
    $message = "🤖 *Cómo funciono*\n\n";
    $message .= "Soy un asistente conversacional. No necesitás usar comandos, simplemente hablá conmigo naturalmente.\n\n";
    $message .= "Algunos ejemplos de lo que podés preguntarme:\n\n";
    $message .= "• \"Quiero crear una aplicación SaaS\"\n";
    $message .= "• \"Necesito consultoría para mi startup\"\n";
    $message .= "• \"¿Cuánto cuesta desarrollar una app?\"\n";
    $message .= "• \"Quiero ser Beta Tester\"\n";
    $message .= "• \"¿Qué es una DAO?\"\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "📌 *Comandos útiles:*\n";
    $message .= "/start - Reiniciar conversación\n";
    $message .= "/dao - Información sobre DAO\n";
    $message .= "/join - Unirse al programa Beta\n\n";
    $message .= "¿En qué puedo ayudarte hoy?";

    sendMessage($chat_id, $message, 'Markdown');
}

/**
 * Comando /dao
 */
function handleDAO($chat_id) {
    $message = "🌟 *¿Qué es una DAO?*\n\n";
    $message .= "*DAO* = Decentralized Autonomous Organization\n";
    $message .= "(Organización Autónoma Descentralizada)\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "🎯 *Características:*\n\n";
    $message .= "• Sin jefes tradicionales\n";
    $message .= "• Decisiones por votación\n";
    $message .= "• Reglas en smart contracts\n";
    $message .= "• 100% transparente\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "💎 *Para Guarani App Store:*\n\n";
    $message .= "Estamos evaluando esta transición. Si lo hacemos:\n\n";
    $message .= "🗳️ Tokens de gobernanza\n";
    $message .= "💰 Participación en ganancias\n";
    $message .= "📈 Crecimiento del valor\n";
    $message .= "🎯 Voz en decisiones\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "🎁 *Beta Testers tendrán prioridad*\n";
    $message .= "Usa /join para unirte ahora y asegurar tu lugar.";

    sendMessage($chat_id, $message, 'Markdown');
}

/**
 * Comando /join
 */
function handleJoin($chat_id) {
    $message = "🚀 *Programa Beta Tester*\n\n";
    $message .= "✅ Acceso GRATIS de por vida\n";
    $message .= "✅ Todas las funciones premium\n";
    $message .= "✅ Tu nombre en los créditos\n";
    $message .= "✅ Voz directa con devs\n\n";
    $message .= "💎 *BONUS: Prioridad DAO*\n";
    $message .= "Cuando nos convirtamos en DAO, los beta testers tendrán acceso prioritario al accionariado.\n\n";
    $message .= "━━━━━━━━━━━━━━━━\n\n";
    $message .= "📝 *Registrate aquí:*\n";
    $message .= SITE_URL . "/beta/join\n\n";
    $message .= "¿Preguntas? Simplemente preguntáme.";

    sendMessage($chat_id, $message, 'Markdown');
}

/**
 * Enviar mensaje
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
