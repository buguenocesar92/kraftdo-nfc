<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Chip NFC</title>
    @vite(['resources/css/app.css'])
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            background-attachment: fixed;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen text-white">
    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-2xl text-center">
            
            <!-- Icono de error -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-32 h-32 bg-white/20 rounded-full shadow-2xl mb-6">
                    <i class="fas fa-exclamation-triangle text-6xl text-white"></i>
                </div>
                <div class="text-8xl mb-4">😔</div>
            </div>

            <!-- Mensaje principal -->
            <div class="bg-white/10 backdrop-blur-md rounded-3xl shadow-2xl p-8 border border-white/20 mb-8">
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                    ¡Oops! Algo no está bien
                </h1>
                
                <p class="text-xl text-white/90 mb-6">
                    {{ $message ?? 'Ha ocurrido un error inesperado' }}
                </p>

                @if(isset($suggestions) && is_array($suggestions))
                    <div class="text-left bg-white/10 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-white mb-3">
                            <i class="fas fa-lightbulb text-yellow-400 mr-2"></i>
                            Posibles soluciones:
                        </h3>
                        <ul class="space-y-2">
                            @foreach($suggestions as $suggestion)
                                <li class="flex items-start space-x-3 text-white/80">
                                    <i class="fas fa-check-circle text-green-400 mt-1 flex-shrink-0"></i>
                                    <span>{{ $suggestion }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(isset($details))
                    <div class="text-sm text-white/70 bg-white/10 rounded-xl p-4 mb-6">
                        <strong>Detalles técnicos:</strong><br>
                        {{ $details }}
                    </div>
                @endif
            </div>

            <!-- Acciones -->
            <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                <button onclick="history.back()" 
                        class="w-full sm:w-auto bg-white/20 hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver
                </button>
                
                <a href="{{ route('home') }}" 
                   class="w-full sm:w-auto bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-medium transition-all inline-block">
                    <i class="fas fa-home mr-2"></i>
                    Ir al Inicio
                </a>
            </div>

            <!-- Información de soporte -->
            <div class="mt-8 text-sm text-white/60">
                <p>Si el problema persiste, contacta a soporte técnico</p>
                <p class="mt-1">
                    <i class="fas fa-envelope mr-1"></i>
                    soporte@kraftdo.com
                </p>
            </div>
        </div>
    </div>
</body>
</html>