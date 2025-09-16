# Multimedia Components

Componentes genéricos y reutilizables para manejo de contenido multimedia (galería, video y audio).

## Componentes Disponibles

### 1. Gallery (`x-multimedia.gallery`)

Componente de galería avanzada con soporte para diferentes layouts y funcionalidades.

#### Props
- `images` (array): Array de imágenes con estructura:
  ```php
  [
      ['src' => 'url', 'alt' => 'text', 'caption' => 'text', 'id' => 'unique_id'],
      // más imágenes...
  ]
  ```
- `theme` (array): Configuración de tema
- `showStats` (bool): Mostrar estadísticas de la galería
- `layout` (string): `masonry` o `grid`
- `columns` (string): Clases CSS para columnas responsivas
- `gap` (string): Clases CSS para espaciado

#### Ejemplo de uso
```php
<x-multimedia.gallery 
    :images="$galleryImages"
    :theme="[
        'background' => 'from-purple-50 via-pink-50 to-yellow-50',
        'text' => 'text-gray-600'
    ]"
    layout="masonry"
    :show-stats="true" />
```

### 2. Video Player (`x-multimedia.video-player`)

Reproductor de video avanzado con soporte para HTML5, YouTube y Vimeo.

#### Props
- `video` (object|string|array): Fuente del video
- `theme` (array): Configuración de tema
- `autoplay` (bool): Reproducción automática
- `showThumbnail` (bool): Mostrar miniatura antes de reproducir
- `customControls` (bool): Usar controles personalizados
- `aspectRatio` (string): `video`, `square`, `vertical`
- `size` (string): `full` o `contained`

#### Ejemplo de uso
```php
<x-multimedia.video-player 
    :video="$contentMultimedia"
    :theme="[
        'background' => 'from-gray-50 via-blue-50 to-purple-50',
        'primary' => 'blue-500',
        'secondary' => 'purple-600'
    ]"
    :custom-controls="true"
    size="contained" />
```

### 3. Audio Player (`x-multimedia.audio-player`)

Reproductor de audio con visualizaciones y controles avanzados.

#### Props
- `audio` (object|string|array): Fuente del audio
- `theme` (array): Configuración de tema
- `visualization` (string): `waveform`, `bars`, `circle`, `none`
- `showMetadata` (bool): Mostrar metadatos
- `size` (string): `full` o `contained`

#### Ejemplo de uso
```php
<x-multimedia.audio-player 
    :audio="$contentMultimedia"
    :theme="[
        'background' => 'from-blue-50 via-purple-50 to-pink-50',
        'primary' => 'blue-500',
        'secondary' => 'purple-600'
    ]"
    visualization="waveform"
    :show-metadata="true" />
```

### 4. Modal (`x-multimedia.modal`)

Modal genérico para visualización de imágenes con zoom y navegación.

#### Ejemplo de uso
```php
<x-multimedia.modal 
    :theme="[
        'background' => 'bg-black',
        'text' => 'text-white'
    ]" />
```

## Instalación y Configuración

### 1. Incluir archivos CSS y JavaScript

En tu vista Blade, incluye los archivos necesarios:

```php
{{-- CSS --}}
@vite(['resources/css/multimedia-components.css'])

{{-- JavaScript --}}
@vite(['resources/js/multimedia-components.js'])
```

### 2. Inicializar Alpine.js

Asegúrate de que el elemento contenedor tenga el componente Alpine.js apropiado:

```php
<body x-data="multimediaGallery({ images: [] })">
    <!-- Tu contenido -->
    
    <!-- Modal (requerido si usas galería) -->
    <x-multimedia.modal />
</body>
```

## Temas Predefinidos

Los componentes soportan varios temas predefinidos:

### Tema Azul
```php
[
    'background' => 'from-blue-50 via-indigo-50 to-purple-50',
    'primary' => 'blue-500',
    'secondary' => 'indigo-600',
    'text' => 'text-blue-600'
]
```

### Tema Rosa
```php
[
    'background' => 'from-pink-50 via-rose-50 to-red-50',
    'primary' => 'pink-500',
    'secondary' => 'rose-600',
    'text' => 'text-pink-600'
]
```

### Tema Verde
```php
[
    'background' => 'from-green-50 via-emerald-50 to-teal-50',
    'primary' => 'green-500',
    'secondary' => 'emerald-600',
    'text' => 'text-green-600'
]
```

## Funcionalidades Avanzadas

### Galería
- Layout masonry y grid responsivos
- Lazy loading de imágenes
- Estados de carga y error
- Modal con zoom y pan
- Navegación con teclado
- Slideshow automático
- Controles de zoom

### Video
- Soporte HTML5, YouTube, Vimeo
- Controles personalizados
- Picture-in-Picture
- Pantalla completa
- Detección de video vertical
- Estados de carga y error
- Reintentos automáticos

### Audio
- Reproductor con controles avanzados
- Visualizaciones (waveform, barras, circular)
- Control de volumen y velocidad
- Loop y salto de tiempo
- Extracción de metadatos
- Soporte servicios externos

## Compatibilidad

- **Navegadores**: Chrome 80+, Firefox 75+, Safari 13+, Edge 80+
- **Responsive**: Totalmente responsivo con Tailwind CSS
- **Accesibilidad**: Cumple con WCAG 2.1
- **Alpine.js**: Versión 3.x requerida
- **Tailwind CSS**: Versión 3.x requerida

## Personalización

### CSS Personalizado
Puedes sobrescribir los estilos creando tu propio archivo CSS:

```css
/* custom-multimedia.css */
.multimedia-gallery .gallery-item {
    border-radius: 1rem;
}

.multimedia-video .play-button {
    background: linear-gradient(45deg, #your-color1, #your-color2);
}
```

### JavaScript Personalizado
Extiende la funcionalidad registrando tus propios métodos:

```javascript
document.addEventListener('alpine:init', () => {
    Alpine.data('customMultimediaGallery', () => ({
        ...Alpine.data('multimediaGallery')(),
        
        // Tus métodos personalizados
        customMethod() {
            // Tu lógica aquí
        }
    }));
});
```

## Troubleshooting

### Problema: Componentes no se cargan
**Solución**: Verificar que Alpine.js y los archivos CSS/JS estén incluidos correctamente.

### Problema: Imágenes no se muestran
**Solución**: Verificar que las URLs de las imágenes sean accesibles y tengan el accessor `image_source`.

### Problema: Videos no reproducen
**Solución**: Verificar permisos de autoplay del navegador y configuración CORS para videos externos.

## Ejemplos de Implementación

Ver los archivos de ejemplo en:
- `resources/views/token/profile.blade.php` - Implementación en perfil
- `resources/views/token/gift.blade.php` - Implementación en regalo