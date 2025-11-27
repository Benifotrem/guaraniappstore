#!/bin/bash

#######################################################
# Script de Despliegue - Sistema de Gestión de Suscriptores
# Guarani App Store
#######################################################

set -e  # Salir si hay algún error

echo "╔════════════════════════════════════════════════════════════╗"
echo "║   Despliegue: Sistema de Gestión de Suscriptores         ║"
echo "║   Guarani App Store                                        ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para imprimir mensajes
print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

print_error() {
    echo -e "${RED}[✗]${NC} $1"
}

# Verificar que estamos en el directorio correcto
if [ ! -f "public_html/config.php" ]; then
    print_error "No se encontró public_html/config.php"
    print_error "Por favor ejecuta este script desde el directorio raíz del proyecto"
    exit 1
fi

print_success "Directorio verificado"

# Paso 1: Verificar archivos nuevos
echo ""
print_info "Verificando archivos del sistema..."

FILES=(
    "includes/classes/BrevoMailer.php"
    "includes/controllers/api_subscribers.php"
    "includes/views/admin/subscribers.php"
    "DEPLOYMENT_SUBSCRIBER_SYSTEM.md"
)

MISSING_FILES=0

for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        print_success "$file ✓"
    else
        print_error "$file ✗ (NO ENCONTRADO)"
        MISSING_FILES=$((MISSING_FILES + 1))
    fi
done

if [ $MISSING_FILES -gt 0 ]; then
    print_error "Faltan $MISSING_FILES archivo(s). Por favor ejecuta 'git pull' primero."
    exit 1
fi

# Paso 2: Establecer permisos correctos
echo ""
print_info "Configurando permisos de archivos..."

chmod 644 includes/classes/BrevoMailer.php
chmod 644 includes/controllers/api_subscribers.php
chmod 644 includes/views/admin/subscribers.php
chmod 644 includes/controllers/admin_subscribers.php
chmod 644 includes/controllers/subscribe.php
chmod 644 includes/controllers/verify_subscription.php

print_success "Permisos configurados"

# Paso 3: Verificar configuración de PHP
echo ""
print_info "Verificando extensiones de PHP..."

php -r "echo (extension_loaded('curl') ? '${GREEN}cURL está habilitado${NC}\n' : '${RED}cURL NO está habilitado${NC}\n');"
php -r "echo (extension_loaded('pdo') ? '${GREEN}PDO está habilitado${NC}\n' : '${RED}PDO NO está habilitado${NC}\n');"
php -r "echo (extension_loaded('pdo_mysql') ? '${GREEN}PDO MySQL está habilitado${NC}\n' : '${RED}PDO MySQL NO está habilitado${NC}\n');"

# Paso 4: Verificar tabla de base de datos
echo ""
print_info "Verificando tabla de base de datos..."

TABLE_EXISTS=$(mysql -N -s -e "
    SELECT COUNT(*)
    FROM information_schema.tables
    WHERE table_schema = DATABASE()
    AND table_name = 'blog_subscribers'
" 2>/dev/null || echo "0")

if [ "$TABLE_EXISTS" -eq "1" ]; then
    print_success "Tabla blog_subscribers existe"

    # Contar suscriptores
    SUBSCRIBER_COUNT=$(mysql -N -s -e "SELECT COUNT(*) FROM blog_subscribers" 2>/dev/null || echo "0")
    print_info "Suscriptores actuales: $SUBSCRIBER_COUNT"
else
    print_warning "Tabla blog_subscribers no encontrada"
    print_info "Por favor ejecuta el schema de la base de datos"
fi

# Paso 5: Verificar configuración de Brevo
echo ""
print_info "Verificando configuración de Brevo..."

EMAIL_ENABLED=$(grep "define('EMAIL_ENABLED'" public_html/config.php | grep -o "true\|false" || echo "false")
BREVO_KEY=$(grep "define('BREVO_API_KEY'" public_html/config.php | grep -o "'[^']*'" | tail -n1 | tr -d "'")

if [ "$EMAIL_ENABLED" = "true" ]; then
    print_success "EMAIL_ENABLED está activado"
else
    print_warning "EMAIL_ENABLED está desactivado (false)"
    print_info "⚠️  Edita public_html/config.php y cambia EMAIL_ENABLED a true"
fi

if [ -n "$BREVO_KEY" ] && [ "$BREVO_KEY" != "" ]; then
    print_success "BREVO_API_KEY está configurada"
else
    print_warning "BREVO_API_KEY no está configurada"
    print_info "⚠️  Edita public_html/config.php y agrega tu API Key de Brevo"
fi

# Paso 6: Resumen de configuración
echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║                  RESUMEN DE CONFIGURACIÓN                  ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

if [ "$EMAIL_ENABLED" = "true" ] && [ -n "$BREVO_KEY" ] && [ "$BREVO_KEY" != "" ]; then
    print_success "Sistema completamente configurado ✓"
    echo ""
    print_info "El sistema de suscriptores está listo para usar"
    print_info "Panel de administración: /admin/subscribers"
else
    print_warning "Configuración incompleta"
    echo ""
    print_info "Para completar la configuración:"
    echo ""
    echo "1. Obtén tu API Key de Brevo:"
    echo "   → https://app.brevo.com/ → Settings → API Keys"
    echo ""
    echo "2. Edita la configuración:"
    echo "   → nano public_html/config.php"
    echo ""
    echo "3. Actualiza estas líneas (alrededor de la línea 66):"
    echo "   define('EMAIL_ENABLED', true);"
    echo "   define('BREVO_API_KEY', 'TU_API_KEY_AQUI');"
    echo ""
fi

# Paso 7: Información adicional
echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║                    PRÓXIMOS PASOS                          ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""
echo "1. Verifica tu email en Brevo:"
echo "   → https://app.brevo.com/ → Settings → Senders & IP"
echo ""
echo "2. Configura tu API Key (si aún no lo hiciste):"
echo "   → nano public_html/config.php"
echo ""
echo "3. Prueba el sistema:"
echo "   → Suscríbete desde el footer de la página"
echo "   → Revisa tu email de verificación"
echo "   → Accede al panel: /admin/subscribers"
echo ""
echo "4. Lee la documentación completa:"
echo "   → cat DEPLOYMENT_SUBSCRIBER_SYSTEM.md"
echo ""

# Paso 8: Test de conectividad (opcional)
echo ""
read -p "¿Quieres probar la conectividad con Brevo API? (s/n): " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Ss]$ ]]; then
    if [ -n "$BREVO_KEY" ] && [ "$BREVO_KEY" != "" ]; then
        print_info "Probando conexión con Brevo API..."

        HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" \
            -X GET "https://api.brevo.com/v3/account" \
            -H "accept: application/json" \
            -H "api-key: $BREVO_KEY")

        if [ "$HTTP_CODE" -eq "200" ]; then
            print_success "Conexión exitosa con Brevo API ✓"
        else
            print_error "Error al conectar con Brevo API (HTTP $HTTP_CODE)"
            print_info "Verifica que tu API Key sea correcta"
        fi
    else
        print_warning "No se puede probar sin BREVO_API_KEY configurada"
    fi
fi

echo ""
print_success "Despliegue completado"
echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║              ¡SISTEMA LISTO PARA USAR! 🎉                 ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""
