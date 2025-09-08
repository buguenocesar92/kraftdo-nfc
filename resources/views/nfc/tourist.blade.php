<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content->title }} - Información Turística</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    
</head>
<body class="bg-gradient-to-br {{ $content->colors['gradient'] ?? 'from-green-50 to-blue-100' ?? 'from-green-50 to-blue-100' }} min-h-screen">
    
    <div class="max-w-4xl mx-auto px-4 py-6">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="bg-white rounded-xl shadow-lg p-8 {{ $content->colors['border'] ?? 'border-green-200' }} border-2">
                <div class="mb-6">
                    <i class="{{ $content->icon }} text-6xl {{ $content->colors['primary'] }}"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">{{ $content->title }}</h1>
                @if($content->description)
                    <p class="text-gray-600 text-lg">{{ $content->description }}</p>
                @endif
                @if($content->image_url)
                    <img src="{{ $content->image_url }}" alt="{{ $content->title }}" 
                         class="w-full h-64 object-cover rounded-lg mt-6">
                @endif
            </div>
        </div>

        <!-- Ubicación -->
        @if(isset($content->data['location_info']['name']))
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 {{ $content->colors['border'] ?? 'border-green-200' }} border">
                <h2 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-4">
                    <i class="fas fa-map-marker-alt"></i> Ubicación
                </h2>
                <div class="bg-gradient-to-r {{ $content->colors['gradient'] ?? 'from-green-50 to-blue-100' }} rounded-lg p-4">
                    <p class="text-lg font-semibold text-gray-800">
                        📍 {{ $content->data['location_info']['name'] }}
                        @if(isset($content->data['location_info']['region']))
                            , {{ $content->data['location_info']['region'] }}
                        @endif
                    </p>
                    @if(isset($content->data['location_info']['address']))
                        <p class="text-gray-600 mt-2">
                            <i class="fas fa-map-pin"></i> {{ $content->data['location_info']['address'] }}
                        </p>
                    @endif
                </div>
                
                <!-- Puntos Destacados -->
                @if(isset($content->data['location_info']['highlights']) && is_array($content->data['location_info']['highlights']))
                    <div class="mt-4">
                        <h3 class="font-bold text-gray-800 mb-3">
                            <i class="fas fa-star text-yellow-500"></i> Puntos Destacados
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach($content->data['location_info']['highlights'] as $highlight)
                                <div class="flex items-center bg-yellow-50 rounded-lg p-2">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span class="text-gray-700 text-sm">{{ $highlight }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Galería de Fotos Turísticas -->
        @if(isset($content->data['gallery']['main_image']) || (isset($content->data['gallery']['other_images']) && count($content->data['gallery']['other_images']) > 0))
            <div class="bg-gradient-to-br {{ $content->colors['gradient'] ?? 'from-green-50 to-blue-100' }} rounded-xl shadow-xl p-8 mb-8 {{ $content->colors['border'] ?? 'border-green-200' }} border-2">
                <div class="text-center mb-8">
                    <h2 class="text-4xl font-bold {{ $content->colors['primary'] }} mb-3">
                        📸 Galería Turística
                    </h2>
                    <p class="text-lg text-gray-700 font-medium">Explora la belleza e historia de estos lugares únicos</p>
                    <div class="w-24 h-1 bg-gradient-to-r {{ $content->colors['gradient'] ?? 'from-green-50 to-blue-100' }} mx-auto mt-4 rounded-full"></div>
                </div>
                
                <!-- Foto destacada (imagen principal) -->
                <div class="mb-6">
                    @if(isset($content->data['gallery']['main_image']))
                        <div class="relative overflow-hidden rounded-2xl shadow-2xl hover:shadow-3xl transition-all duration-500 group">
                            <img src="{{ $content->data['gallery']['main_image'] }}" 
                                 alt="Foto principal de {{ $content->data['location_info']['name'] ?? 'lugar turístico' }}" 
                                 class="w-full h-64 md:h-80 object-cover cursor-pointer group-hover:scale-110 transition-transform duration-700"
                                 onclick="openPhotoModal('{{ $content->data['gallery']['main_image'] }}', 'Foto principal')">
                            
                            <!-- Overlay con gradiente -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-80 pointer-events-none"></div>
                            
                            <!-- Título destacado -->
                            <div class="absolute bottom-0 left-0 right-0 p-6 pointer-events-none">
                                <h3 class="text-2xl font-bold text-white mb-2">Foto Principal</h3>
                                <p class="text-white/90 text-sm">Haz clic para ampliar</p>
                            </div>
                            
                            <!-- Icono de ampliar -->
                            <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-sm rounded-full p-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                                <i class="fas fa-expand-alt text-white text-lg"></i>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Galería secundaria -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @if(isset($content->data['gallery']['other_images']) && is_array($content->data['gallery']['other_images']))
                        @foreach($content->data['gallery']['other_images'] as $index => $imageUrl)
                            <div class="relative overflow-hidden rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 group">
                                <img src="{{ $imageUrl }}" 
                                     alt="Foto turística {{ $index + 1 }}" 
                                     class="w-full h-24 md:h-32 object-cover cursor-pointer group-hover:scale-110 transition-transform duration-500"
                                     onclick="openPhotoModal('{{ $imageUrl }}', 'Foto turística {{ $index + 1 }}')">
                                
                                <!-- Overlay hover -->
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 pointer-events-none"></div>
                                
                                <!-- Título en hover -->
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-2 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300 pointer-events-none">
                                    <p class="text-white text-xs font-medium">Foto {{ $index + 1 }}</p>
                                </div>
                            
                            <!-- Número de foto -->
                            <div class="absolute top-2 left-2 bg-{{ $content->colors['primary'] }} text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                                {{ $index + 2 }}
                            </div>
                            
                            <!-- Icono de ampliar -->
                            <div class="absolute top-2 right-2 bg-white/80 rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                                <i class="fas fa-search-plus text-gray-800 text-xs"></i>
                            </div>
                        </div>
                    @endforeach
                    @endif
                </div>
                
                <!-- Contador de fotos -->
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600 font-medium">
                        <i class="fas fa-images {{ $content->colors['secondary'] }} mr-2"></i>
                        @php
                            $photoCount = 0;
                            if (isset($content->data['gallery']['main_image'])) $photoCount++;
                            if (isset($content->data['gallery']['other_images']) && is_array($content->data['gallery']['other_images'])) {
                                $photoCount += count($content->data['gallery']['other_images']);
                            }
                        @endphp
                        {{ $photoCount }} fotos increíbles para descubrir
                    </p>
                </div>
            </div>
        @endif

        <!-- Alojamiento -->
        @if(isset($content->data['accommodation']['main_hotel']) || isset($content->data['accommodation']['other_options']))
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 {{ $content->colors['border'] ?? 'border-green-200' }} border">
                <h2 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-6">
                    <i class="fas fa-bed"></i> Alojamiento
                </h2>
                <div class="space-y-4">
                    @if(isset($content->data['accommodation']['main_hotel']))
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="font-bold text-blue-800 mb-2">
                                <i class="fas fa-hotel"></i> Hotel Principal
                            </h3>
                            <p class="text-blue-700">{{ $content->data['accommodation']['main_hotel'] }}</p>
                            @if(isset($content->data['accommodation']['hotel_phone']))
                                <p class="text-blue-600 text-sm mt-1">
                                    <i class="fas fa-phone"></i> {{ $content->data['accommodation']['hotel_phone'] }}
                                </p>
                            @endif
                        </div>
                    @endif
                    
                    @if(isset($content->data['accommodation']['other_options']) && is_array($content->data['accommodation']['other_options']))
                        <div class="bg-green-50 rounded-lg p-4">
                            <h3 class="font-bold text-green-800 mb-3">
                                <i class="fas fa-list"></i> Otras Opciones
                            </h3>
                            <div class="space-y-2">
                                @foreach($content->data['accommodation']['other_options'] as $option)
                                    <div class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        <span class="text-green-700">{{ $option }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Información de Contacto y Emergencias -->
        @if(isset($content->data['contact_info']['tourism_office']) || isset($content->data['contact_info']['emergency']))
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 {{ $content->colors['border'] ?? 'border-green-200' }} border">
                <h2 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-6">
                    <i class="fas fa-phone"></i> Información de Contacto y Emergencias
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(isset($content->data['contact_info']['tourism_office']))
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="font-bold text-blue-800 mb-2">
                                <i class="fas fa-info-circle"></i> Oficina de Turismo
                            </h3>
                            <p class="text-blue-700">{{ $content->data['contact_info']['tourism_office'] }}</p>
                        </div>
                    @endif
                    
                    @if(isset($content->data['contact_info']['emergency']))
                        <div class="bg-red-50 rounded-lg p-4">
                            <h3 class="font-bold text-red-800 mb-2">
                                <i class="fas fa-exclamation-triangle"></i> Emergencias
                            </h3>
                            <p class="text-red-700">{{ $content->data['contact_info']['emergency'] }}</p>
                        </div>
                    @endif
                    
                    @if(isset($content->data['contact_info']['police']))
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="font-bold text-blue-800 mb-2">
                                <i class="fas fa-shield-alt"></i> Carabineros
                            </h3>
                            <p class="text-blue-700">{{ $content->data['contact_info']['police'] }}</p>
                        </div>
                    @endif
                    
                    @if(isset($content->data['contact_info']['hospital']))
                        <div class="bg-green-50 rounded-lg p-4">
                            <h3 class="font-bold text-green-800 mb-2">
                                <i class="fas fa-hospital"></i> Hospital
                            </h3>
                            <p class="text-green-700">{{ $content->data['contact_info']['hospital'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif



        <!-- Actividades y Tours -->
        @if(isset($content->data['activities']['main_activities']))
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 {{ $content->colors['border'] ?? 'border-green-200' }} border">
                <h2 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-6">
                    <i class="fas fa-hiking"></i> Actividades y Tours
                </h2>
                <div class="space-y-4">
                    @if(isset($content->data['activities']['main_activities']) && is_array($content->data['activities']['main_activities']))
                        @foreach($content->data['activities']['main_activities'] as $activity)
                            <div class="bg-gradient-to-r {{ $content->colors['gradient'] ?? 'from-green-50 to-blue-100' }} rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-500 mr-3 text-xl"></i>
                                    <span class="font-semibold text-gray-800">{{ $activity }}</span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    
                    @if(isset($content->data['activities']['main_agency']))
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="font-bold text-blue-800 mb-2">
                                <i class="fas fa-building"></i> Agencia Principal
                            </h3>
                            <p class="text-blue-700">{{ $content->data['activities']['main_agency'] }}</p>
                            @if(isset($content->data['activities']['agency_phone']))
                                <p class="text-blue-600 text-sm mt-1">
                                    <i class="fas fa-phone"></i> {{ $content->data['activities']['agency_phone'] }}
                                </p>
                            @endif
                        </div>
                    @endif
                    
                    @if(isset($content->data['activities']['prices']) && is_array($content->data['activities']['prices']))
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <h3 class="font-bold text-yellow-800 mb-3">
                                <i class="fas fa-dollar-sign"></i> Precios Referenciales
                            </h3>
                            <div class="space-y-2">
                                @foreach($content->data['activities']['prices'] as $price)
                                    <div class="flex items-center">
                                        <i class="fas fa-tag text-yellow-500 mr-2"></i>
                                        <span class="text-yellow-700">{{ $price }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Gastronomía Local -->
        @if(isset($content->data['gastronomy']['typical_dishes']))
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 {{ $content->colors['border'] ?? 'border-green-200' }} border">
                <h2 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-6">
                    <i class="fas fa-utensils"></i> Gastronomía Local
                </h2>
                <div class="space-y-4">
                    @if(isset($content->data['gastronomy']['typical_dishes']) && is_array($content->data['gastronomy']['typical_dishes']))
                        <div class="bg-orange-50 rounded-lg p-4">
                            <h3 class="font-bold text-orange-800 mb-3">
                                <i class="fas fa-star"></i> Platos Típicos
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach($content->data['gastronomy']['typical_dishes'] as $dish)
                                    <div class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        <span class="text-orange-700">{{ $dish }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if(isset($content->data['gastronomy']['recommended_restaurant']))
                        <div class="bg-green-50 rounded-lg p-4">
                            <h3 class="font-bold text-green-800 mb-2">
                                <i class="fas fa-restaurant"></i> Restaurante Recomendado
                            </h3>
                            <p class="text-green-700">{{ $content->data['gastronomy']['recommended_restaurant'] }}</p>
                            @if(isset($content->data['gastronomy']['restaurant_phone']))
                                <p class="text-green-600 text-sm mt-1">
                                    <i class="fas fa-phone"></i> {{ $content->data['gastronomy']['restaurant_phone'] }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Clima y Mejor Época -->
        @if(isset($content->data['climate']['average_temperature']))
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 {{ $content->colors['border'] ?? 'border-green-200' }} border">
                <h2 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-6">
                    <i class="fas fa-cloud-sun"></i> Clima y Mejor Época
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(isset($content->data['climate']['average_temperature']))
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="font-bold text-blue-800 mb-2">
                                <i class="fas fa-thermometer-half"></i> Temperatura Promedio
                            </h3>
                            <p class="text-blue-700">{{ $content->data['climate']['average_temperature'] }}</p>
                        </div>
                    @endif
                    
                    @if(isset($content->data['climate']['best_season']))
                        <div class="bg-green-50 rounded-lg p-4">
                            <h3 class="font-bold text-green-800 mb-2">
                                <i class="fas fa-calendar-check"></i> Mejor Época
                            </h3>
                            <p class="text-green-700">{{ $content->data['climate']['best_season'] }}</p>
                        </div>
                    @endif
                    
                    @if(isset($content->data['climate']['description']))
                        <div class="md:col-span-2 bg-yellow-50 rounded-lg p-4">
                            <h3 class="font-bold text-yellow-800 mb-2">
                                <i class="fas fa-info-circle"></i> Descripción del Clima
                            </h3>
                            <p class="text-yellow-700">{{ $content->data['climate']['description'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Información Adicional -->
        @if(isset($content->data['additional_info']['visiting_hours']))
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6 {{ $content->colors['border'] ?? 'border-green-200' }} border">
                <h2 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-6">
                    <i class="fas fa-info-circle"></i> Información Adicional
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(isset($content->data['additional_info']['visiting_hours']))
                        <div class="bg-purple-50 rounded-lg p-4">
                            <h3 class="font-bold text-purple-800 mb-2">
                                <i class="fas fa-clock"></i> Horarios de Visita
                            </h3>
                            <p class="text-purple-700">{{ $content->data['additional_info']['visiting_hours'] }}</p>
                        </div>
                    @endif
                    
                    @if(isset($content->data['additional_info']['entrance_fee']))
                        <div class="bg-orange-50 rounded-lg p-4">
                            <h3 class="font-bold text-orange-800 mb-2">
                                <i class="fas fa-ticket-alt"></i> Entrada
                            </h3>
                            <p class="text-orange-700">{{ $content->data['additional_info']['entrance_fee'] }}</p>
                        </div>
                    @endif
                    
                    @if(isset($content->data['additional_info']['recommendations']))
                        <div class="md:col-span-2 bg-blue-50 rounded-lg p-4">
                            <h3 class="font-bold text-blue-800 mb-2">
                                <i class="fas fa-lightbulb"></i> Recomendaciones
                            </h3>
                            @if(is_array($content->data['additional_info']['recommendations']))
                                <div class="space-y-2">
                                    @foreach($content->data['additional_info']['recommendations'] as $recommendation)
                                        <div class="flex items-center">
                                            <i class="fas fa-check text-green-500 mr-2"></i>
                                            <span class="text-blue-700">{{ $recommendation }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-blue-700">{{ $content->data['additional_info']['recommendations'] }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif



        <!-- Mensaje de Bienvenida -->
        <div class="text-center mt-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-4">
                    🌟 ¡Descubre Chile! 🌟
                </h3>
                <p class="text-gray-600">
                    Explora la riqueza histórica, natural y cultural de estos destinos únicos. 
                    ¡Que tengas una experiencia inolvidable llena de descubrimientos!
                </p>
                <div class="mt-4 text-4xl">
                    ⛰️🏭🌄
                </div>
            </div>
        </div>

        <x-shared.footer 
            :content="$content" 
            theme="tourist" 
            :showAdminInfo="request()->has('admin')" 
        />

    </div>

    <!-- Modal Simple para Fotos -->
    <div id="photoModal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50" style="display: none;">
        <!-- Botón Cerrar -->
        <button onclick="closeModal()" class="absolute top-5 right-5 text-white text-3xl font-bold z-60 hover:text-gray-300">
            ✕
        </button>
        
        <!-- Botones de Navegación -->
        <button onclick="prevPhoto()" class="absolute left-5 top-1/2 transform -translate-y-1/2 text-white text-4xl font-bold z-60 hover:text-gray-300">
            ‹
        </button>
        
        <button onclick="nextPhoto()" class="absolute right-5 top-1/2 transform -translate-y-1/2 text-white text-4xl font-bold z-60 hover:text-gray-300">
            ›
        </button>
        
        <!-- Contenedor de la Imagen -->
        <div class="relative max-w-full max-h-full p-10">
            <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain">
            
            <!-- Info de la Foto -->
            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-60 text-white p-3 text-center">
                <h3 id="modalTitle" class="text-lg font-bold"></h3>
                <p id="modalCounter" class="text-sm"></p>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let currentIndex = 0;
        let photos = [];
        
        // Preparar array de fotos
        @if(isset($content->data['gallery']['main_image']))
            photos.push('{{ $content->data['gallery']['main_image'] }}');
        @endif
        @if(isset($content->data['gallery']['other_images']) && is_array($content->data['gallery']['other_images']))
            @foreach($content->data['gallery']['other_images'] as $imageUrl)
                photos.push('{{ $imageUrl }}');
            @endforeach
        @endif
        
        // Función principal para abrir modal
        function openPhotoModal(imageUrl, title) {
            // Encontrar índice de la foto
            currentIndex = photos.indexOf(imageUrl);
            if (currentIndex === -1) currentIndex = 0;
            
            // Mostrar modal
            const modal = document.getElementById('photoModal');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Cargar foto actual
            updatePhoto();
        }
        
        // Actualizar foto en el modal
        function updatePhoto() {
            const photoUrl = photos[currentIndex];
            if (!photoUrl) return;
            
            document.getElementById('modalImage').src = photoUrl;
            document.getElementById('modalTitle').textContent = 'Foto turística';
            document.getElementById('modalCounter').textContent = `${currentIndex + 1} de ${photos.length}`;
        }
        
        // Cerrar modal
        function closeModal() {
            document.getElementById('photoModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Foto anterior
        function prevPhoto() {
            currentIndex = currentIndex > 0 ? currentIndex - 1 : photos.length - 1;
            updatePhoto();
        }
        
        // Foto siguiente
        function nextPhoto() {
            currentIndex = currentIndex < photos.length - 1 ? currentIndex + 1 : 0;
            updatePhoto();
        }
        
        // Cerrar con Escape y navegar con flechas
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('photoModal');
            if (modal.style.display === 'flex') {
                if (e.key === 'Escape') closeModal();
                if (e.key === 'ArrowLeft') prevPhoto();
                if (e.key === 'ArrowRight') nextPhoto();
            }
        });
        
        // Cerrar al hacer clic fuera de la imagen
        document.getElementById('photoModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>

    <!-- Footer fuera del contenedor principal para ancho completo -->
    <x-shared.footer 
        :content="$content" 
        theme="tourist" 
        :showAdminInfo="request()->has('admin')" 
    />

</body>
</html> 