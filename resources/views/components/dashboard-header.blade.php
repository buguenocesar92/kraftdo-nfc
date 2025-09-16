{{-- Dashboard Header Component --}}
<div class="flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold bg-gradient-to-r from-kraftdo-blue to-kraftdo-green bg-clip-text text-transparent">
            <i class="fas fa-tachometer-alt mr-3"></i>
            {{ __('Dashboard') }}
        </h2>
        <p class="text-gray-300 mt-1">Panel de control y estadísticas</p>
    </div>
    <div class="flex items-center space-x-4">
        <div class="kraftdo-gradient text-white px-4 py-2 rounded-full text-sm font-medium">
            <i class="fas fa-user-check mr-2"></i>
            Activo
        </div>
    </div>
</div>