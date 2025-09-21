# 🚀 Optimizaciones KraftDo NFC Staging

## 📋 Resumen de Optimizaciones Aplicadas

Este documento detalla las optimizaciones implementadas para soportar **1500-2000 usuarios concurrentes** en el entorno de staging.

### 🖥️ Especificaciones del Servidor
- **CPU**: 3 cores
- **RAM**: 3.6GB
- **Storage**: SSD
- **Target**: 1500-2000 usuarios concurrentes

---

## 🐳 Optimizaciones Docker Compose

### Contenedor Web (PHP-FPM + Nginx)
- **Límites de recursos**: 1.5GB RAM, 2.5 CPU cores
- **CPU Affinity**: Cores 0-2 
- **Shared Memory**: 512MB para OPcache
- **tmpfs**: 1GB para archivos temporales
- **Security**: `no-new-privileges` habilitado

### Contenedor Redis
- **Límites de recursos**: 1GB RAM, 1.0 CPU core
- **CPU Affinity**: Core 2 dedicado
- **Configuración optimizada**: 768MB memory, lazy freeing, tcp optimizations
- **Conexiones**: Hasta 10,000 clientes simultáneos

### Red Optimizada
- **Bridge personalizado** con subnet dedicada
- **Sysctls de red**: somaxconn=65535, tcp_keepalive optimizado
- **Health checks**: Intervalos más frecuentes (5-10s)

---

## ⚙️ Optimizaciones PHP

### Configuración Principal (`docker/php/php.ini`)
- **memory_limit**: 1024M (2x incremento)
- **realpath_cache_size**: 32768K (4x incremento para SSD)
- **realpath_cache_ttl**: 10800s (3 horas)
- **max_input_vars**: 10000 (2x incremento)
- **output_buffering**: 16384 (4x incremento)
- **Sesiones**: Configuradas para Redis
- **Seguridad**: Funciones peligrosas deshabilitadas

### OPcache (`docker/php/opcache.ini`)
- **memory_consumption**: 1024MB (2x incremento)
- **interned_strings_buffer**: 64MB (2x incremento)
- **max_accelerated_files**: 50000 (1.7x incremento)
- **JIT**: Habilitado con 256MB buffer
- **File cache**: Configurado para SSD
- **validate_timestamps**: Deshabilitado para máximo rendimiento

### PHP-FPM Pool (`docker/php-fpm/pool.conf`)
- **pm.max_children**: 75 (optimizado para 3 cores)
- **pm.start_servers**: 25
- **pm.min_spare_servers**: 15
- **pm.max_spare_servers**: 35
- **pm.max_requests**: 1000
- **Logging**: Mejorado con slow query log
- **Límites**: 65536 file descriptors

---

## 🔧 Optimizaciones Laravel

### Variables de Entorno
- **APP_DEBUG**: false (producción)
- **LOG_LEVEL**: warning
- **Cache drivers**: Redis para todo
- **Database**: Timeouts optimizados
- **OPcache**: Validación deshabilitada

### Optimizaciones de Cache
- **Prefix**: kraftdo_staging
- **Paths**: Configurados para tmpfs
- **Queue**: Redis con balance automático

---

## 📊 Monitoreo y Scripts

### Scripts Incluidos
1. **`scripts/setup-optimization.sh`**: Configura directorios y límites del sistema
2. **`scripts/monitor-performance.sh`**: Monitorea rendimiento en tiempo real

### Métricas Clave a Monitorear
- **CPU usage**: Debe mantenerse < 80%
- **Memory usage**: Debe mantenerse < 85%
- **OPcache hit rate**: Debe ser > 95%
- **Redis memory**: Debe mantenerse < 768MB
- **PHP-FPM active processes**: Debe mantenerse < 75

---

## 🚀 Proceso de Deployment

### 1. Preparación del Sistema
```bash
# Ejecutar script de optimización (solo primera vez)
./scripts/setup-optimization.sh
```

### 2. Backup y Deploy
```bash
# El backup ya fue creado automáticamente
ls docker-compose.staging.yml.backup

# Hacer deploy normal con GitHub Actions
git add .
git commit -m "Apply performance optimizations for 1500-2000 concurrent users"
git push
```

### 3. Verificación Post-Deploy
```bash
# Monitorear rendimiento
./scripts/monitor-performance.sh

# Verificar logs
docker logs kraftdo-nfc-staging-web --tail 50
docker logs kraftdo-nfc-staging-redis --tail 20
```

---

## 📈 Resultados Esperados

### Antes vs Después
| Métrica | Antes | Después | Mejora |
|---------|-------|---------|--------|
| Usuarios concurrentes | ~500 | 1500-2000 | 3-4x |
| Memory limit PHP | 512MB | 1024MB | 2x |
| OPcache memory | 512MB | 1024MB | 2x |
| PHP-FPM processes | 20 | 75 | 3.75x |
| Redis memory | 512MB | 768MB | 1.5x |
| Realpath cache | 8MB | 32MB | 4x |

### Beneficios Clave
- ✅ **Rendimiento**: 3-4x más usuarios concurrentes
- ✅ **Estabilidad**: Menos memory exhaustion
- ✅ **Velocidad**: OPcache y JIT optimizados
- ✅ **Monitoreo**: Scripts de performance incluidos
- ✅ **Seguridad**: Configuraciones hardened

---

## ⚠️ Consideraciones Importantes

1. **Monitoreo**: Usar `monitor-performance.sh` regularmente
2. **Backup**: Siempre mantener `docker-compose.staging.yml.backup`
3. **Escalabilidad**: Si necesitas >2000 usuarios, considera horizontal scaling
4. **Logs**: Revisar logs regularmente para detectar bottlenecks

---

## 🆘 Rollback

Si necesitas volver a la configuración anterior:
```bash
cp docker-compose.staging.yml.backup docker-compose.staging.yml
git add docker-compose.staging.yml
git commit -m "Rollback performance optimizations"
git push
```