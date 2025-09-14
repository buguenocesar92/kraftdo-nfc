#!/bin/bash

# Script de inicio optimizado para Laravel Octane con FrankenPHP
# Soporta: desarrollo (dev), producción (prod), worker, scheduler

set -e

# Colores para logs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para logging
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

log_warn() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARNING: $1${NC}"
}

log_error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}"
}

log_info() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')] INFO: $1${NC}"
}

# Detectar el entorno
ENV_MODE="${1:-${APP_ENV:-production}}"

log "🚀 Iniciando KraftDo NFC con Laravel Octane + FrankenPHP"
log "📦 Modo: $ENV_MODE"

# Verificar directorio de trabajo
if [ ! -f "/app/artisan" ]; then
    log_error "No se encontró Laravel en /app"
    exit 1
fi

cd /app

# ==========================================
# CONFIGURACIÓN COMÚN
# ==========================================

# Crear directorios necesarios
log "📁 Creando directorios necesarios..."
mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    database

# Configurar permisos
log "🔐 Configurando permisos..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Configurar base de datos SQLite si es necesario
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    if [ ! -f "database/database.sqlite" ]; then
        log "🗄️  Creando base de datos SQLite..."
        touch database/database.sqlite
        chmod 664 database/database.sqlite
        chown www-data:www-data database/database.sqlite
    fi
fi

# Esperar a que estén disponibles los servicios externos
wait_for_service() {
    local host=$1
    local port=$2
    local service=$3
    local timeout=${4:-60}
    local count=0
    
    log_info "⏳ Esperando a $service ($host:$port)..."
    while ! nc -z "$host" "$port" 2>/dev/null; do
        if [ $count -ge $timeout ]; then
            log_error "$service no está disponible después de ${timeout}s"
            exit 1
        fi
        log_warn "$service no está disponible - esperando..."
        sleep 2
        count=$((count + 2))
    done
    log "✅ $service disponible"
}

# Esperar servicios según la configuración
if [ "${DB_CONNECTION:-}" = "mysql" ]; then
    wait_for_service "${DB_HOST:-mysql}" "${DB_PORT:-3306}" "MySQL"
fi

if [ "${REDIS_HOST:-}" != "" ] && [ "${CACHE_DRIVER:-}" = "redis" ]; then
    wait_for_service "${REDIS_HOST}" "${REDIS_PORT:-6379}" "Redis"
fi

# ==========================================
# CONFIGURACIÓN POR ENTORNO
# ==========================================

case $ENV_MODE in
    "dev"|"local"|"development")
        log "🛠️  Configurando entorno de DESARROLLO"
        
        # Instalar dependencias si no existen
        if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
            log "📦 Instalando dependencias de Composer..."
            composer install --optimize-autoloader
        fi
        
        # Generar clave de aplicación si no existe
        if [ -z "${APP_KEY:-}" ] || [ "${APP_KEY}" = "base64:" ]; then
            log "🔑 Generando clave de aplicación..."
            php artisan key:generate --no-interaction --force
        fi
        
        # Limpiar cachés
        log "🧹 Limpiando cachés de desarrollo..."
        php artisan config:clear || true
        php artisan route:clear || true
        php artisan view:clear || true
        php artisan cache:clear || true
        
        # Ejecutar migraciones si es necesario
        if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
            log "📊 Ejecutando migraciones..."
            php artisan migrate --force --no-interaction || log_warn "Error en migraciones"
        fi
        
        # Publicar assets de Filament
        log "🎨 Publicando assets de Filament..."
        php artisan filament:assets || true
        
        # Crear enlace de storage
        log "🔗 Creando enlace de storage..."
        php artisan storage:link || true
        
        log "✅ Desarrollo configurado - iniciando Laravel Octane con FrankenPHP"
        log "🌐 Aplicación disponible en: http://localhost:8080"
        log "🔧 Admin panel: http://localhost:8080/admin"
        
        # Iniciar Laravel Octane con FrankenPHP en modo desarrollo
        exec php artisan octane:start \
            --server=frankenphp \
            --host=0.0.0.0 \
            --port=80 \
            --admin-port=2019 \
            --workers=1 \
            --watch
        ;;
        
    "production"|"prod")
        log "🏭 Configurando entorno de PRODUCCIÓN"
        
        # Validar variables críticas
        if [ -z "${APP_KEY:-}" ]; then
            log_error "APP_KEY es requerida en producción"
            exit 1
        fi
        
        # Optimizar aplicación
        log "⚡ Optimizando aplicación para producción..."
        php artisan config:cache
        php artisan route:cache  
        php artisan view:cache
        php artisan event:cache
        
        # Ejecutar migraciones si es necesario
        if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
            log "📊 Ejecutando migraciones..."
            php artisan migrate --force --no-interaction
        fi
        
        # Publicar assets
        php artisan filament:assets
        php artisan storage:link
        
        log "✅ Producción configurada - iniciando Laravel Octane con FrankenPHP"
        log "🌐 Aplicación disponible en: ${APP_URL:-https://localhost}"
        
        # Iniciar Laravel Octane con FrankenPHP en modo producción
        exec php artisan octane:start \
            --server=frankenphp \
            --host=0.0.0.0 \
            --port=80 \
            --admin-port=2019 \
            --workers=${OCTANE_WORKERS:-auto} \
            --max-requests=${OCTANE_MAX_REQUESTS:-500}
        ;;
        
    "worker"|"queue")
        log "👷 Configurando QUEUE WORKER"
        
        # Validar configuración de queue
        if [ -z "${QUEUE_CONNECTION:-}" ]; then
            log_error "QUEUE_CONNECTION no está configurada"
            exit 1
        fi
        
        log "⚙️  Iniciando worker para cola: ${QUEUE_CONNECTION}"
        log "🔧 Workers: ${QUEUE_WORKERS:-1}"
        log "⏱️  Timeout: ${QUEUE_TIMEOUT:-90}s"
        log "🔄 Reintentos: ${QUEUE_TRIES:-3}"
        
        # Ejecutar worker con configuración específica
        exec php artisan queue:work \
            --verbose \
            --tries="${QUEUE_TRIES:-3}" \
            --timeout="${QUEUE_TIMEOUT:-90}" \
            --memory="${QUEUE_MEMORY:-512}" \
            --sleep="${QUEUE_SLEEP:-3}" \
            --max-jobs="${QUEUE_MAX_JOBS:-1000}"
        ;;
        
    "scheduler"|"cron")
        log "⏰ Configurando SCHEDULER"
        
        log "📅 Iniciando Laravel scheduler..."
        
        # Ejecutar scheduler
        exec php artisan schedule:work
        ;;
        
    "migrate")
        log "📊 Ejecutando MIGRACIONES"
        
        php artisan migrate --force --no-interaction
        log "✅ Migraciones completadas"
        exit 0
        ;;
        
    "seed")
        log "🌱 Ejecutando SEEDERS"
        
        php artisan db:seed --force
        log "✅ Seeders completados"
        exit 0
        ;;
        
    "test")
        log "🧪 Ejecutando TESTS"
        
        # Preparar entorno de testing
        php artisan config:clear
        php artisan migrate:fresh --env=testing --force --no-interaction
        
        # Ejecutar tests
        exec php artisan test --parallel
        ;;
        
    "octane:install")
        log "📦 Instalando Laravel Octane..."
        
        # Instalar Octane si no está instalado
        composer require laravel/octane
        php artisan octane:install --server=frankenphp --no-interaction
        
        log "✅ Laravel Octane instalado con FrankenPHP"
        exit 0
        ;;
        
    *)
        log_error "Modo no reconocido: $ENV_MODE"
        log "Modos válidos: dev, production, worker, scheduler, migrate, seed, test, octane:install"
        exit 1
        ;;
esac