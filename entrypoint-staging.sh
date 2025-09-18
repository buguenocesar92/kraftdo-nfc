#!/bin/bash
set -e

echo "Iniciando entrypoint de staging..."

# PRIMERO: Crear .env si no existe
if [ ! -f /var/www/html/.env ]; then
    echo "Creando .env desde .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# SEGUNDO: Crear directorios si no existen
echo "Creando directorios necesarios..."
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# TERCERO: Configurar permisos ANTES de limpiar
echo "Configurando permisos..."
chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# CUARTO: Limpiar cache completamente
echo "Limpiando cache..."
rm -rf /var/www/html/storage/framework/views/*
rm -rf /var/www/html/storage/framework/cache/*
rm -rf /var/www/html/bootstrap/cache/*

# QUINTO: Verificar permisos después de limpiar
chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# SEXTO: Generar clave si no existe
if [ -z "$APP_KEY" ]; then
    echo "Generando APP_KEY..."
    php artisan key:generate --no-interaction
fi

# SEPTIMO: Optimizar Laravel (en orden correcto)
echo "Optimizando Laravel..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
# NO hacer view:cache aquí para evitar problemas de permisos iniciales

# OCTAVO: Crear enlace de storage
php artisan storage:link --no-interaction || true

# NOVENO: Ejecutar migraciones si es necesario
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "Ejecutando migraciones..."
    php artisan migrate --force --no-interaction
fi

echo "Entrypoint completado. Iniciando supervisord..."

# Iniciar supervisor
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf