# 🐳 KraftDo NFC - Guía Docker Optimizada

## 📋 Estructura de Configuración

El proyecto ahora cuenta con una estructura Docker completamente configurable y optimizada:

```
📁 docker-configs/
├── 🏭 docker-compose.prod.yml     # Configuración de producción
├── 🛠️  docker-compose.dev.yml      # Configuración de desarrollo 
├── 🔄 docker-compose.hybrid.yml   # Configuración híbrida actual
├── ⚙️  docker-compose.override.yml # Overrides locales personalizados
├── 📄 .env.example               # Template completo de variables
├── 🏭 .env.production.example    # Template específico para producción
└── 🛠️  Makefile                   # Comandos simplificados
```

## 🎯 Modos de Operación

### 🔄 Híbrido (Configuración Principal - FUNCIONANDO)
```bash
# La configuración que funciona actualmente
make hybrid
# O simplemente:
make up

# O manualmente:
docker compose -f docker-compose.hybrid.yml up -d
```

**Características:**
- ✅ **PROBADA Y FUNCIONANDO** - Basada en tu setup actual
- Nginx + PHP-FPM (frontend y backend) 
- Proxy optimizado para /storage/ hacia PHP-FPM
- Base de datos externa configurada (192.168.100.20)
- Redis para cache y sesiones
- Configuración híbrida que resolvió los problemas de Livewire

### 🛠️ Desarrollo (Basado en Híbrido + herramientas dev)
```bash
# Configuración para desarrollo con herramientas adicionales
make env-setup
make dev

# O manualmente:
docker compose -f docker-compose.dev.yml up -d
```

**Características:**
- Basado en la configuración híbrida funcional
- MySQL local incluido (opcional - puede usar externo)
- MailHog para testing de emails
- Debugging y hot reload habilitado
- Variables configurables para desarrollo

### 🏭 Producción (Híbrido optimizado para servidor)
```bash
# Configuración para producción
make env-prod-setup  # Editar .env después
make prod

# O manualmente:
docker compose -f docker-compose.prod.yml up -d
```

**Características:**
- Arquitectura híbrida probada + optimizaciones de producción
- SSL/TLS configurado
- Workers múltiples y queue workers
- Volúmenes optimizados y código read-only
- Health checks y monitoring

## ⚙️ Variables de Entorno

### 📋 Configuración Básica
```bash
# Copiar template apropiado
cp .env.example .env                    # Para desarrollo
cp .env.production.example .env         # Para producción

# Variables críticas a configurar:
APP_KEY=                                # Generar con artisan key:generate
DB_PASSWORD=                           # Password seguro de base de datos
REDIS_PASSWORD=                        # Password de Redis (producción)
```

### 🎛️ Variables Principales

| Variable | Desarrollo | Producción | Descripción |
|----------|------------|------------|-------------|
| `APP_ENV` | `local` | `production` | Entorno de aplicación |
| `APP_DEBUG` | `true` | `false` | Debugging habilitado |
| `APP_PORT` | `8080` | `80` | Puerto principal |
| `QUEUE_WORKERS` | `1` | `4` | Workers de cola |
| `OPCACHE_VALIDATE_TIMESTAMPS` | `1` | `0` | Validación OPcache |

## 🚀 Comandos Quick Start

### Instalación Inicial
```bash
# Usar la configuración que funciona (híbrida)
make init                    # Configuración inicial
make hybrid                 # Iniciar configuración híbrida
# ¡Ya está funcionando!

# O si prefieres desarrollo con herramientas extras:
make env-setup              # Setup de desarrollo
make dev                    # Iniciar desarrollo completo
```

### Uso Diario - Configuración Híbrida (Recomendado)
```bash
make hybrid                 # Iniciar (o simplemente: make up)
make hybrid-logs           # Ver logs en tiempo real
make shell                  # Acceso al contenedor (detecta automático)
make migrate               # Ejecutar migraciones
make test                  # Ejecutar tests

# Comandos específicos híbridos:
make hybrid-build          # Reconstruir imágenes
make hybrid-stop           # Parar servicios
make hybrid-clean          # Limpiar completamente
```

### Desarrollo con Herramientas Adicionales
```bash
make dev                    # Iniciar desarrollo completo
make dev-logs              # Ver logs de desarrollo
make dev-shell             # Acceso al contenedor de desarrollo

# Comandos Laravel directos:
make dev-artisan CMD="route:list"
make dev-composer CMD="install"
make dev-npm CMD="run build"
```

