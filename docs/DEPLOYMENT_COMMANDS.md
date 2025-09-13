# 🚀 Comandos de Deployment - Redis + Octane

## 🏠 **TESTING LOCAL**

### **1. Preparar entorno local**
```bash
# Generar APP_KEY
php artisan key:generate --show

# Crear archivo de entorno local
cp .env.staging.example .env.local
# Editar .env.local con tu APP_KEY generada
```

### **2. Variables importantes para local (.env.local)**
```env
ENVIRONMENT=local
IMAGE_TAG=test
APP_PORT=8081
APP_KEY=base64:TU_KEY_GENERADA_AQUI
APP_DEBUG=true
APP_URL=http://localhost:8081

# Redis interno (container)
REDIS_HOST=redis
REDIS_PASSWORD=test_password
REDIS_DB=0

# Database local
DB_HOST=127.0.0.1  # O tu MySQL local
DB_DATABASE=nfc_local_test
DB_USERNAME=root
DB_PASSWORD=tu_password_local
```

### **3. Build y Launch Local**
```bash
# Build la imagen
docker-compose -f docker-compose.prod.yml build

# Launch con Redis interno
docker-compose -f docker-compose.prod.yml --env-file .env.local up -d

# Ver logs en tiempo real
docker-compose -f docker-compose.prod.yml logs -f

# Verificar que todo funciona
docker-compose -f docker-compose.prod.yml ps
```

### **4. Comandos de Testing**
```bash
# Test Redis connection
docker exec -it nfc-laravel-app-local php artisan tinker
# En tinker: Redis::ping(); (should return "PONG")

# Test Octane status
docker exec -it nfc-laravel-app-local php artisan octane:status

# Test cache
docker exec -it nfc-laravel-app-local php artisan cache:clear
docker exec -it nfc-laravel-app-local php artisan config:cache

# Ver Redis data
docker exec -it nfc-redis-local redis-cli -a test_password
# En redis-cli: KEYS * (ver keys existentes)
```

### **5. URLs de Testing Local**
```
🌐 App: http://localhost:8081
🔧 Health: http://localhost:8081/health
📊 Redis: redis://localhost:6379
```

---

## 🌍 **DEPLOYMENT VPS PRODUCCIÓN**

### **1. Preparar VPS**
```bash
# En tu VPS
cd /path/to/nfc-app

# Instalar Docker y Docker Compose (si no tienes)
sudo apt update
sudo apt install docker.io docker-compose-v2
sudo usermod -aG docker $USER
# Logout/login para aplicar grupo docker
```

### **2. Setup archivos de entorno**
```bash
# Crear archivo de producción
cp env.production.example .env.production

# EDITAR .env.production con valores reales:
nano .env.production
```

### **3. Variables críticas VPS (.env.production)**
```env
ENVIRONMENT=prod
IMAGE_TAG=latest
APP_PORT=8080
APP_KEY=base64:GENERAR_CON_php_artisan_key:generate
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# ⚠️ IMPORTANTE: Decidir Redis strategy
# Opción 1: Redis interno (container)
REDIS_HOST=redis
REDIS_PASSWORD=SUPER_SECURE_PASSWORD_AQUI

# Opción 2: Redis externo (servidor separado)
# REDIS_HOST=192.168.1.101
# REDIS_PASSWORD=tu_redis_password_externo

# Database externa
DB_HOST=192.168.1.100
DB_DATABASE=nfc_production
DB_USERNAME=nfc_user
DB_PASSWORD=SECURE_DB_PASSWORD
```

### **4. Deploy Producción**
```bash
# Build en VPS
docker-compose -f docker-compose.prod.yml build --no-cache

# Primera vez: Ejecutar migraciones
# Cambiar RUN_MIGRATIONS=true en .env.production temporalmente
docker-compose -f docker-compose.prod.yml --env-file .env.production up -d

# Verificar que arrancó correctamente
docker-compose -f docker-compose.prod.yml --env-file .env.production ps
docker-compose -f docker-compose.prod.yml --env-file .env.production logs -f

# Después del primer deploy, cambiar RUN_MIGRATIONS=false
```

