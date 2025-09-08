<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content->title }} - Menú Digital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/restaurant-menu.css'])
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
</head>
<body>
    <!-- Header Hero Section -->
    <section class="header-section">
        <div class="hero-decorations">
            🍽️
        </div>
        <div class="container mx-auto px-6 py-16 text-center relative z-10">
            <h1 class="restaurant-title">{{ $content->title }}</h1>
            @if($content->description)
                <p class="restaurant-subtitle">{{ $content->description }}</p>
            @endif
            <div class="mt-8 text-6xl opacity-30">
                👨‍🍳 🌟 🍷
            </div>
        </div>
    </section>

    <div class="container mx-auto px-6 py-8 space-y-12">
        <!-- Restaurant Info Section -->
        @if(isset($content->data['restaurant_info']))
        <section class="restaurant-info-grid fade-in">
            <div class="grid grid-cols-1 md:grid-cols-4">
                @if(isset($content->data['restaurant_info']['address']))
                <div class="info-item">
                    <i class="fas fa-map-marker-alt info-icon"></i>
                    <h3 class="font-semibold text-gray-800 mb-2">Dirección</h3>
                    <p class="text-gray-600 text-sm">{{ $content->data['restaurant_info']['address'] }}</p>
                </div>
                @endif
                
                @if(isset($content->data['restaurant_info']['phone']))
                <div class="info-item">
                    <i class="fas fa-phone info-icon"></i>
                    <h3 class="font-semibold text-gray-800 mb-2">Teléfono</h3>
                    <p class="text-gray-600 text-sm">{{ $content->data['restaurant_info']['phone'] }}</p>
                </div>
                @endif
                
                @if(isset($content->data['restaurant_info']['hours']))
                <div class="info-item">
                    <i class="fas fa-clock info-icon"></i>
                    <h3 class="font-semibold text-gray-800 mb-2">Horarios</h3>
                    <p class="text-gray-600 text-sm">{{ $content->data['restaurant_info']['hours'] }}</p>
                </div>
                @endif
                
                @if(isset($content->data['restaurant_info']['whatsapp']))
                <div class="info-item">
                    <i class="fab fa-whatsapp info-icon"></i>
                    <h3 class="font-semibold text-gray-800 mb-2">WhatsApp</h3>
                    <p class="text-gray-600 text-sm">{{ $content->data['restaurant_info']['whatsapp'] }}</p>
                </div>
                @endif
            </div>
        </section>
        @endif

        <!-- Menu Section -->
        @if(isset($content->data['menu_items']) && count($content->data['menu_items']) > 0)
        <section class="menu-section fade-in">
            <!-- Section Header -->
            <div class="section-header">
                <h2 class="section-title">Nuestro Menú</h2>
                <p class="mt-2 text-lg opacity-90">Platillos preparados con amor y los mejores ingredientes</p>
            </div>

            <!-- Category Navigation -->
            @php
                $categories = [];
                foreach($content->data['menu_items'] as $item) {
                    $category = $item['category'] ?? 'General';
                    if (!isset($categories[$category])) {
                        $categories[$category] = [];
                    }
                    $categories[$category][] = $item;
                }
            @endphp

            @if(count($categories) > 1)
            <div class="category-nav">
                <button class="category-btn active" onclick="showCategory('all', this)">
                    <i class="fas fa-th-large mr-2"></i>
                    Todos los Platos
                </button>
                @foreach($categories as $category => $items)
                <button class="category-btn" onclick="showCategory('{{ strtolower(str_replace(' ', '-', $category)) }}', this)">
                    <i class="fas fa-{{ 
                        match($category) {
                            'Entradas' => 'seedling',
                            'Platos Principales' => 'drumstick-bite',
                            'Postres' => 'ice-cream',
                            'Bebidas' => 'glass-cheers',
                            'Especiales' => 'star',
                            default => 'utensils'
                        }
                    }} mr-2"></i>
                    {{ $category }} ({{ count($items) }})
                </button>
                @endforeach
            </div>
            @endif

            <!-- Menu Categories -->
            @foreach($categories as $category => $items)
            <div class="menu-category category-section" data-category="{{ strtolower(str_replace(' ', '-', $category)) }}">
                <h3 class="category-title">{{ $category }}</h3>
                
                <div class="dish-grid">
                    @foreach($items as $item)
                    <div class="dish-card">
                        <div class="dish-image">
                            @if(isset($item['image_url']) && !empty($item['image_url']))
                                <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}">
                            @else
                                <i class="fas fa-utensils"></i>
                            @endif
                            
                            @if(isset($item['popular']) && $item['popular'])
                            <div class="dish-popular">
                                <i class="fas fa-star"></i>
                                Popular
                            </div>
                            @endif
                        </div>
                        
                        <div class="dish-content">
                            <div class="dish-header">
                                <h4 class="dish-name">{{ $item['name'] }}</h4>
                                <span class="dish-price">${{ number_format($item['price'], 0) }}</span>
                            </div>
                            
                            @if(isset($item['description']))
                            <p class="dish-description">{{ $item['description'] }}</p>
                            @endif
                            
                            <div class="flex justify-between items-center mt-4">
                                <span class="dish-category-badge">{{ $item['category'] ?? 'General' }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </section>
        @endif

        <div class="decorative-divider">
            ✨ 🍽️ ✨
        </div>

        <!-- Contact & Order Section -->
        <section class="contact-section fade-in">
            <div class="p-8 text-center relative z-10">
                <h3 class="text-3xl font-bold mb-4">
                    <i class="fas fa-utensils mr-3"></i>
                    ¡Haz tu Pedido Ahora!
                </h3>
                <p class="text-xl mb-8 opacity-90">
                    Ordena directamente por WhatsApp y disfruta de nuestros deliciosos platillos
                </p>
                
                @if(isset($content->data['restaurant_info']['whatsapp']))
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $content->data['restaurant_info']['whatsapp']) }}?text=Hola, me gustaría hacer un pedido del menú de {{ $content->title }}" 
                   class="whatsapp-btn" target="_blank">
                    <i class="fab fa-whatsapp text-2xl"></i>
                    Ordenar por WhatsApp
                </a>
                @endif
                
                <div class="mt-8 text-5xl opacity-20">
                    🚀 📞 💚
                </div>
            </div>
        </section>
    </div>

    <script>
        // Category filtering
        function showCategory(categorySlug, clickedButton) {
            const categories = document.querySelectorAll('.category-section');
            const buttons = document.querySelectorAll('.category-btn');
            
            // Remove active class from all buttons
            buttons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            if (clickedButton) {
                clickedButton.classList.add('active');
            } else {
                // Si no hay botón, buscar el botón correspondiente
                const targetButton = categorySlug === 'all' 
                    ? document.querySelector('.category-btn[onclick*="\'all\'"]')
                    : document.querySelector(`.category-btn[onclick*="'${categorySlug}'"]`);
                if (targetButton) {
                    targetButton.classList.add('active');
                }
            }
            
            if (categorySlug === 'all') {
                categories.forEach(cat => cat.classList.remove('hidden'));
            } else {
                categories.forEach(cat => {
                    if (cat.dataset.category === categorySlug) {
                        cat.classList.remove('hidden');
                    } else {
                        cat.classList.add('hidden');
                    }
                });
            }
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Show all categories by default
            showCategory('all', null);
            
            // Add entrance animations with stagger
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(20px)';
                    el.style.animation = `fadeIn 0.8s ease-in-out ${index * 0.1}s forwards`;
                }, 100);
            });
        });

        // Smooth scrolling for better UX
        document.documentElement.style.scrollBehavior = 'smooth';
    </script>

    <!-- Footer -->
    <x-shared.footer 
        :content="$content" 
        theme="menu" 
        :showAdminInfo="request()->has('admin')" 
    />

</body>
</html>