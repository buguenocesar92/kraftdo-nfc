#!/bin/bash
set -e

echo "🚀 Iniciando entrypoint de staging..."

# Configurar permisos críticos
echo "🔒 Configurando permisos..."
chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Limpiar vistas compiladas
echo "🧹 Limpiando cache de vistas..."
rm -rf /var/www/html/storage/framework/views/*

# Optimizar Laravel
echo "⚡ Optimizando Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones si es necesario
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "📊 Ejecutando migraciones..."
    php artisan migrate --force
fi

echo "✅ Entrypoint completado. Iniciando supervisord..."

# Iniciar supervisor para manejar nginx y php-fpm
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf