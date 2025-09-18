#!/bin/bash
set -e

echo "Iniciando entrypoint de staging..."

# PRIMERO: Crear directorios si no existen
echo "Creando directorios necesarios..."
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# SEGUNDO: Configurar permisos ANTES de limpiar
echo "Configurando permisos..."
chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# TERCERO: Limpiar cache
echo "Limpiando cache..."
rm -rf /var/www/html/storage/framework/views/*
rm -rf /var/www/html/storage/framework/cache/*

# CUARTO: Verificar permisos después de limpiar
chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# QUINTO: Generar clave si no existe
if [ -z "$APP_KEY" ]; then
    echo "Generando APP_KEY..."
    php artisan key:generate --no-interaction
fi

# SEXTO: Optimizar Laravel (en orden correcto)
echo "Optimizando Laravel..."
php artisan config:cache
php artisan route:cache
# NO hacer view:cache aquí para evitar problemas de permisos iniciales

# SEPTIMO: Crear enlace de storage
php artisan storage:link --no-interaction || true

# OCTAVO: Ejecutar migraciones si es necesario
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "Ejecutando migraciones..."
    php artisan migrate --force --no-interaction
fi

echo "Entrypoint completado. Iniciando supervisord..."

# Iniciar supervisor
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf