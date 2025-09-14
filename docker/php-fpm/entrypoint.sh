#!/bin/bash

set -e

echo "🚀 Iniciando Laravel con PHP-FPM..."

# Verificar si estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo "❌ Error: No se encontró Laravel en /var/www/html"
    exit 1
fi

# Esperar a que MySQL esté disponible
echo "⏳ Esperando a MySQL..."
while ! nc -z mysql 3306; do
    sleep 2
done
echo "✅ MySQL disponible"

# Instalar dependencias si no existen
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "📦 Instalando dependencias de Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generar clave de aplicación si no existe
if [ -z "${APP_KEY:-}" ] || [ "${APP_KEY}" = "base64:" ]; then
    echo "🔑 Generando clave de aplicación..."
    php artisan key:generate --no-interaction --force
fi

# Crear directorios necesarios
echo "📁 Creando directorios..."
mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache

# Configurar permisos
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Limpiar cachés
echo "🧹 Limpiando cachés..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Ejecutar migraciones
echo "📊 Ejecutando migraciones..."
php artisan migrate --force --no-interaction

# Publicar assets de Filament
echo "🎨 Publicando assets de Filament..."
php artisan filament:assets

# Crear enlace de storage
echo "🔗 Creando enlace de storage..."
php artisan storage:link

echo "✅ Laravel configurado correctamente"
echo "🌐 Listo para recibir peticiones en PHP-FPM puerto 9000"

# Iniciar PHP-FPM
exec php-fpm