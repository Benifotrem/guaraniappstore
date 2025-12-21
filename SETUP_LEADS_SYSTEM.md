# 🤖 Sistema Inteligente de Captación de Leads

## 📋 Descripción

Sistema conversacional con IA para captar leads automáticamente a través de:
- ✅ Bot de Telegram conversacional
- ✅ Widget web interactivo
- ✅ Detección automática de intenciones
- ✅ Calificación de leads (scoring)
- ✅ Notificaciones en tiempo real

---

## 🚀 Instalación

### 1. Crear las tablas en la base de datos

```bash
mysql -u tu_usuario -p tu_base_de_datos < database/leads_system.sql
```

### 2. Configurar el webhook del bot de Telegram

**Opción A: Usar el nuevo bot inteligente (RECOMENDADO)**

```bash
# Actualizar el webhook para apuntar al nuevo bot
curl "https://api.telegram.org/bot<TU_TOKEN>/setWebhook?url=https://guaraniappstore.com/telegram_bot_intelligent.php"
```

**Opción B: Reemplazar el bot actual**

```bash
# Renombrar el bot antiguo (backup)
mv public_html/telegram_bot.php public_html/telegram_bot_old.php

# Renombrar el nuevo bot
mv public_html/telegram_bot_intelligent.php public_html/telegram_bot.php

# El webhook ya debería estar configurado
```

### 3. Configurar tu Telegram ID para notificaciones

Necesitás obtener tu Telegram ID para recibir notificaciones de leads:

