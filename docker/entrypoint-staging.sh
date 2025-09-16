#!/bin/sh

echo "🚀 Starting Laravel NFC App - Staging Mode..."

# Verificar directorio de trabajo
if [ ! -f "artisan" ]; then
    echo "❌ Error: Laravel not found in /var/www/html"
    exit 1
fi

# Wait for external database connection
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "localhost" ]; then
    echo "⏳ Waiting for external database on $DB_HOST:$DB_PORT..."
    while ! nc -z "$DB_HOST" "$DB_PORT"; do
        echo "   Database unavailable - retrying in 2s..."
        sleep 2
    done
    echo "✅ External database ready!"
fi

# Verificar Redis si está configurado
if [ "$CACHE_DRIVER" = "redis" ] || [ "$SESSION_DRIVER" = "redis" ]; then
    echo "⏳ Waiting for Redis on ${REDIS_HOST:-redis}:${REDIS_PORT:-6379}..."
    while ! nc -z "${REDIS_HOST:-redis}" "${REDIS_PORT:-6379}"; do
        echo "   Redis unavailable - retrying in 2s..."
        sleep 2
    done
    echo "✅ Redis ready!"
fi

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --no-interaction --force
fi

# Crear directorios necesarios
echo "📁 Creating required directories..."
mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache

# SOLUCIÓN DEFINITIVA: Configurar permisos críticos para evitar errores de vistas
echo "🔒 Setting critical permissions..."
# Solo cambiar permisos de directorios críticos, no todo el código
chown -R www:www storage bootstrap/cache
chmod -R 777 storage bootstrap/cache
# Limpiar vistas compiladas para evitar problemas de permisos
rm -rf storage/framework/views/*
chown -R www:www storage/framework/views
chmod -R 777 storage/framework/views

# Configurar permisos de Nginx
echo "🔧 Configuring Nginx permissions..."
mkdir -p /var/lib/nginx/tmp/{client_body,fastcgi,proxy,scgi,uwsgi}
chown -R www:www /var/lib/nginx/tmp
chmod -R 755 /var/lib/nginx/tmp

# Optimizar Laravel para staging
echo "⚙️  Optimizing Laravel for staging..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Run migrations if enabled
if [ "$RUN_MIGRATIONS" = "true" ] && nc -z "$DB_HOST" "$DB_PORT"; then
    echo "🗄️  Running migrations..."
    php artisan migrate --force --no-interaction || echo "⚠️ Migration warning, continuing..."
fi

# Publicar assets de Filament
echo "🎨 Publishing Filament assets..."
php artisan filament:assets || true

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link || true

# Cache para staging (menos agresivo que producción)
echo "⚡ Caching for staging..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Laravel NFC App ready for staging!"
echo "🌐 Starting Nginx + PHP-FPM on port 80"

# Iniciar supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf