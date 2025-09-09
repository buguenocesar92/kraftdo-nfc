<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Token No Disponible</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 50%, #4facfe 100%);
        }
    </style>
</head>
<body class="h-full gradient-bg">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <div class="mb-6">
                    <div class="text-6xl mb-4">😴</div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Token No Disponible</h1>
                    <p class="text-gray-600">Este token NFC no está activo en este momento.</p>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <p class="text-yellow-800 text-sm">
                        <strong>Estado:</strong> {{ ucfirst($token->status ?? 'inactivo') }}
                    </p>
                </div>

                <div class="text-center">
                    <p class="text-gray-500 text-sm">
                        Si crees que esto es un error, contacta al propietario del token.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>