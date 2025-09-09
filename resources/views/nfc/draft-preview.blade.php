<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contenido en Preparación - Chip NFC</title>
    @vite(['resources/css/app.css'])
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            background-attachment: fixed;
        }
        .animate-fade-in {
            animation: fadeIn 1s ease-out;
        }
        .animate-bounce-slow {
            animation: bounce 2s infinite;
        }
        .animate-pulse-slow {
            animation: pulse 3s infinite;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen text-white">
    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-2xl text-center">
            
            <!-- Icono animado -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-32 h-32 bg-white/20 rounded-full shadow-2xl mb-6 animate-pulse-slow">
                    <i class="fas fa-paintbrush text-6xl text-white"></i>
                </div>
                <div class="text-8xl mb-4 animate-bounce-slow">🎨</div>
            </div>

            <!-- Mensaje principal -->
            <div class="bg-white/10 backdrop-blur-md rounded-3xl shadow-2xl p-8 border border-white/20 mb-8 animate-fade-in">
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                    ¡Contenido en Preparación!
                </h1>
                
                <p class="text-xl text-white/90 mb-6">
                    {{ $message ?? 'El contenido aún está siendo personalizado... ¡Pronto estará listo!' }}
                </p>

                <!-- Proceso visual -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white/10 rounded-xl p-6 border border-white/20">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-check text-white text-xl"></i>
                        </div>
                        <h4 class="font-bold text-white mb-2">Chip Asignado</h4>
                        <p class="text-sm text-white/70">Tu chip está registrado</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-6 border border-white/20 relative">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-3 animate-pulse-slow">
                            <i class="fas fa-magic text-white text-xl"></i>
                        </div>
                        <h4 class="font-bold text-white mb-2">Personalizando</h4>
                        <p class="text-sm text-white/70">Creando contenido único</p>
                        <!-- Indicador de progreso -->
                        <div class="absolute top-2 right-2">
                            <div class="w-3 h-3 bg-yellow-400 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                    <div class="bg-white/10 rounded-xl p-6 border border-white/20 opacity-50">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-rocket text-white text-xl"></i>
                        </div>
                        <h4 class="font-bold text-white mb-2">Listo</h4>
                        <p class="text-sm text-white/70">Próximamente...</p>
                    </div>
                </div>

                <!-- Mensaje motivacional -->
                <div class="bg-gradient-to-r from-pink-500/20 to-red-500/20 rounded-2xl p-6 mb-6 border border-pink-300/30">
                    <div class="flex items-center justify-center space-x-2 mb-3">
                        <i class="fas fa-heart text-pink-400 text-xl animate-pulse"></i>
                        <h3 class="text-xl font-bold text-white">¡Será Increíble!</h3>
                        <i class="fas fa-heart text-pink-400 text-xl animate-pulse"></i>
                    </div>
                    <p class="text-white/90">
                        Estamos preparando algo especial para ti. El propietario está añadiendo toques personales para hacer este contenido único.
                    </p>
                </div>

                <!-- Características que tendrá -->
                <div class="text-left bg-white/10 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-3 text-center">
                        <i class="fas fa-sparkles text-yellow-400 mr-2"></i>
                        Lo que puedes esperar:
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="flex items-center space-x-3 text-white/80">
                            <i class="fas fa-palette text-purple-400 flex-shrink-0"></i>
                            <span>Diseño personalizado</span>
                        </div>
                        <div class="flex items-center space-x-3 text-white/80">
                            <i class="fas fa-images text-blue-400 flex-shrink-0"></i>
                            <span>Contenido multimedia</span>
                        </div>
                        <div class="flex items-center space-x-3 text-white/80">
                            <i class="fas fa-mobile-alt text-green-400 flex-shrink-0"></i>
                            <span>Experiencia interactiva</span>
                        </div>
                        <div class="flex items-center space-x-3 text-white/80">
                            <i class="fas fa-share-alt text-pink-400 flex-shrink-0"></i>
                            <span>Fácil de compartir</span>
                        </div>
                    </div>
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
                       class="w-full sm:w-auto bg-gradient-to-r from-pink-500 to-red-600 hover:from-pink-600 hover:to-red-700 text-white px-6 py-3 rounded-xl font-medium transition-all">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Verificar de Nuevo
                </button>
            </div>

            <!-- Información adicional -->
            <div class="mt-8 p-6 bg-white/10 rounded-2xl border border-white/20">
                <h4 class="text-lg font-semibold text-white mb-3">
                    <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                    Mientras tanto...
                </h4>
                <div class="text-sm text-white/80 space-y-2">
                    <p class="flex items-start space-x-2">
                        <i class="fas fa-clock text-yellow-400 mt-1 flex-shrink-0"></i>
                        <span>El proceso de personalización puede tomar algunos minutos</span>
                    </p>
                    <p class="flex items-start space-x-2">
                        <i class="fas fa-bell text-green-400 mt-1 flex-shrink-0"></i>
                        <span>Recibirás una notificación cuando esté listo (si proporcionaste tu email)</span>
                    </p>
                    <p class="flex items-start space-x-2">
                        <i class="fas fa-bookmark text-purple-400 mt-1 flex-shrink-0"></i>
                        <span>Puedes guardar esta página y volver más tarde</span>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-sm text-white/60">
                <p>
                    <i class="fas fa-heart text-red-400 mr-1"></i>
                    Hecho con amor y tecnología NFC
                </p>
            </div>
        </div>
    </div>
</body>
</html>