@extends('layouts.nfc')

@section('title', 'Crear Cuenta - Kraftdo NFC')
@section('description', 'Crea tu cuenta y personaliza tu chip NFC en minutos')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md">
        
        <!-- Hero Section -->
        <x-nfc.hero-section 
            title="¡Crear tu Cuenta!"
            subtitle="Solo necesitamos algunos datos para empezar"
            emoji="🎯"
            icon="fas fa-user-plus"
            :animated="true">
        </x-nfc.hero-section>

        <!-- Formulario -->
        <x-nfc.glass-card delay="0.2s">
            <form action="{{ route('nfc.create-account') }}" method="POST" 
                  x-data="kraftdoRegistrationForm()" 
                  @submit="handleSubmit($event)" 
                  class="space-y-6">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="id" value="{{ $id }}">

                <!-- Campo Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-user mr-2"></i>Nombre completo
                        <span class="text-red-300">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           x-model="form.name"
                           value="{{ old('name') }}"
                           required
                           maxlength="255"
                           class="w-full px-4 py-3 kraftdo-glass border border-white/30 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all backdrop-blur-sm"
                           placeholder="Tu nombre completo">
                    @error('name')
                        <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campo Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-envelope mr-2"></i>Correo electrónico
                        <span class="text-red-300">*</span>
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           x-model="form.email"
                           value="{{ old('email') }}"
                           required
                           maxlength="255"
                           class="w-full px-4 py-3 kraftdo-glass border border-white/30 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all backdrop-blur-sm"
                           placeholder="tu@email.com">
                    @error('email')
                        <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campo Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-lock mr-2"></i>Contraseña
                        <span class="text-red-300">*</span>
                    </label>
                    <div class="relative">
                        <input x-bind:type="showPassword ? 'text' : 'password'"
                               id="password"
                               name="password"
                               x-model="form.password"
                               required
                               class="w-full px-4 py-3 pr-12 kraftdo-glass border border-white/30 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all backdrop-blur-sm"
                               placeholder="Tu contraseña segura">
                        <button type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/70 hover:text-white">
                            <i x-bind:class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-white/60">Mínimo 8 caracteres</p>
                </div>

                <!-- Campo Confirmar Contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-lock mr-2"></i>Confirmar contraseña
                        <span class="text-red-300">*</span>
                    </label>
                    <div class="relative">
                        <input x-bind:type="showPasswordConfirm ? 'text' : 'password'"
                               id="password_confirmation"
                               name="password_confirmation"
                               x-model="form.passwordConfirm"
                               required
                               class="w-full px-4 py-3 pr-12 kraftdo-glass border border-white/30 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all backdrop-blur-sm"
                               placeholder="Confirma tu contraseña">
                        <button type="button" 
                                @click="showPasswordConfirm = !showPasswordConfirm"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/70 hover:text-white">
                            <i x-bind:class="showPasswordConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                    
                    <!-- Password Match Indicators -->
                    <template x-if="form.passwordConfirm && passwordsMatch">
                        <div class="mt-2 flex items-center text-sm text-green-300">
                            <i class="fas fa-check mr-2"></i>
                            Las contraseñas coinciden
                        </div>
                    </template>
                    
                    <template x-if="form.passwordConfirm && !passwordsMatch">
                        <div class="mt-2 flex items-center text-sm text-red-300">
                            <i class="fas fa-times mr-2"></i>
                            Las contraseñas no coinciden
                        </div>
                    </template>
                    
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
                               x-model="form.terms"
                               class="mt-1 w-5 h-5 bg-white/20 border border-white/30 rounded focus:ring-2 focus:ring-white/50 text-indigo-600 transition-all">
                        <span class="text-sm text-white/80">
                            Acepto los 
                            <a href="#" class="text-white underline hover:text-white/80 font-medium">términos y condiciones</a> 
                            y la 
                            <a href="#" class="text-white underline hover:text-white/80 font-medium">política de privacidad</a>
                        </span>
                    </label>
                    @error('terms')
                        <p class="mt-2 text-sm text-red-300 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Botón de envío -->
                <div>
                    <button type="submit" 
                            x-bind:disabled="!isFormValid"
                            x-bind:class="!isFormValid ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105'"
                            class="w-full font-semibold transition-all duration-300 transform focus:outline-none focus:ring-4 focus:ring-white/20 disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white kraftdo-shadow hover:shadow-xl px-8 py-4 text-lg rounded-xl">
                        <i class="fas fa-magic mr-3"></i>
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
        </x-nfc.glass-card>

        <!-- Ya tienes cuenta -->
        <div class="text-center mt-6 kraftdo-animate-fade-in" style="animation-delay: 0.4s;">
            <p class="text-white/80">
                ¿Ya tienes una cuenta? 
                <a href="{{ route('login') }}" class="text-white underline hover:text-white/80 font-medium">
                    Inicia sesión aquí
                </a>
            </p>
        </div>
    </div>
</div>

<script>
    function kraftdoRegistrationForm() {
        return {
            showPassword: false,
            showPasswordConfirm: false,
            form: {
                name: '',
                password: '',
                passwordConfirm: '',
                email: '',
                terms: false
            },
            
            get passwordsMatch() {
                if (!this.form.passwordConfirm) return true; // No mostrar error si está vacío
                return this.form.password === this.form.passwordConfirm;
            },
            
            get isFormValid() {
                return this.form.name.length > 0 &&
                       this.form.email.length > 0 && 
                       this.form.password.length >= 8 && 
                       this.passwordsMatch && 
                       this.form.terms;
            },
            
            handleSubmit(event) {
                console.log('Form validation:', {
                    name: this.form.name.length > 0,
                    email: this.form.email.length > 0,
                    password: this.form.password.length >= 8,
                    passwordsMatch: this.passwordsMatch,
                    terms: this.form.terms,
                    isValid: this.isFormValid
                });
                
                if (!this.isFormValid) {
                    event.preventDefault();
                    alert('Por favor completa todos los campos correctamente.');
                    return false;
                }
                
                return true;
            }
        }
    }
</script>

@if($errors->any() && !collect(['name', 'email', 'password', 'password_confirmation', 'terms'])->intersect($errors->keys())->count())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($errors->all() as $error)
                kraftdoToast('error', 'Error', '{{ $error }}');
            @endforeach
        });
    </script>
@endif

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            kraftdoToast('success', 'Éxito', '{{ session('success') }}');
        });
    </script>
@endif
@endsection