{{-- Dashboard Type Distribution Component --}}
@props(['tokenStats', 'hasTokens'])

@if($hasTokens)
<div class="bg-kraftdo-dark/50 backdrop-blur-md rounded-3xl shadow-xl border border-kraftdo-navy/30 overflow-hidden animate-fade-in-up mb-8" style="animation-delay: 0.5s;">
    <div class="p-8 border-b border-kraftdo-navy/30">
        <h3 class="text-2xl font-bold bg-gradient-to-r from-kraftdo-blue to-kraftdo-green bg-clip-text text-transparent">
            <i class="fas fa-chart-pie text-kraftdo-green mr-3"></i>
            Distribución por Tipo
        </h3>
        <p class="text-gray-300 mt-1">Resumen de tus tokens NFC por categoría</p>
    </div>

    <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $typeIcons = [
                    'PROFILE' => ['icon' => 'fas fa-user', 'color' => 'kraftdo-gradient', 'name' => 'Perfil'],
                    'GIFT' => ['icon' => 'fas fa-gift', 'color' => 'kraftdo-gradient-reverse', 'name' => 'Regalo'],
                    'BUSINESS' => ['icon' => 'fas fa-building', 'color' => 'kraftdo-gradient', 'name' => 'Negocio'],
                    'TOURIST' => ['icon' => 'fas fa-map-marked-alt', 'color' => 'kraftdo-gradient-reverse', 'name' => 'Turístico'],
                    'EVENT' => ['icon' => 'fas fa-calendar-alt', 'color' => 'kraftdo-gradient', 'name' => 'Evento'],
                    'PRODUCT' => ['icon' => 'fas fa-box', 'color' => 'kraftdo-gradient-reverse', 'name' => 'Producto'],
                    'BUS_STOP' => ['icon' => 'fas fa-bus', 'color' => 'kraftdo-gradient', 'name' => 'Paradero'],
                ];
            @endphp
            
            @foreach($tokenStats['by_type'] ?? [] as $type => $count)
                @if($count > 0)
                <div class="bg-kraftdo-navy/30 backdrop-blur-sm border border-kraftdo-green/20 rounded-2xl p-6 hover:shadow-lg hover:border-kraftdo-green/40 transition-all duration-300">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full shadow-lg {{ $typeIcons[$type]['color'] ?? 'kraftdo-gradient' }}">
                            <i class="{{ $typeIcons[$type]['icon'] ?? 'fas fa-circle' }} text-white text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-white">{{ $typeIcons[$type]['name'] ?? $type }}</h4>
                            <p class="text-sm text-gray-300">{{ $count }} {{ $count == 1 ? 'token' : 'tokens' }}</p>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif