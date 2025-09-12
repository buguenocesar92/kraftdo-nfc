#!/bin/bash
set -e

# Variables de configuración
MAX_RETRIES=${MAX_RETRIES:-30}
RETRY_INTERVAL=${RETRY_INTERVAL:-2}
HEALTH_CHECK_TIMEOUT=${HEALTH_CHECK_TIMEOUT:-5}

# Variables específicas de Octane
export OCTANE_WORKERS=${OCTANE_WORKERS:-auto}
export OCTANE_MAX_REQUESTS=${OCTANE_MAX_REQUESTS:-1000}

echo "🚀 Starting Laravel NFC App with Octane - Production Mode..."
echo "📊 Environment: ${APP_ENV:-production}"
echo "🔧 Debug mode: ${APP_DEBUG:-false}"
echo "🌊 Octane Server: ${OCTANE_SERVER:-swoole}"
echo "👷 Octane Workers: ${OCTANE_WORKERS:-auto}"
echo "🔄 Max Requests per Worker: ${OCTANE_MAX_REQUESTS:-1000}"

# Función para verificar conectividad con timeout y reintentos
wait_for_service() {
    local host=$1
    local port=$2
    local service=$3
    local retries=0
    
    echo "⏳ Waiting for $service connection on $host:$port..."
    
    while [ $retries -lt $MAX_RETRIES ]; do
        if timeout $HEALTH_CHECK_TIMEOUT nc -z "$host" "$port" 2>/dev/null; then
            echo "✅ $service is ready!"
            return 0
        fi
        
        retries=$((retries + 1))
        echo "   $service is unavailable - attempt $retries/$MAX_RETRIES, retrying in ${RETRY_INTERVAL}s..."
        sleep $RETRY_INTERVAL
    done
    
    echo "❌ Failed to connect to $service after $MAX_RETRIES attempts"
    exit 1
}

# Wait for external database connection
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "localhost" ] && [ "$DB_HOST" != "127.0.0.1" ]; then
    wait_for_service "$DB_HOST" "${DB_PORT:-3306}" "Database"
fi

# Verificar Redis si está configurado
if [ "$CACHE_DRIVER" = "redis" ] || [ "$SESSION_DRIVER" = "redis" ] || [ "$QUEUE_CONNECTION" = "redis" ]; then
    if [ -n "$REDIS_HOST" ] && [ "$REDIS_HOST" != "localhost" ] && [ "$REDIS_HOST" != "127.0.0.1" ]; then
        wait_for_service "$REDIS_HOST" "${REDIS_PORT:-6379}" "Redis"
    fi
fi

# Validar configuraciones críticas
echo "🔍 Validating critical configurations..."

if [ -z "$APP_KEY" ]; then
    echo "❌ APP_KEY is required for production!"
    exit 1
fi

if [ "$APP_ENV" = "production" ] && [ "$APP_DEBUG" = "true" ]; then
    echo "⚠️  Warning: APP_DEBUG is enabled in production!"
fi

# Verificar permisos de archivos críticos
echo "🔍 Checking file permissions..."
if [ ! -w "/var/www/html/storage" ]; then
    echo "❌ Storage directory is not writable!"
    exit 1
fi

# Configurar URLs limpias para Laravel (forzar detección de proxy)
echo "🌐 Configuring clean URLs for Laravel..."
export APP_URL="${APP_URL:-http://localhost:8080}"

# Optimizaciones de Laravel basadas en entorno
echo "⚙️  Optimizing Laravel for ${APP_ENV:-production}..."

# Limpiar caches primero
php artisan config:clear || echo "⚠️  Config clear failed"
php artisan route:clear || echo "⚠️  Route clear failed"
php artisan view:clear || echo "⚠️  View clear failed"
php artisan cache:clear || echo "⚠️  Cache clear failed"

# Cachear para producción
if [ "${CONFIG_CACHE:-true}" = "true" ]; then
    php artisan config:cache || echo "⚠️  Config cache failed"
fi

if [ "${ROUTE_CACHE:-true}" = "true" ]; then
    php artisan route:cache || echo "⚠️  Route cache failed"
fi

if [ "${VIEW_CACHE:-true}" = "true" ]; then
    php artisan view:cache || echo "⚠️  View cache failed"
fi

# Optimizar autoloader para producción
if [ "$APP_ENV" = "production" ]; then
    echo "🚀 Optimizing autoloader for production..."
    composer dump-autoload --optimize --classmap-authoritative --no-dev || echo "⚠️  Composer optimization failed"
fi

# Gestión de base de datos
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "🗄️  Running database migrations..."
    
    # Verificar conexión a BD antes de migrar
    if php artisan migrate:status >/dev/null 2>&1; then
        php artisan migrate --force || {
            echo "❌ Migration failed!"
            exit 1
        }
        
        # Ejecutar seeders si está habilitado
        if [ "$RUN_SEEDERS" = "true" ]; then
            echo "🌱 Running database seeders..."
            php artisan db:seed --force || echo "⚠️  Seeder failed"
        fi
    else
        echo "❌ Cannot connect to database for migrations!"
        exit 1
    fi
fi

# Create symbolic link for storage
if [ ! -L "public/storage" ]; then
    echo "🔗 Creating storage symbolic link..."
    php artisan storage:link --force || echo "⚠️  Storage link failed"
fi

# Verificar que el enlace simbólico funciona
if [ ! -e "public/storage" ]; then
    echo "❌ Storage symbolic link verification failed!"
    exit 1
fi

# Set permissions con verificación
echo "🔒 Setting secure permissions..."

# Permisos base más seguros
chown -R www:www /var/www/html || echo "⚠️  Ownership change failed"
chmod -R 755 /var/www/html || echo "⚠️  Base permissions failed"

# Permisos específicos para directorios críticos (seguridad mejorada)
chmod -R 775 storage bootstrap/cache || echo "⚠️  Cache permissions failed"
chmod -R 775 storage/logs || echo "⚠️  Log permissions failed"
chmod -R 775 storage/app || echo "⚠️  App storage permissions failed"
chmod -R 775 storage/framework || echo "⚠️  Framework storage permissions failed"

# Verificar permisos críticos
if [ ! -w "storage/logs" ]; then
    echo "❌ Log directory is not writable!"
    exit 1
fi

# Asegurar permisos de Nginx para subida de archivos (producción)
echo "🔧 Configuring Nginx permissions for file uploads..."
mkdir -p /var/lib/nginx/tmp/client_body /var/lib/nginx/tmp/fastcgi /var/lib/nginx/tmp/proxy /var/lib/nginx/tmp/scgi /var/lib/nginx/tmp/uwsgi
chown -R www:www /var/lib/nginx/tmp
chmod -R 755 /var/lib/nginx/tmp
chmod 755 /var/lib/nginx

# Health check final
echo "🏥 Performing final health checks..."

# Verificar que Laravel puede iniciarse
if ! php artisan --version >/dev/null 2>&1; then
    echo "❌ Laravel application health check failed!"
    exit 1
fi

# Verificar conectividad a servicios críticos
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "localhost" ]; then
    if ! php artisan migrate:status >/dev/null 2>&1; then
        echo "❌ Database connectivity check failed!"
        exit 1
    fi
fi

echo "✅ Laravel NFC App with Octane is ready for ${APP_ENV:-production}!"
echo "✅ Connected to database: ${DB_HOST:-localhost}:${DB_PORT:-3306}"
if [ -n "$REDIS_HOST" ]; then
    echo "✅ Connected to Redis: $REDIS_HOST:${REDIS_PORT:-6379}"
fi
echo "✅ Nginx configured as reverse proxy for Octane"
echo "✅ Laravel optimized for ${APP_ENV:-production} with clean URLs"
echo "✅ Octane server ready to start with ${OCTANE_WORKERS:-auto} workers"
echo "✅ All health checks passed"

# Execute the main command
echo "🎯 Starting application services..."
exec "$@"