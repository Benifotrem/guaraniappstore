# Changelog

Todos los cambios notables de este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Versionado Semántico](https://semver.org/lang/es/).

## [2.0.0] - 2025-12-04

### ✨ Fase 2: Automatización y Panel Admin - COMPLETO

#### Sprint 2.3: Mejoras de UX
##### Añadido
- Sistema de logout con botón en dashboard
- Checkbox "Recordar sesión por 30 días" con cookies seguras
- Sistema de recuperación de token por email
- Página de editar perfil (nombre y telegram username)
- Sistema de baja/cancelar cuenta con confirmación
- Headers anti-cache para prevenir sesiones fantasma
- Mensajes de confirmación para todas las acciones

##### Mejorado
- Seguridad de sesiones con regeneración de ID
- Validación de formatos en formularios
- UX de formularios con estados disabled/enabled
- Diseño de páginas de gestión de cuenta

#### Sprint 2.2: Sistema de Notificaciones Automáticas
##### Añadido
- Tabla `notification_logs` para tracking de notificaciones
- Sistema de logs completo (tipo, canal, status, errores)
- Email automático de activación de cuenta con diseño HTML
- Email de cambio de nivel con badges y stats
- Notificación Telegram de cambio de nivel
- Notificación admin por email de nuevos registros
- Archivo `includes/helpers/notifications.php` con funciones centralizadas
- Constantes de Telegram Bot y Admin Email en config

##### Mejorado
- Templates de email con diseño HTML responsive
- Integración de notificaciones en controllers existentes
- Logging de todas las notificaciones enviadas

#### Sprint 2.1: Panel Admin de Beta Testers
##### Añadido
- Ruta `/admin/beta-testers` con vista principal
- Controller `admin_beta_testers.php` con filtros y estadísticas
- Vista completa con tabla, cards de stats y filtros
- Funcionalidad de aprobar/activar cuentas pending → active
- Funcionalidad de cambiar niveles manualmente
- Filtros por estado (pending/active/inactive)
- Filtros por nivel de contribución (bronze/silver/gold/platinum)
- Búsqueda por nombre, email o telegram username
- Estadísticas globales (total, pending, active, bugs, sugerencias)
- Campo `telegram_username` en formulario de registro
- Menú lateral con item "🚀 Beta Testers"

##### Archivos Nuevos
- `includes/controllers/admin_beta_testers.php`
- `includes/controllers/admin_beta_testers_approve.php`
- `includes/controllers/admin_beta_testers_change_level.php`
- `includes/controllers/beta_logout.php`
- `includes/controllers/beta_recover_token.php`
- `includes/controllers/beta_edit_profile.php`
- `includes/controllers/beta_unsubscribe.php`
- `includes/views/admin/beta-testers/list.php`
- `includes/views/beta/recover-token.php`
- `includes/views/beta/edit-profile.php`
- `includes/views/beta/unsubscribe.php`
- `includes/helpers/notifications.php`

##### Modificado
- `includes/views/admin/layout/header.php` - Menú actualizado
- `includes/views/beta/join.php` - Campo telegram añadido
- `includes/controllers/beta_register.php` - Procesar telegram_username
- `includes/controllers/beta_login_process.php` - Cookie remember me
- `includes/controllers/beta.php` - Verificación de cookie
- `includes/controllers/beta_dashboard.php` - Anti-cache headers
- `includes/views/beta/dashboard.php` - Botones de perfil y logout
- `includes/views/beta/login.php` - Checkbox remember y links
- `public_html/config.php` - 8 rutas nuevas

##### Base de Datos
- Tabla `notification_logs` creada
- Columnas `telegram_username` y `telegram_id` en `beta_testers`

## [1.0.0] - 2025-12-03

### ✨ Fase 1: Sistema Base - COMPLETO

#### Añadido
- Sistema de landing page con showcase de aplicaciones
- Sistema completo de Blog con categorías y tags
- Suscripción por email con verificación
- Panel de administración básico
- Programa de Beta Testers con registro
- Dashboard personal para beta testers
- Sistema de niveles (Bronze, Silver, Gold, Platinum)
- Leaderboard con ranking de contribuciones
- Bot de Telegram (@guaraniappstore_bot)
- Integración de Brevo para emails transaccionales
- Sistema de autenticación con tokens
- Gestión de suscriptores del blog
- FAQ para beta testers
- Footer responsive en todas las páginas

#### Archivos Principales
- Arquitectura MVC implementada
- Router custom con sistema de rutas limpio
- Clase Database con patrón Singleton
- Helper functions para utilidades comunes
- Sistema de logging de errores

#### Base de Datos
- Tabla `blog_subscribers` con verificación
- Tabla `beta_testers` con sistema de niveles
- Tabla `admin_users` para panel admin
- Esquema completo documentado

---

## Tipos de Cambios

- `Añadido` para funcionalidades nuevas
- `Modificado` para cambios en funcionalidades existentes
- `Obsoleto` para funcionalidades que serán removidas
- `Eliminado` para funcionalidades removidas
- `Corregido` para corrección de bugs
- `Seguridad` para vulnerabilidades corregidas
