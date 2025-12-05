# 🚀 Guarani App Store - Plataforma Completa

Sistema completo de showcase para aplicaciones web con panel de administración y blog automatizado con IA.

## ✨ Características Principales

### 🎨 Landing Page
- **Diseño Guarani** preservado (colores verde Paraguay HSL 84, 40%, 35%)
- Hero section, features, testimonials, CTA, footer responsivo
- Showcase de webapps destacadas
- Últimos artículos del blog
- Sistema de suscripción al blog
- WhatsApp float button
- 100% responsive

### 📱 Gestión de Webapps
- CRUD completo desde panel admin
- Upload de logo, cover image, screenshots
- Gestión de categorías, tags, tech stack
- Control de estado (draft/published/archived)
- Marcar como destacada para home
- Orden personalizado de visualización
- Tracking de vistas y clics
- Página de detalle con diseño atractivo

### 📝 Blog Automatizado con IA
- **Generación automática cada 2 días**
- Integración con **OpenRouter + DeepSeek R1**
- Análisis de tendencias Google Trends (simulado)
- Preferencia por apps publicadas en el sitio
- Firma como **César Ruzafa**
- **NO menciona que fue generado por IA**
- 800-1200 palabras por artículo
- SEO optimizado automático
- También permite crear artículos manuales

### 🔐 Panel de Administración
- Login seguro con protección anti fuerza bruta
- **2FA con Google Authenticator/Authy** (TOTP)
- Dashboard con estadísticas en tiempo real
- Gestión completa de webapps (crear, editar, eliminar)
- Gestión completa de blog (crear, editar, eliminar, generar con IA)
- Vista de suscriptores con métricas
- Configuración del sitio y API keys
- Diseño responsivo con tema Guarani

### 👥 Sistema de Suscriptores
- Suscripción desde landing page
- Verificación por email (token)
- Unsubscribe con un clic
- Estados: pending, active, unsubscribed
- Dashboard de métricas

### 📊 Analíticas
- Vistas de webapps
- Clics en webapps
- Vistas de artículos
- Tracking automático de todas las interacciones

## 🛠️ Stack Tecnológico

- **Frontend**: HTML5, CSS3, JavaScript vanilla (ES6) - Sin frameworks
- **Backend**: PHP 7.4+ puro (sin frameworks)
- **Base de Datos**: MySQL 5.7+ / MariaDB 10.2+
- **API Externa**: OpenRouter (DeepSeek R1) accedido vía REST API
- **Autenticación**: 2FA con TOTP (RFC 6238)
- **Servidor**: Apache con mod_rewrite
- **Compatible con**: Hosting compartido (Hostinger, cPanel, etc)

## 📋 Requisitos del Sistema

- **PHP**: 7.4 o superior
- **MySQL**: 5.7 o superior / MariaDB 10.2+
- **Apache**: con mod_rewrite habilitado
- **Extensiones PHP**:
  - PDO y PDO_MySQL
  - mbstring
  - json
  - curl
  - openssl

## 🚀 Instalación en Hostinger

### Paso 1: Crear Base de Datos

1. Accede a tu panel de Hostinger (hPanel)
2. Ve a **Bases de Datos MySQL**
3. Crea una nueva base de datos:
   - Nombre: `guaraniappstore_db` (o tu preferencia)
   - Usuario: Crea un usuario nuevo
   - Contraseña: Genera una segura

### Paso 2: Importar Schema

1. Accede a **phpMyAdmin**
2. Selecciona tu base de datos
3. Ve a la pestaña **Importar**
4. Selecciona el archivo `database/schema.sql`
5. Haz clic en **Continuar**

### Paso 3: Subir Archivos

#### Opción A: Administrador de Archivos

1. Sube todo el contenido de `public_html/` a tu carpeta pública
2. Crea carpeta `includes/` fuera de public_html
3. Sube contenido de `includes/` a esa carpeta
4. Crea carpeta `database/` y sube el schema
5. Crea carpeta `cron/` y sube el script

