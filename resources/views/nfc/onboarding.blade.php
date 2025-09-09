<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $config['title'] }} - Personalizar Chip NFC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        .animate-fade-in-down {
            animation: fadeInDown 1s ease-out;
        }
        .animate-fade-in-up {
            animation: fadeInUp 1s ease-out;
        }
        .animate-scale-in {
            animation: scaleIn 0.8s ease-out;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes fadeInDown {
            0% { opacity: 0; transform: translateY(-30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes scaleIn {
            0% { opacity: 0; transform: scale(0.8); }
            100% { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen text-white">
    
    <!-- Elementos Decorativos Animados -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full animate-float"></div>
        <div class="absolute top-32 right-16 w-16 h-16 bg-white/20 rounded-full animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-20 left-20 w-24 h-24 bg-white/10 rounded-full animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-32 right-10 w-12 h-12 bg-white/15 rounded-full animate-float" style="animation-delay: 0.5s;"></div>
        <div class="absolute top-1/2 left-1/4 text-6xl opacity-5 animate-float">{{ $config['emoji'] }}</div>
        <div class="absolute top-1/3 right-1/4 text-4xl opacity-10 animate-float" style="animation-delay: 1.5s;">✨</div>
    </div>

    <div class="relative z-10 w-full max-w-4xl mx-auto px-3 py-4 sm:px-6 lg:px-8 min-h-screen">
        
        <!-- Header -->
        <div class="text-center mb-12 animate-fade-in-down">
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-28 h-28 sm:w-36 sm:h-36 bg-white/20 rounded-full shadow-2xl animate-float backdrop-blur-sm">
                    <i class="{{ $config['icon'] }} text-4xl sm:text-6xl text-white"></i>
                </div>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4">
                ¡Bienvenido a tu Chip NFC!
            </h1>
            <div class="text-6xl mb-4 animate-float">{{ $config['emoji'] }}</div>
            <p class="text-lg sm:text-xl text-white/90 font-medium">
                {{ $config['title'] }} • {{ $config['message'] }}
            </p>
        </div>

        <!-- Contenido Principal -->
        <div class="mb-12 animate-fade-in-up" style="animation-delay: 0.3s;">
            <div class="bg-white/10 backdrop-blur-md rounded-3xl shadow-2xl p-8 sm:p-10 lg:p-12 border border-white/20 relative overflow-hidden">
                
                <!-- Elementos decorativos del contenido -->
                <div class="absolute top-6 left-6 text-5xl sm:text-7xl opacity-10 text-white">🎨</div>
                <div class="absolute bottom-6 right-6 text-4xl sm:text-5xl opacity-10 text-white">🚀</div>
                
                <div class="relative z-10 text-center">
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-6">
                        ✨ Contenido Listo para Personalizar
                    </h2>
                    
                    <!-- Preview del Contenido -->
                    <div class="bg-white/10 rounded-2xl p-6 mb-8 border border-white/20">
                        <div class="text-4xl mb-4">{{ $config['emoji'] }}</div>
                        <h3 class="text-xl font-bold text-white mb-2">{{ $config['title'] }}</h3>
                        <p class="text-white/80 mb-4">"{{ $config['message'] }}"</p>
                        <div class="inline-flex items-center px-4 py-2 bg-white/20 rounded-full text-sm font-medium text-white">
                            <i class="fas fa-magic mr-2"></i>
                            Listo para personalizar
                        </div>
                    </div>
                    
                    <!-- Características Destacadas -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                        <div class="bg-white/10 rounded-xl p-4 border border-white/20">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-paint-brush text-white"></i>
                            </div>
                            <h4 class="font-semibold text-white mb-1">Personalizable</h4>
                            <p class="text-sm text-white/70">Agrega tu toque personal</p>
                        </div>
                        <div class="bg-white/10 rounded-xl p-4 border border-white/20">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-share-alt text-white"></i>
                            </div>
                            <h4 class="font-semibold text-white mb-1">Compartible</h4>
                            <p class="text-sm text-white/70">URL pública permanente</p>
                        </div>
                        <div class="bg-white/10 rounded-xl p-4 border border-white/20">
                            <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-mobile-alt text-white"></i>
                            </div>
                            <h4 class="font-semibold text-white mb-1">Responsive</h4>
                            <p class="text-sm text-white/70">Funciona en cualquier dispositivo</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div class="mb-12 animate-scale-in" style="animation-delay: 0.6s;">
            <div class="bg-white/10 backdrop-blur-md rounded-3xl shadow-2xl p-8 sm:p-10 border border-white/20 relative overflow-hidden">
                
                <!-- Efectos de fondo -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-600/10"></div>
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-purple-600"></div>
                
                <div class="relative z-10 text-center">
                    @auth
                        <!-- Usuario ya autenticado -->
                        <div class="mb-6">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mb-4">
                                <i class="fas fa-user-check text-white text-2xl"></i>
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-bold text-white mb-4">
                                ¿Asignar este Chip a tu Cuenta?
                            </h2>
                            <p class="text-white/80 text-lg mb-8">
                                Este chip se asignará automáticamente a tu cuenta existente
                            </p>
                        </div>
                        
                        <!-- Beneficios Destacados -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                            <div class="flex items-center justify-center space-x-3 bg-white/10 rounded-xl p-4 border border-white/20">
                                <i class="fas fa-check-circle text-green-400 text-xl"></i>
                                <span class="font-medium text-white">Asignación Inmediata</span>
                            </div>
                            <div class="flex items-center justify-center space-x-3 bg-white/10 rounded-xl p-4 border border-white/20">
                                <i class="fas fa-palette text-blue-400 text-xl"></i>
                                <span class="font-medium text-white">Personalización Total</span>
                            </div>
                            <div class="flex items-center justify-center space-x-3 bg-white/10 rounded-xl p-4 border border-white/20">
                                <i class="fas fa-tachometer-alt text-purple-400 text-xl"></i>
                                <span class="font-medium text-white">Dashboard Completo</span>
                            </div>
                        </div>
                        
                        <form action="{{ route('nfc.assign-token') }}" method="POST" class="mb-6">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">
                            <input type="hidden" name="id" value="{{ $id }}">
                            <button type="submit" 
                                    class="group relative inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-2xl hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl text-lg font-semibold">
                                <i class="fas fa-link mr-3 group-hover:animate-pulse"></i>
                                Asignar a Mi Cuenta
                            </button>
                        </form>
                        
                        <p class="text-sm text-white/70 bg-white/10 rounded-full px-4 py-2 inline-block">
                            <i class="fas fa-info-circle mr-2 text-blue-400"></i>
                            Ya tienes {{ auth()->user()->dynamicContents()->count() }} contenido(s) • Asignación inmediata
                        </p>
                    @else
                        <!-- Usuario no autenticado -->
                        <div class="mb-6">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mb-4">
                                <i class="fas fa-magic text-white text-2xl"></i>
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-bold text-white mb-4">
                                ¿Quieres Personalizar este Contenido?
                            </h2>
                            <p class="text-white/80 text-lg mb-8">
                                Crea una cuenta y configura tu chip único con tu información personal
                            </p>
                        </div>
                        
                        <!-- Proceso Visual -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                            <div class="bg-white/10 rounded-xl p-6 border border-white/20">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <span class="text-white font-bold text-xl">1</span>
                                </div>
                                <h4 class="font-bold text-white mb-2">Crea tu Cuenta</h4>
                                <p class="text-sm text-white/70">Solo toma 2 minutos</p>
                            </div>
                            <div class="bg-white/10 rounded-xl p-6 border border-white/20">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <span class="text-white font-bold text-xl">2</span>
                                </div>
                                <h4 class="font-bold text-white mb-2">Personaliza Todo</h4>
                                <p class="text-sm text-white/70">Mensajes, colores, contenido</p>
                            </div>
                            <div class="bg-white/10 rounded-xl p-6 border border-white/20">
                                <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <span class="text-white font-bold text-xl">3</span>
                                </div>
                                <h4 class="font-bold text-white mb-2">Comparte</h4>
                                <p class="text-sm text-white/70">URL pública para siempre</p>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <a href="{{ route('nfc.onboarding') }}?TYPE={{ $type }}&ID={{ $id }}" 
                               class="group relative inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-2xl hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl text-lg font-semibold">
                                <i class="fas fa-magic mr-3 group-hover:animate-pulse"></i>
                                Personalizar Mi Chip
                            </a>
                        </div>
                        
                        <p class="text-sm text-white/70 bg-white/10 rounded-full px-4 py-2 inline-block">
                            <i class="fas fa-gift mr-2 text-green-400"></i>
                            Completamente gratis • Sin compromisos • Solo 2 minutos
                        </p>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="mb-8 animate-fade-in-up" style="animation-delay: 0.9s;">
            <div class="bg-white/10 backdrop-blur-md rounded-3xl shadow-xl p-8 border border-white/20">
                <div class="text-center mb-6">
                    <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">
                        <i class="fas fa-lightbulb text-yellow-400 mr-2"></i>
                        ¿Cómo Funciona la Magia?
                    </h3>
                    <p class="text-white/80">Tu chip NFC se convertirá en algo único y especial</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-mobile-alt text-white text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-white mb-2">Escanea</h4>
                        <p class="text-sm text-white/70">Con cualquier teléfono</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-edit text-white text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-white mb-2">Personaliza</h4>
                        <p class="text-sm text-white/70">Tu contenido único</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-share-alt text-white text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-white mb-2">Comparte</h4>
                        <p class="text-sm text-white/70">Con quien quieras</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-heart text-white text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-white mb-2">Disfruta</h4>
                        <p class="text-sm text-white/70">Momentos especiales</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-white/70 text-sm animate-fade-in-up" style="animation-delay: 1.2s;">
            <p class="mb-2">
                <i class="fas fa-shield-alt text-green-400 mr-2"></i>
                Seguro • Privado • Fácil de usar
            </p>
            <p>Powered by NFC Technology ✨</p>
        </div>
    </div>

    @if($errors->any())
        <div x-data="{ show: true }" x-show="show" class="fixed top-4 right-4 bg-red-500 text-white p-4 rounded-lg shadow-lg z-50">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
            <button @click="show = false" class="mt-2 text-sm underline">Cerrar</button>
        </div>
    @endif
</body>
</html>