**Paso 1:** Habla con el bot [@userinfobot](https://t.me/userinfobot) en Telegram

**Paso 2:** Te dará tu ID (algo como `123456789`)

**Paso 3:** Guárdalo en la base de datos:

```sql
INSERT INTO site_settings (setting_key, setting_value, description)
VALUES ('admin_telegram_id', 'TU_ID_AQUI', 'Telegram ID del admin para notificaciones de leads');
```

O desde el panel de admin:
```
Settings → admin_telegram_id → TU_ID_AQUI
```

---

## 💬 Cómo Funciona el Bot

### Conversación Contextual

El bot **NO requiere comandos**. Simplemente conversa naturalmente:

**Usuario:** "Quiero crear una aplicación SaaS para gestión de inventarios"

**Bot:** "¡Excelente! Me encantaría ayudarte... ¿Podrías contarme qué problema específico quiere resolver?"

**Usuario:** "Necesito controlar stock en tiempo real de 3 sucursales"

**Bot:** "Interesante. ¿Ya identificaste tu mercado objetivo?..."

### Detección de Intenciones

El bot detecta automáticamente cuando alguien quiere:

- 🚀 **Crear un SaaS** → Inicia flujo de captación
- 💡 **Consultoría** → Califica necesidad
- 🤝 **Partnership** → Registra interés
- 💰 **Presupuesto** → Solicita detalles
- 🎯 **Beta Testing** → Deriva al programa

### Extracción de Datos

Automáticamente detecta y guarda:
- ✉️ Emails
- 📱 Teléfonos
- 💰 Presupuestos mencionados
- 🎯 Necesidades y pain points

### Sistema de Scoring

Cada lead recibe un puntaje automático (0-100):

| Score | Prioridad | Acción |
|-------|-----------|--------|
| 70-100 | 🔥🔥🔥 Urgente | Notifica inmediatamente |
| 50-69 | 🔥 Alta | Notifica |
| 30-49 | 💼 Media | Registra |
| 0-29 | 📋 Baja | Registra |

**Factores de scoring:**
- Email proporcionado: +20
- Teléfono: +10
- Descripción detallada: +15
- Presupuesto alto/enterprise: +25-30
- Timeline urgente: +20
- Múltiples interacciones: +10

---

## 🔔 Notificaciones al Admin

Cuando un lead alcanza **40+ puntos**, recibirás automáticamente un mensaje en Telegram:

```
🔥 NUEVO LEAD - Score: 65/100

👤 Contacto:
Nombre: Juan Pérez
Email: juan@empresa.com
Teléfono: +595981123456
Empresa: TechCorp SRL

📋 Detalles:
Tipo: Cliente potencial
Interés: Crear SaaS
Presupuesto: $15,000 - $30,000
Timeline: 1 mes

💡 Proyecto:
Sistema de gestión de inventarios multi-sucursal
con sincronización en tiempo real...

🔗 Dashboard: https://guaraniappstore.com/admin/leads/view?id=123
⏰ Hora: 21/12/2025 14:30
```

---

## 📊 Ver Leads Capturados

### Opción 1: Desde la base de datos

```sql
-- Ver todos los leads
SELECT id, name, email, phone, interest, score, status, created_at
FROM leads
ORDER BY score DESC, created_at DESC;

-- Ver conversaciones de un lead
SELECT role, message, created_at
FROM conversations
WHERE lead_id = 1
ORDER BY created_at ASC;
```

### Opción 2: Panel de Admin (próximamente)

Estamos preparando un dashboard completo donde podrás:
- Ver lista de leads con filtros
- Detalles de cada conversación
- Cambiar estado y prioridad
- Agregar notas
- Estadísticas

---

## 🎯 Widget Web (Próximo Paso)

El widget flotante en la web también será conversacional:

```javascript
// Ya está implementado con conversación básica
// Próximamente: guardar en BD y notificar
```

---

## 🧪 Testing

### Probar el bot

1. Abre [@guaraniappstore_bot](https://t.me/guaraniappstore_bot) en Telegram
2. Envía: `/start`
3. Luego escribe naturalmente: "Quiero crear una aplicación SaaS"
4. El bot debería iniciar una conversación
5. Si configuraste bien tu Telegram ID, deberías recibir notificación

### Verificar scoring

```sql
-- Ver lead con su score
SELECT id, name, email, score, interest, project_description
FROM leads
WHERE telegram_id = TU_TELEGRAM_ID;

-- Ver conversación completa
SELECT * FROM conversations
WHERE telegram_id = TU_TELEGRAM_ID
ORDER BY created_at ASC;
```

---

## 🔧 Configuración Avanzada

### Personalizar flujos de conversación

Los flujos están en la tabla `conversation_flows`:

```sql
-- Ver flujos actuales
SELECT * FROM conversation_flows;

-- Editar un flujo
UPDATE conversation_flows
SET steps = '...'
WHERE flow_name = 'crear_saas';
```

### Agregar nuevos intents

Edita `LeadManager.php`, método `detectIntent()`:

```php
'tu_nuevo_intent' => ['palabra clave 1', 'palabra clave 2'],
```

### Personalizar scoring

Edita `LeadManager.php`, método `calculateLeadScore()`.

---

## 🐛 Troubleshooting

### No recibo notificaciones

1. Verifica tu Telegram ID:
```sql
SELECT * FROM site_settings WHERE setting_key = 'admin_telegram_id';
```

2. Prueba enviarte un mensaje manualmente:
```bash
curl "https://api.telegram.org/bot<TOKEN>/sendMessage?chat_id=TU_ID&text=Test"
```

### El bot no responde

1. Verifica el webhook:
```bash
curl "https://api.telegram.org/bot<TOKEN>/getWebhookInfo"
```

2. Revisa los logs:
```bash
tail -f logs/telegram_bot.log
```

### Leads no se guardan

1. Verifica que las tablas existan:
```sql
SHOW TABLES LIKE 'leads';
SHOW TABLES LIKE 'conversations';
```

2. Verifica permisos de la base de datos

---

## 📈 Próximas Funciones

- [ ] Panel de admin completo para leads
- [ ] Widget web con persistencia en BD
- [ ] Integración con email (enviar propuestas)
- [ ] CRM básico
- [ ] Estadísticas y analytics
- [ ] Exportar leads a CSV
- [ ] Integraciones (Google Sheets, etc.)

---

## 🆘 Soporte

Si tenés problemas, revisá:
1. Logs: `logs/telegram_bot.log`
2. Tablas creadas correctamente
3. Webhook configurado
4. Telegram ID correcto
5. Permisos de archivos PHP

---

¡Listo! Ya tenés un sistema inteligente de captación de leads funcionando 🚀
