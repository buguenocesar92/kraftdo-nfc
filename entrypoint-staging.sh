#!/bin/bash
set -e

echo "Iniciando entrypoint de staging..."

# PRIMERO: Crear .env si no existe
if [ ! -f /var/www/html/.env ]; then
    echo "Creando .env desde .env.example..."
    if [ -f /var/www/html/.env.example ]; then
        cp /var/www/html/.env.example /var/www/html/.env
    else
        echo "Archivo .env.example no encontrado, creando .env básico..."
        cat > /var/www/html/.env << 'EOF'
APP_NAME=KraftDo-NFC
APP_ENV=staging
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
EOF
    fi
fi

# SEGUNDO: Crear directorios si no existen
echo "Creando directorios necesarios..."
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache
mkdir -p /var/log/nginx

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

echo "Entrypoint completado. Iniciando servicios..."

# Crear directorio de logs de nginx
mkdir -p /var/log/nginx

# Iniciar PHP-FPM en background
echo "Iniciando PHP-FPM..."
php-fpm &

# Iniciar Nginx en foreground (proceso principal)
echo "Iniciando Nginx..."
exec nginx -g "daemon off;"