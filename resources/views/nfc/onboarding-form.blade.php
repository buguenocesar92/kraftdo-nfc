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
                  @submit="handleSubmit()" 
                  class="space-y-6">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="id" value="{{ $id }}">

                <!-- Campo Nombre -->
                <x-nfc.form-field 
                    type="text"
                    name="name" 
                    label="Nombre completo"
                    icon="fas fa-user"
                    placeholder="Tu nombre completo"
                    :value="old('name')"
                    :error="$errors->first('name')"
                    maxlength="255"
                    required />

                <!-- Campo Email -->
                <x-nfc.form-field 
                    type="email"
                    name="email" 
                    label="Correo electrónico"
                    icon="fas fa-envelope"
                    placeholder="tu@email.com"
                    :value="old('email')"
                    :error="$errors->first('email')"
                    maxlength="255"
                    validation="email"
                    required />

                <!-- Campo Contraseña -->
                <x-nfc.form-field 
                    type="password"
                    name="password" 
                    label="Contraseña"
                    icon="fas fa-lock"
                    placeholder="Tu contraseña segura"
                    validation="password"
                    x-model="form.password"
                    required />

                <!-- Campo Confirmar Contraseña -->
                <x-nfc.form-field 
                    type="password"
                    name="password_confirmation" 
                    label="Confirmar contraseña"
                    icon="fas fa-lock"
                    placeholder="Confirma tu contraseña"
                    x-model="form.passwordConfirm"
                    x-on:input="validatePasswordMatch()"
                    :class="form.passwordConfirm && !passwordsMatch ? 'border-red-400' : ''"
                    required>
                    
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
                </x-nfc.form-field>

                <!-- Términos y Condiciones -->
                <x-nfc.form-field 
                    type="checkbox"
                    name="terms"
                    :error="$errors->first('terms')"
                    required>
                    Acepto los 
                    <a href="#" class="text-white underline hover:text-white/80 font-medium">términos y condiciones</a> 
                    y la 
                    <a href="#" class="text-white underline hover:text-white/80 font-medium">política de privacidad</a>
                </x-nfc.form-field>

                <!-- Botón de envío -->
                <div>
                    <x-nfc.kraftdo-button 
                        type="submit"
                        variant="primary"
                        size="lg"
                        icon="fas fa-magic"
                        class="w-full"
                        x-bind:disabled="!isFormValid"
                        x-bind:class="!isFormValid ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105'">
                        ¡Crear Mi Cuenta y Personalizar!
                    </x-nfc.kraftdo-button>
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
            form: {
                password: '',
                passwordConfirm: '',
                email: '',
                terms: false
            },
            
            init() {
                // Set up form validation
                this.setupValidation();
            },
            
            get passwordsMatch() {
                return this.form.password === this.form.passwordConfirm;
            },
            
            get isFormValid() {
                return this.form.password.length >= 8 && 
                       this.passwordsMatch && 
                       this.form.email.length > 0 && 
                       this.form.terms;
            },
            
            validatePasswordMatch() {
                // Visual feedback is handled by the template conditions
            },
            
            handleSubmit() {
                if (!this.isFormValid) {
                    kraftdoToast('error', 'Formulario Incompleto', 'Por favor completa todos los campos correctamente.');
                    return false;
                }
                
                // Show loading state
                this.$dispatch('set-loading', { state: true, message: 'Creando tu cuenta...' });
                return true;
            },
            
            setupValidation() {
                // Additional form validation can be added here
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