### Producción
```bash
make env-prod-setup         # Configurar variables
make prod-build            # Construir imágenes
make prod                  # Iniciar producción
make prod-optimize         # Optimizar aplicación
make prod-backup-db        # Backup de base de datos
```

## 🔧 Personalización Local

### docker-compose.override.yml
Este archivo permite personalizar la configuración sin modificar los archivos principales:

```yaml
# Ejemplo de override personal
services:
  app:
    ports:
      - "8090:80"  # Cambiar puerto si está ocupado
    environment:
      - DB_HOST=host.docker.internal  # Usar MySQL local
      - CUSTOM_VARIABLE=mi_valor
    volumes:
      - ./mi-config:/app/config/local
```

### Variables de Entorno Personalizadas
```bash
# En tu .env local:
COMPOSE_PROJECT_NAME=mi-kraftdo-nfc
APP_PORT=8090
DB_PORT=3307
REDIS_PORT=6380
```

## 🌐 URLs de Acceso

### Desarrollo
- **Aplicación**: http://localhost:8080
- **Admin Panel**: http://localhost:8080/admin
- **MailHog**: http://localhost:8025
- **Adminer**: http://localhost:8081

### Producción
- **Aplicación**: Configurado según `APP_URL`
- **Admin Panel**: `{APP_URL}/admin`
- **Monitoreo**: `{APP_URL}:9090` (Prometheus)
- **Dashboards**: `{APP_URL}:3000` (Grafana)

## 🛡️ Mejores Prácticas de Seguridad

### Desarrollo
- ✅ Usar `.env.example` como base
- ✅ Nunca commitear `.env`
- ✅ Usar passwords simples para desarrollo
- ✅ SQLite para desarrollo rápido

### Producción
- 🔒 Generar passwords fuertes únicos
- 🔒 Configurar SSL/TLS obligatorio
- 🔒 Usar secrets management
- 🔒 Configurar backup automático
- 🔒 Monitoreo y alertas habilitadas

## 📊 Monitoreo y Debugging

### Ver Estado de Servicios
```bash
make status                # Estado de contenedores
make health               # Health check de servicios
make monitor              # Monitoreo de recursos
```

### Logs y Debugging
```bash
make logs                 # Logs combinados
make dev-logs            # Logs de desarrollo
make prod-logs           # Logs de producción
make debug               # Información de debug
```

### Performance
```bash
make prod-optimize       # Optimizar para producción
make test-coverage      # Tests con cobertura
make lint               # Verificar código
```

## 🔄 Migración entre Entornos

### De Híbrido a Desarrollo
```bash
make hybrid-down         # Parar híbrido
make env-setup          # Configurar desarrollo  
make dev                # Iniciar desarrollo
```

### De Desarrollo a Producción
```bash
make down               # Parar desarrollo
make env-prod-setup     # Configurar producción
# Editar .env con valores de producción
make prod-build        # Construir para producción
make prod              # Iniciar producción
```

## 🆘 Troubleshooting

### Problemas Comunes

**Puerto ocupado:**
```bash
# Cambiar puerto en .env o override
APP_PORT=8090
make dev
```

**Permisos de storage:**
```bash
make shell
chown -R www-data:www-data storage bootstrap/cache
```

**Base de datos no conecta:**
```bash
# Verificar servicios
make status
make logs
```

**Limpiar y empezar de cero:**
```bash
make clean              # Limpiar Docker
make dev-clean         # Limpiar desarrollo
make fresh             # Reset base de datos
```

### Comandos de Emergencia
```bash
# Reset completo
make down && make clean && make install

# Backup antes de cambios críticos
make prod-backup-db

# Logs en tiempo real para debugging
make logs -f
```

## 📈 Escalabilidad

### Configuración de Workers
```bash
# En .env para ajustar según recursos del servidor:
QUEUE_WORKERS=4            # Workers para procesar colas
QUEUE_WORKERS=2            # Según carga de trabajos
WORKER_REPLICAS=2          # Múltiples contenedores worker
```

### Monitoreo Avanzado
```bash
# Activar monitoreo completo
docker compose -f docker-compose.prod.yml --profile monitoring up -d

# URLs:
# Prometheus: http://localhost:9090
# Grafana: http://localhost:3000
```

---

**💡 Pro tip**: Usa `make help` para ver todos los comandos disponibles en cualquier momento.