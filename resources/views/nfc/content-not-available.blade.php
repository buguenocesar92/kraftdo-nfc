<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contenido No Disponible - Chip NFC</title>
    @vite(['resources/css/app.css'])
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
        }
        .animate-fade-in {
            animation: fadeIn 1s ease-out;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen text-white">
    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-2xl text-center">
            
            <!-- Estado del chip -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-32 h-32 bg-white/20 rounded-full shadow-2xl mb-6">
                    <i class="fas fa-pause-circle text-6xl text-white"></i>
                </div>
                <div class="text-8xl mb-4">⏸️</div>
            </div>

            <!-- Mensaje principal -->
            <div class="bg-white/10 backdrop-blur-md rounded-3xl shadow-2xl p-8 border border-white/20 mb-8">
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                    {{ $reason ?? 'Contenido No Disponible' }}
                </h1>
                
                <p class="text-xl text-white/90 mb-6">
                    Este chip está registrado pero aún no ha sido configurado por su propietario.
                </p>

                <!-- Información del chip -->
                @if($token)
                    <div class="text-left bg-white/10 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-white mb-3">
                            <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                            Información del Chip:
                        </h3>
                        <div class="space-y-2 text-white/80">
                            <p><span class="font-medium">Tipo:</span> {{ strtoupper($type) }}</p>
                            <p><span class="font-medium">Estado:</span> 
                                @if(!$token->is_active)
                                    <span class="text-red-300">Desactivado</span>
                                @else
                                    <span class="text-yellow-300">Sin contenido configurado</span>
                                @endif
                            </p>
                            @if($token->purchased_at)
                                <p><span class="font-medium">Registrado:</span> {{ $token->purchased_at->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Sugerencias -->
                <div class="text-left bg-white/10 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-white mb-3">
                        <i class="fas fa-lightbulb text-yellow-400 mr-2"></i>
                        Posibles razones:
                    </h3>
                    <ul class="space-y-2">
                        <li class="flex items-start space-x-3 text-white/80">
                            <i class="fas fa-clock text-blue-400 mt-1 flex-shrink-0"></i>
                            <span>El propietario aún no ha configurado el contenido</span>
                        </li>
                        <li class="flex items-start space-x-3 text-white/80">
                            <i class="fas fa-pause text-orange-400 mt-1 flex-shrink-0"></i>
                            <span>El chip ha sido temporalmente desactivado</span>
                        </li>
                        <li class="flex items-start space-x-3 text-white/80">
                            <i class="fas fa-cog text-purple-400 mt-1 flex-shrink-0"></i>
                            <span>Se está actualizando el contenido</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Acciones -->
            <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                <button onclick="history.back()" 
                        class="w-full sm:w-auto bg-white/20 hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver
                </button>
                
                <button onclick="location.reload()" 
                       class="w-full sm:w-auto bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-medium transition-all">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Intentar de Nuevo
                </button>
            </div>

            <!-- Información adicional -->
            <div class="mt-8 text-sm text-white/60">
                <p class="mb-2">
                    <i class="fas fa-question-circle mr-2"></i>
                    ¿Eres el propietario de este chip?
                </p>
                <p>
                    Inicia sesión en tu panel de administración para configurar el contenido.
                </p>
            </div>
        </div>
    </div>
</body>
</html>