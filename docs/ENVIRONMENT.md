# 🔧 Environment Configuration Guide

## 📝 **Environment Files Overview**

Este proyecto utiliza múltiples archivos de entorno para diferentes configuraciones:

### ✅ **Files to Use:**
- `.env.example` - Template con todas las variables disponibles
- `.env` - Tu configuración local de desarrollo  
- `.env.production` - Template para producción (no commitear)

### ❌ **Files Ignored by Git:**
Todos los archivos `.env*` están en `.gitignore` por seguridad:
```
.env
.env.*
.env.local
.env.production
.env.staging
.env.development
.env.backup
.env.dual
```

## 🚀 **Setup Instructions**

### **1. Local Development Setup:**
```bash
# Copy example file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database and Redis
# Edit .env with your local values
```

### **2. Docker Development Setup:**
```bash
# Use dual configuration
cp .env.example .env.dual

# Edit .env.dual for Docker environment
# Default Redis host: redis
# Default DB: external MySQL
```

### **3. Production Setup:**
```bash
# Use production template
cp .env.production .env

# Edit with production values:
# - External MySQL credentials
# - Redis server details
# - Secure session settings
# - Performance optimizations
```

## 🔐 **Security Best Practices**

### **Critical Settings for Production:**
```bash
APP_ENV=production
APP_DEBUG=false
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
DB_CONNECTION=mysql  # NOT sqlite
```

### **Database Configuration:**
- ✅ **Production**: External MySQL (recommended)
- ⚠️ **Development**: SQLite for quick setup
- 🔒 **Always**: Use strong passwords

### **Caching Strategy:**
- ✅ **Production**: Redis for all caching
- ⚠️ **Development**: File cache acceptable
- 🚀 **Performance**: Separate Redis databases

## 📋 **Environment Variables Reference**

### **Database:**
```bash
DB_CONNECTION=mysql|sqlite
DB_HOST=your-mysql-host
DB_PORT=3306
DB_DATABASE=kraftdo_nfc
DB_USERNAME=your-username
DB_PASSWORD=your-secure-password
```

### **Redis:**
```bash
REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
REDIS_DB=0              # Default
REDIS_CACHE_DB=1        # Cache
REDIS_SESSION_DB=2      # Sessions
REDIS_QUEUE_DB=3        # Queues
```

### **Laravel Octane:**
```bash
OCTANE_SERVER=swoole
OCTANE_WORKERS=auto     # Production: auto
OCTANE_MAX_REQUESTS=1000  # Production: higher
```

### **Docker:**
```bash
APP_PORT=8082          # External port
```

## ⚠️ **Important Notes**

1. **NEVER commit `.env` files** - They contain sensitive data
2. **Always use `.env.example`** as template for new environments  
3. **Test configuration** before deploying to production
4. **Use external MySQL** for production (SQLite is development only)
5. **Enable Redis** for production performance

## 🔍 **Troubleshooting**

### **Common Issues:**
```bash
# Clear config cache after changes
php artisan config:clear
php artisan config:cache

# Check current configuration
php artisan config:show database
php artisan config:show cache

# Test Redis connection
php artisan tinker
>>> Redis::ping()

# Test database connection  
>>> DB::connection()->getPdo()
```

### **Docker Issues:**
```bash
# Restart containers after .env changes
docker compose -f docker-compose.dual.yml restart

# Check environment in container
docker compose -f docker-compose.dual.yml exec app env | grep -E "(DB_|REDIS_)"
```