@extends('layouts.nfc')

@section('title', 'Contenido en Preparación - Kraftdo NFC')
@section('description', 'Tu contenido está siendo personalizado y pronto estará listo')

@push('styles')
<style>
    .kraftdo-animate-bounce-slow {
        animation: kraftdoBounce 2s infinite;
    }
    
    @keyframes kraftdoBounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-2xl">
        
        <!-- Hero Section with Animated Icon -->
        <x-nfc.hero-section 
            title="¡Contenido en Preparación!"
            emoji="🎨"
            icon="fas fa-paintbrush"
            :animated="true">
            <div class="kraftdo-animate-bounce-slow mt-4">
                <div class="inline-flex items-center px-4 py-2 kraftdo-glass rounded-full text-sm font-medium text-white">
                    <div class="w-3 h-3 bg-yellow-400 rounded-full kraftdo-animate-pulse-slow mr-2"></div>
                    Personalizando...
                </div>
            </div>
        </x-nfc.hero-section>

        <!-- Message Card -->
        <x-nfc.glass-card class="mb-8">
            <div class="text-center">
                <p class="text-xl text-white/90 mb-8">
                    {{ $message ?? 'El contenido aún está siendo personalizado... ¡Pronto estará listo!' }}
                </p>

                <!-- Progress Steps -->
                <x-nfc.process-steps 
                    :steps="[
                        [
                            'title' => 'Chip Asignado',
                            'description' => 'Tu chip está registrado',
                            'gradient' => 'from-green-500 to-teal-600'
                        ],
                        [
                            'title' => 'Personalizando',
                            'description' => 'Creando contenido único',
                            'gradient' => 'from-yellow-500 to-orange-600'
                        ],
                        [
                            'title' => 'Listo',
                            'description' => 'Próximamente...',
                            'gradient' => 'from-blue-500 to-purple-600'
                        ]
                    ]"
                    :currentStep="2" />

                <!-- Motivational Message -->
                <div class="bg-gradient-to-r from-pink-500/20 to-red-500/20 rounded-2xl p-6 mb-6 border border-pink-300/30">
                    <div class="flex items-center justify-center space-x-2 mb-3">
                        <i class="fas fa-heart text-pink-400 text-xl kraftdo-animate-pulse-slow"></i>
                        <h3 class="text-xl font-bold text-white">¡Será Increíble!</h3>
                        <i class="fas fa-heart text-pink-400 text-xl kraftdo-animate-pulse-slow"></i>
                    </div>
                    <p class="text-white/90">
                        Estamos preparando algo especial para ti. El propietario está añadiendo toques personales para hacer este contenido único.
                    </p>
                </div>

                <!-- Features Preview -->
                <x-nfc.feature-grid 
                    :features="[
                        [
                            'title' => 'Diseño Personalizado',
                            'description' => 'Colores y estilos únicos',
                            'icon' => 'fas fa-palette',
                            'gradient' => 'from-purple-500 to-pink-600'
                        ],
                        [
                            'title' => 'Contenido Multimedia',
                            'description' => 'Fotos, videos y audio',
                            'icon' => 'fas fa-images',
                            'gradient' => 'from-blue-500 to-indigo-600'
                        ],
                        [
                            'title' => 'Experiencia Interactiva',
                            'description' => 'Funciones especiales',
                            'icon' => 'fas fa-mobile-alt',
                            'gradient' => 'from-green-500 to-teal-600'
                        ],
                        [
                            'title' => 'Fácil de Compartir',
                            'description' => 'URL permanente',
                            'icon' => 'fas fa-share-alt',
                            'gradient' => 'from-pink-500 to-red-600'
                        ]
                    ]"
                    columns="2" />
            </div>
        </x-nfc.glass-card>

        <!-- Action Buttons -->
        <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center mb-8">
            <x-nfc.kraftdo-button 
                variant="outline"
                onclick="history.back()">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver
            </x-nfc.kraftdo-button>
            
            <x-nfc.kraftdo-button 
                variant="primary"
                onclick="location.reload()">
                <i class="fas fa-sync-alt mr-2"></i>
                Verificar de Nuevo
            </x-nfc.kraftdo-button>
        </div>

        <!-- Information Card -->
        <x-nfc.glass-card>
            <div class="text-center">
                <h4 class="text-lg font-semibold text-white mb-4">
                    <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                    Mientras tanto...
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="flex flex-col items-center text-center p-4">
                        <i class="fas fa-clock text-yellow-400 text-2xl mb-2"></i>
                        <h5 class="font-semibold text-white mb-1">Tiempo Estimado</h5>
                        <p class="text-sm text-white/70">Algunos minutos para completar</p>
                    </div>
                    <div class="flex flex-col items-center text-center p-4">
                        <i class="fas fa-bell text-green-400 text-2xl mb-2"></i>
                        <h5 class="font-semibold text-white mb-1">Notificaciones</h5>
                        <p class="text-sm text-white/70">Te avisaremos cuando esté listo</p>
                    </div>
                    <div class="flex flex-col items-center text-center p-4">
                        <i class="fas fa-bookmark text-purple-400 text-2xl mb-2"></i>
                        <h5 class="font-semibold text-white mb-1">Guardar Página</h5>
                        <p class="text-sm text-white/70">Puedes volver más tarde</p>
                    </div>
                </div>
            </div>
        </x-nfc.glass-card>
    </div>
</div>
@endsection