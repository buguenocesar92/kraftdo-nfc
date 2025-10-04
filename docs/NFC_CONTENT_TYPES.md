# Tipos de Contenido NFC

Este documento describe todos los tipos de contenido NFC disponibles en el sistema Kraftdo NFC.

## Tipos Activos (Implementados)

### 🎁 Regalo Personalizado (GIFT)
- **Código**: `GIFT`
- **Descripción**: Contenido de regalos con multimedia y mensajes personalizados
- **Modelo**: `ContentGift`
- **Recurso Filament**: `ContentGiftResource`
- **Colores**: Primario: #E91E63, Secundario: #FCE4EC

**Subtipos disponibles:**
- `anniversary` - 💕 Aniversario
- `birthday` - 🎂 Cumpleaños
- `graduation` - 🎓 Graduación
- `wedding` - 💒 Boda
- `valentine` - ❤️ San Valentín
- `mother_day` - 🌸 Día de la Madre
- `father_day` - 👔 Día del Padre
- `christmas` - 🎄 Navidad
- `general` - 🎁 General

### 👤 Perfil Personal (PROFILE)
- **Código**: `PROFILE`
- **Descripción**: Perfiles personales con redes sociales y multimedia
- **Modelo**: `ContentProfile`
- **Recurso Filament**: `ContentProfileResource`
- **Colores**: Primario: #9C27B0, Secundario: #F3E5F5

### 🍽️ Menú de Restaurante (MENU)
- **Código**: `MENU`
- **Descripción**: Menús de restaurantes con multimedia
- **Modelo**: `ContentMenu`
- **Recurso Filament**: `ContentMenuResource`
- **Colores**: Primario: #FF6B35, Secundario: #FFF3E0

### 📅 Evento (EVENT)
- **Código**: `EVENT`
- **Descripción**: Información de eventos con multimedia
- **Modelo**: `ContentEvent`
- **Recurso Filament**: `ContentEventResource`
- **Colores**: Primario: #FF9800, Secundario: #FFF3E0

### 📦 Producto (PRODUCT)
- **Código**: `PRODUCT`
- **Descripción**: Catálogo de productos con multimedia
- **Modelo**: `ContentProduct`
- **Recurso Filament**: `ContentProductResource`
- **Colores**: Primario: #4CAF50, Secundario: #E8F5E8

**Campos específicos:**
- `name` - Nombre del producto
- `price` - Precio (decimal)
- `currency` - Moneda
- `sku` - Código SKU
- `stock` - Cantidad en stock
- `in_stock` - Disponibilidad (boolean)
- `brand` - Marca
- `specifications` - Especificaciones (string)
- `purchase_url` - URL de compra

### 🗺️ Información Turística (TOURIST)
- **Código**: `TOURIST`
- **Descripción**: Landing pages turísticas interactivas con mapas y lugares cercanos
- **Modelo**: `ContentTourist`
- **Recurso Filament**: `ContentTouristResource`
- **Colores**: Primario: #2196F3, Secundario: #E3F2FD
- **Vista**: `token.tourist`

**Características principales:**
- Mapa interactivo con Leaflet + OpenStreetMap
- Marcadores de lugares cercanos personalizados
- Galería de imágenes con modal
- Información práctica (horarios, precios, accesibilidad)
- SEO optimizado con metadatos
- URLs del sistema NFC: `/nfc/{token_id}` o `/c/{content_id}`

**Campos específicos:**
- `location_name` - Nombre del lugar
- `place_type` - Tipo (monumento, naturaleza, patrimonio, etc.)
- `location_address` - Dirección completa
- `history` - Historia del lugar (rich text)
- `latitude`, `longitude` - Coordenadas GPS
- `slug` - URL amigable
- `gallery_images` - Galería de imágenes (JSON)
- `opening_hours` - Horarios estructurados (JSON)
- `pricing_info` - Información de precios (JSON)
- `accessibility_info` - Información de accesibilidad (JSON)
- `services` - Servicios disponibles (JSON)
- `attractions` - Atracciones principales (JSON)
- `best_time_to_visit` - Mejor época para visitar
- `languages_spoken` - Idiomas disponibles (JSON)

**Lugares cercanos (`nearby_spots`):**
- Relación uno-a-muchos con `NearbySpot`
- Tipos: restaurante, hotel, transporte, atracción, comercio, servicio, salud, banco
- Marcadores con colores e iconos personalizados
- Información adicional estructurada

### 🏢 Negocio/Feria (BUSINESS)
- **Código**: `BUSINESS`
- **Descripción**: Tarjetas de presentación para negocios y ferias
- **Modelo**: `ContentBusiness`
- **Recurso Filament**: `ContentBusinessResource`
- **Colores**: Primario: #673AB7, Secundario: #F3E5F5

## Tipos Futuros (Planificados)

### 🎨 Portafolio Creativo (PORTFOLIO)
- **Código**: `PORTFOLIO`
- **Descripción**: Portafolios de diseñadores, artistas, fotógrafos
- **Estado**: Planificado
- **Características planeadas**: Galería de trabajos, descripción de proyectos, testimonios de clientes

### 📞 Información de Contacto (CONTACT)
- **Código**: `CONTACT`
- **Descripción**: Información de contacto simple y directa
- **Estado**: Planificado
- **Características planeadas**: vCard, redes sociales, mapa de ubicación

### 📱 Contenido Multimedia (MULTIMEDIA)
- **Código**: `MULTIMEDIA`
- **Descripción**: Contenido centrado en multimedia (videos, audio, fotos)
- **Estado**: Planificado
- **Características planeadas**: Playlist, álbum de fotos, videos

### 🌐 Redes Sociales (SOCIAL)
- **Código**: `SOCIAL`
- **Descripción**: Agregador de todas las redes sociales
- **Estado**: Planificado
- **Características planeadas**: Links a redes, feeds integrados, QR codes

### ⭐ Reseñas y Testimonios (REVIEW)
- **Código**: `REVIEW`
- **Descripción**: Colección de reseñas y testimonios
- **Estado**: Planificado
- **Características planeadas**: Reseñas de clientes, ratings, testimonios

### ⚙️ Contenido Personalizado (CUSTOM)
- **Código**: `CUSTOM`
- **Descripción**: Contenido totalmente personalizable
- **Estado**: Planificado
- **Características planeadas**: Editor visual, plantillas, CSS personalizado

## Configuración

### Archivos de configuración:
- **Tipos activos**: `config/nfc_content_types.php`
- **Constantes del modelo**: `app/Models/DynamicContent.php` (líneas 54-88)
- **Métodos de gestión**: `app/Models/DynamicContent.php` (métodos `getActiveTypes()`, `getFutureTypes()`)

### Estructura de archivos por tipo:
```
app/
├── Models/
│   ├── DynamicContent.php (modelo principal)
│   └── Content{Type}.php (modelos específicos)
├── Filament/Resources/
│   └── Content{Type}s/
│       ├── Content{Type}Resource.php
│       ├── Pages/
│       ├── Schemas/
│       └── Tables/
```

## Para agregar un nuevo tipo:

1. Agregarlo a `future_types` en `config/nfc_content_types.php`
2. Crear migración: `php artisan make:migration create_content_{type}_table`
3. Crear modelo: `php artisan make:model Content{Type}`
4. Crear recurso Filament con formularios, tablas, etc.
5. Mover el tipo de `future_types` a `active_types`
6. Actualizar las constantes en `DynamicContent.php`

## Referencias de código:

- Modelo principal: `app/Models/DynamicContent.php`
- Configuración: `config/nfc_content_types.php`
- Ejemplo de producto: `app/Models/ContentProduct.php`