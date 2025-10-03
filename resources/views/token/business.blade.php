{{-- 
    Business Token View
    
    Modern business card implementation with products catalog, social links and contact info
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $contentBusiness?->description ?? 'Negocio digital de ' . ($contentBusiness?->business_name ?? $token->name ?? 'empresa') . '. Descubre nuestros productos y servicios.' }}">
    <meta name="keywords" content="negocio digital, NFC, tarjeta digital, productos, servicios, {{ $contentBusiness?->business_name ?? $token->name ?? '' }}">
    <meta name="author" content="{{ $contentBusiness?->business_name ?? $token->name ?? 'Negocio' }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="{{ $contentBusiness?->color_palette['primary'] ?? '#3b82f6' }}">
    
    {{-- Page Title --}}
    <title>{{ $contentBusiness?->business_name ?? $token->name ?? 'Negocio Digital' }} | Tarjeta Digital NFC</title>
    
    {{-- Open Graph Meta Tags --}}
    <meta property="og:title" content="{{ $contentBusiness?->business_name ?? $token->name ?? 'Negocio Digital' }}">
    <meta property="og:description" content="{{ $contentBusiness?->description ?? 'Descubre nuestros productos y servicios digitales' }}">
    <meta property="og:type" content="business.business">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:site_name" content="Kraftdo NFC">
    @if($contentBusiness?->logo_url)
        <meta property="og:image" content="{{ asset(Storage::url($contentBusiness->logo_url)) }}">
        <meta property="og:image:width" content="400">
        <meta property="og:image:height" content="400">
        <meta property="og:image:type" content="image/jpeg">
    @endif
    
    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $contentBusiness?->business_name ?? $token->name ?? 'Negocio Digital' }}">
    <meta name="twitter:description" content="{{ $contentBusiness?->description ?? 'Descubre nuestros productos y servicios digitales' }}">
    @if($contentBusiness?->logo_url)
        <meta name="twitter:image" content="{{ asset(Storage::url($contentBusiness->logo_url)) }}">
    @endif
    
    {{-- Additional Meta Tags --}}
    <meta name="format-detection" content="telephone=yes">
    <meta name="format-detection" content="email=yes">
    <link rel="canonical" href="{{ request()->url() }}">
    
    {{-- CSS --}}
    @vite([
        'resources/css/app.css',
        'resources/css/multimedia-components.css',
        'resources/css/profile.css'
    ])
    
    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
