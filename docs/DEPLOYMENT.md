# 🚀 KraftDo NFC - Guía de Deployment de Producción

Esta guía describe cómo desplegar KraftDo NFC en producción usando las herramientas optimizadas del proyecto.

## 📋 Prerequisitos

### Servidor de Producción
- **Docker** 20.10+ y **Docker Compose** v2+
- **Nginx** o proxy reverso (opcional, FrankenPHP incluye servidor web)
- **SSL/TLS** certificados configurados
- **Dominio** apuntando al servidor

### Base de Datos Externa
- **MySQL** 8.0+ configurada y accesible
- **Redis** para cache y sesiones (recomendado externo para HA)

## 🛠️ Configuración Inicial

### 1. Clonar y Configurar Proyecto

```bash
# Clonar repositorio
git clone <repository-url> kraftdo-nfc-prod
cd kraftdo-nfc-prod

# Configurar permisos
chmod +x deploy-prod.sh
chmod +x docker/entrypoint-simple.sh
```

### 2. Configurar Variables de Entorno

```bash
# Copiar template de producción
cp .env.prod.example .env.prod

# Editar configuración (IMPORTANTE: configurar todos los valores)
nano .env.prod
```

#### Variables Críticas a Configurar:

```bash
# OBLIGATORIAS - El deployment fallará sin estas
APP_KEY=base64:tu-clave-secreta-de-32-caracteres
APP_URL=https://tu-dominio.com
SESSION_DOMAIN=tu-dominio.com
REDIS_PASSWORD=contraseña-redis-segura

# BASE DE DATOS EXTERNA
DB_HOST=192.168.100.20
DB_DATABASE=kraftdo
DB_USERNAME=docker
DB_PASSWORD=tu-password-seguro

# SSL/TLS
SERVER_NAME=tu-dominio.com:443
SSL_CERT_PATH=/path/to/cert.pem
SSL_KEY_PATH=/path/to/key.pem
```

## 🚀 Métodos de Deployment

### Opción 1: Deployment Automatizado (Recomendado)

El script `deploy-prod.sh` maneja todo el proceso automáticamente:

```bash
# Deployment completo con script automatizado
make deploy-prod

# O ejecutar directamente
./deploy-prod.sh
```

**El script automáticamente:**
- ✅ Verifica prerequisitos y variables
- ✅ Crea backups de la instalación actual
- ✅ Construye imágenes optimizadas
- ✅ Despliega servicios con healthchecks
- ✅ Optimiza Laravel para producción
- ✅ Ejecuta migraciones (si se confirma)
- ✅ Verifica el health de la aplicación

### Opción 2: Deployment Rápido

Para deployments rápidos sin interacciones:

```bash
# Deployment sin preguntas interactivas
make deploy-prod-quick
```

### Opción 3: Deployment Manual con Makefile

```bash
# Construir imágenes
make prod-build-optimized

# Iniciar servicios
make prod

# Optimizar aplicación
make optimize
```

## 🔧 Comandos de Gestión de Producción

### Monitoreo y Verificación

```bash
# Verificar estado del deployment
make deploy-prod-check

# Ver logs en tiempo real
make deploy-prod-logs

# Monitorear recursos
make monitor

# Health check completo
make health
```

### Acceso y Mantenimiento

```bash
# Acceder al shell de producción
make deploy-prod-shell

# Ejecutar comandos Artisan
make artisan-migrate
make artisan-queue:work

# Optimizaciones manuales
make cache-all
make optimize
```

### Backup y Seguridad

```bash
# Backup específico de producción
make deploy-prod-backup

# Parar servicios de producción
make deploy-prod-down
```

## 🏗️ Arquitectura de Producción

### Servicios Desplegados

1. **php-fpm**: Backend Laravel optimizado
   - PHP 8.3 con extensiones optimizadas
   - FPM pool configurado para alta concurrencia
   - PCOV para profiling opcional

2. **frankenphp**: Proxy y servidor web
   - HTTP/2 y HTTP/3 habilitado
   - SSL/TLS automático con Caddy
   - Compresión y cache de assets

