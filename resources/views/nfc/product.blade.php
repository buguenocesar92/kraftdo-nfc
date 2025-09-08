<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content->title }} - Producto</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/video-container.css'])
    @vite(['resources/js/video-orientation-system.js'])
    
</head>
<body class="bg-gradient-to-br {{ $content->colors['gradient'] ?? 'from-red-50 to-pink-100' }} min-h-screen">
    
    <div class="max-w-3xl mx-auto px-4 py-6">
        
        <!-- Header del Producto -->
        <div class="text-center mb-8">
            <div class="bg-white rounded-xl shadow-lg p-8 {{ $content->colors['border'] ?? 'border-red-200' }} border-2">
                <div class="mb-6">
                    @if($content->image_url)
                        <img src="{{ $content->image_url }}" alt="{{ $content->title }}" 
                             class="w-full h-64 object-cover rounded-lg">
                    @else
                        <i class="{{ $content->icon }} text-6xl {{ $content->colors['primary'] }}"></i>
                    @endif
                </div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">{{ $content->title }}</h1>
                @if($content->description)
                    <p class="text-gray-600 text-lg">{{ $content->description }}</p>
                @endif
                @if(isset($content->data['price']))
                    <div class="mt-4">
                        <span class="text-3xl font-bold {{ $content->colors['primary'] }}">
                            ${{ number_format($content->data['price'], 0, ',', '.') }}
                        </span>
                        @if(isset($content->data['original_price']))
                            <span class="text-lg text-gray-500 line-through ml-2">
                                ${{ number_format($content->data['original_price'], 0, ',', '.') }}
                            </span>
                            <span class="bg-red-500 text-white px-2 py-1 rounded-full text-sm ml-2">
                                ¡OFERTA!
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Características del Producto -->
        @if(isset($content->data['features']))
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 {{ $content->colors['border'] ?? 'border-red-200' }} border">
                <h2 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-6">
                    <i class="fas fa-star"></i> Características
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($content->data['features'] as $feature)
                        <div class="flex items-center bg-gradient-to-r {{ $content->colors['gradient'] ?? 'from-red-50 to-pink-100' }} rounded-lg p-3">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-gray-800">{{ $feature }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Especificaciones Técnicas -->
        @if(isset($content->data['specifications']))
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 {{ $content->colors['border'] ?? 'border-red-200' }} border">
                <h2 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-6">
                    <i class="fas fa-cogs"></i> Especificaciones Técnicas
                </h2>
                <div class="space-y-3">
                    @foreach($content->data['specifications'] as $spec)
                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                            <span class="font-semibold text-gray-700">{{ $spec['name'] }}</span>
                            <span class="text-gray-600">{{ $spec['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Galería de Imágenes -->
        @if(isset($content->data['gallery']) && count($content->data['gallery']) > 0)
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 {{ $content->colors['border'] ?? 'border-red-200' }} border">
                <h2 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-6">
                    <i class="fas fa-images"></i> Galería
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($content->data['gallery'] as $image)
                        <div class="aspect-square overflow-hidden rounded-lg">
                            <img src="{{ $image['url'] }}" 
                                 alt="{{ $image['title'] ?? 'Imagen del producto' }}" 
                                 class="w-full h-full object-cover hover:scale-105 transition-transform cursor-pointer">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Disponibilidad y Envío -->
        @if(isset($content->data['availability']) || isset($content->data['shipping']))
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 {{ $content->colors['border'] ?? 'border-red-200' }} border">
                <h2 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-6">
                    <i class="fas fa-shipping-fast"></i> Disponibilidad y Envío
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(isset($content->data['availability']))
                        <div class="bg-green-50 rounded-lg p-4">
                            <h3 class="font-bold text-green-800 mb-2">
                                <i class="fas fa-boxes"></i> Disponibilidad
                            </h3>
                            <p class="text-green-700">{{ $content->data['availability'] }}</p>
                        </div>
                    @endif
                    
                    @if(isset($content->data['shipping']))
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="font-bold text-blue-800 mb-2">
                                <i class="fas fa-truck"></i> Envío
                            </h3>
                            <p class="text-blue-700">{{ $content->data['shipping'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Garantía y Soporte -->
        @if(isset($content->data['warranty']) || isset($content->data['support']))
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 {{ $content->colors['border'] ?? 'border-red-200' }} border">
                <h2 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-6">
                    <i class="fas fa-shield-alt"></i> Garantía y Soporte
                </h2>
                <div class="space-y-4">
                    @if(isset($content->data['warranty']))
                        <div class="bg-purple-50 rounded-lg p-4">
                            <h3 class="font-bold text-purple-800 mb-2">
                                <i class="fas fa-certificate"></i> Garantía
                            </h3>
                            <p class="text-purple-700">{{ $content->data['warranty'] }}</p>
                        </div>
                    @endif
                    
                    @if(isset($content->data['support']))
                        <div class="bg-indigo-50 rounded-lg p-4">
                            <h3 class="font-bold text-indigo-800 mb-2">
                                <i class="fas fa-headset"></i> Soporte Técnico
                            </h3>
                            <p class="text-indigo-700">{{ $content->data['support'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Botones de Compra -->
        @if(isset($content->data['purchase']))
            <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                <h3 class="text-xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-shopping-cart {{ $content->colors['primary'] }}"></i> 
                    ¿Interesado en este producto?
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if(isset($content->data['purchase']['website']))
                        <a href="{{ $content->data['purchase']['website'] }}" 
                           target="_blank"
                           class="bg-orange-500 text-white py-3 px-6 rounded-lg hover:bg-orange-600 transition flex items-center justify-center">
                            <i class="fas fa-external-link-alt mr-2"></i> Comprar Online
                        </a>
                    @endif
                    @if(isset($content->data['purchase']['phone']))
                        <a href="tel:{{ $content->data['purchase']['phone'] }}" 
                           class="bg-blue-500 text-white py-3 px-6 rounded-lg hover:bg-blue-600 transition flex items-center justify-center">
                            <i class="fas fa-phone mr-2"></i> Llamar
                        </a>
                    @endif
                    @if(isset($content->data['purchase']['whatsapp']))
                        <a href="https://wa.me/{{ $content->data['purchase']['whatsapp'] }}" 
                           target="_blank"
                           class="bg-green-500 text-white py-3 px-6 rounded-lg hover:bg-green-600 transition flex items-center justify-center">
                            <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                        </a>
                    @endif
                    @if(isset($content->data['purchase']['email']))
                        <a href="mailto:{{ $content->data['purchase']['email'] }}" 
                           class="bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition flex items-center justify-center">
                            <i class="fas fa-envelope mr-2"></i> Consultar
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Contenido Multimedia -->
        @if(isset($content->data['multimedia']))
            @php
                $multimedia = $content->data['multimedia'];
            @endphp
            
            @if(!empty($multimedia['video']))
                <!-- Video Section -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6 {{ $content->colors['border'] ?? 'border-red-200' }} border">
                    <h2 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-6">
                        <i class="fas fa-video"></i> Video del Producto
                    </h2>
                    
                    @php
                        $video = $multimedia['video'];
                        $embedUrl = '';
                        
                        if ($video['type'] === 'youtube') {
                            $videoId = '';
                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $video['url'], $matches)) {
                                $videoId = $matches[1];
                            }
                            $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                        }
                    @endphp
                    
                    @if($embedUrl)
                        <div class="aspect-video rounded-lg overflow-hidden shadow-lg">
                            <iframe 
                                src="{{ $embedUrl }}" 
                                frameborder="0" 
                                allow="autoplay; encrypted-media" 
                                allowfullscreen
                                class="w-full h-full">
                            </iframe>
                        </div>
                    @elseif($video['type'] === 'file_upload')
                        <div class="video-container-public loading" id="product-video-container">
                            <video 
                                controls 
                                class="video-player-public"
                                data-video-enhanced="true"
                                preload="metadata"
                                playsinline>
                                <source src="{{ $video['url'] }}" type="video/mp4">
                                <source src="{{ $video['url'] }}" type="video/webm">
                                Tu navegador no soporta el elemento de video.
                            </video>
                        </div>
                    @endif
                </div>
            @endif
        @endif

        <!-- Mensaje de Confianza -->
        <div class="text-center mt-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-4">
                    💎 Calidad Garantizada 💎
                </h3>
                <p class="text-gray-600">
                    Todos nuestros productos pasan por rigurosos controles de calidad. 
                    Tu satisfacción es nuestra prioridad.
                </p>
                <div class="mt-4 text-4xl">
                    ⭐🛍️✅
                </div>
            </div>
        </div>

    </div>

    <!-- Footer fuera del contenedor principal para ancho completo -->
    @if(!isset($hideFooter) || !$hideFooter)
        <x-shared.footer 
            :content="$content" 
            theme="product" 
            :showAdminInfo="request()->has('admin')" 
        />
    @endif



</body>
</html> 