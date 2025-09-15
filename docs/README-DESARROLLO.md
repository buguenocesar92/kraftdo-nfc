# 🚀 KraftDo NFC - Flujo de Desarrollo y Deployment

> **Guía completa del flujo de desarrollo profesional para el equipo KraftDo NFC**

## 📋 **Índice**

- [🔄 Flujo Completo](#-flujo-completo)
- [🛠️ Setup Inicial](#️-setup-inicial)
- [💻 Desarrollo Local](#-desarrollo-local)
- [📝 Pull Requests](#-pull-requests)
- [🤖 CI/CD Automático](#-cicd-automático)
- [🚀 Deployment a VPS](#-deployment-a-vps)
- [📊 Monitoreo](#-monitoreo)
- [🔄 Rollback](#-rollback)
- [⚡ Comandos Rápidos](#-comandos-rápidos)

---

## 🔄 **Flujo Completo**

```mermaid
graph TD
    A[Desarrollo Local] --> B[make quality-check]
    B --> C[git commit + push]
    C --> D[GitHub Actions CI/CD]
    D --> E{Branch?}
    E -->|develop| F[Deploy a Staging]
    E -->|main| G[Deploy a Producción]
    F --> H[staging.kraftdo.cl]
    G --> I[kraftdo.cl]
    I --> J[Monitoreo & Health Checks]
    J --> K{¿Todo OK?}
    K -->|No| L[Rollback Automático]
    K -->|Sí| M[✅ Deploy Exitoso]
```

### **📊 Diagrama de Arquitectura:**

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   LOCAL DEV     │────│   GITHUB        │────│      VPS        │
│                 │    │                 │    │                 │
│ • make dev      │    │ • GitHub Actions│    │ • Docker Prod   │
│ • make test     │    │ • CodeQL        │    │ • Health Checks │
│ • make quality  │    │ • Dependabot    │    │ • Auto Rollback │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

---

## 🛠️ **Setup Inicial**

### **Prerequisites:**
- Docker 20.10+ y Docker Compose v2+
- Git configurado
- Make (opcional pero recomendado)
- Acceso al repositorio GitHub privado

### **Setup Local (Una sola vez):**

```bash
# 1. Clonar repositorio
git clone https://github.com/kraftdo/kraftdo-nfc.git
cd kraftdo-nfc

# 2. Instalación automática completa
make install

# 3. Verificar que todo funciona
make health
make test-full
```

### **Variables de Entorno:**

```bash
# El archivo .env se crea automáticamente desde .env.example
# Para desarrollo, las configuraciones por defecto funcionan
```

---

## 💻 **Desarrollo Local**

### **🔄 Workflow Diario:**

```bash
# 1. Actualizar main
git checkout main
git pull origin main

# 2. Crear feature branch
git checkout -b feature/nueva-funcionalidad

# 3. Iniciar desarrollo
make dev                    # Levantar contenedores

# 4. Durante desarrollo
make logs                   # Ver logs en tiempo real
make shell                  # Acceder al contenedor
make artisan-migrate       # Ejecutar migraciones
make npm-run dev           # Build assets en desarrollo

# 5. Testing continuo
make test-full             # Ejecutar todos los tests
make quality-check         # Verificar calidad de código

# 6. Antes del commit
make fix-all              # Arreglar problemas automáticamente
make ci-test-full         # Tests como en CI
```

### **🧪 Comandos de Testing:**

```bash
# Testing básico
make test                  # Tests rápidos
make test-full            # Suite completa de tests
make ci-test-unit         # Solo unit tests (90% coverage)
make ci-test-feature      # Solo feature tests

# Quality & Security
make quality-check        # Verificación completa de calidad
make ci-quality-full      # Quality checks como en CI
make ci-security-check    # Auditoría de seguridad

# Usando composer directamente
composer test-coverage    # Tests con coverage detallado  
composer quality          # All quality checks
composer security         # Security audit
composer cs-fix           # Fix code style
```

### **🔧 Comandos de Desarrollo:**

```bash
# Gestión de contenedores
make up                   # Iniciar (detecta mejor configuración)
make down                 # Parar todos los contenedores
make restart              # Reinicio inteligente
make clean-all           # Limpieza completa

# Cache y optimización
make cache-clear         # Limpiar caches
make cache-all          # Cachear todo (config, routes, views)
make optimize           # Optimización completa Laravel/Filament

# Debugging
make debug              # Información completa de debug
make monitor            # Monitoreo en tiempo real
make performance        # Análisis de performance
```

---

## 📝 **Pull Requests**

### **🎯 Preparación del PR:**

```bash
# 1. Verificar calidad antes del PR
make quality-check
make ci-test-full
make ci-security-check

# 2. Commit con formato profesional
git add .
git commit -m "feat(nfc): add real-time analytics dashboard

- Implement live metrics visualization with charts
- Add caching layer for better performance  
- Include comprehensive tests with 95% coverage
- Add security validations for user access

Closes #123"

# 3. Push al repositorio
git push origin feature/nueva-funcionalidad
```

### **📋 Template de PR:**

Al crear el PR en GitHub, usa el template automático que incluye:

- ✅ **Descripción** clara del cambio
- ✅ **Tipo de cambio** (feature, fix, refactor, etc.)
- ✅ **Testing realizado** con detalles
- ✅ **Screenshots** si aplica
- ✅ **Checklist completo** de desarrollo
- ✅ **Impacto en performance**
- ✅ **Breaking changes** si los hay

---

## 🤖 **CI/CD Automático**

### **🔥 Para `develop` Branch (Staging):**

```yaml
Trigger: Push a develop
├── 🧪 Tests (80% coverage mínimo)
├── 🔍 Quality & Security Checks  
├── 🏗️ Build Docker Image (develop-latest)
├── 🚀 Deploy Automático a staging.kraftdo.cl
└── 📊 Notificaciones de resultado
```

**Comandos ejecutados automáticamente:**
```bash
make ci-install           # Dependencias optimizadas
make ci-setup            # Setup entorno testing  
make ci-build-assets     # Build frontend assets
make ci-test-unit-dev    # Unit tests (80% threshold)
make ci-test-feature     # Feature tests
make ci-quality-full     # Quality + Security checks
```

### **🎯 Para `main` Branch (Producción):**

```yaml
Trigger: Push a main
├── 🧪 Tests (90% coverage mínimo)
├── 🔍 Quality & Security Checks Estrictos
├── 🏗️ Build Multi-Platform Docker Image  
├── 🚀 Deploy Automático a kraftdo.cl
├── 🏥 Health Checks Post-Deploy
└── 📈 Métricas y Monitoreo
```

**Comandos ejecutados automáticamente:**
```bash
make ci-install           # Dependencias production-ready
make ci-setup            # Setup entorno testing
make ci-build-assets     # Assets optimizados  
make ci-test-full        # Tests completos (90% coverage)
make ci-quality-full     # Análisis exhaustivo
# + Deploy con ./deploy-prod.sh integrado
```

### **🛡️ Security Scanning Automático:**

```yaml
CodeQL Analysis (Semanal):
├── 🔍 PHP Static Analysis
├── 🔍 JavaScript Security Scan
├── 🔒 Dependency Vulnerability Check
└── 📊 SARIF Results en Security Tab
```

---

## 🚀 **Deployment a VPS**

### **⚙️ Setup Inicial VPS (Una sola vez):**

```bash
# En tu VPS como usuario deploy
cd /opt
git clone https://github.com/kraftdo/kraftdo-nfc.git kraftdo-nfc
cd kraftdo-nfc

# Configurar variables de producción
cp .env.prod.example .env.prod
nano .env.prod  # ⚠️ COMPLETAR TODAS las variables requeridas

# Permisos
chmod +x deploy-prod.sh
chmod +x docker/entrypoint-simple.sh

# Variables críticas requeridas:
# APP_KEY, APP_URL, SESSION_DOMAIN, REDIS_PASSWORD
# DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
```

### **🎯 Variables GitHub Secrets (Configurar una vez):**

```bash
# En GitHub Repository > Settings > Secrets:
VPS_HOST=tu-servidor.kraftdo.cl
VPS_USERNAME=deploy  
VPS_SSH_KEY=-----BEGIN OPENSSH PRIVATE KEY-----...
VPS_SSH_PASSPHRASE=tu-passphrase-si-aplica
VPS_PORT=22
PROJECT_PATH=/opt/kraftdo-nfc
```

### **🚀 Deployment Automático:**

```bash
# Al hacer push a main, automáticamente:
1. 📦 Build imagen Docker multi-platform
2. 📤 Push imagen a GitHub Container Registry
3. 🔄 SSH al VPS y ejecutar deployment
4. 📥 Pull imagen desde GHCR  
5. 💾 Backup automático pre-deployment
6. 🔄 Update docker-compose con nueva imagen
7. 🚀 Deploy con health checks
8. ⚡ Optimizaciones Laravel automáticas
9. 🏥 Verificación de deployment
10. ✅ Confirmación de éxito o rollback
```

### **🛠️ Deployment Manual (si necesitas control):**

```bash
# En el VPS:
ssh deploy@tu-servidor.kraftdo.cl
cd /opt/kraftdo-nfc

# Opciones de deployment:
./deploy-prod.sh              # Script completo con confirmaciones
make deploy-prod              # Usando Makefile  
make deploy-prod-quick        # Deploy rápido sin confirmaciones

# Verificación:
make deploy-prod-check        # Verificar estado
make deploy-prod-logs         # Ver logs
make health                   # Health check completo
```

---

## 📊 **Monitoreo Post-Deploy**

### **🏥 Health Checks:**

```bash
# Verificación completa del sistema
make health
# Output:
# ✓ App: 200 (0.123s)
# ✓ Database: Connected  
# ✓ Redis: Connected
# ✓ Queue: Processing
# ✓ Storage: Writable

# Monitoreo en tiempo real
make monitor
# Muestra CPU, RAM, containers status

# Performance analysis
make performance
# Memory usage, Octane status, Cache hit ratio, Queue status
```

### **📋 Logs y Debugging:**

```bash
# Logs específicos
make deploy-prod-logs         # Logs de producción
make logs                     # Logs del entorno actual

# Acceso al sistema
make deploy-prod-shell        # Shell de producción
make shell                    # Shell del entorno actual

# Debugging avanzado
make debug                    # Info completa del sistema
```

### **📈 Métricas Clave:**

```bash
# Performance targets:
- Response time: < 200ms promedio
- Memory usage: < 512MB por container
- Redis hit ratio: > 95%
- Queue processing: < 30s promedio
- Uptime: 99.9%
```

---

## 🔄 **Rollback en Caso de Problemas**

### **🚨 Rollback Automático:**

```bash
# Si el health check falla después del deploy:
# 1. GitHub Actions detecta el fallo
# 2. Ejecuta rollback automático
# 3. Restaura la versión anterior
# 4. Verifica que el rollback funciona
# 5. Notifica del rollback
```

### **🛠️ Rollback Manual:**

```bash
# En el VPS:
make rollback
# Te muestra backups disponibles:
# backups/20250115_143022/
# backups/20250114_091545/
# Selecciona cual restaurar

# O usando el script directo:
make deploy-prod-down         # Para servicios
# Restaurar backup manualmente
# Reiniciar servicios
```

### **📊 Backups Automáticos:**

```bash
# Cada deployment crea backups automáticos:
make deploy-prod-backup       # Backup manual específico
ls backups/prod/              # Ver backups disponibles

# Estructura de backups:
# backups/prod/redis-TIMESTAMP.rdb
# backups/prod/storage-TIMESTAMP.tar.gz  
```

---

## ⚡ **Comandos Rápidos - Cheat Sheet**

### **🎯 Desarrollo:**
```bash
make install          # Setup inicial completo
make dev             # Iniciar desarrollo
make test-full       # Tests completos
make quality-check   # Verificar calidad
make fix-all         # Arreglar problemas
make health          # Verificar estado
```

### **🚀 Deployment:**
```bash
make deploy-prod           # Deploy completo
make deploy-prod-quick     # Deploy rápido  
make deploy-prod-check     # Verificar deploy
make deploy-prod-logs      # Ver logs producción
make rollback             # Rollback si hay problemas
```

### **🔧 Utilidades:**
```bash
make shell               # Acceder al contenedor
make cache-clear         # Limpiar caches
make optimize           # Optimizar aplicación
make monitor            # Monitoreo tiempo real
make clean-all          # Limpieza completa
```

### **🧪 Testing & Quality:**
```bash
make ci-test-full        # Tests como en CI
make ci-quality-full     # Quality checks completos
make ci-security-check   # Auditoría seguridad
composer quality         # All quality checks
composer security        # Security audit
```

---

## 🎯 **Workflows Específicos**

### **🔥 Hotfix de Emergencia:**
```bash
git checkout main
git pull origin main
git checkout -b hotfix/critical-security-fix
# ... hacer el fix ...
make quality-check
git commit -m "fix(security): patch critical auth vulnerability"
git push origin hotfix/critical-security-fix
# PR directo a main → Deploy automático a producción
```

### **✨ Feature Development:**
```bash
# 1. Desarrollo
git checkout -b feature/nueva-funcionalidad
make dev
# ... desarrollo ...
make test-full && make quality-check

# 2. PR a develop
git push origin feature/nueva-funcionalidad
# PR a develop → Deploy automático a staging

# 3. Testing en staging  
# Verificar en staging.kraftdo.cl

# 4. Release a producción
# PR de develop a main → Deploy automático a producción
```

### **🔄 Maintenance:**
```bash
# Actualizaciones de dependencias
make down
composer update
npm update
make test-full
# Si todo OK, commit y push

# Optimización de performance
make optimize
make performance
make benchmark
```

---

## 📞 **Soporte y Escalación**

### **🆘 En caso de problemas:**

1. **Verificar logs**: `make deploy-prod-logs` 
2. **Health check**: `make deploy-prod-check`
3. **Rollback inmediato**: `make rollback`
4. **Contactar equipo**: dev@kraftdo.cl
5. **Issue crítico**: Crear GitHub Issue con label "critical"

### **📊 Escalación:**

- **Nivel 1**: Developer individual
- **Nivel 2**: Tech Lead review  
- **Nivel 3**: CTO approval (cambios críticos)
- **Nivel 4**: CEO approval (cambios de infraestructura)

---

## 🔒 **Políticas de Seguridad**

- ✅ **Todos los commits** deben pasar quality checks
- ✅ **PRs requieren** code review obligatorio
- ✅ **Main branch** está protegido (no push directo)
- ✅ **Secrets** nunca en el código  
- ✅ **Dependencies** monitoreadas por Dependabot
- ✅ **Security scans** automáticos semanales
- ✅ **Access logs** auditados regularmente

---

**🚀 Desarrollado con ❤️ por el equipo KraftDo - Confidencial y Propietario**

> **📧 Contacto**: dev@kraftdo.cl | **🌐 Web**: https://kraftdo.cl