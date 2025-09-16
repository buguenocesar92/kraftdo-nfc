@extends('layouts.nfc')

@section('title', 'Error - Kraftdo NFC')
@section('description', 'Ha ocurrido un error con tu chip NFC')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-2xl">
        
        <!-- Hero Section with Error Icon -->
        <x-nfc.hero-section 
            title="¡Oops! Algo no está bien"
            emoji="😔"
            icon="fas fa-exclamation-triangle"
            :animated="true">
        </x-nfc.hero-section>

        <!-- Error Message Card -->
        <x-nfc.glass-card class="mb-8">
            <div class="text-center">
                <p class="text-xl text-white/90 mb-6">
                    {{ $message ?? 'Ha ocurrido un error inesperado' }}
                </p>

                <!-- Suggestions -->
                @if(isset($suggestions) && is_array($suggestions))
                    <div class="text-left kraftdo-glass rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
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

                <!-- Technical Details -->
                @if(isset($details))
                    <div class="text-sm text-white/70 kraftdo-glass rounded-xl p-4 mb-6">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                            <strong>Detalles técnicos:</strong>
                        </div>
                        <p class="font-mono text-xs">{{ $details }}</p>
                    </div>
                @endif
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
                href="{{ route('home') }}"
                variant="primary">
                <i class="fas fa-home mr-2"></i>
                Ir al Inicio
            </x-nfc.kraftdo-button>
        </div>

        <!-- Support Information -->
        <x-nfc.glass-card>
            <div class="text-center">
                <h3 class="text-lg font-semibold text-white mb-4">
                    <i class="fas fa-life-ring text-blue-400 mr-2"></i>
                    ¿Necesitas ayuda?
                </h3>
                <p class="text-white/80 mb-4">
                    Si el problema persiste, nuestro equipo de soporte está aquí para ayudarte
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <div class="flex items-center text-white/70">
                        <i class="fas fa-envelope mr-2 text-blue-400"></i>
                        <span>soporte@kraftdo.com</span>
                    </div>
                    <div class="flex items-center text-white/70">
                        <i class="fas fa-clock mr-2 text-green-400"></i>
                        <span>24/7 disponible</span>
                    </div>
                </div>
            </div>
        </x-nfc.glass-card>
    </div>
</div>
@endsection