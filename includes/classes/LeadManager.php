<?php
/**
 * Lead Manager - Sistema inteligente de captación de leads
 * Maneja conversaciones contextuales y calificación de leads
 */

class LeadManager {
    private $db;
    private $admin_telegram_id; // ID de Telegram del admin para notificaciones

    public function __construct() {
        $this->db = Database::getInstance();
        // Obtener el Telegram ID del admin desde configuración
        $this->admin_telegram_id = get_setting('admin_telegram_id', '');
    }

    /**
     * Crear o actualizar un lead
     */
    public function upsertLead($data) {
        $telegram_id = $data['telegram_id'] ?? null;

        // Verificar si ya existe
        $existing = null;
        if ($telegram_id) {
            $existing = $this->db->fetchOne("SELECT id FROM leads WHERE telegram_id = ?", [$telegram_id]);
        }

        if ($existing) {
            // Actualizar
            $update_fields = [];
            $params = [];

            foreach ($data as $key => $value) {
                if ($key !== 'telegram_id' && $key !== 'id') {
                    $update_fields[] = "`$key` = ?";
                    $params[] = is_array($value) ? json_encode($value) : $value;
                }
            }

            $params[] = $existing['id'];
            $sql = "UPDATE leads SET " . implode(', ', $update_fields) . ", last_interaction = NOW() WHERE id = ?";
            $this->db->query($sql, $params);

            return $existing['id'];
        } else {
            // Crear nuevo
            $fields = array_keys($data);
            $placeholders = array_fill(0, count($fields), '?');
            $values = array_map(function($v) { return is_array($v) ? json_encode($v) : $v; }, array_values($data));

            $sql = "INSERT INTO leads (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
            $this->db->query($sql, $values);

            return $this->db->lastInsertId();
        }
    }

