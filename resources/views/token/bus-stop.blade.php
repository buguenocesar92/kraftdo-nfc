<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content->title ?? 'Paradero' }} - Información de Transporte</title>
    <meta name="description" content="{{ $content->description ?? 'Información del paradero' }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .bus-route {
            transition: all 0.3s ease;
        }
        .bus-route:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header con información de la municipalidad -->
    <header class="gradient-bg text-white">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @if($busStop->municipality_logo_url)
                        <img src="{{ $busStop->municipality_logo_url }}" alt="Logo {{ $busStop->municipality_name }}" class="h-16 w-16 rounded-full bg-white p-2">
                    @else
                        <div class="h-16 w-16 bg-white rounded-full flex items-center justify-center">
                            <i class="fas fa-city text-blue-600 text-2xl"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-bold">{{ $busStop->municipality_name }}</h1>
                        <p class="text-blue-100">{{ $content->title }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                        <i class="fas fa-map-marker-alt text-lg"></i>
                        <span class="ml-2 font-semibold">{{ $busStop->stop_id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Información del paradero -->
    <section class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-3">
                        <i class="fas fa-bus text-blue-600 mr-2"></i>
                        Información del Paradero
                    </h2>
                    <div class="space-y-2 text-gray-600">
                        <p><i class="fas fa-map-marker-alt w-5"></i> {{ $busStop->address }}</p>
                        <p><i class="fas fa-clock w-5"></i> Actualizado: {{ $content->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                
                @if($busStop->municipality_description)
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Sobre {{ $busStop->municipality_name }}</h3>
                    <p class="text-gray-600 text-sm">{{ $busStop->municipality_description }}</p>
                    @if($busStop->municipality_website)
                        <a href="{{ $busStop->municipality_website }}" target="_blank" class="inline-flex items-center mt-2 text-blue-600 hover:text-blue-800">
                            <i class="fas fa-external-link-alt mr-1 text-sm"></i>
                            Sitio web municipal
                        </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Rutas de transporte -->
    <section class="py-8">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-route text-green-600 mr-2"></i>
                Recorridos y Horarios
            </h2>
            
            @if($routes->count() > 0)
                <div class="grid gap-6">
                    @foreach($routes as $route)
                        <div class="bus-route bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-white text-blue-600 rounded-full w-10 h-10 flex items-center justify-center font-bold">
                                            {{ $route->route_number }}
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-lg">{{ $route->name }}</h3>
                                            <p class="text-blue-100">{{ $route->operator }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-xl">{{ $route->formatted_fare }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <div class="mb-4">
                                    <p class="text-gray-700 font-medium">
                                        <i class="fas fa-route text-green-600 mr-2"></i>
                                        {{ $route->full_route }}
                                    </p>
                                </div>
                                
                                <!-- Horarios por día -->
                                @if($route->schedules->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($route->schedules as $schedule)
                                            <div class="border-l-4 border-blue-400 pl-4">
                                                <div class="flex items-center justify-between mb-2">
                                                    <h4 class="font-semibold text-gray-800">{{ $schedule->day_name }}</h4>
                                                    @if($schedule->frequency_minutes)
                                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">
                                                            Cada {{ $schedule->frequency_minutes }} min
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-600">
                                                    <p class="mb-1">{{ $schedule->formatted_times }}</p>
                                                    @if($schedule->notes)
                                                        <p class="text-xs text-gray-500 italic">{{ $schedule->notes }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 italic">Horarios no disponibles</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-3xl mb-3"></i>
                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">Sin rutas disponibles</h3>
                    <p class="text-yellow-700">Actualmente no hay información de recorridos para este paradero.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Teléfonos de utilidad -->
    <section class="bg-gray-100 py-8">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-phone text-red-600 mr-2"></i>
                Teléfonos de Utilidad Pública
            </h2>
            
            @if($utilityPhones->count() > 0)
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($utilityPhones->groupBy('category') as $category => $phones)
                        <div class="bg-white rounded-lg shadow-md p-4">
                            <h3 class="font-bold text-gray-800 mb-3 border-b pb-2">
                                @switch($category)
                                    @case('emergencia')
                                        <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                                        Emergencias
                                        @break
                                    @case('salud')
                                        <i class="fas fa-heartbeat text-pink-600 mr-2"></i>
                                        Salud
                                        @break
                                    @case('municipal')
                                        <i class="fas fa-building text-blue-600 mr-2"></i>
                                        Municipal
                                        @break
                                    @case('servicios')
                                        <i class="fas fa-tools text-orange-600 mr-2"></i>
                                        Servicios
                                        @break
                                    @case('transporte')
                                        <i class="fas fa-car text-green-600 mr-2"></i>
                                        Transporte
                                        @break
                                @endswitch
                            </h3>
                            
                            <div class="space-y-2">
                                @foreach($phones as $phone)
                                    <a href="{{ $phone->call_link }}" class="block p-2 hover:bg-gray-50 rounded transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                @if($phone->icon)
                                                    <span class="text-lg">{{ $phone->icon }}</span>
                                                @endif
                                                <div>
                                                    <p class="font-medium text-gray-800">{{ $phone->name }}</p>
                                                    @if($phone->description)
                                                        <p class="text-xs text-gray-500">{{ $phone->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-bold text-blue-600">{{ $phone->formatted_phone }}</p>
                                                @if($phone->is_emergency)
                                                    <span class="bg-red-100 text-red-700 px-1 py-0.5 rounded text-xs">
                                                        Emergencia
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <i class="fas fa-phone-slash text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-600">No hay teléfonos de utilidad registrados para este paradero.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto px-4 text-center">
            <p class="mb-2">
                <i class="fas fa-qrcode mr-2"></i>
                Sistema NFC/QR para Paraderos - {{ $busStop->municipality_name }}
            </p>
            <p class="text-gray-400 text-sm">
                Información actualizada: {{ $content->updated_at->format('d/m/Y H:i') }}
            </p>
            @if(isset($token))
                <p class="text-gray-500 text-xs mt-2">
                    Token: {{ $token->token_id }}
                </p>
            @endif
        </div>
    </footer>
</body>
</html>