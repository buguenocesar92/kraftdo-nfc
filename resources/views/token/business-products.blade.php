{{-- 
    Business Products Catalog View
    
    Complete product catalog page for business tokens
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Catálogo completo de productos de {{ $contentBusiness?->business_name ?? $token->name ?? 'negocio' }}. Descubre todos nuestros productos y servicios.">
    <meta name="keywords" content="catálogo, productos, {{ $contentBusiness?->business_name ?? $token->name ?? '' }}, negocio digital, NFC">
    <meta name="author" content="{{ $contentBusiness?->business_name ?? $token->name ?? 'Negocio' }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="{{ $contentBusiness?->color_palette['primary'] ?? '#3b82f6' }}">
    
    {{-- Page Title --}}
    <title>Productos - {{ $contentBusiness?->business_name ?? $token->name ?? 'Negocio' }} | Catálogo Digital</title>
    
    {{-- Open Graph --}}
    <meta property="og:title" content="Productos - {{ $contentBusiness?->business_name ?? $token->name ?? 'Negocio' }}">
    <meta property="og:description" content="Catálogo completo de productos de {{ $contentBusiness?->business_name ?? $token->name ?? 'negocio' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    @if($contentBusiness?->logo_url)
        <meta property="og:image" content="{{ asset('storage/' . $contentBusiness->logo_url) }}">
    @endif
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Productos - {{ $contentBusiness?->business_name ?? $token->name ?? 'Negocio' }}">
    <meta name="twitter:description" content="Catálogo completo de productos de {{ $contentBusiness?->business_name ?? $token->name ?? 'negocio' }}">
    
    {{-- Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Custom CSS --}}
    <style>
        :root {
            --primary-color: {{ $contentBusiness?->color_palette['primary'] ?? '#3b82f6' }};
            --secondary-color: {{ $contentBusiness?->color_palette['secondary'] ?? '#8b5cf6' }};
            --accent-color: {{ $contentBusiness?->color_palette['accent'] ?? '#ec4899' }};
        }
        
        .bg-primary { background-color: var(--primary-color); }
        .text-primary { color: var(--primary-color); }
        .border-primary { border-color: var(--primary-color); }
        .bg-secondary { background-color: var(--secondary-color); }
        .text-secondary { color: var(--secondary-color); }
        .bg-accent { background-color: var(--accent-color); }
        .text-accent { color: var(--accent-color); }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased">
    <div class="min-h-screen">
        {{-- Header con navegación --}}
        <header class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-40">
            <div class="max-w-4xl mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    {{-- Botón de regreso --}}
                    <a href="{{ route('token.show', $token->token_id) }}" 
                       class="flex items-center space-x-2 text-gray-600 dark:text-gray-400 hover:text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="font-medium">Volver</span>
                    </a>
                    
                    {{-- Título --}}
                    <div class="text-center flex-1">
                        <h1 class="text-lg font-bold text-gray-900 dark:text-white">Catálogo de Productos</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $contentBusiness->business_name }}</p>
                    </div>
                    
                    {{-- Logo --}}
                    @if($contentBusiness?->logo_url)
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                            <img src="{{ asset('storage/' . $contentBusiness->logo_url) }}" 
                                 alt="{{ $contentBusiness->business_name }}"
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm">
                            {{ substr($contentBusiness->business_name ?? 'N', 0, 1) }}
                        </div>
                    @endif
                </div>
            </div>
        </header>

        {{-- Contenido principal --}}
        <main class="max-w-4xl mx-auto px-4 py-6">
            {{-- Información del negocio --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 mb-6">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ $contentBusiness->business_name }}
                    </h2>
                    @if($contentBusiness->description)
                        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $contentBusiness->description }}</p>
                    @endif
                    <div class="flex items-center justify-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2L13.09 8.26L20 9L15 13.74L16.18 20.02L10 16.77L3.82 20.02L5 13.74L0 9L6.91 8.26L10 2Z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ $products->count() }} productos</span>
                        </span>
                        @if($contentBusiness->business_type)
                            <span>{{ ucfirst($contentBusiness->business_type) }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Grid de productos --}}
            <div class="space-y-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                    Todos nuestros productos
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($products as $product)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            {{-- Imagen del producto --}}
                            <div class="h-48 bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                                <svg class="w-20 h-20 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            
                            {{-- Información del producto --}}
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-3">
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-white flex-1">
                                        {{ $product->name ?? 'Producto' }}
                                    </h4>
                                    @if($product->in_stock)
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full ml-2">
                                            Disponible
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full ml-2">
                                            Agotado
                                        </span>
                                    @endif
                                </div>
                                
                                {{-- Precio --}}
                                @if($product->price)
                                    <div class="mb-3">
                                        <span class="text-2xl font-bold text-primary">
                                            ${{ number_format($product->price, 0, ',', '.') }}
                                        </span>
                                        <span class="text-sm text-gray-500 ml-1">{{ $product->currency }}</span>
                                    </div>
                                @endif
                                
                                {{-- Descripción --}}
                                @if($product->specifications)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 leading-relaxed">
                                        {{ $product->specifications }}
                                    </p>
                                @endif
                                
                                {{-- Detalles adicionales --}}
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @if($product->brand)
                                        <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded">
                                            {{ $product->brand }}
                                        </span>
                                    @endif
                                    @if($product->sku)
                                        <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded">
                                            SKU: {{ $product->sku }}
                                        </span>
                                    @endif
                                    @if($product->stock)
                                        <span class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded">
                                            Stock: {{ $product->stock }}
                                        </span>
                                    @endif
                                </div>
                                
                                {{-- Botón de compra --}}
                                @if($product->purchase_url && $product->in_stock)
                                    <a href="{{ $product->purchase_url }}" target="_blank"
                                       class="w-full bg-primary text-white py-3 px-4 rounded-xl font-medium text-center block hover:opacity-90 transition-opacity">
                                        Comprar ahora
                                    </a>
                                @elseif($contentBusiness->whatsapp_number && $product->in_stock)
                                    <a href="{{ $contentBusiness->getWhatsappUrlAttribute() }}?text=Hola, me interesa el producto: {{ $product->name }}" 
                                       target="_blank"
                                       class="w-full bg-green-500 text-white py-3 px-4 rounded-xl font-medium text-center block hover:opacity-90 transition-opacity flex items-center justify-center space-x-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.893 3.488"/>
                                        </svg>
                                        <span>Consultar por WhatsApp</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                No hay productos disponibles
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                Este negocio aún no ha agregado productos a su catálogo.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Información de contacto --}}
            @if($contentBusiness->hasContactInfo())
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 mt-8">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">¿Tienes dudas?</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($contentBusiness->contact_phone)
                            <a href="tel:{{ $contentBusiness->contact_phone }}" 
                               class="flex items-center space-x-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                                <span class="text-gray-900 dark:text-white">{{ $contentBusiness->contact_phone }}</span>
                            </a>
                        @endif
                        
                        @if($contentBusiness->whatsapp_number)
                            <a href="{{ $contentBusiness->getWhatsappUrlAttribute() }}" target="_blank"
                               class="flex items-center space-x-3 p-3 rounded-xl bg-green-50 dark:bg-green-900 hover:bg-green-100 dark:hover:bg-green-800 transition-colors">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.893 3.488"/>
                                </svg>
                                <span class="text-green-900 dark:text-green-100">WhatsApp</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </main>

        {{-- Footer --}}
        <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-12">
            <div class="max-w-4xl mx-auto px-4 py-6">
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Catálogo digital creado con 
                        <span class="font-semibold text-primary">Kraftdo NFC</span>
                    </p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>