    /**
     * Guardar mensaje de conversación
     */
    public function saveMessage($lead_id, $telegram_id, $role, $message, $intent = null, $platform = 'telegram') {
        $this->db->query("
            INSERT INTO conversations (lead_id, telegram_id, role, message, intent, platform, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ", [$lead_id, $telegram_id, $role, $message, $intent, $platform]);

        // Actualizar última interacción del lead
        $this->db->query("UPDATE leads SET last_interaction = NOW() WHERE id = ?", [$lead_id]);
    }

    /**
     * Obtener contexto de conversación reciente
     */
    public function getConversationContext($telegram_id, $limit = 10) {
        return $this->db->fetchAll("
            SELECT role, message, intent, created_at
            FROM conversations
            WHERE telegram_id = ?
            ORDER BY created_at DESC
            LIMIT ?
        ", [$telegram_id, $limit]);
    }

    /**
     * Detectar intención del mensaje
     */
    public function detectIntent($message) {
        $message_lower = mb_strtolower($message);

        // Palabras clave para diferentes intenciones
        $intent_patterns = [
            'create_saas' => ['crear saas', 'crear app', 'desarrollar aplicación', 'necesito una app', 'quiero crear', 'monetizar', 'crear plataforma'],
            'consulting' => ['consultoría', 'asesoría', 'consulta técnica', 'necesito ayuda', 'orientación'],
            'beta_testing' => ['beta tester', 'probar apps', 'testear', 'feedback'],
            'partnership' => ['partner', 'colaborar', 'alianza', 'trabajar juntos'],
            'pricing' => ['precio', 'costo', 'cuánto cuesta', 'presupuesto', 'cotización'],
            'contact' => ['contacto', 'hablar', 'llamar', 'reunión', 'email']
        ];

        foreach ($intent_patterns as $intent => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($message_lower, $pattern) !== false) {
                    return $intent;
                }
            }
        }

        return 'general';
    }

    /**
     * Extraer entidades del mensaje (email, teléfono, etc.)
     */
    public function extractEntities($message) {
        $entities = [];

        // Email
        if (preg_match('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', $message, $matches)) {
            $entities['email'] = $matches[0];
        }

        // Teléfono (formato simple)
        if (preg_match('/\b(\+?595|0)?9\d{8}\b/', $message, $matches)) {
            $entities['phone'] = $matches[0];
        }

        // Presupuesto (números con $ o USD)
        if (preg_match('/(\$|USD)\s?(\d{1,3}(,\d{3})*(\.\d{2})?|\d+)/', $message, $matches)) {
            $entities['budget'] = $matches[0];
        }

        return $entities;
    }

    /**
     * Calcular score del lead
     */
    public function calculateLeadScore($lead_id) {
        $lead = $this->db->fetchOne("SELECT * FROM leads WHERE id = ?", [$lead_id]);
        if (!$lead) return 0;

        $score = 0;

        // Email proporcionado (+20)
        if (!empty($lead['email'])) $score += 20;

        // Teléfono proporcionado (+10)
        if (!empty($lead['phone'])) $score += 10;

        // Descripción del proyecto (+15)
        if (!empty($lead['project_description'])) $score += 15;

        // Presupuesto definido
        $budget_scores = [
            'enterprise' => 30,
            'alto' => 25,
            'medio' => 15,
            'bajo' => 5
        ];
        $score += $budget_scores[$lead['budget_range']] ?? 0;

        // Timeline urgente
        $timeline_scores = [
            'urgente' => 20,
            '1_mes' => 15,
            '3_meses' => 10,
            '6_meses' => 5
        ];
        $score += $timeline_scores[$lead['timeline']] ?? 0;

        // Número de interacciones
        $interactions = $this->db->fetchOne("SELECT COUNT(*) as count FROM conversations WHERE lead_id = ?", [$lead_id]);
        if ($interactions['count'] > 5) $score += 10;

        // Actualizar score en la BD
        $this->db->query("UPDATE leads SET score = ? WHERE id = ?", [$score, $lead_id]);

        return $score;
    }

    /**
     * Notificar al admin sobre un nuevo lead
     */
    public function notifyAdmin($lead_id, $type = 'new_lead') {
        $lead = $this->db->fetchOne("SELECT * FROM leads WHERE id = ?", [$lead_id]);
        if (!$lead || $lead['admin_notified']) return;

        $score = $this->calculateLeadScore($lead_id);

        // Determinar prioridad
        $priority = 'medium';
        if ($score >= 70) $priority = 'urgent';
        elseif ($score >= 50) $priority = 'high';

        $message = $this->buildNotificationMessage($lead, $score);

        // Guardar notificación en BD
        $this->db->query("
            INSERT INTO lead_notifications (lead_id, notification_type, channel, recipient, message, sent, sent_at)
            VALUES (?, ?, 'telegram', ?, ?, TRUE, NOW())
        ", [$lead_id, $type, $this->admin_telegram_id, $message]);

        // Enviar notificación por Telegram si está configurado
        if (!empty($this->admin_telegram_id)) {
            $this->sendTelegramNotification($this->admin_telegram_id, $message);
        }

        // Marcar lead como notificado
        $this->db->query("
            UPDATE leads SET admin_notified = TRUE, notified_at = NOW(), priority = ? WHERE id = ?
        ", [$priority, $lead_id]);
    }

    /**
     * Construir mensaje de notificación
     */
    private function buildNotificationMessage($lead, $score) {
        $emoji_priority = $score >= 70 ? '🔥🔥🔥' : ($score >= 50 ? '🔥' : '💼');

        $message = "$emoji_priority *NUEVO LEAD - Score: $score/100*\n\n";
        $message .= "👤 *Contacto:*\n";
        $message .= "Nombre: " . ($lead['name'] ?? 'No proporcionado') . "\n";
        $message .= "Email: " . ($lead['email'] ?? 'No proporcionado') . "\n";
        $message .= "Teléfono: " . ($lead['phone'] ?? 'No proporcionado') . "\n";
        $message .= "Empresa: " . ($lead['company'] ?? 'No proporcionado') . "\n\n";

        $message .= "📋 *Detalles:*\n";
        $message .= "Tipo: " . $this->translateEnum($lead['lead_type']) . "\n";
        $message .= "Interés: " . $this->translateEnum($lead['interest']) . "\n";
        $message .= "Presupuesto: " . $this->translateEnum($lead['budget_range']) . "\n";
        $message .= "Timeline: " . $this->translateEnum($lead['timeline']) . "\n\n";

        if (!empty($lead['project_description'])) {
            $message .= "💡 *Proyecto:*\n" . substr($lead['project_description'], 0, 200) . "\n\n";
        }

        $message .= "🔗 *Dashboard:* " . SITE_URL . "/admin/leads/view?id=" . $lead['id'] . "\n";
        $message .= "⏰ *Hora:* " . date('d/m/Y H:i');

        return $message;
    }

    /**
     * Traducir enums a español
     */
    private function translateEnum($value) {
        $translations = [
            'client' => 'Cliente potencial',
            'beta_tester' => 'Beta Tester',
            'partner' => 'Partner',
            'create_saas' => 'Crear SaaS',
            'create_webapp' => 'Crear App Web',
            'consulting' => 'Consultoría',
            'partnership' => 'Partnership',
            'beta_testing' => 'Beta Testing',
            'bajo' => '< $5,000',
            'medio' => '$5,000 - $15,000',
            'alto' => '$15,000 - $30,000',
            'enterprise' => '> $30,000',
            'no_definido' => 'No definido',
            'urgente' => 'Urgente',
            '1_mes' => '1 mes',
            '3_meses' => '3 meses',
            '6_meses' => '6 meses',
            'flexible' => 'Flexible'
        ];

        return $translations[$value] ?? $value;
    }

    /**
     * Enviar notificación por Telegram
     */
    private function sendTelegramNotification($chat_id, $message) {
        $bot_token = getenv('TELEGRAM_BOT_TOKEN') ?: '8507170288:AAEEIvm4WjStUHBi6AqckcZWfr6a8UpYlJ8';
        $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";

        $data = [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => true
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Obtener estado de conversación del usuario
     */
    public function getConversationState($telegram_id) {
        return $this->db->fetchOne("
            SELECT * FROM leads WHERE telegram_id = ? ORDER BY created_at DESC LIMIT 1
        ", [$telegram_id]);
    }
}
