{{-- 
    Tourist Token View - Mobile First Design
    
    Modern tourist destination implementation with interactive maps and content
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $tourist?->location_name ?? $content->title }} - {{ Str::limit(strip_tags($tourist?->history ?? $content->description ?? ''), 160) }}">
    <meta name="keywords" content="turismo, {{ $tourist?->place_type }}, {{ $tourist?->location_name ?? $content->title }}, NFC, Chile">
    <meta name="author" content="{{ $tourist?->location_name ?? $content->title }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#2196F3">
    
    {{-- Page Title --}}
    <title>{{ $tourist?->location_name ?? $content->title }} | Destino Turístico NFC</title>
    
    {{-- Open Graph Meta Tags --}}
    <meta property="og:title" content="{{ $tourist?->location_name ?? $content->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($tourist?->history ?? $content->description ?? ''), 160) }}">
    <meta property="og:type" content="place">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:site_name" content="Kraftdo NFC">
    @if($tourist?->getMainImage())
        <meta property="og:image" content="{{ $tourist->getMainImage() }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:type" content="image/jpeg">
    @endif
    
    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $tourist?->location_name ?? $content->title }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($tourist?->history ?? $content->description ?? ''), 160) }}">
    @if($tourist?->getMainImage())
        <meta name="twitter:image" content="{{ $tourist->getMainImage() }}">
    @endif
    
    {{-- Additional Meta Tags --}}
    <meta name="format-detection" content="telephone=yes">
    <meta name="format-detection" content="email=yes">
    <link rel="canonical" href="{{ request()->url() }}">
    
    {{-- External Assets --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin="" />
    
    {{-- Vite Assets --}}
    @vite([
        'resources/css/app.css',
        'resources/css/multimedia-components.css',
        'resources/js/app.js',
        'resources/js/multimedia-components.js'
    ])
</head>

@php
    $primaryColor = '#2196F3';
    $secondaryColor = '#1976D2';
    $accentColor = '#03A9F4';
@endphp

<body class="h-full" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }}, {{ $accentColor }})">
    <div class="min-h-screen flex items-center justify-center py-6 px-4 sm:py-12 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-4 sm:space-y-6">
            
            @if(!$tourist)
                {{-- Fallback Card --}}
                <div class="bg-white rounded-3xl shadow-xl p-6 sm:p-8 animate-fade-in">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L13.09 8.26L22 9L13.09 9.74L12 16L10.91 9.74L2 9L10.91 8.26L12 2Z"/>
                            </svg>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">{{ $content->title }}</h1>
                        <p class="text-sm sm:text-base text-gray-600 mb-4">{{ $content->description }}</p>
                        <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs sm:text-sm font-medium">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20,2H4A2,2 0 0,0 2,4V20A2,2 0 0,0 4,22H20A2,2 0 0,0 22,20V4A2,2 0 0,0 20,2M20,20H4V4H20V20Z"/>
                            </svg>
                            Powered by Kraftdo NFC
                        </div>
                    </div>
                </div>
            @else
                {{-- Hero Card --}}
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in">
                    {{-- Hero Image --}}
                    @if($tourist->getMainImage())
                        <div class="relative h-48 sm:h-56 md:h-64 overflow-hidden">
                            <img src="{{ $tourist->getMainImage() }}" 
                                 alt="{{ $tourist->location_name }}"
                                 class="w-full h-full object-cover transition-transform duration-700 hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                            
                            {{-- Place Type Badge --}}
                            <div class="absolute top-4 left-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/90 backdrop-blur-sm text-blue-800 text-xs sm:text-sm font-medium">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                                    </svg>
                                    {{ ucfirst($tourist->place_type) }}
                                </span>
                            </div>
                            
                            {{-- Title Overlay --}}
                            <div class="absolute bottom-4 left-4 right-4">
                                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-1 drop-shadow-lg">
                                    {{ $tourist->location_name }}
                                </h1>
                                @if($tourist->location_address)
                                    <p class="text-sm sm:text-base text-white/90 drop-shadow">
                                        {{ $tourist->location_address }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @else
                        {{-- Header without image --}}
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-8 sm:px-8 sm:py-12">
                            <div class="text-center">
                                <div class="inline-flex items-center px-3 py-1 rounded-full bg-white/20 backdrop-blur-sm text-white text-xs sm:text-sm font-medium mb-4">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                                    </svg>
                                    {{ ucfirst($tourist->place_type) }}
                                </div>
                                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-2">
                                    {{ $tourist->location_name }}
                                </h1>
                                @if($tourist->location_address)
                                    <p class="text-sm sm:text-base text-white/90">
                                        {{ $tourist->location_address }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    {{-- Content --}}
                    <div class="p-6 sm:p-8">
                        {{-- Description --}}
                        @if($content->description)
                            <p class="text-sm sm:text-base text-gray-600 mb-6 leading-relaxed">
                                {{ $content->description }}
                            </p>
                        @endif
                        
                        {{-- Quick Info Cards --}}
                        <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6">
                            {{-- Hours --}}
                            @if($tourist->getTodayHours())
                                <div class="bg-green-50 rounded-xl p-3 sm:p-4">
                                    <div class="flex items-center mb-1">
                                        <svg class="w-4 h-4 text-green-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.2,16.2L11,13V7H12.5V12.2L17,14.9L16.2,16.2Z"/>
                                        </svg>
                                        <span class="text-xs sm:text-sm font-medium text-green-800">Hoy</span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-green-700">{{ $tourist->getTodayHours() }}</p>
                                </div>
                            @endif
                            
                            {{-- Location --}}
                            @if($tourist->latitude && $tourist->longitude)
                                <div class="bg-blue-50 rounded-xl p-3 sm:p-4">
                                    <div class="flex items-center mb-1">
                                        <svg class="w-4 h-4 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                                        </svg>
                                        <span class="text-xs sm:text-sm font-medium text-blue-800">Ubicación</span>
                                    </div>
                                    <a href="{{ $tourist->getGoogleMapsUrl() }}" 
                                       target="_blank"
                                       class="text-xs sm:text-sm text-blue-700 hover:text-blue-900 transition-colors">
                                        Ver en Maps
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Contact Info --}}
                        @if($tourist->contact_phone || $tourist->contact_email || $tourist->website_url)
                            <div class="border-t border-gray-100 pt-6">
                                <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-3">Contacto</h3>
                                <div class="space-y-2">
                                    @if($tourist->contact_phone)
                                        <a href="tel:{{ $tourist->contact_phone }}" 
                                           class="flex items-center text-sm sm:text-base text-gray-600 hover:text-blue-600 transition-colors">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/>
                                            </svg>
                                            {{ $tourist->contact_phone }}
                                        </a>
                                    @endif
                                    
                                    @if($tourist->contact_email)
                                        <a href="mailto:{{ $tourist->contact_email }}" 
                                           class="flex items-center text-sm sm:text-base text-gray-600 hover:text-blue-600 transition-colors">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/>
                                            </svg>
                                            {{ $tourist->contact_email }}
                                        </a>
                                    @endif
                                    
                                    @if($tourist->website_url)
                                        <a href="{{ $tourist->website_url }}" 
                                           target="_blank"
                                           class="flex items-center text-sm sm:text-base text-gray-600 hover:text-blue-600 transition-colors">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M16.36,14C16.44,13.34 16.5,12.68 16.5,12C16.5,11.32 16.44,10.66 16.36,10H19.74C19.9,10.64 20,11.31 20,12C20,12.69 19.9,13.36 19.74,14M14.59,19.56C15.19,18.45 15.65,17.25 15.97,16H18.92C17.96,17.65 16.43,18.93 14.59,19.56M14.34,14H9.66C9.56,13.34 9.5,12.68 9.5,12C9.5,11.32 9.56,10.65 9.66,10H14.34C14.43,10.65 14.5,11.32 14.5,12C14.5,12.68 14.43,13.34 14.34,14M12,19.96C11.17,18.76 10.5,17.43 10.09,16H13.91C13.5,17.43 12.83,18.76 12,19.96M8,8H5.08C6.03,6.34 7.57,5.06 9.4,4.44C8.8,5.55 8.35,6.75 8,8M5.08,16H8C8.35,17.25 8.8,18.45 9.4,19.56C7.57,18.93 6.03,17.65 5.08,16M4.26,14C4.1,13.36 4,12.69 4,12C4,11.31 4.1,10.64 4.26,10H7.64C7.56,10.66 7.5,11.32 7.5,12C7.5,12.68 7.56,13.34 7.64,14M12,4.03C12.83,5.23 13.5,6.57 13.91,8H10.09C10.5,6.57 11.17,5.23 12,4.03M18.92,8H15.97C15.65,6.75 15.19,5.55 14.59,4.44C16.43,5.07 17.96,6.34 18.92,8M12,2C6.47,2 2,6.5 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                                            </svg>
                                            Sitio web
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- History Card --}}
                @if($tourist->history)
                    <div class="bg-white rounded-3xl shadow-xl p-6 sm:p-8 animate-fade-in" style="animation-delay: 0.2s;">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4">Historia</h2>
                        <div class="prose prose-sm sm:prose-base max-w-none text-gray-600">
                            {!! $tourist->history !!}
                        </div>
                    </div>
                @endif
                
                {{-- Gallery Card --}}
                @if($tourist->hasGallery())
                    @php
                        // Convertir array de paths a formato esperado por el componente multimedia
                        $galleryImages = collect($tourist->gallery_images)->map(function($imagePath, $index) use ($tourist) {
                            return (object) [
                                'id' => $index,
                                'image_source' => \Storage::url($imagePath),
                                'alt_text' => "Imagen " . ($index + 1) . " de " . $tourist->location_name,
                                'caption' => ''
                            ];
                        });
                    @endphp
                    
                    <div class="bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in" style="animation-delay: 0.4s;">
                        <div class="p-6 sm:p-8 pb-4 sm:pb-6">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4">Galería</h2>
                        </div>
                        <div class="px-6 sm:px-8 pb-6 sm:pb-8">
                            <x-multimedia.gallery 
                                :images="$galleryImages"
                                :theme="[
                                    'background' => 'from-blue-50 via-sky-50 to-cyan-50',
                                    'text' => 'text-blue-600'
                                ]"
                                layout="grid"
                                columns="sm:grid-cols-2 lg:grid-cols-3"
                                gap="gap-3 sm:gap-4"
                                :showStats="true"
                            />
                        </div>
                    </div>
                @endif
                
                {{-- Map Card --}}
                @if(isset($mapData) && !empty($mapData))
                    <div class="bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in" style="animation-delay: 0.6s;"
                         x-data="touristMap(@js($mapData))" x-init="initMap()">
                        <div class="p-6 sm:p-8 pb-4 sm:pb-6">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4">Ubicación y Lugares Cercanos</h2>
                        </div>
                        <div class="px-6 sm:px-8 pb-6 sm:pb-8">
                            <div id="tourist-map" class="w-full h-64 sm:h-80 md:h-96 rounded-xl"></div>
                        </div>
                    </div>
                @endif
                
                {{-- Practical Info Card --}}
                @if($tourist->opening_hours || $tourist->pricing_info || $tourist->accessibility_info)
                    <div class="bg-white rounded-3xl shadow-xl p-6 sm:p-8 animate-fade-in" style="animation-delay: 0.8s;">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-6">Información Práctica</h2>
                        
                        <div class="space-y-6">
                            {{-- Opening Hours --}}
                            @if($tourist->opening_hours)
                                <div>
                                    <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-3 flex items-center">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.2,16.2L11,13V7H12.5V12.2L17,14.9L16.2,16.2Z"/>
                                        </svg>
                                        Horarios
                                    </h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs sm:text-sm">
                                        @foreach($tourist->opening_hours as $day => $hours)
                                            <div class="flex justify-between py-1">
                                                <span class="font-medium text-gray-700 capitalize">{{ ucfirst($day) }}</span>
                                                <span class="text-gray-600">{{ $hours }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Pricing Info --}}
                            @if($tourist->pricing_info)
                                <div>
                                    <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-3 flex items-center">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z"/>
                                        </svg>
                                        Precios
                                    </h3>
                                    <div class="space-y-2 text-xs sm:text-sm">
                                        @foreach($tourist->pricing_info as $item => $price)
                                            <div class="flex justify-between py-1">
                                                <span class="text-gray-700 capitalize">{{ str_replace('_', ' ', $item) }}</span>
                                                <span class="text-gray-600 font-medium">{{ $price }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Accessibility --}}
                            @if($tourist->accessibility_info)
                                <div>
                                    <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-3 flex items-center">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M12,5.5A2,2 0 0,1 14,7.5A2,2 0 0,1 12,9.5A2,2 0 0,1 10,7.5A2,2 0 0,1 12,5.5M12,11A2,2 0 0,1 14,13V18H10V13A2,2 0 0,1 12,11Z"/>
                                        </svg>
                                        Accesibilidad
                                    </h3>
                                    <div class="space-y-2 text-xs sm:text-sm">
                                        @foreach($tourist->accessibility_info as $item => $info)
                                            <div class="flex justify-between py-1">
                                                <span class="text-gray-700 capitalize">{{ str_replace('_', ' ', $item) }}</span>
                                                <span class="text-gray-600">{{ $info }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                
                {{-- NFC Footer --}}
                <div class="text-center pt-4">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm text-white text-xs sm:text-sm font-medium">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20,2H4A2,2 0 0,0 2,4V20A2,2 0 0,0 4,22H20A2,2 0 0,0 22,20V4A2,2 0 0,0 20,2M20,20H4V4H20V20Z"/>
                        </svg>
                        Powered by Kraftdo NFC
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Leaflet JavaScript --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
            crossorigin=""></script>
    
    {{-- Tourist Map Component --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('touristMap', (mapData) => ({
                map: null,
                markers: [],
                
                initMap() {
                    if (!mapData || !mapData.center) return;
                    
                    // Initialize map
                    this.map = L.map('tourist-map').setView([mapData.center.lat, mapData.center.lng], mapData.zoom || 15);
                    
                    // Add tile layer
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(this.map);
                    
                    // Add main marker
                    if (mapData.mainMarker) {
                        const mainIcon = L.divIcon({
                            className: 'custom-div-icon-main',
                            html: `<div style="background-color: ${mapData.mainMarker.color}; color: white; padding: 8px; border-radius: 50%; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">
                                     <svg style="width: 16px; height: 16px;" fill="currentColor" viewBox="0 0 24 24">
                                       <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                                     </svg>
                                   </div>`,
                            iconSize: [32, 32],
                            iconAnchor: [16, 16]
                        });
                        
                        L.marker([mapData.mainMarker.lat, mapData.mainMarker.lng], { icon: mainIcon })
                            .bindPopup(`<strong>${mapData.mainMarker.title}</strong><br>${mapData.mainMarker.description}`)
                            .addTo(this.map);
                    }
                    
                    // Add nearby spots
                    if (mapData.nearbySpots) {
                        mapData.nearbySpots.forEach(spot => {
                            const spotIcon = L.divIcon({
                                className: 'custom-div-icon-spot',
                                html: `<div style="background-color: ${spot.color}; color: white; padding: 4px; border-radius: 50%; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; border: 2px solid white; box-shadow: 0 1px 3px rgba(0,0,0,0.3);">
                                         <svg style="width: 12px; height: 12px;" fill="currentColor" viewBox="0 0 24 24">
                                           <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                                         </svg>
                                       </div>`,
                                iconSize: [24, 24],
                                iconAnchor: [12, 12]
                            });
                            
                            const popupContent = `<strong>${spot.title}</strong><br>${spot.description}<br><small style="color: #666;">${spot.distance} km away</small>`;
                            
                            L.marker([spot.lat, spot.lng], { icon: spotIcon })
                                .bindPopup(popupContent)
                                .addTo(this.map);
                        });
                    }
                    
                    // Fit bounds to show all markers
                    if (mapData.nearbySpots && mapData.nearbySpots.length > 0) {
                        const group = new L.featureGroup();
                        
                        // Add main marker to group
                        if (mapData.mainMarker) {
                            group.addLayer(L.marker([mapData.mainMarker.lat, mapData.mainMarker.lng]));
                        }
                        
                        // Add nearby spots to group
                        mapData.nearbySpots.forEach(spot => {
                            group.addLayer(L.marker([spot.lat, spot.lng]));
                        });
                        
                        this.map.fitBounds(group.getBounds().pad(0.1));
                    }
                }
            }));
        });
    </script>
    
    {{-- Animations --}}
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fade-in 0.6s ease-out forwards;
        }
        
        .custom-div-icon-main,
        .custom-div-icon-spot {
            background: transparent !important;
            border: none !important;
        }
    </style>
</body>
</html>