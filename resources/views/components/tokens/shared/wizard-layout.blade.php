@props(['token', 'content'])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Configurar {{ $token->name }} - Sistema NFC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/animations.css'])
    @vite(['resources/js/csrf-helpers.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="kraftdo-bg-pattern min-h-screen font-kraftdo">
    
    
    <div class="relative z-0">
        <!-- Header Moderno -->
        <header class="bg-kraftdo-dark/90 backdrop-blur-md shadow-2xl border-b border-kraftdo-navy/30 animate-fade-in-down">
            <div class="max-w-6xl mx-auto px-4 py-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-6">
                        <a href="{{ route('dashboard') }}" 
                           class="group flex items-center justify-center w-12 h-12 kraftdo-gradient rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110">
                            <i class="fas fa-arrow-left text-white group-hover:animate-pulse"></i>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-kraftdo-blue to-kraftdo-green bg-clip-text text-transparent">
                                <i class="{{ \App\Models\DynamicContent::getTypeIcon($token->content_type) }} text-kraftdo-green mr-2"></i>
                                Configurar: {{ $token->name }}
                            </h1>
                            <p class="text-kraftdo-lime/80 mt-1">Personaliza el contenido de tu chip NFC</p>
                        </div>
                    </div>
                    
                    <!-- Estado del Token -->
                    <div class="hidden md:flex items-center space-x-4">
                        <div class="bg-kraftdo-navy/30 border border-kraftdo-green/30 rounded-full px-4 py-2">
                            <span class="text-kraftdo-green font-medium text-sm">
                                <i class="fas fa-microchip text-kraftdo-blue mr-2"></i>
                                {{ $token->content_type }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="max-w-6xl mx-auto px-4 py-8">
            {{ $slot }}
        </div>
    </div>

    <!-- Footer fuera del contenedor principal para ancho completo -->
    <x-shared.footer 
        :content="$content" 
        theme="default" 
        :showAdminInfo="request()->has('admin')" 
    />

</body>
</html> 