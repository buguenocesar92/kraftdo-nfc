<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Personalizar Chip NFC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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
        <div class="w-full max-w-md">
            
            <!-- Header -->
            <div class="text-center mb-8 animate-fade-in">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full shadow-2xl mb-4">
                    <i class="fas fa-user-plus text-3xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">
                    ¡Crear tu Cuenta!
                </h1>
                <p class="text-white/80">Solo necesitamos algunos datos para empezar</p>
            </div>

            <!-- Formulario -->
            <div class="bg-white/10 backdrop-blur-md rounded-3xl shadow-2xl p-8 border border-white/20 animate-fade-in" style="animation-delay: 0.2s;">
                <form action="{{ route('nfc.create-account') }}" method="POST" x-data="{ 
                    showPassword: false,
                    showPasswordConfirm: false,
                    password: '',
                    passwordConfirm: '',
                    passwordsMatch() {
                        return this.password === this.passwordConfirm;
                    }
                }" class="space-y-6">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="id" value="{{ $id }}">

                    <!-- Nombre -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-white mb-2">
                            <i class="fas fa-user mr-2"></i>Nombre completo
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               required
                               maxlength="255"
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all"
                               placeholder="Tu nombre completo">
                        @error('name')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-white mb-2">
                            <i class="fas fa-envelope mr-2"></i>Correo electrónico
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               required
                               maxlength="255"
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all"
                               placeholder="tu@email.com">
                        @error('email')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-white mb-2">
                            <i class="fas fa-lock mr-2"></i>Contraseña
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" 
                                   id="password" 
                                   name="password" 
                                   x-model="password"
                                   required
                                   class="w-full px-4 py-3 pr-12 bg-white/20 border border-white/30 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all"
                                   placeholder="Tu contraseña segura">
                            <button type="button" 
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/70 hover:text-white">
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-white/60">Mínimo 8 caracteres</p>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-white mb-2">
                            <i class="fas fa-lock mr-2"></i>Confirmar contraseña
                        </label>
                        <div class="relative">
                            <input :type="showPasswordConfirm ? 'text' : 'password'" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   x-model="passwordConfirm"
                                   required
                                   class="w-full px-4 py-3 pr-12 bg-white/20 border border-white/30 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all"
                                   :class="passwordConfirm && !passwordsMatch() ? 'border-red-400' : ''"
                                   placeholder="Confirma tu contraseña">
                            <button type="button" 
                                    @click="showPasswordConfirm = !showPasswordConfirm"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/70 hover:text-white">
                                <i :class="showPasswordConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                        <div x-show="passwordConfirm && passwordsMatch()" class="mt-2 flex items-center text-sm text-green-300">
                            <i class="fas fa-check mr-2"></i>
                            Las contraseñas coinciden
                        </div>
                        <div x-show="passwordConfirm && !passwordsMatch()" class="mt-2 flex items-center text-sm text-red-300">
                            <i class="fas fa-times mr-2"></i>
                            Las contraseñas no coinciden
                        </div>
                        @error('password_confirmation')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Términos y Condiciones -->
                    <div>
                        <label class="flex items-start space-x-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="terms" 
                                   value="1"
                                   required
                                   class="mt-1 w-5 h-5 bg-white/20 border border-white/30 rounded focus:ring-2 focus:ring-white/50">
                            <span class="text-sm text-white/80">
                                Acepto los 
                                <a href="#" class="text-white underline hover:text-white/80">términos y condiciones</a> 
                                y la 
                                <a href="#" class="text-white underline hover:text-white/80">política de privacidad</a>
                            </span>
                        </label>
                        @error('terms')
                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Botón de envío -->
                    <div>
                        <button type="submit" 
                                :disabled="!passwordsMatch() || password.length < 8"
                                :class="!passwordsMatch() || password.length < 8 ? 'opacity-50 cursor-not-allowed' : 'hover:from-blue-600 hover:to-purple-700 transform hover:scale-105'"
                                class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-4 rounded-xl font-semibold shadow-xl transition-all duration-300">
                            <i class="fas fa-magic mr-2"></i>
                            ¡Crear Mi Cuenta y Personalizar!
                        </button>
                    </div>

                    <!-- Info adicional -->
                    <div class="text-center">
                        <p class="text-sm text-white/70 mb-2">
                            <i class="fas fa-shield-alt text-green-400 mr-1"></i>
                            Tu información está segura y protegida
                        </p>
                        <p class="text-xs text-white/60">
                            Al crear tu cuenta, podrás personalizar este chip y muchos más
                        </p>
                    </div>
                </form>
            </div>

            <!-- Ya tienes cuenta -->
            <div class="text-center mt-6 animate-fade-in" style="animation-delay: 0.4s;">
                <p class="text-white/80">
                    ¿Ya tienes una cuenta? 
                    <a href="{{ route('login') }}" class="text-white underline hover:text-white/80 font-medium">
                        Inicia sesión aquí
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- Mostrar errores generales -->
    @if($errors->any() && !$errors->has('name') && !$errors->has('email') && !$errors->has('password') && !$errors->has('password_confirmation') && !$errors->has('terms'))
        <div x-data="{ show: true }" x-show="show" class="fixed top-4 right-4 bg-red-500 text-white p-4 rounded-lg shadow-lg z-50 max-w-sm">
            @foreach($errors->all() as $error)
                <p class="text-sm">{{ $error }}</p>
            @endforeach
            <button @click="show = false" class="mt-2 text-sm underline opacity-80">Cerrar</button>
        </div>
    @endif

    <!-- Mensaje de éxito -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" class="fixed top-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg z-50 max-w-sm">
            <p class="text-sm">{{ session('success') }}</p>
            <button @click="show = false" class="mt-2 text-sm underline opacity-80">Cerrar</button>
        </div>
    @endif
</body>
</html>