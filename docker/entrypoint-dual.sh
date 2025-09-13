#!/bin/bash
set -e

echo "🚀 Starting Dual Server NFC Application..."

# Function to wait for service
wait_for_service() {
    local service=$1
    local port=$2
    echo "⏳ Waiting for $service on port $port..."
    while ! nc -z localhost $port; do
        sleep 1
    done
    echo "✅ $service is ready!"
}

# Create necessary directories
mkdir -p /var/log/supervisor
mkdir -p /run/php
mkdir -p /run/nginx
mkdir -p /var/lib/nginx/tmp/{client_body,fastcgi,proxy,scgi,uwsgi}

# Set proper permissions
chown -R www:www /var/www/html/storage
chown -R www:www /var/www/html/bootstrap/cache
chown -R www:www /run/php
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Prepare Laravel application
echo "🔧 Preparing Laravel application..."

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --no-interaction
fi

# Run migrations if enabled
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "📊 Running database migrations..."
    php artisan migrate --force --no-interaction
fi

# Clear and cache configurations if enabled
if [ "${OPTIMIZE_CLEAR:-true}" = "true" ]; then
    echo "🧹 Clearing caches..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
fi

if [ "${CONFIG_CACHE:-true}" = "true" ]; then
    echo "⚡ Caching configurations..."
    php artisan config:cache
fi

if [ "${ROUTE_CACHE:-true}" = "true" ]; then
    echo "🛣️ Caching routes..."
    php artisan route:cache
fi

if [ "${VIEW_CACHE:-false}" = "true" ]; then
    echo "👁️ Caching views..."
    php artisan view:cache
else
    echo "⏭️ Skipping view cache (disabled)"
fi

# Publish assets for Filament
echo "📦 Publishing Filament assets..."
php artisan filament:assets

# Storage link
echo "🔗 Creating storage link..."
php artisan storage:link || true

echo "🎯 Dual Server Configuration:"
echo "   📋 Admin Panel (Filament): PHP-FPM"
echo "   🚀 Frontend NFC: Octane + Swoole"
echo "   🌐 Nginx: Proxy + Static Files"

# Start supervisord
echo "🎬 Starting all services via Supervisor..."
exec "$@"