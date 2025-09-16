{{-- Dashboard Global Stats Component --}}
@props(['globalStats', 'tokenStats'])

<div class="bg-kraftdo-dark/50 backdrop-blur-md rounded-3xl shadow-xl border border-kraftdo-navy/30 overflow-hidden animate-fade-in-up mb-8" style="animation-delay: 0.5s;">
    <div class="p-8 border-b border-kraftdo-navy/30">
        <h3 class="text-2xl font-bold bg-gradient-to-r from-kraftdo-blue to-kraftdo-green bg-clip-text text-transparent">
            <i class="fas fa-chart-bar text-kraftdo-green mr-3"></i>
            Estadísticas Globales de Mi Cuenta
        </h3>
        <p class="text-gray-300 mt-1">Resumen completo de la actividad de todos mis tokens NFC (últimos 30 días)</p>
    </div>

    <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total de Visitas de Mi Cuenta -->
            <div class="bg-kraftdo-navy/30 backdrop-blur-sm border border-kraftdo-blue/30 rounded-2xl p-6 text-center">
                <div class="kraftdo-gradient p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
                <h4 class="text-lg font-semibold text-white mb-2">Total Visitas</h4>
                <p class="text-3xl font-bold text-kraftdo-green">{{ number_format($globalStats['total_views'] ?? 0) }}</p>
                <p class="text-sm text-gray-400 mt-1">
                    {{ round($globalStats['average_views_per_day'] ?? 0, 1) }}/día promedio
                </p>
            </div>

            <!-- Visitantes Únicos de Mi Cuenta -->
            <div class="bg-kraftdo-navy/30 backdrop-blur-sm border border-kraftdo-green/30 rounded-2xl p-6 text-center">
                <div class="kraftdo-gradient-reverse p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <h4 class="text-lg font-semibold text-white mb-2">Visitantes Únicos</h4>
                <p class="text-3xl font-bold text-kraftdo-lime">{{ number_format($globalStats['unique_visitors'] ?? 0) }}</p>
                <p class="text-sm text-gray-400 mt-1">
                    Personas diferentes que visitaron
                </p>
            </div>

            <!-- Mis Contenidos con Visitas -->
            <div class="bg-kraftdo-navy/30 backdrop-blur-sm border border-kraftdo-blue/30 rounded-2xl p-6 text-center">
                <div class="kraftdo-gradient p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-layer-group text-white text-xl"></i>
                </div>
                <h4 class="text-lg font-semibold text-white mb-2">Contenidos Visitados</h4>
                <p class="text-3xl font-bold text-kraftdo-blue">{{ number_format($globalStats['unique_contents'] ?? 0) }}</p>
                <p class="text-sm text-gray-400 mt-1">
                    De {{ $tokenStats['total'] }} tokens totales
                </p>
            </div>

            <!-- Tokens Activos -->
            <div class="bg-kraftdo-navy/30 backdrop-blur-sm border border-kraftdo-lime/30 rounded-2xl p-6 text-center">
                <div class="kraftdo-gradient-reverse p-4 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-microchip text-white text-xl"></i>
                </div>
                <h4 class="text-lg font-semibold text-white mb-2">Tokens Activos</h4>
                <p class="text-3xl font-bold text-kraftdo-green">{{ number_format($tokenStats['active'] ?? 0) }}</p>
                <p class="text-sm text-gray-400 mt-1">
                    Configurados y funcionando
                </p>
            </div>
        </div>

        <!-- Gráfico de Distribución por Tipo -->
        @if(isset($globalStats['views_by_type']) && count($globalStats['views_by_type']) > 0)
        <div class="bg-kraftdo-navy/20 rounded-2xl p-6">
            <h4 class="text-lg font-bold text-white mb-6 text-center">
                <i class="fas fa-chart-pie text-kraftdo-lime mr-2"></i>
                Distribución de Mis Visitas por Tipo de Contenido
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @php
                    $typeIcons = [
                        'PROFILE' => ['icon' => 'fas fa-user', 'color' => 'kraftdo-blue', 'name' => 'Perfiles'],
                        'GIFT' => ['icon' => 'fas fa-gift', 'color' => 'kraftdo-green', 'name' => 'Regalos'],
                        'MENU' => ['icon' => 'fas fa-utensils', 'color' => 'kraftdo-lime', 'name' => 'Menús'],
                        'TOURIST' => ['icon' => 'fas fa-map-marked-alt', 'color' => 'kraftdo-blue', 'name' => 'Turístico'],
                        'EVENT' => ['icon' => 'fas fa-calendar-alt', 'color' => 'kraftdo-green', 'name' => 'Eventos'],
                        'PRODUCT' => ['icon' => 'fas fa-box', 'color' => 'kraftdo-lime', 'name' => 'Productos'],
                    ];
                    $totalGlobalViews = array_sum($globalStats['views_by_type']);
                @endphp
                
                @foreach($globalStats['views_by_type'] as $type => $views)
                    @php
                        $percentage = $totalGlobalViews > 0 ? round(($views / $totalGlobalViews) * 100, 1) : 0;
                        $typeData = $typeIcons[$type] ?? ['icon' => 'fas fa-circle', 'color' => 'kraftdo-green', 'name' => $type];
                    @endphp
                    <div class="bg-kraftdo-dark/40 rounded-xl p-4 text-center hover:bg-kraftdo-dark/60 transition-all duration-300">
                        @php
                            $iconColor = match($typeData['color']) {
                                'kraftdo-blue' => 'text-kraftdo-blue',
                                'kraftdo-green' => 'text-kraftdo-green', 
                                'kraftdo-lime' => 'text-kraftdo-lime',
                                default => 'text-kraftdo-green'
                            };
                            $bgColor = match($typeData['color']) {
                                'kraftdo-blue' => 'bg-kraftdo-blue',
                                'kraftdo-green' => 'bg-kraftdo-green', 
                                'kraftdo-lime' => 'bg-kraftdo-lime',
                                default => 'bg-kraftdo-green'
                            };
                        @endphp
                        <div class="text-2xl mb-3 {{ $iconColor }}">
                            <i class="{{ $typeData['icon'] }}"></i>
                        </div>
                        <h5 class="text-white font-semibold text-sm mb-1">{{ $typeData['name'] }}</h5>
                        <p class="font-bold text-lg {{ $iconColor }}">{{ number_format($views) }}</p>
                        <p class="text-gray-400 text-xs">{{ $percentage }}%</p>
                        
                        <!-- Barra de progreso -->
                        <div class="w-full bg-kraftdo-navy/50 rounded-full h-2 mt-2">
                            <div class="h-2 rounded-full transition-all duration-500 {{ $bgColor }}" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>