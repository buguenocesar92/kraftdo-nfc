# 📋 Guía de Uso del Panel de Filament

## 🎯 **Recursos Principales vs Especializados**

### **📱 Contenido Principal**

#### **DynamicContentResource** ⭐ (RECOMENDADO)
- **Ubicación**: `Contenido Principal > Contenido Dinámico`
- **Uso**: Para crear contenido NFC completo desde cero
- **Características**:
  - ✅ Formulario dinámico que cambia según el tipo
  - ✅ Solo muestra secciones relevantes al tipo seleccionado
  - ✅ Permite seleccionar contenido existente o crear nuevo
  - ✅ Gestión completa de todos los tipos de contenido

**Flujo recomendado**:
1. Ir a **Contenido Principal > Contenido Dinámico**
2. Seleccionar **Crear**
3. Elegir el **tipo** (GIFT, MENU, PROFILE, etc.)
4. Solo aparecerán las secciones relevantes
5. En cada sección puedes:
   - Seleccionar contenido existente del tipo apropiado
   - O crear contenido nuevo

---

### **🔧 Contenido Especializado**

#### **ContentGiftResource**
- **Ubicación**: `Contenido Especializado > Regalos`
- **Uso**: Para gestionar solo datos específicos de regalos
- **Limitaciones**: Solo campos de regalo + selector de contenido dinámico tipo GIFT

#### **ContentMenuResource**
- **Ubicación**: `Contenido Especializado > Restaurantes`
- **Uso**: Para gestionar solo datos de restaurantes y menús
- **Limitaciones**: Solo campos de menú + selector de contenido dinámico tipo MENU

#### **ContentProfileResource**
- **Ubicación**: `Contenido Especializado > Perfiles`
- **Uso**: Para gestionar solo datos de perfiles personales
- **Limitaciones**: Solo campos de perfil + selector de contenido dinámico tipo PROFILE

#### **ContentEventResource**
- **Ubicación**: `Contenido Especializado > Eventos`
- **Uso**: Para gestionar solo datos de eventos
- **Limitaciones**: Solo campos de evento + selector de contenido dinámico tipo EVENT

#### **ContentProductResource**
- **Ubicación**: `Contenido Especializado > Productos`
- **Uso**: Para gestionar solo datos de productos
- **Limitaciones**: Solo campos de producto + selector de contenido dinámico tipo PRODUCT

#### **ContentTouristResource**
- **Ubicación**: `Contenido Especializado > Lugares Turísticos`
- **Uso**: Para gestionar solo datos de lugares turísticos
- **Limitaciones**: Solo campos turísticos + selector de contenido dinámico tipo TOURIST

#### **ContentMultimediaResource**
- **Ubicación**: `Contenido Especializado > Multimedia`
- **Uso**: Para gestionar solo contenido multimedia
- **Limitaciones**: Solo campos multimedia (video, audio, galería)

---

## 🎨 **Ejemplos de Uso**

### ✅ **Correcto - Crear un regalo**:
1. **Contenido Principal > Contenido Dinámico > Crear**
2. Seleccionar tipo: **GIFT**
3. Solo aparecen secciones: Información Básica, Multimedia, Datos de Regalo
4. Completar los campos necesarios
5. Guardar

### ❌ **Problemático - Usar recursos especializados**:
- Si vas a `Contenido Especializado > Regalos`, solo verás campos de regalo
- No tendrás acceso al contexto completo del contenido dinámico
- Es más útil para editar datos específicos de regalos existentes

---

## 🎯 **Selectores Inteligentes**

Cuando uses **DynamicContentResource**, los selectores son inteligentes:

- **📹 Multimedia**: Muestra multimedia de cualquier tipo
- **🎁 Regalo**: Solo muestra regalos existentes (tipo GIFT)
- **🍽️ Menú**: Solo muestra restaurantes (tipo MENU)  
- **👤 Perfil**: Solo muestra perfiles (tipo PROFILE)
- **📅 Evento**: Solo muestra eventos (tipo EVENT)
- **📦 Producto**: Solo muestra productos (tipo PRODUCT)
- **🗺️ Turístico**: Solo muestra lugares turísticos (tipo TOURIST)

---

## 💡 **Recomendaciones**

### **Para crear contenido nuevo**:
- ⭐ Usa **DynamicContentResource** siempre
- El formulario se adapta automáticamente al tipo
- Solo verás las secciones relevantes

### **Para gestionar contenido existente**:
- **DynamicContentResource**: Vista completa y edición contextual
- **Recursos especializados**: Solo para ediciones específicas de datos

### **Para evitar confusiones**:
- **GIFT**: Solo aparece sección de regalos y multimedia
- **MENU**: Solo aparece sección de restaurante y multimedia  
- **PROFILE**: Solo aparece sección de perfil, social links y multimedia
- etc.

---

## 🔍 **Solución de Problemas**

### **"Veo datos de otros tipos"**
- ✅ **Solución**: Usa **DynamicContentResource** en lugar de recursos especializados
- El problema ocurre cuando usas `ContentGiftResource` directamente

### **"No aparecen las secciones correctas"**
- ✅ **Solución**: Asegúrate de haber seleccionado el **tipo** primero
- Las secciones son dinámicas y aparecen según el tipo seleccionado

### **"Los selectores muestran contenido incorrecto"**
- ✅ **Solución**: Los selectores están filtrados por tipo
- Si ves contenido incorrecto, podría ser un problema de caché
- Ejecuta: `php artisan config:clear && php artisan route:clear`

---

## 📊 **Arquitectura del Sistema**

```
DynamicContent (tabla principal)
├── ContentMultimedia (video, audio, galería)
├── ContentGift (remitente, destinatario, mensaje)
├── ContentMenu (restaurante, teléfono, menú)
├── ContentProfile (email, teléfono, bio)
├── ContentEvent (ubicación, fecha, organizador)
├── ContentProduct (precio, stock, SKU)
└── ContentTourist (lugar, dirección, atracciones)
```

Cada tipo de contenido tiene su tabla especializada, pero se gestiona desde **DynamicContentResource** con formularios contextuales.