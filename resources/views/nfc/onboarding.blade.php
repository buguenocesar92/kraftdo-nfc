@extends('layouts.nfc')

@section('title', $config['title'] . ' - Kraftdo NFC')
@section('description', 'Personaliza tu chip NFC con ' . $config['title'])

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('kraftdo', {
            setBackgroundEmoji(emoji) {
                document.dispatchEvent(new CustomEvent('set-background-emoji', { detail: emoji }));
            }
        });
    });
    
    document.addEventListener('set-background-emoji', (e) => {
        const app = document.querySelector('[x-data*="kraftdoApp"]');
        if (app && app._x_dataStack) {
            app._x_dataStack[0].setBrandingEmoji(e.detail);
        }
    });
</script>
@endpush

@section('content')
<div class="w-full max-w-4xl mx-auto px-3 py-4 sm:px-6 lg:px-8 min-h-screen">
    
    <!-- Hero Section -->
    <x-nfc.hero-section 
        title="¡Bienvenido a tu Chip NFC!"
        :subtitle="$config['title'] . ' • ' . $config['message']"
        :emoji="$config['emoji']"
        :icon="$config['icon']">
    </x-nfc.hero-section>

    <!-- Content Preview Card -->
    <x-nfc.glass-card class="mb-12" delay="0.3s">
        <!-- Decorative elements -->
        <div class="absolute top-6 left-6 text-5xl sm:text-7xl opacity-10 text-white">🎨</div>
        <div class="absolute bottom-6 right-6 text-4xl sm:text-5xl opacity-10 text-white">🚀</div>
        
        <div class="text-center">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-6">
                ✨ Contenido Listo para Personalizar
            </h2>
            
            <!-- Preview del Contenido -->
            <div class="kraftdo-glass rounded-2xl p-6 mb-8 border border-white/20">
                <div class="text-4xl mb-4">{{ $config['emoji'] }}</div>
                <h3 class="text-xl font-bold text-white mb-2">{{ $config['title'] }}</h3>
                <p class="text-white/80 mb-4">"{{ $config['message'] }}"</p>
                <div class="inline-flex items-center px-4 py-2 kraftdo-glass rounded-full text-sm font-medium text-white">
                    <i class="fas fa-magic mr-2"></i>
                    Listo para personalizar
                </div>
            </div>
            
            <!-- Feature Grid -->
            <x-nfc.feature-grid 
                :features="[
                    [
                        'title' => 'Personalizable',
                        'description' => 'Agrega tu toque personal',
                        'icon' => 'fas fa-paint-brush',
                        'gradient' => 'from-blue-500 to-green-500'
                    ],
                    [
                        'title' => 'Compartible',
                        'description' => 'URL pública permanente',
                        'icon' => 'fas fa-share-alt',
                        'gradient' => 'from-green-500 to-lime-500'
                    ],
                    [
                        'title' => 'Responsive',
                        'description' => 'Funciona en cualquier dispositivo',
                        'icon' => 'fas fa-mobile-alt',
                        'gradient' => 'from-navy-500 to-blue-500'
                    ]
                ]"
                columns="3" />
        </div>
    </x-nfc.glass-card>

    <!-- CTA Section -->
    <x-nfc.glass-card delay="0.6s">
        <div class="text-center">
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
                
                <!-- Beneficios -->
                <x-nfc.feature-grid 
                    :features="[
                        [
                            'title' => 'Asignación Inmediata',
                            'description' => 'Se asigna al instante',
                            'icon' => 'fas fa-check-circle',
                            'gradient' => 'from-green-500 to-emerald-600'
                        ],
                        [
                            'title' => 'Personalización Total',
                            'description' => 'Controla cada detalle',
                            'icon' => 'fas fa-palette',
                            'gradient' => 'from-blue-500 to-green-500'
                        ],
                        [
                            'title' => 'Dashboard Completo',
                            'description' => 'Gestiona todos tus chips',
                            'icon' => 'fas fa-tachometer-alt',
                            'gradient' => 'from-green-500 to-lime-500'
                        ]
                    ]"
                    columns="3" />
                
                <form action="{{ route('nfc.assign-token') }}" method="POST" class="mb-6">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="id" value="{{ $id }}">
                    
                    <x-nfc.kraftdo-button 
                        type="submit"
                        variant="kraftdo"
                        size="lg"
                        icon="fas fa-link"
                        class="group">
                        Asignar a Mi Cuenta
                    </x-nfc.kraftdo-button>
                </form>
                
                <p class="text-sm text-white/70 kraftdo-glass rounded-full px-4 py-2 inline-block">
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
                <x-nfc.process-steps 
                    :steps="[
                        [
                            'title' => 'Crea tu Cuenta',
                            'description' => 'Solo toma 2 minutos',
                            'gradient' => 'from-green-500 to-teal-600'
                        ],
                        [
                            'title' => 'Personaliza Todo',
                            'description' => 'Mensajes, colores, contenido',
                            'gradient' => 'from-blue-500 to-purple-600'
                        ],
                        [
                            'title' => 'Comparte',
                            'description' => 'URL pública para siempre',
                            'gradient' => 'from-pink-500 to-red-600'
                        ]
                    ]"
                    :currentStep="1" />
                
                <div class="mb-6">
                    <x-nfc.kraftdo-button 
                        href="{{ route('nfc.onboarding.by-id', $id) }}"
                        variant="kraftdo"
                        size="lg"
                        icon="fas fa-magic"
                        class="group">
                        Personalizar Mi Chip
                    </x-nfc.kraftdo-button>
                </div>
                
                <p class="text-sm text-white/70 kraftdo-glass rounded-full px-4 py-2 inline-block">
                    <i class="fas fa-gift mr-2 text-green-400"></i>
                    Completamente gratis • Sin compromisos • Solo 2 minutos
                </p>
            @endauth
        </div>
    </x-nfc.glass-card>

    <!-- How It Works Section -->
    <x-nfc.glass-card delay="0.9s" class="mb-8">
        <div class="text-center mb-6">
            <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">
                <i class="fas fa-lightbulb text-yellow-400 mr-2"></i>
                ¿Cómo Funciona la Magia?
            </h3>
            <p class="text-white/80">Tu chip NFC se convertirá en algo único y especial</p>
        </div>
        
        <x-nfc.feature-grid 
            :features="[
                [
                    'title' => 'Escanea',
                    'description' => 'Con cualquier teléfono',
                    'icon' => 'fas fa-mobile-alt',
                    'gradient' => 'from-navy-500 to-blue-500'
                ],
                [
                    'title' => 'Personaliza',
                    'description' => 'Tu contenido único',
                    'icon' => 'fas fa-edit',
                    'gradient' => 'from-green-500 to-lime-500'
                ],
                [
                    'title' => 'Comparte',
                    'description' => 'Con quien quieras',
                    'icon' => 'fas fa-share-alt',
                    'gradient' => 'from-blue-500 to-green-500'
                ],
                [
                    'title' => 'Disfruta',
                    'description' => 'Momentos especiales',
                    'icon' => 'fas fa-heart',
                    'gradient' => 'from-green-500 to-lime-500'
                ]
            ]"
            columns="4" />
    </x-nfc.glass-card>

    <!-- Footer -->
    <div class="text-center text-white/80 text-sm kraftdo-animate-fade-in" style="animation-delay: 1.2s;">
        <div class="kraftdo-glass rounded-2xl p-6 border border-white/20">
            <div class="flex items-center justify-center mb-3">
                <div class="w-8 h-8 kraftdo-gradient rounded-full flex items-center justify-center mr-3">
                    <span class="text-white font-black text-sm">K</span>
                </div>
                <span class="text-lg font-bold text-white tracking-wide">KRAFTDO</span>
                <span class="text-xs text-green-300 ml-2 font-semibold">NFC</span>
            </div>
            <p class="mb-2">
                <i class="fas fa-shield-alt text-green-400 mr-2"></i>
                Seguro • Privado • Fácil de usar
            </p>
            <p class="text-green-200 font-medium">Tecnología NFC de próxima generación ✨</p>
        </div>
    </div>
</div>

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($errors->all() as $error)
                kraftdoToast('error', 'Error', '{{ $error }}');
            @endforeach
        });
    </script>
@endif
@endsection