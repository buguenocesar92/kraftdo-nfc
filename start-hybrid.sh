#!/bin/bash

echo "🚀 Iniciando Configuración Híbrida: PHP-FPM + FrankenPHP..."
echo ""

# Preparar Caddyfile
cp docker/frankenphp/Caddyfile.hybrid docker/frankenphp/Caddyfile
echo "✅ Caddyfile híbrido configurado"

# Verificar .env
if [ ! -f .env ]; then
    echo "⚠️  No se encontró .env. Creando desde .env de ejemplo..."
    cp .env.example .env 2>/dev/null || echo "Crea tu archivo .env manualmente"
fi

echo "📋 Verificando configuraciones necesarias en .env:"
echo "   - DB_HOST=127.0.0.1 (o tu IP de base de datos externa)"  
echo "   - DB_DATABASE=kraftdo"
echo "   - DB_USERNAME=root"
echo "   - DB_PASSWORD=Hero2025."
echo "   - APP_URL=http://localhost:8080"
echo ""

# Iniciar servicios  
echo "🐳 Iniciando contenedores..."
docker compose -f docker-compose.hybrid.yml up -d --build

echo ""
echo "🎉 ¡Configuración híbrida iniciada!"
echo ""
echo "📍 ARQUITECTURA:"
echo "   ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐"
echo "   │   FrankenPHP    │    │     PHP-FPM     │    │  MySQL Externo  │"
echo "   │   (Frontend)    │◄──►│    (Backend)    │◄──►│  127.0.0.1:3306 │"
echo "   │   Puerto 8080   │    │   Puerto 9000   │    │                 │"
echo "   └─────────────────┘    └─────────────────┘    └─────────────────┘"
echo ""
echo "📍 URLs disponibles:"
echo "   🌐 Frontend: http://localhost:8080"
echo "   🔧 Admin: http://localhost:8080/admin"  
echo "   🏥 Health: http://localhost:8080/health"
echo "   📊 Redis: localhost:6379"
echo ""
echo "🔧 Comandos útiles:"
echo "   📊 Ver logs: docker compose -f docker-compose.hybrid.yml logs -f"
echo "   🐚 PHP Shell: docker compose -f docker-compose.hybrid.yml exec php-fpm bash"
echo "   🛑 Parar: docker compose -f docker-compose.hybrid.yml down"
echo ""
echo "🧪 Para probar Livewire:"
echo "   1. Crea usuario admin: docker compose -f docker-compose.hybrid.yml exec php-fpm php artisan db:seed --class=RolesAndPermissionsSeeder"
echo "   2. Crea usuarios: docker compose -f docker-compose.hybrid.yml exec php-fpm php artisan db:seed --class=AdminUserSeeder"
echo "   3. Accede a: http://localhost:8080/admin"
echo "   4. Login: admin@kraftdo-nfc.com / password"