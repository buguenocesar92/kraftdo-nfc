## 🎯 **Descripción**

Describe brevemente qué cambios incluye este Pull Request y por qué son necesarios.

Fixes #(issue)

## 📋 **Tipo de Cambio**

- [ ] 🐛 Bug fix (cambio que arregla un problema)
- [ ] ✨ Nueva característica (cambio que agrega funcionalidad)
- [ ] 💥 Breaking change (fix o feature que causaría que funcionalidad existente no funcione como se esperaba)
- [ ] 📚 Documentación (cambios solo en documentación)
- [ ] 🔧 Configuración (cambios en configuración, CI/CD, etc.)
- [ ] ♻️ Refactoring (cambio de código que no corrige bug ni agrega característica)
- [ ] ⚡ Performance (mejora de rendimiento)
- [ ] 🧪 Tests (agregar tests faltantes o corregir tests existentes)

## 🧪 **Testing**

Describe las pruebas que ejecutaste para verificar tus cambios:

- [ ] Tests unitarios pasan (`make test-full`)
- [ ] Tests de integración pasan
- [ ] Verificado manualmente en desarrollo
- [ ] Verificado en staging (si aplica)

**Configuración de testing:**
* Entorno: [desarrollo/staging/otro]
* Navegadores probados: [Chrome/Firefox/Safari/Mobile]

## 📸 **Screenshots**

Si aplica, agrega screenshots que ayuden a explicar el cambio:

| Antes | Después |
|-------|---------|
| [screenshot] | [screenshot] |

## ✅ **Checklist**

### **Desarrollo**
- [ ] Mi código sigue las convenciones del proyecto
- [ ] He realizado self-review de mi código
- [ ] He comentado mi código en áreas complejas
- [ ] He actualizado la documentación correspondiente
- [ ] Mis cambios no generan nuevos warnings
- [ ] He agregado tests que prueban mi fix/feature
- [ ] Tests nuevos y existentes pasan localmente

### **Base de Datos** (si aplica)
- [ ] He creado/actualizado migraciones necesarias
- [ ] Las migraciones son reversibles (`down()` implementado)
- [ ] He probado las migraciones en ambiente limpio
- [ ] He actualizado seeders si es necesario

### **Frontend** (si aplica)
- [ ] Los cambios son responsivos (mobile/tablet/desktop)
- [ ] He compilado assets (`npm run build`)
- [ ] He probado en diferentes navegadores
- [ ] Las validaciones funcionan correctamente

### **API** (si aplica)
- [ ] He actualizado la documentación de API
- [ ] Los endpoints mantienen backward compatibility
- [ ] He agregado validación adecuada
- [ ] He probado todos los casos edge

### **Deployment**
- [ ] Los cambios son compatibles con el entorno de producción
- [ ] He verificado variables de entorno necesarias
- [ ] No hay secrets o información sensible en el código
- [ ] El deployment no requiere pasos manuales adicionales

## 🔄 **Migración/Breaking Changes**

Si este PR incluye breaking changes o requiere pasos de migración:

### **Pasos de Migración:**
1. [Describir paso 1]
2. [Describir paso 2]
3. [etc.]

### **Breaking Changes:**
- [Describir qué se rompería]
- [Cómo migrar código existente]

## 📊 **Impacto en Performance**

- [ ] No hay impacto esperado en performance
- [ ] Mejora la performance
- [ ] Podría impactar la performance (explicar abajo)

**Detalles del impacto:**
[Describir cualquier impacto en performance y mediciones si las tienes]

## 🔗 **Issues Relacionados**

- Relacionado con #[número]
- Dependiente de #[número]
- Bloquea #[número]

## 📝 **Notas Adicionales**

Cualquier información adicional que los reviewers deberían saber:

---

## 👥 **Para Reviewers**

### **Áreas de Enfoque:**
- [ ] Lógica de negocio
- [ ] Seguridad
- [ ] Performance  
- [ ] UI/UX
- [ ] Tests
- [ ] Documentación

### **Testing Sugerido:**
1. [Describir escenarios específicos para probar]
2. [Casos edge importantes]
3. [Integraciones a verificar]

---

**🚀 ¡Gracias por tu review! Tu feedback es valioso para mantener la calidad de KraftDo NFC.**