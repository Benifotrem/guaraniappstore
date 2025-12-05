# 🤖 Telegram Bot - Guía de Configuración

Bot oficial de Guarani App Store para gestión de beta testers.

## 📋 Requisitos Previos

- Cuenta de Telegram
- Acceso a @BotFather
- Servidor web con HTTPS (requerido por Telegram)
- PHP 7.4+

## 🚀 Configuración Paso a Paso

### 1. Crear el Bot en Telegram

1. Abre Telegram y busca **@BotFather**
2. Envía el comando `/newbot`
3. Sigue las instrucciones:
   - Nombre del bot: `Guarani App Store Beta Bot`
   - Username: `guarani_appstore_bot` (debe terminar en `_bot` o `Bot`)
4. Guarda el **token** que te proporciona (ejemplo: `123456789:ABCdefGHIjklMNOpqrsTUVwxyz`)

### 2. Configurar Comandos del Bot

Envía este mensaje a @BotFather:

```
/setcommands
```

Selecciona tu bot y pega estos comandos:

```
start - Registrarte o ver tu perfil
apps - Ver apps disponibles para testear
bug - Reportar un bug
feature - Sugerir una nueva feature
stats - Ver tus estadísticas personales
leaderboard - Ver ranking de beta testers
help - Ver ayuda y comandos disponibles
```

### 3. Configurar Descripción del Bot

```
/setdescription
```

Descripción sugerida:
```
Bot oficial de Guarani App Store para beta testers.
Reporta bugs, sugiere features y accede a tu dashboard personal.
```

### 4. Configurar Foto de Perfil

Envía `/setuserpic` a @BotFather y sube tu logo

### 5. Configurar el Token en el Servidor

**Opción A: Variable de Entorno (Recomendado)**

```bash
# En tu servidor, agrega al archivo .bashrc o .profile
export TELEGRAM_BOT_TOKEN="TU_TOKEN_AQUI"

# O en el cPanel de Hostinger:
# Variables de Entorno > Agregar variable
# Nombre: TELEGRAM_BOT_TOKEN
# Valor: tu token
```

**Opción B: Hardcoded en el archivo**

Edita `telegram_bot.php` línea 27:
```php
$bot_token = 'TU_TOKEN_AQUI';
```

### 6. Configurar Webhook

El webhook es la URL que Telegram usará para enviar actualizaciones a tu bot.

**Usando curl:**

```bash
curl -X POST "https://api.telegram.org/bot<TU_TOKEN>/setWebhook?url=https://guaraniappstore.com/telegram_bot.php"
```

**Usando navegador:**

Visita esta URL (reemplaza `<TU_TOKEN>`):
```
https://api.telegram.org/bot<TU_TOKEN>/setWebhook?url=https://guaraniappstore.com/telegram_bot.php
```

Deberías ver:
```json
{
  "ok": true,
  "result": true,
  "description": "Webhook was set"
}
```

### 7. Verificar Webhook

Verifica que el webhook esté configurado correctamente:

```bash
curl "https://api.telegram.org/bot<TU_TOKEN>/getWebhookInfo"
```

Deberías ver tu URL en `url` y `pending_update_count: 0`.

### 8. Crear Directorio de Logs

```bash
mkdir -p logs
chmod 755 logs
```

### 9. Actualizar Base de Datos

Agrega la columna `telegram_id` a la tabla `beta_testers` si no existe:

```sql
ALTER TABLE beta_testers
ADD COLUMN telegram_id BIGINT NULL AFTER telegram_username,
ADD INDEX idx_telegram_id (telegram_id);
```

## 🧪 Probar el Bot

1. Busca tu bot en Telegram: `@guarani_appstore_bot`
2. Envía `/start`
3. El bot debería responder con un mensaje de bienvenida
4. Prueba otros comandos: `/apps`, `/help`, `/leaderboard`

## 📝 Comandos Disponibles