#### Opción B: FTP/SFTP

1. Conecta vía FTP (FileZilla o similar)
2. Sube todos los archivos manteniendo la estructura

### Paso 4: Configurar Variables de Entorno

1. Copia `.env.example` a `.env` en `public_html/`
2. Edita `.env` con tus credenciales:

```env
DB_HOST=localhost
DB_NAME=guaraniappstore_db
DB_USER=tu_usuario
DB_PASS=tu_password

SECURITY_SALT=GENERA_CLAVE_UNICA_64_CARACTERES
# Genera en: https://randomkeygen.com/

OPENROUTER_API_KEY=sk-or-v1-tu-api-key
SITE_URL=https://tudominio.com
DEBUG_MODE=false
```

3. También edita `public_html/config.php` con los mismos datos

### Paso 5: Configurar Permisos

```bash
chmod 755 public_html/uploads/
chmod 755 logs/
```

### Paso 6: Configurar Cron Job (Blog Automático)

1. En hPanel, ve a **Avanzado** → **Cron Jobs**
2. Agrega un nuevo cron job:

```bash
# Comando
php /home/tu_usuario/cron/generate_blog_post.php

# Frecuencia: Cada 2 días a las 10:00 AM
0 10 */2 * *
```

### Paso 7: Primer Acceso

1. Visita: `https://tudominio.com`
2. Panel admin: `https://tudominio.com/admin`

**Credenciales por defecto**:
- Usuario: `admin`
- Contraseña: `Admin123!`

⚠️ **IMPORTANTE**: Cambia la contraseña inmediatamente.

### Paso 8: Configuración Inicial

