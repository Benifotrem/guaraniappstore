# 🔖 PUNTO DE RESTAURACIÓN SEGURO

## 📍 Información del Punto de Restauración

- **Tag:** `stable-beta-testers-complete-v2.0`
- **Estado:** Todo funcionando correctamente
- **Funcionalidades:** Beta Testers + Blog + IA + Notificaciones

## 🚨 CÓMO RESTAURAR SI ALGO FALLA

### Opción Rápida (Recomendada):

```bash
# Crear branch desde el punto seguro
git checkout stable-beta-testers-complete-v2.0
git checkout -b restauracion-emergencia
git push -u origin restauracion-emergencia
# Luego crear PR en GitHub para mergear a main
```

### Opción Reset (CUIDADO - Borra commits posteriores):

```bash
# Backup primero
git tag backup-antes-restaurar-$(date +%s)

# Reset a punto seguro
git reset --hard stable-beta-testers-complete-v2.0
git push origin main --force
```

## ✅ Verificar después de restaurar:

1. Sitio carga: `curl -I https://guaraniappstore.com`
2. Beta Testers: `curl -I https://guaraniappstore.com/beta`
3. Config correcto: `grep SITE_URL public_html/config.php`

---
Creado: 2025-12-05 | Por: César Ruzafa
