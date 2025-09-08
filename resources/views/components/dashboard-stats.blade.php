{{-- Dashboard Stats Section Component --}}
@props(['tokenStats', 'userStats'])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total de Tokens -->
    <div class="bg-kraftdo-dark/50 backdrop-blur-md rounded-2xl shadow-lg border border-kraftdo-navy/30 p-6 animate-fade-in-up" style="animation-delay: 0.1s;">
        <div class="flex items-center">
            <div class="kraftdo-gradient p-3 rounded-full shadow-lg">
                <i class="fas fa-microchip text-white text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-300">Total Tokens</h3>
                <p class="text-2xl font-bold text-white">{{ $tokenStats['total'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Tokens Configurados -->
    <div class="bg-kraftdo-dark/50 backdrop-blur-md rounded-2xl shadow-lg border border-kraftdo-navy/30 p-6 animate-fade-in-up" style="animation-delay: 0.2s;">
        <div class="flex items-center">
            <div class="kraftdo-gradient-reverse p-3 rounded-full shadow-lg">
                <i class="fas fa-check-circle text-white text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-300">Configurados</h3>
                <p class="text-2xl font-bold text-white">{{ $tokenStats['configured'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Tokens Activos -->
    <div class="bg-kraftdo-dark/50 backdrop-blur-md rounded-2xl shadow-lg border border-kraftdo-navy/30 p-6 animate-fade-in-up" style="animation-delay: 0.3s;">
        <div class="flex items-center">
            <div class="kraftdo-gradient p-3 rounded-full shadow-lg">
                <i class="fas fa-power-off text-white text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-300">Activos</h3>
                <p class="text-2xl font-bold text-white">{{ $tokenStats['active'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Total Visitas del Usuario -->
    <div class="bg-kraftdo-dark/50 backdrop-blur-md rounded-2xl shadow-lg border border-kraftdo-navy/30 p-6 animate-fade-in-up" style="animation-delay: 0.4s;">
        <div class="flex items-center">
            <div class="kraftdo-gradient-reverse p-3 rounded-full shadow-lg">
                <i class="fas fa-eye text-white text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-300">Mis Visitas</h3>
                <p class="text-2xl font-bold text-white">{{ $userStats['total_visits'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>