1. Ve a **Configuración** en el panel admin
2. Configura:
   - API Key de OpenRouter (obtén una en https://openrouter.ai/)
   - Información de contacto (email, WhatsApp)
   - Activa generación automática del blog
3. Ve a **Mi Perfil** → **Activar 2FA** (recomendado)

## 🔒 Activar 2FA

1. Descarga **Google Authenticator** o **Authy**
2. En el panel: **Mi Perfil** → **Seguridad** → **Activar 2FA**
3. Escanea el código QR con tu app
4. Ingresa el código de 6 dígitos para confirmar

## 📝 Uso del Sistema

### Publicar una Webapp

1. **Admin** → **Webapps** → **Nueva Webapp**
2. Completa:
   - Título (se genera slug automático)
   - Descripción corta y completa
   - URL de la aplicación
   - Logo y cover image (URLs)
   - Categoría, tags, tech stack
3. Marca como **Destacada** para que aparezca en home
4. Estado: **Publicado**

### Generar Artículo con IA

1. **Admin** → **Blog** → **Generar con IA**
2. Haz clic en **⚡ Generar Artículo Ahora**
3. Espera 30-60 segundos
4. El artículo se crea automáticamente
5. Puedes editarlo antes de publicar

### Crear Artículo Manual

1. **Admin** → **Blog** → **Nuevo Artículo**
2. Escribe el contenido en HTML
3. Agrega imagen destacada, categoría, tags
4. Publica

### Ver Estadísticas

- **Dashboard**: Vista general de todo
- **Webapps**: Vistas y clics por app
- **Blog**: Vistas por artículo
- **Suscriptores**: Total, activos, pendientes

## 🗂️ Estructura del Proyecto

```
guaraniappstore/
├── database/
│   └── schema.sql              # Base de datos completa
├── includes/
│   ├── classes/
│   │   ├── Auth.php            # Autenticación + 2FA
│   │   ├── Database.php        # PDO wrapper
│   │   └── BlogGenerator.php  # Generador IA
│   ├── controllers/            # 40+ controladores MVC
│   ├── views/
│   │   ├── landing/            # Vistas públicas
│   │   ├── public/             # Webapps, blog
│   │   └── admin/              # Panel admin
│   └── helpers/
│       └── functions.php       # 50+ funciones
├── cron/
│   └── generate_blog_post.php  # Cron blog automático
├── public_html/
│   ├── index.php               # Entry point
│   ├── config.php              # Configuración
│   ├── .htaccess               # Apache config
│   ├── assets/
│   │   ├── css/                # Estilos Guarani
│   │   └── js/                 # JavaScript
│   └── uploads/                # Archivos subidos
├── logs/                       # Logs del sistema
└── README.md
```

## 🎯 Características Técnicas

### Seguridad
- Prepared statements (PDO) - previene SQL injection
- CSRF protection en todos los formularios
- XSS protection (htmlspecialchars)
- Password hashing con bcrypt
- Rate limiting en login (5 intentos)
- 2FA con TOTP
- Sesiones seguras con tokens

### Performance
- CSS/JS optimizados
- Lazy loading de imágenes
- Cache de archivos estáticos (.htaccess)
- Queries optimizadas con índices
- Procedimientos almacenados

### SEO
- URLs limpias (mod_rewrite)
- Meta tags automáticos
- Sitemap friendly
- Schema markup ready
- Open Graph tags

## 🐛 Solución de Problemas

### Error 500

1. Verifica permisos de carpetas
2. Revisa `logs/error.log`
3. Verifica PHP >= 7.4
4. Comprueba extensiones PHP

### Error de Base de Datos

1. Verifica credenciales en `config.php`
2. Asegúrate de que el schema se importó correctamente
3. Verifica permisos del usuario MySQL

### Blog no se genera

1. Verifica API Key de OpenRouter en **Configuración**
2. Revisa `logs/error.log`
3. Verifica que el cron job esté configurado
4. Prueba manualmente: `php cron/generate_blog_post.php`

### 2FA no funciona

1. Verifica que la hora del servidor sea correcta
2. Asegúrate de usar el código actual (se renueva cada 30 seg)
3. Verifica extensión OpenSSL en PHP

## 📊 Analíticas y Métricas

El sistema registra automáticamente:
- ✅ Vistas de cada webapp
- ✅ Clics en enlaces de webapps
- ✅ Vistas de cada artículo
- ✅ IP, user agent, referrer de cada visita
- ✅ Suscripciones al blog
- ✅ Desuscripciones

Accede a las estadísticas desde el **Dashboard** del panel admin.

## 🔄 Actualización del Sistema

```bash
git pull origin claude/redesign-repository-structure-01P9PQKuSTs9D7sCEtqB5x3m
# Subir archivos actualizados vía FTP
# Ejecutar migraciones de BD si hay cambios en schema
```

## 📞 Soporte

- **Email**: admin@guaraniappstore.com
- **WhatsApp**: (+595) 992-462343

## 📄 Licencia

Propiedad de Guarani App Store - Todos los derechos reservados

## 🎯 Roadmap Futuro (Opcional)

- [ ] Upload directo de imágenes (sin URLs)
- [ ] Editor WYSIWYG para artículos
- [ ] Newsletter automático a suscriptores
- [ ] Integración real con Google Trends API
- [ ] Panel de analíticas avanzadas
- [ ] Exportar suscriptores a CSV
- [ ] Multi-idioma (Español/Guaraní)
- [ ] PWA (Progressive Web App)

---

## 🏆 Créditos

**Desarrollado en Paraguay 🇵🇾**

### Tecnologías Utilizadas

- **PHP 7.4+ Vanilla** (sin frameworks, 100% puro)
- **MySQL 5.7+** con PDO (Prepared Statements)
- **JavaScript ES6** vanilla (sin librerías ni frameworks)
- **CSS3** con variables personalizadas
- **Apache** con mod_rewrite para URLs amigables
- **OpenRouter AI** (acceso vía REST API)
- **DeepSeek R1** (modelo de IA para generación de contenido)

### Diseño

- Sistema de diseño Guarani (colores verde Paraguay)
- Responsive design mobile-first
- Animaciones CSS3
- Iconos SVG

---

**Versión**: 3.0.0 - Sistema Completo
**Última actualización**: 2025-01-18
**Estado**: ✅ 100% Funcional y Listo para Producción

