<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contenido No Disponible - NFC</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-gradient-to-br from-red-500 to-pink-600">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
                <div class="flex justify-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-pink-600 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
                
                <h1 class="text-2xl font-bold text-gray-900 mb-4">
                    Contenido No Disponible
                </h1>
                
                <p class="text-gray-600 mb-6">
                    {{ $reason }}
                </p>
                
                @if($token)
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <p class="text-sm text-gray-700">
                            <strong>Token:</strong> {{ $token->token_id }}<br>
                            <strong>Tipo:</strong> {{ $type }}<br>
                            <strong>ID:</strong> {{ $id }}
                        </p>
                    </div>
                @else
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <p class="text-sm text-gray-700">
                            <strong>Tipo:</strong> {{ $type }}<br>
                            <strong>ID:</strong> {{ $id }}
                        </p>
                    </div>
                @endif
                
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-4">
                        El contenido solicitado no está disponible en este momento.
                    </p>
                    
                    <button onclick="window.history.back()" 
                            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Volver
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>