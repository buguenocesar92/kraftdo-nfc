# 🎯 KraftDo NFC

[![CI/CD Pipeline](https://github.com/kraftdo/kraftdo-nfc/actions/workflows/deploy.yml/badge.svg)](https://github.com/kraftdo/kraftdo-nfc/actions)
[![Development Pipeline](https://github.com/kraftdo/kraftdo-nfc/actions/workflows/develop.yml/badge.svg)](https://github.com/kraftdo/kraftdo-nfc/actions)
[![codecov](https://codecov.io/gh/kraftdo/kraftdo-nfc/branch/main/graph/badge.svg)](https://codecov.io/gh/kraftdo/kraftdo-nfc)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-Proprietary-red.svg)](LICENSE)

> **Plataforma moderna de contenido dinámico NFC construida con Laravel 11, Filament 3, y Nginx + PHP-FPM para máximo rendimiento.**

## 🌟 **Características Principales**

- 🏷️ **Gestión de Tokens NFC** - Crear y gestionar contenido dinámico para tarjetas NFC
- 📊 **Analytics Avanzados** - Métricas detalladas de visualizaciones y engagement  
- 🎨 **Panel de Administración** - Interfaz moderna con Filament 3
- ⚡ **Alto Rendimiento** - Nginx + PHP-FPM optimizado
- 🔒 **Seguridad Robusta** - Autenticación, autorización y validación completa
- 📱 **Responsive Design** - Compatible con todos los dispositivos
- 🐳 **Containerizado** - Docker con configuraciones optimizadas  
- 🚀 **CI/CD Completo** - Pipelines automatizados con GitHub Actions

## 🌐 **Acceso Rápido**

- **Producción**: https://kraftdo.cl
- **Staging**: https://staging.kraftdo.cl  
- **Admin Panel**: `http://localhost:8080/admin`
- **Frontend**: `http://localhost:8080/`

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
- **Performance**: Nginx + PHP-FPM + Redis
- **Cache**: Redis
- **Database**: MySQL
- **Containers**: Docker + Nginx

## 📚 **Documentación**

Toda la documentación técnica está en la carpeta [`docs/`](./docs/):

- [🔧 Configuración de Entornos](./docs/ENVIRONMENT.md)
- [🐳 Configuración Docker](./docs/DOCKER.md)
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

```

## 📈 **Características**

- ✅ **NFC Content Management**: Gestión dinámica de contenido NFC
- ✅ **Multi-Content Types**: Perfiles, eventos, productos, menús, turismo
- ✅ **Real-time Performance**: Nginx + PHP-FPM + Redis
- ✅ **Admin Interface**: Filament panel con Livewire
- ✅ **Media Management**: Galería, audio, video
- ✅ **QR Code Generation**: Generación automática de códigos
- ✅ **Analytics**: Seguimiento de interacciones
- ✅ **Role-based Access**: Sistema de permisos
- ✅ **Mobile Optimized**: Responsive design

## 📚 **Documentación Completa**

Para información detallada sobre desarrollo, deployment y contribución:

- 📖 **[Índice de Documentación](./docs/INDEX.md)** - Centro completo de documentación
- 🚀 **[Flujo de Desarrollo](./docs/README-DESARROLLO.md)** - Workflow completo y comandos
- 🤝 **[Guía de Contribución](./docs/CONTRIBUTING.md)** - Estándares y proceso de PRs
- 🏭 **[Deployment en Producción](./docs/DEPLOYMENT.md)** - Setup y deployment al VPS
- 🔒 **[Política de Seguridad](./docs/SECURITY.md)** - Reporte de vulnerabilidades
- 📋 **[Historial de Cambios](./docs/CHANGELOG.md)** - Releases y versiones

## 🤝 **Contribuir**

1. Lee la **[Guía de Contribución](./docs/CONTRIBUTING.md)**
2. Revisa el **[Flujo de Desarrollo](./docs/README-DESARROLLO.md)**
3. Crea tu feature branch (`git checkout -b feature/amazing-feature`)
4. Ejecuta `make quality-check` antes del commit
5. Commit con formato estándar (`git commit -m 'feat: amazing new feature'`)
6. Push y crea Pull Request usando el template

## 📄 **Licencia y Propiedad**

Este proyecto es **software propietario** de **KraftDo SpA**.
- **Copyright**: © 2025 KraftDo SpA. Todos los derechos reservados.
- **Licencia**: Propietaria - Ver [LICENSE](LICENSE) para detalles completos
- **Uso autorizado**: Solo empleados, contratistas y socios de KraftDo

## 🆘 **Soporte**

- 📖 **Documentación**: [./docs/INDEX.md](./docs/INDEX.md)
- 🐛 **Issues**: [GitHub Issues](https://github.com/kraftdo/kraftdo-nfc/issues)
- 💬 **Discusiones**: [GitHub Discussions](https://github.com/kraftdo/kraftdo-nfc/discussions)
- 📧 **Email**: dev@kraftdo.cl
- 🔒 **Seguridad**: security@kraftdo.cl

---

**🚀 Desarrollado con ❤️ por el equipo KraftDo**