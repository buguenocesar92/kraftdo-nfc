# 🚀 KraftDo NFC - Dynamic Content Platform

Sistema dinámico de gestión de contenido NFC con arquitectura dual para máximo rendimiento y compatibilidad.

## ⚡ **Arquitectura Dual**

- **Panel Administrador**: PHP-FPM + Livewire (Estabilidad)
- **Frontend NFC**: Laravel Octane + Swoole (Alto Rendimiento)

## 🌐 **Acceso Rápido**

- **Admin Panel**: `http://localhost:8082/admin`
- **Frontend**: `http://localhost:8082/`

## 🚀 **Inicio Rápido**

```bash
# 1. Clonar repositorio
git clone <repository-url>
cd kraftdo-nfc

# 2. Configurar entorno
cp .env.example .env
php artisan key:generate

# 3. Levantar con Docker
docker compose -f docker-compose.dual.yml up -d

# 4. Ejecutar migraciones
docker compose -f docker-compose.dual.yml exec app php artisan migrate

# 5. Publicar assets de Livewire
docker compose -f docker-compose.dual.yml exec app php artisan livewire:publish --assets
```

## 📋 **Requisitos**

- **Base de Datos**: MySQL externa (recomendado para producción)
- **Cache**: Redis (recomendado)
- **PHP**: 8.3+
- **Docker**: Para desarrollo y producción

## 🏗️ **Stack Tecnológico**

- **Framework**: Laravel 12
- **Frontend**: Livewire v3 + Alpine.js
- **Admin**: Filament v4
- **Performance**: Laravel Octane + Swoole
- **Cache**: Redis
- **Database**: MySQL
- **Containers**: Docker + Nginx

## 📚 **Documentación**

Toda la documentación técnica está en la carpeta [`docs/`](./docs/):

- [🔧 Configuración de Entornos](./docs/ENVIRONMENT.md)
- [🐳 Configuración Docker](./docs/DOCKER.md)
- [🚀 Migración Octane](./docs/OCTANE_MIGRATION.md)
- [⚡ Redis + Octane Setup](./docs/REDIS_OCTANE_SETUP.md)
- [👨‍💻 Uso de Filament](./docs/FILAMENT_USAGE.md)
- [🎭 Tipos de Contenido](./docs/ADDING_NEW_CONTENT_TYPES.md)
- [🔐 Permisos de Tokens](./docs/TOKEN_PERMISSIONS.md)
- [🚀 Comandos de Deploy](./docs/DEPLOYMENT_COMMANDS.md)

## 🛠️ **Desarrollo**

### **Configuración Local:**
```bash
# Instalar dependencias
composer install
npm install

# Compilar assets
npm run dev

# Servir aplicación
php artisan serve --port=8082
```

### **Configuración Docker:**
```bash
# Desarrollo
docker compose -f docker-compose.dual.yml up -d

# Ver logs
docker compose -f docker-compose.dual.yml logs -f app

# Ejecutar comandos
docker compose -f docker-compose.dual.yml exec app php artisan [command]
```

## 🔧 **Configuración**

### **Variables de Entorno Importantes:**
```bash
# Base de datos (externa recomendada)
DB_CONNECTION=mysql
DB_HOST=your-mysql-host
DB_DATABASE=kraftdo_nfc

# Cache y sesiones
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=your-redis-host

# Octane
OCTANE_SERVER=swoole
OCTANE_WORKERS=auto
```

## 📈 **Características**

- ✅ **NFC Content Management**: Gestión dinámica de contenido NFC
- ✅ **Multi-Content Types**: Perfiles, eventos, productos, menús, turismo
- ✅ **Real-time Performance**: Laravel Octane + Swoole
- ✅ **Admin Interface**: Filament panel con Livewire
- ✅ **Media Management**: Galería, audio, video
- ✅ **QR Code Generation**: Generación automática de códigos
- ✅ **Analytics**: Seguimiento de interacciones
- ✅ **Role-based Access**: Sistema de permisos
- ✅ **Mobile Optimized**: Responsive design

## 🤝 **Contribuir**

1. Fork el proyecto
2. Crea tu feature branch (`git checkout -b feature/amazing-feature`)
3. Commit cambios (`git commit -m 'Add amazing feature'`)
4. Push al branch (`git push origin feature/amazing-feature`)
5. Abre un Pull Request

## 📄 **Licencia**

Este proyecto es propiedad de KraftDo.

## 🆘 **Soporte**

Para problemas técnicos o preguntas, consulta la [documentación](./docs/) o contacta al equipo de desarrollo.

---

**🚀 Desarrollado con ❤️ por el equipo KraftDo**