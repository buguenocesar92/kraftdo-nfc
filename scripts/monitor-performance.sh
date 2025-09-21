#!/bin/bash

# =============================================================================
# SCRIPT DE MONITOREO DE RENDIMIENTO - KRAFTDO NFC
# =============================================================================
# Monitorea el rendimiento y recursos del sistema optimizado

echo "📊 KRAFTDO NFC - MONITOREO DE RENDIMIENTO"
echo "=========================================="
echo "🕐 $(date)"
echo ""

# Información del contenedor web
echo "🌐 CONTENEDOR WEB (PHP-FPM + Nginx)"
echo "-----------------------------------"
WEB_CONTAINER=$(docker ps --format "table {{.Names}}" | grep kraftdo-nfc-staging-web)
if [ ! -z "$WEB_CONTAINER" ]; then
    echo "📈 CPU y Memoria:"
    docker stats --no-stream --format "table {{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.MemPerc}}" $WEB_CONTAINER
    
    echo ""
    echo "🔧 PHP-FPM Status:"
    docker exec $WEB_CONTAINER php-fpm83 -t 2>/dev/null && echo "✅ PHP-FPM OK" || echo "❌ PHP-FPM Error"
    
    echo ""
    echo "💾 OPcache Status:"
    docker exec $WEB_CONTAINER php -r "
    if (function_exists('opcache_get_status')) {
        \$status = opcache_get_status();
        if (\$status) {
            echo '✅ OPcache: ' . round(\$status['memory_usage']['used_memory']/1024/1024, 2) . 'MB usado de ' . round(\$status['memory_usage']['free_memory']/1024/1024, 2) . 'MB libre\n';
            echo '📁 Archivos cached: ' . \$status['opcache_statistics']['num_cached_scripts'] . ' / ' . \$status['opcache_statistics']['max_cached_keys'] . '\n';
            echo '🎯 Hit rate: ' . round(\$status['opcache_statistics']['opcache_hit_rate'], 2) . '%\n';
        } else {
            echo '❌ OPcache no disponible\n';
        }
    } else {
        echo '❌ OPcache no instalado\n';
    }
    "
else
    echo "❌ Contenedor web no encontrado"
fi

echo ""

# Información del contenedor Redis
echo "🔴 CONTENEDOR REDIS"
echo "-------------------"
REDIS_CONTAINER=$(docker ps --format "table {{.Names}}" | grep kraftdo-nfc-staging-redis)
if [ ! -z "$REDIS_CONTAINER" ]; then
    echo "📈 CPU y Memoria:"
    docker stats --no-stream --format "table {{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.MemPerc}}" $REDIS_CONTAINER
    
    echo ""
    echo "📊 Redis Info:"
    docker exec $REDIS_CONTAINER redis-cli info memory | grep -E "(used_memory_human|used_memory_peak_human|maxmemory_human)"
    docker exec $REDIS_CONTAINER redis-cli info clients | grep connected_clients
    docker exec $REDIS_CONTAINER redis-cli info stats | grep -E "(total_commands_processed|total_connections_received|keyspace_hits|keyspace_misses)"
else
    echo "❌ Contenedor Redis no encontrado"
fi

echo ""

# Información del sistema host
echo "🖥️  SISTEMA HOST"
echo "---------------"
echo "💾 Memoria total del sistema:"
free -h | head -2

echo ""
echo "🔧 CPU cores utilizables:"
nproc

echo ""
echo "📁 Espacio en disco:"
df -h / | tail -1

echo ""
echo "🌐 Conexiones de red activas:"
ss -tuln | grep :8084 && echo "✅ Puerto 8084 activo" || echo "❌ Puerto 8084 no disponible"

echo ""
echo "📈 Load average:"
uptime

echo ""
echo "=========================================="
echo "✅ Monitoreo completado - $(date)"