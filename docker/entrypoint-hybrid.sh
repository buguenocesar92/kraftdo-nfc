#!/bin/bash

# Entrypoint para PHP-FPM híbrido
# Basado en las configuraciones existentes

set -e

echo "🚀 Iniciando PHP-FPM para Laravel NFC..."

# Verificar si estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo "❌ Error: No se encontró Laravel en /var/www/html"
    exit 1
fi

# Esperar a que Redis esté disponible
echo "⏳ Esperando a Redis..."
while ! nc -z redis 6379; do
    sleep 2
done
echo "✅ Redis disponible"

# Verificar conectividad con base de datos externa
echo "⏳ Verificando conexión a base de datos externa..."
if nc -z $DB_HOST $DB_PORT; then
    echo "✅ Base de datos externa accesible"
else
    echo "⚠️  Advertencia: No se puede conectar a la base de datos externa. Continuando..."
fi

# Instalar/actualizar dependencias si es necesario
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "📦 Instalando dependencias de Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
else
    echo "✅ Dependencias de Composer ya instaladas"
fi

# Generar clave de aplicación si no existe
if [ -z "${APP_KEY:-}" ] || [ "${APP_KEY}" = "base64:" ]; then
    echo "🔑 Generando clave de aplicación..."
    php artisan key:generate --no-interaction --force
fi

# Crear directorios necesarios
echo "📁 Creando directorios necesarios..."
mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Configurar permisos CRÍTICOS para evitar errores de compilación de vistas
echo "🔐 Configurando permisos críticos..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
# SOLUCIÓN DEFINITIVA: Asegurar que views siempre tenga ownership correcto
rm -rf storage/framework/views/*
chown -R 105:105 storage/framework/views
chmod -R 775 storage/framework/views
echo "✅ Directorio views configurado con UID 105 y permisos 777"

# Limpiar cachés de Laravel (incluyendo vistas para evitar problemas de permisos)
echo "🧹 Limpiando cachés..."
php artisan config:clear
php artisan route:clear  
php artisan view:clear
php artisan cache:clear

# Intentar ejecutar migraciones (solo si DB está disponible)
if nc -z $DB_HOST $DB_PORT; then
    echo "📊 Ejecutando migraciones..."
    php artisan migrate --force --no-interaction || echo "⚠️ Error en migraciones, continuando..."
else
    echo "⚠️ Saltando migraciones - DB externa no disponible"
fi

# Publicar assets de Filament
echo "🎨 Publicando assets de Filament..."
php artisan filament:assets || true

# Crear enlace de storage
echo "🔗 Creando enlace de storage..."
php artisan storage:link || true

echo "✅ PHP-FPM configurado correctamente"
echo "🌐 Escuchando en puerto 9000 para peticiones FastCGI"
echo "🌍 También sirviendo HTTP en puerto 80 para debugging"

# Ejecutar el entrypoint original de webdevops pero sin supervisor
echo "🚀 Iniciando servicios con webdevops entrypoint..."
exec /entrypoint supervisord -n