@extends('layouts.nfc')

@section('title', 'Contenido No Disponible - Kraftdo NFC')
@section('description', 'Este chip NFC aún no ha sido configurado por su propietario')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-2xl">
        
        <!-- Hero Section -->
        <x-nfc.hero-section 
            title="{{ $reason ?? 'Contenido No Disponible' }}"
            emoji="⏸️"
            icon="fas fa-pause-circle"
            :animated="true">
        </x-nfc.hero-section>

        <!-- Main Message Card -->
        <x-nfc.glass-card class="mb-8">
            <div class="text-center">
                <p class="text-xl text-white/90 mb-8">
                    Este chip está registrado pero aún no ha sido configurado por su propietario.
                </p>

                <!-- Chip Information -->
                @if($token)
                    <div class="text-left kraftdo-glass rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                            <i class="fas fa-microchip text-blue-400 mr-2"></i>
                            Información del Chip:
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-tag text-purple-400"></i>
                                <div>
                                    <p class="text-sm text-white/60">Tipo</p>
                                    <p class="font-medium text-white">{{ strtoupper($type) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-info-circle text-yellow-400"></i>
                                <div>
                                    <p class="text-sm text-white/60">Estado</p>
                                    <p class="font-medium {{ !$token->is_active ? 'text-red-300' : 'text-yellow-300' }}">
                                        @if(!$token->is_active)
                                            Desactivado
                                        @else
                                            Sin contenido configurado
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if($token->purchased_at)
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-calendar text-green-400"></i>
                                    <div>
                                        <p class="text-sm text-white/60">Registrado</p>
                                        <p class="font-medium text-white">{{ $token->purchased_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Possible Reasons -->
                <div class="text-left kraftdo-glass rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <i class="fas fa-lightbulb text-yellow-400 mr-2"></i>
                        Posibles razones:
                    </h3>
                    
                    <x-nfc.feature-grid 
                        :features="[
                            [
                                'title' => 'En Configuración',
                                'description' => 'El propietario está preparando el contenido',
                                'icon' => 'fas fa-cog',
                                'gradient' => 'from-blue-500 to-indigo-600'
                            ],
                            [
                                'title' => 'Temporalmente Pausado',
                                'description' => 'El chip fue desactivado temporalmente',
                                'icon' => 'fas fa-pause',
                                'gradient' => 'from-orange-500 to-red-600'
                            ],
                            [
                                'title' => 'Actualizando Contenido',
                                'description' => 'Se está mejorando la experiencia',
                                'icon' => 'fas fa-sync',
                                'gradient' => 'from-green-500 to-teal-600'
                            ]
                        ]"
                        columns="3" />
                </div>
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
                Intentar de Nuevo
            </x-nfc.kraftdo-button>
        </div>

        <!-- Owner Information Card -->
        <x-nfc.glass-card>
            <div class="text-center">
                <h4 class="text-lg font-semibold text-white mb-4">
                    <i class="fas fa-user-circle text-blue-400 mr-2"></i>
                    ¿Eres el propietario de este chip?
                </h4>
                <p class="text-white/80 mb-6">
                    Si este chip te pertenece, puedes configurarlo desde tu panel de administración.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <x-nfc.kraftdo-button 
                        href="/admin"
                        variant="primary">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Ir al Panel de Administración
                    </x-nfc.kraftdo-button>
                    
                    <x-nfc.kraftdo-button 
                        href="/nfc/info"
                        variant="outline">
                        <i class="fas fa-info-circle mr-2"></i>
                        Más Información sobre NFC
                    </x-nfc.kraftdo-button>
                </div>
                
                <div class="mt-6 pt-6 border-t border-white/20">
                    <p class="text-sm text-white/70 mb-2">
                        <i class="fas fa-question-circle mr-2"></i>
                        ¿Necesitas ayuda configurando tu chip?
                    </p>
                    <p class="text-xs text-white/60">
                        Visita nuestra guía de configuración o contacta a soporte técnico
                    </p>
                </div>
            </div>
        </x-nfc.glass-card>
    </div>
</div>
@endsection