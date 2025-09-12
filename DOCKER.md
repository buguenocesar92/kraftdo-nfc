# Configuración Docker Consolidada - NFC Laravel App

## 📋 Estructura Simplificada

**Archivos Docker consolidados:**
- `Dockerfile` - Multi-stage optimizado para producción/staging
- `Dockerfile.local` - Configuración para desarrollo local
- `docker-compose.prod.yml` - Producción Y staging (con variables de entorno)
- `docker-compose.local.yml` - Desarrollo local con BD externa

## Mejoras Implementadas

### 🚀 Dockerfile Principal
- **Multi-stage build optimizado** con capas separadas para dependencias PHP, assets Node.js y imagen final
- **Extensiones PHP adicionales**: Redis, PostgreSQL, intl, soap, bcmath, pcntl, posix
- **Optimizaciones de seguridad**: Configuraciones adicionales de PHP, headers mejorados
- **Health check integrado**: Endpoint `/health` para verificación de estado
- **Soporte para Brotli** y compresión avanzada

### 🔧 Configuraciones PHP
- **php.ini optimizado**: Memoria 512MB, timeouts 120s, buffers mejorados
- **OPcache avanzado**: JIT habilitado, preload configurado, 512MB memoria
- **Configuración para desarrollo**: php-dev.ini con debugging habilitado
- **Sesiones seguras**: SameSite strict, HttpOnly, configuración avanzada

### 🌐 Nginx Optimizado
- **Rate limiting**: Zonas diferenciadas para login, API y general
- **Logging estructurado**: JSON y formato estándar con métricas de rendimiento
- **Compresión avanzada**: Gzip optimizado, soporte Brotli preparado
- **Security headers**: HSTS, CSP, Referrer Policy, Permissions Policy
- **Configuraciones específicas** para producción y staging

### 🐳 Docker Compose Consolidado
#### Producción/Staging (`docker-compose.prod.yml`)
- **Configuración unificada**: Un solo archivo para prod y staging usando variables
- **Variables validadas**: Campos requeridos marcados con `:?error`
- **Recursos optimizados**: 1GB RAM, 1 CPU, políticas de restart
- **Volúmenes nombrados**: Separación automática por entorno
- **Health checks**: Verificación cada 30s con /health endpoint
- **Logging estructurado**: JSON con rotación y compresión
- **Redes separadas**: prod (172.20.0.0/16) y staging (172.21.0.0/16)

#### Local (`docker-compose.local.yml`)
- **Variables de entorno dinámicas**: Soporte para .env.docker.local
- **Host flexible**: `host.docker.internal` por defecto
- **Configuración segura**: APP_KEY y credenciales no hardcodeadas

### 📜 Scripts de Entrada Mejorados
#### `entrypoint.sh` (Producción)
- **Validaciones robustas**: Verificación de APP_KEY, permisos, conectividad
- **Health checks**: Verificación de servicios antes del inicio
- **Optimizaciones automáticas**: Config cache, route cache, autoloader
- **Error handling**: Manejo de errores con códigos de salida apropiados
- **Timeouts configurables**: Reintentos con backoff exponencial

#### `entrypoint-local.sh` (Desarrollo)
- **IP dinámica**: Soporte para diferentes entornos de desarrollo
- **Migraciones automáticas**: Ejecuta migraciones y seeders en local
- **Assets building**: Compilación automática de assets

### 🏥 Health Check
- **Endpoint dedicado**: `/health` con verificaciones completas
- **Verificaciones múltiples**: Laravel, storage, base de datos, Redis
- **Métricas incluidas**: Uso de memoria, versión PHP, timestamps
- **Respuestas estructuradas**: JSON con códigos HTTP apropiados

## Uso

### Desarrollo Local
```bash
# Crear archivo de configuración
cp .env.docker.local.example .env.docker.local
# Editar variables según tu entorno
vim .env.docker.local

# Iniciar contenedor
docker-compose -f docker-compose.local.yml --env-file .env.docker.local up
```

### Staging
```bash
# Crear archivo de configuración para staging
cp .env.staging.example .env.staging
# Editar variables según tu entorno de staging
vim .env.staging

# Desplegar staging
docker-compose -f docker-compose.prod.yml --env-file .env.staging up -d
```

### Producción
```bash
# Usar archivo .env.production con variables reales
cp env.production.example .env.production
# Editar variables según tu entorno de producción
vim .env.production

# Desplegar producción (puerto 8080 por defecto)
docker-compose -f docker-compose.prod.yml --env-file .env.production up -d
```

## Verificación

### Health Check
```bash
curl http://localhost:8080/health
```

### Logs
```bash
# Logs de aplicación
docker-compose logs -f app

# Logs específicos
docker exec -it container_name tail -f /var/log/nginx/access.log
docker exec -it container_name tail -f /var/log/php_errors.log
```

### Métricas
```bash
# Verificar OPcache
docker exec -it container_name php -i | grep opcache

# Verificar extensiones
docker exec -it container_name php -m
```

## Variables de Entorno Importantes

### Requeridas para Producción
- `APP_KEY`: Clave de aplicación Laravel
- `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: Credenciales de BD
- `REDIS_HOST`: Host de Redis (si se usa cache/sessions con Redis)

### Opcionales
- `RUN_MIGRATIONS`: true/false para ejecutar migraciones
- `RUN_SEEDERS`: true/false para ejecutar seeders
- `CONFIG_CACHE`, `ROUTE_CACHE`, `VIEW_CACHE`: Control de cache Laravel
- `LOG_LEVEL`: error/warning/info/debug
- `REDIS_PASSWORD`: Contraseña de Redis si es necesario

### Configuraciones de Rendimiento
- `MAX_RETRIES`: Número máximo de reintentos para conexiones (default: 30)
- `RETRY_INTERVAL`: Intervalo entre reintentos en segundos (default: 2)
- `HEALTH_CHECK_TIMEOUT`: Timeout para health checks en segundos (default: 5)

## Optimizaciones de Seguridad

1. **No root**: Todos los procesos corren como usuario `www`
2. **Secrets management**: Variables sensibles no hardcodeadas
3. **Headers de seguridad**: HSTS, CSP, frame options
4. **Rate limiting**: Protección contra ataques de fuerza bruta
5. **Permisos mínimos**: Solo los archivos necesarios son escribibles
6. **Health checks**: Verificación continua de estado de servicios

## Troubleshooting

### Problema: Container no inicia
```bash
# Verificar logs
docker-compose logs app

# Verificar configuración
docker-compose config
```

### Problema: No conecta a base de datos
```bash
# Verificar conectividad desde container
docker exec -it container_name nc -zv $DB_HOST $DB_PORT

# Verificar variables
docker exec -it container_name env | grep DB_
```

### Problema: Permisos de archivos
```bash
# Recrear container con permisos correctos
docker-compose down
docker-compose up --build
```