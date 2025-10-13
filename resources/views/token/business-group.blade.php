{{-- 
    Business Group Token View
    
    Modern business group implementation with member businesses grid
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $businessGroup?->description ?? 'Grupo de negocios de ' . ($businessGroup?->group_name ?? $token->name ?? 'empresa') . '. Descubre nuestros negocios miembros.' }}">
    <meta name="keywords" content="grupo de negocios, food court, NFC, tarjeta digital, {{ $businessGroup?->group_name ?? $token->name ?? '' }}">
    <meta name="author" content="{{ $businessGroup?->group_name ?? $token->name ?? 'Grupo de Negocios' }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#795548">
    
    {{-- Page Title --}}
    <title>{{ $businessGroup?->group_name ?? $token->name ?? 'Grupo de Negocios' }} | Tarjeta Digital NFC</title>
    
    {{-- Open Graph Meta Tags --}}
    <meta property="og:title" content="{{ $businessGroup?->group_name ?? $token->name ?? 'Grupo de Negocios' }}">
    <meta property="og:description" content="{{ $businessGroup?->description ?? 'Descubre nuestros negocios miembros' }}">
    <meta property="og:type" content="business.business">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:site_name" content="Kraftdo NFC">
    @if($businessGroup?->logo_url)
        <meta property="og:image" content="{{ $businessGroup->logo_url }}">
        <meta property="og:image:width" content="400">
        <meta property="og:image:height" content="400">
        <meta property="og:image:type" content="image/jpeg">
    @endif
    
    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $businessGroup?->group_name ?? $token->name ?? 'Grupo de Negocios' }}">
    <meta name="twitter:description" content="{{ $businessGroup?->description ?? 'Descubre nuestros negocios miembros' }}">
    @if($businessGroup?->logo_url)
        <meta name="twitter:image" content="{{ $businessGroup->logo_url }}">
    @endif
    
    {{-- Additional Meta Tags --}}
    <meta name="format-detection" content="telephone=yes">
    <meta name="format-detection" content="email=yes">
    <link rel="canonical" href="{{ request()->url() }}">
    
    {{-- CSS --}}
    @vite([
        'resources/css/app.css',
        'resources/css/multimedia-components.css',
    ])
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .business-card {
            transition: all 0.3s ease;
        }
        .business-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .amenity-badge {
            @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800;
        }
    </style>
</head>

<body class="h-full bg-gradient-to-br from-amber-50 to-orange-100">
    <div class="min-h-full">
        {{-- Header Section --}}
        <div class="relative bg-gradient-to-r from-amber-600 to-orange-600 text-white">
            @if($businessGroup?->banner_image)
                <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                <img src="{{ $businessGroup->banner_image }}" alt="Banner" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-black/30"></div>
            @endif
            
            <div class="relative px-4 py-8 sm:px-6 lg:px-8">
                <div class="max-w-4xl mx-auto text-center">
                    @if($businessGroup?->logo_url)
                        <div class="mb-4">
                            <img src="{{ $businessGroup->logo_url }}" alt="Logo" class="w-24 h-24 mx-auto rounded-full border-4 border-white shadow-lg">
                        </div>
                    @endif
                    
                    <h1 class="text-3xl sm:text-4xl font-bold mb-2">
                        {{ $businessGroup?->group_name ?? $token->name ?? 'Grupo de Negocios' }}
                    </h1>
                    
                    @if($businessGroup?->description)
                        <p class="text-xl opacity-90 max-w-2xl mx-auto">
                            {{ $businessGroup->description }}
                        </p>
                    @endif
                    
                    {{-- Group Type Badge --}}
                    @if($businessGroup?->group_type)
                        <div class="mt-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white">
                                @switch($businessGroup->group_type)
                                    @case('food_court')
                                        🍽️ Food Court
                                        @break
                                    @case('mall')
                                        🏢 Centro Comercial
                                        @break
                                    @case('market')
                                        🛒 Mercado
                                        @break
                                    @case('fair')
                                        🎪 Feria
                                        @break
                                    @default
                                        🏪 {{ ucfirst($businessGroup->group_type) }}
                                @endswitch
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="max-w-6xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            
            {{-- Quick Info Section --}}
            @if($businessGroup?->address || $businessGroup?->contact_phone || $businessGroup?->operating_hours)
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">📍 Información General</h2>
                    <div class="grid md:grid-cols-3 gap-4">
                        @if($businessGroup->address)
                            <div class="text-center">
                                <div class="text-orange-600 text-2xl mb-1">📍</div>
                                <p class="text-sm text-gray-600">Dirección</p>
                                <p class="font-medium">{{ $businessGroup->address }}</p>
                            </div>
                        @endif
                        
                        @if($businessGroup->contact_phone)
                            <div class="text-center">
                                <div class="text-orange-600 text-2xl mb-1">📞</div>
                                <p class="text-sm text-gray-600">Teléfono</p>
                                <a href="tel:{{ $businessGroup->contact_phone }}" class="font-medium text-orange-600 hover:text-orange-700">
                                    {{ $businessGroup->contact_phone }}
                                </a>
                            </div>
                        @endif
                        
                        @if($businessGroup->operating_hours)
                            <div class="text-center">
                                <div class="text-orange-600 text-2xl mb-1">🕒</div>
                                <p class="text-sm text-gray-600">Horarios</p>
                                @php
                                    $today = strtolower(now()->format('l'));
                                    $todayHours = $businessGroup->operating_hours[$today] ?? 'No disponible';
                                @endphp
                                <p class="font-medium">{{ $todayHours }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Amenities Section --}}
            @if($businessGroup?->amenities && count($businessGroup->amenities) > 0)
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">✨ Servicios y Comodidades</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($businessGroup->amenities as $amenity)
                            <span class="amenity-badge">
                                @switch($amenity)
                                    @case('parking')
                                        🅿️ Estacionamiento
                                        @break
                                    @case('wifi')
                                        📶 WiFi Gratuito
                                        @break
                                    @case('restrooms')
                                        🚻 Baños
                                        @break
                                    @case('playground')
                                        🎮 Área de Juegos
                                        @break
                                    @case('live_music')
                                        🎵 Música en Vivo
                                        @break
                                    @case('pet_friendly')
                                        🐕 Pet Friendly
                                        @break
                                    @default
                                        {{ ucfirst(str_replace('_', ' ', $amenity)) }}
                                @endswitch
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Member Businesses Section --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">🏪 Nuestros Negocios</h2>
                    <span class="bg-orange-100 text-orange-800 text-sm font-medium px-3 py-1 rounded-full">
                        {{ $memberBusinesses->count() }} {{ $memberBusinesses->count() === 1 ? 'Negocio' : 'Negocios' }}
                    </span>
                </div>

                @if($memberBusinesses && $memberBusinesses->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($memberBusinesses as $business)
                            @php
                                // Get the token for this business to create the link
                                $businessToken = $business->dynamicContent?->nfcToken;
                            @endphp
                            
                            <div class="business-card bg-gradient-to-br from-white to-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                                @if($business->logo_url)
                                    <div class="h-32 bg-gray-100 flex items-center justify-center">
                                        <img src="{{ $business->logo_url }}" alt="{{ $business->business_name }}" class="w-20 h-20 object-contain rounded-lg">
                                    </div>
                                @endif
                                
                                <div class="p-4">
                                    <h3 class="font-bold text-lg text-gray-900 mb-2">{{ $business->business_name }}</h3>
                                    
                                    @if($business->description)
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $business->description }}</p>
                                    @endif
                                    
                                    {{-- Featured Badge --}}
                                    @if($business->pivot && $business->pivot->is_featured)
                                        <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full mb-2">
                                            ⭐ Destacado
                                        </span>
                                    @endif
                                    
                                    {{-- Visit Button --}}
                                    @if($businessToken)
                                        <a href="/token/{{ $businessToken->token_id }}" 
                                           class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 text-center block">
                                            Ver Detalles →
                                        </a>
                                    @else
                                        <div class="w-full bg-gray-300 text-gray-500 font-medium py-2 px-4 rounded-lg text-center">
                                            Información no disponible
                                        </div>
                                    @endif
                                    
                                    {{-- Quick Contact --}}
                                    @if($business->contact_phone)
                                        <a href="tel:{{ $business->contact_phone }}" 
                                           class="mt-2 w-full bg-green-600 hover:bg-green-700 text-white font-medium py-1 px-3 rounded text-sm text-center block">
                                            📞 Llamar
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-4xl mb-4">🏪</div>
                        <p class="text-gray-500">No hay negocios registrados aún.</p>
                    </div>
                @endif
            </div>

            {{-- Special Instructions --}}
            @if($businessGroup?->special_instructions)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mt-8">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">ℹ️ Información Especial</h3>
                    <p class="text-blue-800">{{ $businessGroup->special_instructions }}</p>
                </div>
            @endif

            {{-- Contact Information --}}
            @if($businessGroup?->contact_email || $businessGroup?->contact_website)
                <div class="bg-white rounded-xl shadow-lg p-6 mt-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">📧 Contacto</h2>
                    <div class="flex flex-wrap gap-4">
                        @if($businessGroup->contact_email)
                            <a href="mailto:{{ $businessGroup->contact_email }}" 
                               class="flex items-center bg-blue-100 hover:bg-blue-200 text-blue-800 px-4 py-2 rounded-lg transition-colors">
                                📧 {{ $businessGroup->contact_email }}
                            </a>
                        @endif
                        
                        @if($businessGroup->contact_website)
                            <a href="{{ $businessGroup->contact_website }}" target="_blank" 
                               class="flex items-center bg-green-100 hover:bg-green-200 text-green-800 px-4 py-2 rounded-lg transition-colors">
                                🌐 Sitio Web
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <footer class="bg-gray-800 text-white py-6 mt-12">
            <div class="max-w-4xl mx-auto px-4 text-center">
                <p class="text-sm opacity-75">
                    Powered by <strong>Kraftdo NFC</strong> • Tarjeta Digital Inteligente
                </p>
            </div>
        </footer>
    </div>

    {{-- Scripts --}}
    @vite(['resources/js/app.js'])
</body>
</html>