</head>
<body class="antialiased h-full bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-blue-900 dark:to-purple-900" 
      style="background: linear-gradient(135deg, {{ $contentBusiness?->color_palette['primary'] ?? '#3b82f6' }}22, {{ $contentBusiness?->color_palette['secondary'] ?? '#8b5cf6' }}22, {{ $contentBusiness?->color_palette['accent'] ?? '#ec4899' }}22);">

    {{-- Background Pattern --}}
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%234f46e5" fill-opacity="0.03"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-60"></div>

    {{-- Main Container --}}
    <div class="relative min-h-full flex items-center justify-center p-4">
        <div class="w-full max-w-md mx-auto">
            {{-- Business Card --}}
            <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-3xl shadow-2xl p-8 space-y-8 border border-white/20 dark:border-gray-700/50">
                
                {{-- Business Header --}}
                <div class="text-center space-y-4">
                    {{-- Logo --}}
                    @if($contentBusiness?->logo_url)
                        <div class="relative mx-auto w-24 h-24 rounded-2xl overflow-hidden shadow-lg bg-gradient-to-br from-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-400 to-{{ $contentBusiness->color_palette['secondary'] ?? 'purple' }}-500 p-1">
                            <img src="{{ asset(Storage::url($contentBusiness->logo_url)) }}" 
                                 alt="{{ $contentBusiness->business_name }}" 
                                 class="w-full h-full object-cover rounded-xl">
                        </div>
                    @else
                        <div class="relative mx-auto w-24 h-24 rounded-2xl bg-gradient-to-br from-{{ $contentBusiness?->color_palette['primary'] ?? 'blue' }}-400 to-{{ $contentBusiness?->color_palette['secondary'] ?? 'purple' }}-500 flex items-center justify-center shadow-lg">
                            <span class="text-white text-2xl font-bold">
                                {{ substr($contentBusiness?->business_name ?? $token->name ?? 'N', 0, 1) }}
                            </span>
                        </div>
                    @endif

                    {{-- Business Name & Type --}}
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ $contentBusiness?->business_name ?? $token->name ?? 'Negocio Digital' }}
                        </h1>
                        @if($contentBusiness?->business_type)
                            <span class="inline-block px-3 py-1 text-sm font-medium bg-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-100 text-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-800 rounded-full">
                                {{ ucfirst($contentBusiness->business_type) }}
                            </span>
                        @endif
                    </div>

                    {{-- Description --}}
                    @if($contentBusiness?->description)
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            {{ $contentBusiness->description }}
                        </p>
                    @endif

                    {{-- Operating Status --}}
                    @if($contentBusiness && method_exists($contentBusiness, 'isOpenNow'))
                        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-sm font-medium {{ $contentBusiness->isOpenNow() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <div class="w-2 h-2 rounded-full {{ $contentBusiness->isOpenNow() ? 'bg-green-500' : 'bg-red-500' }}"></div>
                            <span>{{ $contentBusiness->isOpenNow() ? 'Abierto ahora' : 'Cerrado' }}</span>
                        </div>
                    @endif
                </div>

                {{-- Contact Information --}}
                @if($contentBusiness && $contentBusiness->hasContactInfo())
                    <div class="space-y-3">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Contacto</h3>
                        
                        {{-- Phone --}}
                        @if($contentBusiness->contact_phone)
                            <a href="tel:{{ $contentBusiness->contact_phone }}" 
                               class="flex items-center space-x-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700 hover:bg-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-50 dark:hover:bg-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-900/20 transition-colors">
                                <div class="w-10 h-10 rounded-full bg-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-500 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $contentBusiness->contact_phone }}</span>
                            </a>
                        @endif

                        {{-- Email --}}
                        @if($contentBusiness->contact_email)
                            <a href="mailto:{{ $contentBusiness->contact_email }}" 
                               class="flex items-center space-x-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700 hover:bg-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-50 dark:hover:bg-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-900/20 transition-colors">
                                <div class="w-10 h-10 rounded-full bg-{{ $contentBusiness->color_palette['secondary'] ?? 'purple' }}-500 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $contentBusiness->contact_email }}</span>
                            </a>
                        @endif

                        {{-- Website --}}
                        @if($contentBusiness->contact_website)
                            <a href="{{ $contentBusiness->contact_website }}" target="_blank"
                               class="flex items-center space-x-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700 hover:bg-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-50 dark:hover:bg-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-900/20 transition-colors">
                                <div class="w-10 h-10 rounded-full bg-{{ $contentBusiness->color_palette['accent'] ?? 'pink' }}-500 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9m0 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-900 dark:text-white font-medium">Sitio Web</span>
                            </a>
                        @endif

                        {{-- Address --}}
                        @if($contentBusiness->address)
                            <div class="flex items-start space-x-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700">
                                <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-900 dark:text-white font-medium">{{ $contentBusiness->address }}</p>
                                    @if($contentBusiness->google_maps_url || $contentBusiness->getGoogleMapsAutoUrlAttribute())
                                        <a href="{{ $contentBusiness->google_maps_url ?: $contentBusiness->getGoogleMapsAutoUrlAttribute() }}" 
                                           target="_blank" 
                                           class="text-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-600 text-sm hover:underline">
                                            Ver en Google Maps
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Social Media --}}
                @if($contentBusiness && $contentBusiness->hasSocialMedia())
                    <div class="space-y-3">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Síguenos</h3>
                        <div class="grid grid-cols-2 gap-3">
                            {{-- Instagram --}}
                            @if($contentBusiness->instagram_url)
                                <a href="{{ $contentBusiness->instagram_url }}" target="_blank"
                                   class="flex items-center justify-center space-x-2 p-3 rounded-xl bg-gradient-to-r from-purple-500 to-pink-500 text-white hover:shadow-lg transition-all">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.621 5.367 11.988 11.988 11.988s11.987-5.367 11.987-11.988C24.004 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.542-3.293-1.445C4.184 14.518 3.642 13.367 3.642 12.07s.542-2.448 1.514-3.363c.845-.772 1.996-1.245 3.293-1.245s2.448.473 3.293 1.245c.972.915 1.514 2.066 1.514 3.363s-.542 2.448-1.514 3.473c-.845.903-1.996 1.445-3.293 1.445zm7.138 0c-1.297 0-2.448-.542-3.293-1.445-.972-1.025-1.514-2.176-1.514-3.473s.542-2.448 1.514-3.363c.845-.772 1.996-1.245 3.293-1.245s2.448.473 3.293 1.245c.972.915 1.514 2.066 1.514 3.363s-.542 2.448-1.514 3.473c-.845.903-1.996 1.445-3.293 1.445z"/>
                                    </svg>
                                    <span class="font-medium">Instagram</span>
                                </a>
                            @endif

                            {{-- Facebook --}}
                            @if($contentBusiness->facebook_url)
                                <a href="{{ $contentBusiness->facebook_url }}" target="_blank"
                                   class="flex items-center justify-center space-x-2 p-3 rounded-xl bg-blue-600 text-white hover:shadow-lg transition-all">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    <span class="font-medium">Facebook</span>
                                </a>
                            @endif

                            {{-- WhatsApp --}}
                            @if($contentBusiness->whatsapp_number)
                                <a href="{{ $contentBusiness->getWhatsappUrlAttribute() }}" target="_blank"
                                   class="flex items-center justify-center space-x-2 p-3 rounded-xl bg-green-500 text-white hover:shadow-lg transition-all col-span-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.893 3.488"/>
                                    </svg>
                                    <span class="font-medium">WhatsApp</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Services --}}
                @if($contentBusiness?->services && count($contentBusiness->services) > 0)
                    <div class="space-y-3">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Servicios</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($contentBusiness->services as $service)
                                <span class="px-3 py-1 text-sm bg-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-100 text-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-800 rounded-full">
                                    {{ $service }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Google Reviews --}}
                @if($contentBusiness?->google_reviews_url)
                    <div class="space-y-3">
                        <a href="{{ $contentBusiness->google_reviews_url }}" target="_blank"
                           class="flex items-center justify-center space-x-3 p-4 rounded-xl bg-gradient-to-r from-yellow-400 to-orange-500 text-white hover:shadow-lg transition-all">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0l3.09 6.26L22 7.27l-5 4.87 1.18 6.88L12 15.77l-6.18 3.25L7 12.14 2 7.27l6.91-1.01L12 0z"/>
                            </svg>
                            <span class="font-semibold">Déjanos una reseña en Google</span>
                        </a>
                    </div>
                @endif

                {{-- Operating Hours --}}
                @if($contentBusiness && $contentBusiness->operating_hours && count($contentBusiness->operating_hours) > 0)
                    <div class="space-y-3">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Horarios</h3>
                        <div class="space-y-1">
                            @foreach($contentBusiness->getFormattedOperatingHours() as $day => $hours)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ $day }}</span>
                                    <span class="text-gray-900 dark:text-white">{{ $hours }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Products Catalog Preview --}}
                @if($contentBusiness && $contentBusiness->hasCatalog())
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Productos</h3>
                            <span class="text-sm text-gray-500">{{ $contentBusiness->products()->count() }} productos</span>
                        </div>
                        
                        {{-- Show first 3 products --}}
                        <div class="grid grid-cols-1 gap-3">
                            @foreach($contentBusiness->products()->limit(3)->get() as $product)
                                <div class="flex items-center space-x-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700">
                                    {{-- Product image or icon --}}
                                    <div class="w-12 h-12 rounded-lg bg-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-500 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $product->name ?? $product->dynamicContent->title ?? 'Producto' }}</h4>
                                        @if($product->specifications)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-1">{{ $product->specifications }}</p>
                                        @endif
                                        @if($product->price)
                                            <p class="text-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-600 font-semibold">
                                                ${{ number_format($product->price, 0, ',', '.') }} {{ $product->currency }}
                                            </p>
                                        @endif
                                        @if($product->brand)
                                            <p class="text-xs text-gray-500">{{ $product->brand }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($contentBusiness->products()->count() > 3)
                            <a href="{{ route('token.products', $token->token_id) }}" 
                               class="w-full py-3 text-center text-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-600 font-medium hover:underline block bg-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-50 dark:bg-{{ $contentBusiness->color_palette['primary'] ?? 'blue' }}-900 rounded-xl transition-colors">
                                Ver todos los productos ({{ $contentBusiness->products()->count() }})
                            </a>
                        @endif
                    </div>
                @endif

                {{-- Footer --}}
                <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Tarjeta digital creada con 
                            <span class="font-semibold text-{{ $contentBusiness?->color_palette['primary'] ?? 'blue' }}-600">Kraftdo NFC</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    @vite(['resources/js/app.js'])
</body>
</html>