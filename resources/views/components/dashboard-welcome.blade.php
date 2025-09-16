{{-- Dashboard Welcome Section Component --}}
@props(['userName'])

<div class="kraftdo-gradient rounded-3xl shadow-xl text-white p-8 mb-8 animate-fade-in-up relative overflow-hidden">
    <div class="absolute top-0 right-0 w-32 h-32 kraftdo-gradient-reverse rounded-full opacity-20 transform translate-x-16 -translate-y-16"></div>
    <div class="flex items-center justify-between relative z-10">
        <div>
            <h1 class="text-3xl font-bold mb-2">¡Bienvenido, {{ $userName }}!</h1>
            <p class="text-white/80 text-lg">Aquí tienes un resumen de tu actividad NFC</p>
        </div>
        <div class="hidden lg:block">
            <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
                <i class="fas fa-chart-line text-4xl"></i>
            </div>
        </div>
    </div>
</div>