3. **redis**: Cache distribuido
   - Persistencia configurada (AOF + RDB)
   - Password protegido
   - Límites de memoria configurables

4. **worker**: Procesamiento de colas
   - Múltiples workers configurables
   - Retry automático con backoff
   - Monitoreo de salud

5. **scheduler**: Tareas programadas
   - Cron jobs de Laravel
   - Limpieza automática de cache
   - Maintenance tasks

6. **monitoring** (opcional): Métricas y salud
   - Health checks personalizados
   - Métricas de performance
   - Alertas configurables

### Red y Volúmenes

- **Red aislada**: `172.30.0.0/24` por defecto
- **Volúmenes persistentes**: Redis, storage, bootstrap cache
- **Bind mounts**: Configurables para NFS/GlusterFS

## 🔒 Seguridad

### Configuraciones de Seguridad

- **HTTPS forzado** por defecto
- **Headers de seguridad** habilitados
- **Rate limiting** en proxy
- **Redis con autenticación**
- **Variables sensibles** validadas

### Backup Strategy

```bash
# Backup automático antes de cada deployment
./deploy-prod.sh  # Incluye backup automático

# Backups manuales programados
# Configurar en crontab del servidor:
0 2 * * * cd /path/to/kraftdo-nfc-prod && make deploy-prod-backup
```

## 🚨 Troubleshooting

### Problemas Comunes

#### 1. Health Check Falla
```bash
# Verificar logs
make deploy-prod-logs

# Verificar servicios individualmente
docker-compose -f docker-compose.prod.yml ps
```

#### 2. Variables de Entorno Faltantes
```bash
# El script mostrará exactamente qué variables faltan
./deploy-prod.sh
```

#### 3. SSL/TLS Issues
```bash
# Verificar certificados
openssl x509 -in /path/to/cert.pem -text -noout

# Verificar configuración de Caddy
make deploy-prod-shell
cat /etc/caddy/Caddyfile
```

#### 4. Database Connection Issues
```bash
# Test de conexión manual
make deploy-prod-shell
php artisan db:monitor
```

### Logs y Debugging

```bash
# Logs completos de todos los servicios
docker-compose -f docker-compose.prod.yml logs -f

# Logs específicos por servicio
docker-compose -f docker-compose.prod.yml logs -f php-fpm
docker-compose -f docker-compose.prod.yml logs -f frankenphp
docker-compose -f docker-compose.prod.yml logs -f redis
```

## 📊 Monitoring y Performance

### Métricas Clave

- **Response time**: < 200ms promedio
- **Memory usage**: < 512MB por container PHP
- **Redis hit ratio**: > 95%
- **Queue processing**: < 30s promedio

### Herramientas de Monitoreo

```bash
# Performance en tiempo real
make performance

# Benchmark de carga
make benchmark

# Stats de Docker
make monitor
```

## 🔄 Rollback

En caso de problemas, hacer rollback rápido:

```bash
# Rollback automático usando backups
make rollback

# Rollback manual
docker-compose -f docker-compose.prod.yml down
# Restaurar backup específico
# Reiniciar servicios
```

## 📞 Soporte

Para issues específicos de deployment:

1. Verificar logs: `make deploy-prod-logs`
2. Verificar salud: `make deploy-prod-check`
3. Ejecutar debug: `make debug`
4. Reportar issue con logs completos

---

## ✅ Checklist de Deployment

- [ ] **.env.prod** configurado con todas las variables requeridas
- [ ] **SSL/TLS** certificados válidos
- [ ] **Base de datos externa** accesible y configurada
- [ ] **Redis externo** (recomendado) o contenedor Redis configurado
- [ ] **Dominio** apuntando al servidor
- [ ] **Firewall** configurado (puertos 80, 443, 6379)
- [ ] **Backup strategy** configurada
- [ ] **Monitoring** configurado (opcional)
- [ ] **Tests** pasando: `make test-full`
- [ ] **Performance** verificado: `make benchmark`

¡Deployment listo para producción! 🚀