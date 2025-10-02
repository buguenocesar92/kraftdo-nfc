<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Onboarding NFC - {{ $type }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-gradient-to-br from-blue-500 to-purple-600">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
                <div class="flex justify-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                </div>
                
                <h1 class="text-2xl font-bold text-gray-900 mb-4">
                    Configuración NFC
                </h1>
                
                <p class="text-gray-600 mb-6">
                    Tu contenido NFC está siendo configurado para el tipo: <strong>{{ $type }}</strong>
                </p>
                
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        ID: {{ $id }}
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="animate-spin inline-block w-8 h-8 border-4 border-blue-500 border-r-transparent rounded-full mb-4"></div>
                    <p class="text-sm text-gray-500">Configurando tu experiencia...</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>