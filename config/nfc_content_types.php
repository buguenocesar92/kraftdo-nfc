<?php

return [
    /*
    |--------------------------------------------------------------------------
    | NFC Content Types Configuration
    |--------------------------------------------------------------------------
    |
    | Aquí puedes configurar fácilmente los tipos de contenido NFC disponibles.
    | Para agregar un nuevo tipo, simplemente añádelo a la lista correspondiente.
    |
    */

    'active_types' => [
        'GIFT' => [
            'label' => '🎁 Regalo Personalizado',
            'description' => 'Contenido de regalos con multimedia y mensajes personalizados',
            'has_resource' => true,
            'resource_class' => 'App\\Filament\\Resources\\ContentGifts\\ContentGiftResource',
            'model_class' => 'App\\Models\\ContentGift',
        ],
        'PROFILE' => [
            'label' => '👤 Perfil Personal',
            'description' => 'Perfiles personales con redes sociales y multimedia',
            'has_resource' => true,
            'resource_class' => 'App\\Filament\\Resources\\ContentProfiles\\ContentProfileResource',
            'model_class' => 'App\\Models\\ContentProfile',
        ],
        'EVENT' => [
            'label' => '📅 Evento',
            'description' => 'Información de eventos con multimedia',
            'has_resource' => true,
            'resource_class' => 'App\\Filament\\Resources\\ContentEvents\\ContentEventResource',
            'model_class' => 'App\\Models\\ContentEvent',
        ],
        'PRODUCT' => [
            'label' => '📦 Producto',
            'description' => 'Catálogo de productos con multimedia',
            'has_resource' => true,
            'resource_class' => 'App\\Filament\\Resources\\ContentProducts\\ContentProductResource',
            'model_class' => 'App\\Models\\ContentProduct',
        ],
        'TOURIST' => [
            'label' => '🗺️ Información Turística',
            'description' => 'Lugares turísticos con multimedia',
            'has_resource' => true,
            'resource_class' => 'App\\Filament\\Resources\\ContentTourists\\ContentTouristResource',
            'model_class' => 'App\\Models\\ContentTourist',
        ],
        'BUSINESS' => [
            'label' => '🏢 Negocio / Restaurante',
            'description' => 'Negocios, restaurantes y ferias con catálogos de productos',
            'has_resource' => true,
            'resource_class' => 'App\\Filament\\Resources\\ContentBusinesses\\ContentBusinessResource',
            'model_class' => 'App\\Models\\ContentBusiness',
        ],
        'BUS_STOP' => [
            'label' => '🚌 Paradero de Transporte',
            'description' => 'Información de paraderos de transporte público',
            'has_resource' => true,
            'resource_class' => 'App\\Filament\\Resources\\BusStops\\BusStopResource',
            'model_class' => 'App\\Models\\BusStop',
        ],
    ],

    'future_types' => [
        'PORTFOLIO' => [
            'label' => '🎨 Portafolio Creativo',
            'description' => 'Portafolios de diseñadores, artistas, fotógrafos',
            'has_resource' => false,
            'planned_features' => ['Galería de trabajos', 'Descripción de proyectos', 'Cliente testimonios'],
        ],
        'CONTACT' => [
            'label' => '📞 Información de Contacto',
            'description' => 'Información de contacto simple y directa',
            'has_resource' => false,
            'planned_features' => ['vCard', 'Redes sociales', 'Mapa de ubicación'],
        ],
        'MULTIMEDIA' => [
            'label' => '📱 Contenido Multimedia',
            'description' => 'Contenido centrado en multimedia (videos, audio, fotos)',
            'has_resource' => false,
            'planned_features' => ['Playlist', 'Álbum de fotos', 'Videos'],
        ],
        'SOCIAL' => [
            'label' => '🌐 Redes Sociales',
            'description' => 'Agregador de todas las redes sociales',
            'has_resource' => false,
            'planned_features' => ['Links a redes', 'Feeds integrados', 'QR codes'],
        ],
        'REVIEW' => [
            'label' => '⭐ Reseñas y Testimonios',
            'description' => 'Colección de reseñas y testimonios',
            'has_resource' => false,
            'planned_features' => ['Reseñas de clientes', 'Ratings', 'Testimonios'],
        ],
        'CUSTOM' => [
            'label' => '⚙️ Contenido Personalizado',
            'description' => 'Contenido totalmente personalizable',
            'has_resource' => false,
            'planned_features' => ['Editor visual', 'Plantillas', 'CSS personalizado'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Instructions for Adding New Types
    |--------------------------------------------------------------------------
    |
    | Para agregar un nuevo tipo:
    |
    | 1. Agrégalo a 'future_types' primero con has_resource = false
    | 2. Crea la migración: php artisan make:migration create_content_[type]_table
    | 3. Crea el modelo: php artisan make:model Content[Type]
    | 4. Crea el recurso Filament con formularios, tablas, etc.
    | 5. Mueve el tipo de 'future_types' a 'active_types'
    | 6. Actualiza las constantes en DynamicContent.php
    |
    */
];