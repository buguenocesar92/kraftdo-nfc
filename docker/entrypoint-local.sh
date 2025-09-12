#!/bin/sh

# Script de entrada para desarrollo local con servicios externos
echo "🚀 Iniciando aplicación Laravel en modo desarrollo..."

# Esperar a que la base de datos externa esté lista
DB_HOST_TO_CHECK=${DB_HOST:-host.docker.internal}
echo "⏳ Esperando a que MariaDB local esté listo en $DB_HOST_TO_CHECK:${DB_PORT:-3306}..."
while ! nc -z "$DB_HOST_TO_CHECK" "${DB_PORT:-3306}"; do
  echo "   Esperando conexión a MariaDB en $DB_HOST_TO_CHECK:${DB_PORT:-3306}..."
  sleep 2
done
echo "✅ MariaDB local está listo!"

# Verificar Redis si está configurado
if [ "$CACHE_DRIVER" = "redis" ] || [ "$SESSION_DRIVER" = "redis" ]; then
    echo "⏳ Esperando a que Redis local esté listo..."
    while ! nc -z 172.17.0.1 6379; do
      echo "   Esperando conexión a Redis en 172.17.0.1:6379..."
      sleep 2
    done
    echo "✅ Redis local está listo!"
fi

# Configurar permisos con seguridad mejorada
chown -R www:www /var/www/html
chmod -R 755 /var/www/html
chmod -R 775 storage bootstrap/cache

# Asegurar permisos de Nginx para subida de archivos
echo "🔧 Configurando permisos de Nginx..."
mkdir -p /var/lib/nginx/tmp/client_body /var/lib/nginx/tmp/fastcgi /var/lib/nginx/tmp/proxy /var/lib/nginx/tmp/scgi /var/lib/nginx/tmp/uwsgi
chown -R www:www /var/lib/nginx/tmp
chmod -R 775 /var/lib/nginx/tmp
chmod 755 /var/lib/nginx

# Generar clave de aplicación si no existe
if [ ! -f .env ]; then
    echo "📝 Creando archivo .env desde .env.example..."
    cp .env.example .env 2>/dev/null || echo "⚠️  No se encontró .env.example"
fi

# Generar APP_KEY si no existe
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "🔑 Generando clave de aplicación..."
    php artisan key:generate --force
fi

# Instalar/actualizar dependencias si es necesario
if [ ! -d "vendor" ] || [ "composer.json" -nt "vendor/autoload.php" ]; then
    echo "📦 Instalando dependencias de Composer..."
    composer install --no-interaction
fi

if [ ! -d "node_modules" ] || [ "package.json" -nt "node_modules/.package-lock.json" ]; then
    echo "📦 Instalando dependencias de NPM..."
    npm ci
fi

# Limpiar cache
echo "🧹 Limpiando cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Ejecutar migraciones en desarrollo
if [ "$APP_ENV" = "local" ]; then
    echo "🗄️  Ejecutando migraciones..."
    php artisan migrate --force
    
    # Ejecutar seeders si existen
    if php artisan db:seed --class=DatabaseSeeder --dry-run 2>/dev/null; then
        echo "🌱 Ejecutando seeders..."
        php artisan db:seed --force
    fi
fi

# Crear enlace simbólico de storage
php artisan storage:link --force

# Compilar assets para producción (sin dev server)
echo "🎨 Compilando assets..."
npm run build

echo "✅ Aplicación lista en http://localhost:9090"
echo "✅ Conectado a MariaDB local en puerto 3306"
echo "✅ Permisos de Nginx configurados para subida de archivos"

# Ejecutar comando principal
exec "$@" 