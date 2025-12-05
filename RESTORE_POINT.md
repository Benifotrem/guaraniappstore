# 🔖 PUNTO DE RESTAURACIÓN SEGURO

## 📍 Información

- **Tag:** stable-beta-testers-complete-v2.0
- **Estado:** Todo funcionando
- **Funcionalidades:** Beta Testers + Blog + IA + Notificaciones

## 🚨 RESTAURAR SI FALLA

### Opción Rápida:
git checkout stable-beta-testers-complete-v2.0
git checkout -b restauracion-emergencia
git push -u origin restauracion-emergencia

### Opción Reset (CUIDADO):
git reset --hard stable-beta-testers-complete-v2.0
git push origin main --force

## ✅ Verificar:
1. curl -I https://guaraniappstore.com
2. curl -I https://guaraniappstore.com/beta
3. grep SITE_URL public_html/config.php