| Comando | Descripción |
|---------|-------------|
| `/start` | Registro o perfil del usuario |
| `/apps` | Lista de apps disponibles |
| `/bug` | Reportar un bug (con botones interactivos) |
| `/feature` | Sugerir una feature (con botones interactivos) |
| `/stats` | Estadísticas personales del tester |
| `/leaderboard` | Top 10 beta testers |
| `/help` | Ayuda y lista de comandos |

## 🔧 Funciones Administrativas

### Enviar Notificación a Todos los Testers

Desde PHP, puedes usar:

```php
require_once 'telegram_bot.php';

$message = "🚀 *Nueva App Disponible!*\n\n";
$message .= "Acabamos de publicar *Nombre de la App*.\n\n";
$message .= "Úsala y reporta bugs para ganar puntos!\n\n";
$message .= SITE_URL . "/webapps";

notifyAllTesters($message);
```

### Script de Notificación Manual

Crea `notify_testers.php`:

```php
<?php
require_once 'telegram_bot.php';

$message = $argv[1] ?? "Test message";
notifyAllTesters($message);
echo "Notificación enviada a todos los testers activos\n";
```

Uso:
```bash
php notify_testers.php "Tu mensaje aquí"
```

## 🐛 Debugging

### Ver Logs

```bash
tail -f logs/telegram_bot.log
```

### Verificar Errores Comunes

**El bot no responde:**
- Verifica que el webhook esté configurado: `getWebhookInfo`
- Revisa los logs del servidor
- Asegúrate de que el archivo tenga permisos de ejecución

**Errores de base de datos:**
- Verifica las credenciales en `config.php`
- Asegúrate de que `telegram_id` existe en `beta_testers`

**Rate limiting:**
- Telegram limita a 30 mensajes por segundo
- El bot tiene un delay de 100ms entre mensajes masivos

## 🔐 Seguridad

1. **Token del Bot:**
   - NUNCA compartas tu token
   - Usa variables de entorno en producción
   - Si se filtra, revócalo con @BotFather (`/revoke`)

2. **Validación:**
   - El bot valida que los usuarios estén registrados antes de permitir acciones
   - Solo beta testers activos pueden usar comandos avanzados

3. **HTTPS:**
   - Telegram requiere HTTPS para webhooks
   - Asegúrate de tener un certificado SSL válido

## 📊 Monitoreo

### Ver Estadísticas de Uso

```sql
-- Testers con Telegram vinculado
SELECT COUNT(*) as total_telegram_users
FROM beta_testers
WHERE telegram_id IS NOT NULL AND status = 'active';

-- Feedback enviado desde Telegram (futura implementación)
SELECT COUNT(*) as telegram_feedback
FROM feedback_reports
WHERE source = 'telegram';
```

## 🔄 Actualizaciones

Para actualizar el bot:

1. Edita `telegram_bot.php`
2. Haz commit y push
3. El webhook se actualiza automáticamente

No necesitas reconfigurar el webhook a menos que cambies la URL.

## ❓ Preguntas Frecuentes

**¿Puedo tener múltiples webhooks?**
No, solo uno por bot. Si necesitas desarrollo local, usa `ngrok`.

**¿Cómo pruebo localmente?**
Usa ngrok para crear un túnel HTTPS:
```bash
ngrok http 80
# Usa la URL https de ngrok para setWebhook
```

**¿El bot funciona 24/7?**
Sí, mientras tu servidor esté funcionando. Telegram guarda updates por 24 horas si el servidor cae.

**¿Cuántos usuarios soporta?**
Ilimitados. Telegram maneja la escala.

## 📚 Recursos

- [Telegram Bot API](https://core.telegram.org/bots/api)
- [BotFather Commands](https://core.telegram.org/bots#6-botfather)
- [Webhook Guide](https://core.telegram.org/bots/webhooks)

## 🆘 Soporte

Si tienes problemas:
1. Revisa los logs
2. Verifica el webhook info
3. Contacta: [email de soporte]
