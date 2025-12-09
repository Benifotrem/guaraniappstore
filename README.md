# 🇵🇾 Guarani App Store

Plataforma web para showcase de aplicaciones en fase Beta y producción, con sistema de Blog, Beta Testers y Panel de Administración.

![Version](https://img.shields.io/badge/version-2.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.1+-purple.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

> 🎯 **Portfolio Project** - Plataforma completa desarrollada para demostrar dominio del stack PHP/MySQL/JavaScript con integraciones avanzadas, arquitectura MVC limpia y dashboard analytics profesional con Chart.js.

## 🎨 Portfolio Showcase

### Live Demo
🔗 **[https://guaraniappstore.com](https://guaraniappstore.com)**

### Key Highlights para Clientes Potenciales

**📊 Dashboard Analytics Avanzado**
- Visualizaciones interactivas con Chart.js 4.4.0
- Métricas en tiempo real con comparación de períodos
- 4 tipos de gráficos: líneas, barras, dona y barras horizontales
- CTR (Click-Through Rate) calculado automáticamente
- Filtros por período (7, 30, 90 días)

**💬 Sistema de Feedback Completo**
- Formulario widget embebido en páginas públicas
- Panel admin con filtros avanzados (tipo, estado, webapp)
- Estadísticas de feedback por tipo
- Sistema de estados (new, reviewing, accepted, implemented, rejected)
- Integración con beta testers para tracking de contribuciones

**⚡ Programa Beta Testers Gamificado**
- Sistema de niveles automático (Bronze → Silver → Gold → Platinum)
- Contador de bugs reportados y sugerencias aceptadas
- Dashboard personalizado con progreso
- Integración Telegram Bot para notificaciones
- Leaderboard público con ranking

**🎨 Diseño Multicultural**
- Sistema de colores Guaraní (verde oliva) con CSS Variables
- FAQ con expresiones en guaraní y español coloquial
- Responsive design con móviles-first approach
- Dark mode ready con CSS custom properties

**🔧 Stack Técnico Demostrado**
- **Backend**: PHP 8.2+ con arquitectura MVC limpia
- **Database**: MySQL con queries optimizadas y prepared statements
- **Frontend**: Vanilla JavaScript, Chart.js, CSS Grid/Flexbox
- **APIs**: Brevo (emails transaccionales), Telegram Bot API
- **Security**: PDO prepared statements, CSRF tokens, password hashing
- **Architecture**: Singleton pattern, Router custom, MVC separation

### Screenshots Disponibles

Ver [`docs/SCREENSHOTS.md`](docs/SCREENSHOTS.md) para capturas profesionales del admin panel, analytics dashboard, sistema de feedback y páginas públicas.

### Casos de Uso Reales

- ✅ Gestión de comunidad beta testers con gamificación
- ✅ Analytics dashboard para toma de decisiones basada en datos
- ✅ Sistema de feedback para product development
- ✅ Blog para content marketing y SEO
- ✅ Sistema de suscripciones con email automation

### Documentación Técnica Completa

Ver [`PORTFOLIO.md`](PORTFOLIO.md) para análisis técnico detallado, decisiones de arquitectura y desafíos resueltos.

---

## ✨ Características Principales

### 🚀 Programa Beta Tester
- Registro con formulario completo
- Dashboard personal con estadísticas
- Sistema de niveles (Bronze, Silver, Gold, Platinum)
- Leaderboard con ranking de contribuciones
- Bot de Telegram integrado

### 📧 Sistema de Notificaciones
- Emails automáticos (bienvenida, activación, cambio nivel)
- Notificaciones Telegram
- Logs completos de notificaciones
- Templates HTML responsive

### 👥 Panel de Administración
- Gestión de Beta Testers con filtros
- Aprobación manual de cuentas
- Cambio de niveles de contribución
- Estadísticas globales en tiempo real
- Gestión de suscriptores del blog

### 🔐 Gestión de Sesiones
- Login con token único
- "Recordar sesión" por 30 días
- Recuperación de token por email
- Logout seguro con anti-cache
- Sistema de baja/cancelar cuenta

## 🛠️ Stack Tecnológico

- **Backend:** PHP 8.1+, MySQL 8.0+, PDO
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **APIs:** Brevo (emails), Telegram Bot API
- **Arquitectura:** MVC, Router Custom, Singleton Pattern

## 📦 Requisitos

- PHP >= 8.1
- MySQL >= 8.0
- Apache/Nginx con mod_rewrite
- Composer
- SSL Certificate
- Cuenta Brevo (emails)
- Telegram Bot Token (opcional)

## 🚀 Instalación Rápida
```bash
# Clonar repositorio
git clone https://github.com/Benifotrem/guaraniappstore.git
cd guaraniappstore

# Instalar dependencias
composer install

# Configurar base de datos
mysql -u user -p database < database/schema.sql

# Configurar variables de entorno
cp public_html/config.php.example public_html/config.php
# Editar config.php con tus credenciales

# Configurar permisos
chmod -R 755 public_html
chmod -R 777 logs uploads
```

Ver [documentación completa de instalación](docs/INSTALL.md)

## 📁 Estructura del Proyecto
```
guaraniappstore/
├── includes/
│   ├── classes/         # Clases principales
│   ├── controllers/     # Controladores MVC
│   ├── helpers/         # Funciones auxiliares
│   └── views/          # Vistas HTML
├── public_html/
│   ├── assets/         # CSS, JS, imágenes
│   ├── config.php      # Configuración
│   └── index.php       # Entry point
├── database/           # Scripts SQL
├── telegram-bot/       # Bot de Telegram
└── docs/              # Documentación
```

## 🗺️ Roadmap

### Fase 1: Sistema Base ✅
- Sistema de blog
- Suscripciones por email
- Panel admin básico

### Fase 2: Automatización ✅
- Panel Admin Beta Testers
- Sistema de Notificaciones
- Mejoras de UX

### Fase 3: En Progreso 🚧
- Sistema de reportes de bugs
- Dashboard de métricas avanzadas
- API REST

## 📝 Changelog

Ver [CHANGELOG.md](CHANGELOG.md) para historial completo de cambios.

## 🤝 Contribuir

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea tu rama (`git checkout -b feature/AmazingFeature`)
3. Commit (`git commit -m 'Add AmazingFeature'`)
4. Push (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo Licencia MIT. Ver [LICENSE](LICENSE)

## 👤 Autor

**César Ruzafa Alberola**
- GitHub: [@Benifotrem](https://github.com/Benifotrem)
- Email: cesarruzafa@gmail.com
- Telegram: @NodoDexParaguay

---

**Desarrollado con ♥ en Paraguay 🇵🇾**
