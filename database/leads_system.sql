-- ================================================
-- GUARANI APP STORE - SISTEMA DE LEADS Y CONVERSACIONES
-- Sistema inteligente de captación de leads
-- ================================================

-- ================================================
-- TABLA: leads
-- Almacena leads captados por el bot
-- ================================================
CREATE TABLE IF NOT EXISTS `leads` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `telegram_id` BIGINT,
  `telegram_username` VARCHAR(100),
  `name` VARCHAR(200),
  `email` VARCHAR(200),
  `phone` VARCHAR(50),
  `company` VARCHAR(200),

  -- Información del lead
  `lead_type` ENUM('beta_tester', 'client', 'partner', 'other') DEFAULT 'client',
  `interest` ENUM('create_saas', 'create_webapp', 'consulting', 'partnership', 'beta_testing', 'other') DEFAULT 'other',
  `budget_range` ENUM('bajo', 'medio', 'alto', 'enterprise', 'no_definido') DEFAULT 'no_definido',
  `timeline` ENUM('urgente', '1_mes', '3_meses', '6_meses', 'flexible', 'no_definido') DEFAULT 'no_definido',

  -- Contexto capturado
  `project_description` TEXT,
  `needs` JSON COMMENT 'Array de necesidades detectadas',
  `pain_points` JSON COMMENT 'Array de pain points mencionados',

  -- Calificación del lead
  `score` INT DEFAULT 0 COMMENT 'Scoring automático del lead (0-100)',
  `status` ENUM('new', 'contacted', 'qualified', 'proposal_sent', 'won', 'lost', 'nurturing') DEFAULT 'new',
  `priority` ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',

  -- Metadata
  `source` ENUM('telegram_bot', 'web_widget', 'direct', 'referral') DEFAULT 'telegram_bot',
  `referrer_url` VARCHAR(500),
  `utm_source` VARCHAR(100),
  `utm_medium` VARCHAR(100),
  `utm_campaign` VARCHAR(100),

  -- Admin notes
  `admin_notes` TEXT,
  `assigned_to` INT UNSIGNED COMMENT 'ID del admin asignado',

  -- Notificaciones
  `admin_notified` BOOLEAN DEFAULT FALSE,
  `notified_at` DATETIME,

  -- Timestamps
  `last_interaction` DATETIME,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  INDEX `idx_telegram_id` (`telegram_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_priority` (`priority`),
  INDEX `idx_score` (`score`),
  INDEX `idx_lead_type` (`lead_type`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: conversations
-- Almacena conversaciones con el bot
-- ================================================
CREATE TABLE IF NOT EXISTS `conversations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `lead_id` INT UNSIGNED,
  `telegram_id` BIGINT,
  `session_id` VARCHAR(100) COMMENT 'ID de sesión para web widget',

  -- Mensaje
  `role` ENUM('user', 'bot', 'system') NOT NULL,
  `message` TEXT NOT NULL,
  `intent` VARCHAR(100) COMMENT 'Intención detectada',
  `entities` JSON COMMENT 'Entidades extraídas del mensaje',

  -- Contexto
  `context` JSON COMMENT 'Contexto de la conversación',
  `sentiment` ENUM('positive', 'neutral', 'negative', 'unknown') DEFAULT 'unknown',

  -- Metadata
  `platform` ENUM('telegram', 'web', 'api') DEFAULT 'telegram',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (`lead_id`) REFERENCES `leads`(`id`) ON DELETE CASCADE,
  INDEX `idx_lead_id` (`lead_id`),
  INDEX `idx_telegram_id` (`telegram_id`),
  INDEX `idx_session_id` (`session_id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: lead_notifications
-- Notificaciones enviadas al admin
-- ================================================
CREATE TABLE IF NOT EXISTS `lead_notifications` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `lead_id` INT UNSIGNED NOT NULL,
  `notification_type` ENUM('new_lead', 'high_score', 'hot_lead', 'requires_attention') NOT NULL,
  `channel` ENUM('telegram', 'email', 'web') NOT NULL,
  `recipient` VARCHAR(200) COMMENT 'Email o Telegram ID del admin',
  `message` TEXT,
  `sent` BOOLEAN DEFAULT FALSE,
  `sent_at` DATETIME,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (`lead_id`) REFERENCES `leads`(`id`) ON DELETE CASCADE,
  INDEX `idx_lead_id` (`lead_id`),
  INDEX `idx_sent` (`sent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- TABLA: conversation_flows
-- Define flujos de conversación del bot
-- ================================================
CREATE TABLE IF NOT EXISTS `conversation_flows` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `flow_name` VARCHAR(100) UNIQUE NOT NULL,
  `trigger_keywords` JSON COMMENT 'Keywords que activan este flujo',
  `trigger_intents` JSON COMMENT 'Intenciones que activan este flujo',
  `steps` JSON COMMENT 'Pasos del flujo conversacional',
  `active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  INDEX `idx_flow_name` (`flow_name`),
  INDEX `idx_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- INSERTAR FLUJOS DE CONVERSACIÓN POR DEFECTO
-- ================================================

INSERT INTO `conversation_flows` (`flow_name`, `trigger_keywords`, `trigger_intents`, `steps`, `active`) VALUES
('crear_saas',
 '["crear saas", "crear aplicación", "crear app", "desarrollar saas", "necesito una aplicación", "quiero crear", "monetizar"]',
 '["create_saas", "develop_app", "build_product"]',
 '{
   "steps": [
     {
       "step": 1,
       "question": "¡Excelente! Me encantaría ayudarte a crear tu SaaS. 🚀\\n\\n¿Podrías contarme brevemente qué problema quiere resolver tu aplicación?",
       "collect": "project_description",
       "next_step": 2
     },
     {
       "step": 2,
       "question": "Interesante. ¿Ya tenés una audiencia o clientes potenciales identificados? ¿Cuál es tu mercado objetivo?",
       "collect": "target_market",
       "next_step": 3
     },
     {
       "step": 3,
       "question": "¿Qué funcionalidades clave necesitarías en tu MVP (Producto Mínimo Viable)?",
       "collect": "key_features",
       "next_step": 4
     },
     {
       "step": 4,
       "question": "¿Cuál es tu presupuesto aproximado para este proyecto?\\n\\nOpciones:\\na) Menos de $5,000\\nb) $5,000 - $15,000\\nc) $15,000 - $30,000\\nd) Más de $30,000\\ne) Todavía no lo sé",
       "collect": "budget",
       "next_step": 5
     },
     {
       "step": 5,
       "question": "¿Cuál es tu timeline ideal? ¿Cuándo necesitarías tener el producto funcionando?",
       "collect": "timeline",
       "next_step": 6
     },
     {
       "step": 6,
       "message": "¡Perfecto! Tengo toda la información que necesito. 🎯\\n\\nPermitíme compartir tus datos con el equipo. ¿Cuál es tu email para que podamos enviarte una propuesta personalizada?",
       "collect": "email",
       "next_step": 7
     },
     {
       "step": 7,
       "message": "¡Excelente! Ya notifiqué al equipo sobre tu proyecto. Te contactaremos en las próximas 24 horas con una propuesta detallada.\\n\\nMientras tanto, podés ver algunos de nuestros trabajos en: https://guaraniappstore.com/webapps\\n\\n¿Hay algo más en lo que pueda ayudarte?",
       "collect": null,
       "notify_admin": true,
       "end_flow": true
     }
   ]
 }',
 TRUE),

('consulta_general',
 '["consulta", "información", "pregunta", "ayuda", "contacto"]',
 '["general_inquiry", "need_help", "contact"]',
 '{
   "steps": [
     {
       "step": 1,
       "question": "¡Hola! Estoy aquí para ayudarte. 👋\\n\\n¿En qué puedo asistirte hoy?\\n\\na) Crear una aplicación SaaS\\nb) Consultoría tecnológica\\nc) Unirme al programa Beta Tester\\nd) Convertirme en partner\\ne) Otro",
       "collect": "inquiry_type",
       "next_step": 2
     },
     {
       "step": 2,
       "message": "Entiendo. ¿Podrías darme un poco más de contexto sobre lo que necesitás?",
       "collect": "details",
       "next_step": 3
     },
     {
       "step": 3,
       "message": "Perfecto. Para poder ayudarte mejor, ¿cuál es tu email?",
       "collect": "email",
       "next_step": 4
     },
     {
       "step": 4,
       "message": "¡Gracias! Ya notifiqué al equipo. Te contactaremos pronto para discutir tu consulta en detalle.\\n\\n¿Necesitás algo más?",
       "notify_admin": true,
       "end_flow": true
     }
   ]
 }',
 TRUE);
