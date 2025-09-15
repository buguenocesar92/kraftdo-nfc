# 📚 KraftDo NFC - Documentación Completa

> **Centro de documentación para KraftDo NFC - Plataforma de contenido dinámico NFC**

## 📋 **Índice de Documentación**

### **🚀 Para Desarrolladores**

- **[Flujo de Desarrollo](./README-DESARROLLO.md)** - Guía completa del workflow de desarrollo y deployment
- **[Guía de Contribución](./CONTRIBUTING.md)** - Estándares de código, testing, y proceso de PRs
- **[Deployment en Producción](./DEPLOYMENT.md)** - Configuración y deployment al VPS

### **🔒 Seguridad y Compliance**

- **[Política de Seguridad](./SECURITY.md)** - Reporte de vulnerabilidades y medidas de seguridad
- **[Historial de Cambios](./CHANGELOG.md)** - Tracking de versiones y releases

### **📊 Referencias Técnicas**

- **[Configuración Docker](./README.docker.md)** - Guía detallada de Docker y contenedores
- **[Optimización Redis](./REDIS_CACHE_OPTIMIZATION.md)** - Configuración y optimización de Redis
- **[Suite de Testing](./TESTING_SUITE_COMPLETE.md)** - Tests completos y coverage
- **[Configuración de Entornos](../docker-compose.dev.yml)** - Docker Compose para desarrollo
- **[Configuración de Producción](../docker-compose.prod.yml)** - Docker Compose para producción
- **[Variables de Entorno](../.env.example)** - Template de configuración
- **[Makefile](../Makefile)** - Comandos automatizados

---

## 🎯 **Inicio Rápido**

### **Para Nuevos Desarrolladores:**
1. Lee **[Flujo de Desarrollo](./README-DESARROLLO.md)** 
2. Revisa **[Guía de Contribución](./CONTRIBUTING.md)**
3. Ejecuta `make install` en tu ambiente local

### **Para Deployment:**
1. Consulta **[Deployment en Producción](./DEPLOYMENT.md)**
2. Configura variables según **[Variables de Entorno](../.env.prod.example)**
3. Ejecuta `make deploy-prod`

### **Para Seguridad:**
1. Lee **[Política de Seguridad](./SECURITY.md)**
2. Reporta vulnerabilidades a: security@kraftdo.cl
3. Mantente actualizado con **[Changelog](./CHANGELOG.md)**

---

## 📖 **Estructura de Documentación**

```
docs/
├── INDEX.md                          # Este índice
├── README-DESARROLLO.md              # Flujo completo de desarrollo
├── CONTRIBUTING.md                   # Guía para contribuidores
├── SECURITY.md                       # Políticas de seguridad
├── DEPLOYMENT.md                     # Guía de deployment
├── CHANGELOG.md                      # Historial de versiones
├── README.docker.md                  # Configuración Docker detallada
├── REDIS_CACHE_OPTIMIZATION.md      # Optimización Redis
└── TESTING_SUITE_COMPLETE.md        # Suite completa de testing
```

---

## 🔄 **Flujos Documentados**

### **Development Workflow:**
```
Desarrollo Local → GitHub PR → CI/CD → Staging/Producción
```

### **Testing Strategy:**
```
Unit Tests (90%) → Feature Tests → Quality Checks → Security Scan
```

### **Deployment Process:**
```
Build → Test → Security → Deploy → Health Check → Monitor
```

---

## 🛠️ **Herramientas y Comandos**

### **Comandos Más Usados:**
```bash
make dev                # Desarrollo local
make test-full         # Suite completa de tests  
make quality-check     # Verificación de calidad
make deploy-prod       # Deploy a producción
make health           # Verificar estado del sistema
```

### **Scripts Automatizados:**
- **`make install`** - Setup inicial completo
- **`make fix-all`** - Corregir problemas automáticamente
- **`deploy-prod.sh`** - Script de deployment con backups

---

## 📊 **Métricas y Estándares**

### **Calidad de Código:**
- ✅ **Coverage mínimo**: 90% en producción, 80% en desarrollo
- ✅ **Static Analysis**: PHPStan nivel 8
- ✅ **Code Style**: PSR-12 + Laravel conventions
- ✅ **Security**: CodeQL + Dependency scanning

### **Performance Targets:**
- ✅ **Response time**: < 200ms promedio
- ✅ **Memory usage**: < 512MB por container
- ✅ **Cache hit ratio**: > 95%
- ✅ **Uptime**: 99.9%

---

## 🔐 **Información Propietaria**

> **⚠️ CONFIDENCIAL**: Esta documentación contiene información propietaria de KraftDo SpA. 
> Solo para uso autorizado por empleados, contratistas y socios.

### **Licencia:**
- **Tipo**: Propietario (no open source)
- **Copyright**: © 2025 KraftDo SpA
- **Contacto legal**: legal@kraftdo.cl

### **Acceso:**
- **Repositorio**: Privado en GitHub
- **Colaboradores**: Solo autorizados
- **NDA**: Puede ser requerido

---

## 📞 **Contacto y Soporte**

### **Desarrollo:**
- **Email**: dev@kraftdo.cl
- **GitHub Issues**: Para bugs y features
- **GitHub Discussions**: Para preguntas generales

### **Emergencias:**
- **Hotfix**: Crear issue con label "critical"
- **Security**: security@kraftdo.cl
- **Infraestructura**: sysadmin@kraftdo.cl

### **Comercial:**
- **General**: info@kraftdo.cl
- **Legal**: legal@kraftdo.cl
- **Web**: https://kraftdo.cl

---

## 🚀 **Estado del Proyecto**

- **Versión actual**: 1.0.0
- **Última actualización**: 2025-01-15
- **Estado**: ✅ Producción
- **Cobertura de tests**: 86%+
- **Deployment**: Automático con CI/CD

---

**📱 KraftDo NFC - Plataforma moderna de contenido dinámico NFC**  
**🚀 Desarrollado con ❤️ por el equipo KraftDo**