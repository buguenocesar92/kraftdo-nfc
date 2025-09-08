<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content->title }} - Evento</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    
</head>
<body class="bg-gradient-to-br {{ $content->colors['gradient'] ?? 'from-blue-50 to-indigo-100' }} min-h-screen">
    
    <div class="max-w-2xl mx-auto px-4 py-6">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="bg-white rounded-xl shadow-lg p-8 {{ $content->colors['border'] ?? 'border-purple-200' }} border-2">
                <div class="mb-6">
                    <i class="{{ $content->icon }} text-6xl {{ $content->colors['primary'] }}"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $content->title }}</h1>
                @if($content->description)
                    <p class="text-gray-600 text-lg">{{ $content->description }}</p>
                @endif
                @if($content->image_url)
                    <img src="{{ $content->image_url }}" alt="{{ $content->title }}" 
                         class="w-full h-48 object-cover rounded-lg mt-6">
                @endif
            </div>
        </div>

        <!-- Información del Evento -->
        @if(isset($content->data['event_info']))
            <div class="bg-white rounded-xl shadow-lg p-8 mb-6 {{ $content->colors['border'] ?? 'border-purple-200' }} border-2">
                <h2 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-6 text-center">
                    <i class="fas fa-calendar-alt"></i> Detalles del Evento
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(isset($content->data['event_info']['date']))
                        <div class="text-center">
                            <i class="fas fa-calendar text-2xl {{ $content->colors['primary'] }} mb-2"></i>
                            <h3 class="font-semibold text-gray-800 mb-1">Fecha</h3>
                            <p class="text-gray-600">{{ $content->data['event_info']['date'] }}</p>
                        </div>
                    @endif
                    
                    @if(isset($content->data['event_info']['time']))
                        <div class="text-center">
                            <i class="fas fa-clock text-2xl {{ $content->colors['primary'] }} mb-2"></i>
                            <h3 class="font-semibold text-gray-800 mb-1">Hora</h3>
                            <p class="text-gray-600">{{ $content->data['event_info']['time'] }}</p>
                        </div>
                    @endif
                    
                    @if(isset($content->data['event_info']['location']))
                        <div class="text-center">
                            <i class="fas fa-map-marker-alt text-2xl {{ $content->colors['primary'] }} mb-2"></i>
                            <h3 class="font-semibold text-gray-800 mb-1">Lugar</h3>
                            <p class="text-gray-600">{{ $content->data['event_info']['location'] }}</p>
                        </div>
                    @endif
                    
                    @if(isset($content->data['event_info']['dress_code']))
                        <div class="text-center">
                            <i class="fas fa-tshirt text-2xl {{ $content->colors['primary'] }} mb-2"></i>
                            <h3 class="font-semibold text-gray-800 mb-1">Código de Vestimenta</h3>
                            <p class="text-gray-600">{{ $content->data['event_info']['dress_code'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Información de Contacto -->
        @if(isset($content->data['contact']))
            <div class="bg-white rounded-xl shadow-lg p-8 mb-6 {{ $content->colors['border'] ?? 'border-purple-200' }} border-2">
                <h2 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-6 text-center">
                    <i class="fas fa-address-card"></i> Información de Contacto
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(isset($content->data['contact']['organizer']))
                        <div class="text-center">
                            <i class="fas fa-user text-2xl {{ $content->colors['primary'] }} mb-2"></i>
                            <h3 class="font-semibold text-gray-800 mb-1">Organizador</h3>
                            <p class="text-gray-600">{{ $content->data['contact']['organizer'] }}</p>
                        </div>
                    @endif
                    
                    @if(isset($content->data['contact']['phone']))
                        <div class="text-center">
                            <i class="fas fa-phone text-2xl {{ $content->colors['primary'] }} mb-2"></i>
                            <h3 class="font-semibold text-gray-800 mb-1">Teléfono</h3>
                            <p class="text-gray-600">{{ $content->data['contact']['phone'] }}</p>
                        </div>
                    @endif
                    
                    @if(isset($content->data['contact']['email']))
                        <div class="text-center">
                            <i class="fas fa-envelope text-2xl {{ $content->colors['primary'] }} mb-2"></i>
                            <h3 class="font-semibold text-gray-800 mb-1">Email</h3>
                            <p class="text-gray-600">{{ $content->data['contact']['email'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Call to Action -->
        <div class="text-center mt-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-2xl font-bold {{ $content->colors['primary'] }} mb-4">
                    <i class="fas fa-calendar-check"></i> ¡Te Esperamos!
                </h3>
                <p class="text-gray-600 mb-4">
                    No te pierdas este evento especial. ¡Confirma tu asistencia!
                </p>
                <div class="text-4xl">
                    🎉📅🎊
                </div>
            </div>
        </div>

        <x-shared.footer 
            :content="$content" 
            theme="event" 
            :showAdminInfo="request()->has('admin')" 
        />

    </div>

    <!-- Footer fuera del contenedor principal para ancho completo -->
    <x-shared.footer 
        :content="$content" 
        theme="event" 
        :showAdminInfo="request()->has('admin')" 
    />

</body>
</html> 