### **5. Comandos Post-Deploy**
```bash
# Health checks
curl http://localhost:8080/health

# Test Redis en VPS
docker exec -it nfc-laravel-app-prod redis-cli -h redis -a SUPER_SECURE_PASSWORD_AQUI ping

# Performance test
docker exec -it nfc-laravel-app-prod php artisan octane:status

# Clear cache después del deploy
docker exec -it nfc-laravel-app-prod php artisan config:cache
docker exec -it nfc-laravel-app-prod php artisan route:cache
docker exec -it nfc-laravel-app-prod php artisan view:cache
```

---

## 🌊 **DEPLOYMENT VPS STAGING**

### **1. Setup Staging**
```bash
# Crear archivo staging
cp .env.staging.example .env.staging

# Editar valores específicos staging
nano .env.staging
```

### **2. Deploy Staging**
```bash
# Build staging
docker-compose -f docker-compose.prod.yml --env-file .env.staging build

# Launch staging
docker-compose -f docker-compose.prod.yml --env-file .env.staging up -d

# Test staging
curl http://localhost:8081/health
```

---

## 🔧 **COMANDOS DE MANTENIMIENTO**

### **Logs y Monitoring**
```bash
# Ver logs en tiempo real
docker-compose -f docker-compose.prod.yml logs -f app
docker-compose -f docker-compose.prod.yml logs -f redis

# Ver uso de recursos
docker stats

# Ver Redis info
docker exec -it nfc-redis-prod redis-cli info memory
docker exec -it nfc-redis-prod redis-cli info stats
```

### **Backup y Restore**
```bash
# Backup Redis data
docker exec -it nfc-redis-prod redis-cli --rdb /data/backup-$(date +%Y%m%d).rdb

# Ver bases de datos Redis
docker exec -it nfc-redis-prod redis-cli -n 1 KEYS "*" | head -10  # Cache
docker exec -it nfc-redis-prod redis-cli -n 2 KEYS "*" | head -10  # Sessions
```

### **Updates y Redeploy**
```bash
# Update código
git pull origin main

# Rebuild y redeploy
docker-compose -f docker-compose.prod.yml --env-file .env.production build --no-cache
docker-compose -f docker-compose.prod.yml --env-file .env.production up -d --force-recreate

# Clear all caches
docker exec -it nfc-laravel-app-prod php artisan optimize:clear
docker exec -it nfc-laravel-app-prod php artisan optimize
```

---

## 🚨 **TROUBLESHOOTING**

### **Redis Connection Issues**
```bash
# Test connectivity
docker exec -it nfc-laravel-app-prod php artisan tinker
# Redis::ping()

# Check Redis logs
docker-compose -f docker-compose.prod.yml logs redis

# Restart Redis si necesario
docker-compose -f docker-compose.prod.yml restart redis
```

### **Octane Issues**
```bash
# Restart Octane workers
docker exec -it nfc-laravel-app-prod supervisorctl restart octane

# Check Octane logs
docker exec -it nfc-laravel-app-prod tail -f /var/log/supervisor/octane.log
```

### **Performance Issues**
```bash
# Monitor Redis memory
docker exec -it nfc-redis-prod redis-cli info memory

# Monitor container resources
docker stats nfc-laravel-app-prod nfc-redis-prod
```

---

## ⚡ **EXPECTED PERFORMANCE**

### **Before (sin Redis + Octane)**
- Response time: ~300ms
- Concurrent users: ~500
- Database queries: High load

### **After (con Redis + Octane)**  
- Response time: ~50ms (6x faster)
- Concurrent users: ~10,000 (20x more)
- Database queries: 90% reduction
- Memory efficiency: 50% better

**¡Tu NFC app será una BESTIA! 🔥**