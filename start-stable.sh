#!/bin/bash

echo "🚀 Iniciando Laravel con PHP-FPM + FrankenPHP..."

# Copiar .env si no existe
if [ ! -f .env ]; then
    cp .env.stable .env
    echo "✅ Archivo .env creado desde .env.stable"
fi

# Copiar Caddyfile
cp docker/frankenphp/Caddyfile.stable docker/frankenphp/Caddyfile
echo "✅ Caddyfile configurado"

# Iniciar servicios
docker-compose -f docker-compose.stable.yml up -d --build

echo ""
echo "🎉 Servicios iniciados!"
echo ""
echo "📍 URLs disponibles:"
echo "   - Frontend: http://localhost:8080"
echo "   - Admin Panel: http://localhost:8080/admin"
echo "   - MySQL: localhost:3306"
echo "   - Redis: localhost:6379"
echo ""
echo "👤 Credenciales por defecto:"
echo "   - MySQL: laravel / laravel_password"
echo "   - Admin: admin@kraftdo-nfc.com / password"
echo ""
echo "📊 Ver logs: docker-compose -f docker-compose.stable.yml logs -f"
echo "🛑 Parar: docker-compose -f docker-compose.stable.yml down"