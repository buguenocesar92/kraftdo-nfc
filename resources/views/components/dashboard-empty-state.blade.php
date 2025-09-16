{{-- Dashboard Empty State Component --}}
@props(['hasTokens'])

@if(!$hasTokens)
<div class="bg-kraftdo-dark/50 backdrop-blur-md rounded-3xl shadow-xl border border-kraftdo-navy/30 overflow-hidden animate-fade-in-up text-center py-16" style="animation-delay: 0.5s;">
    <div class="text-kraftdo-green/50 text-6xl mb-6">
        <i class="fas fa-microchip"></i>
    </div>
    <h3 class="text-xl font-semibold text-white mb-4">No tienes tokens NFC aún</h3>
    <p class="text-gray-300 mb-8">Compra tu primer token NFC para comenzar a crear contenido dinámico</p>
    <a href="#" class="inline-flex items-center px-6 py-3 kraftdo-gradient text-white font-semibold rounded-2xl hover:kraftdo-gradient-reverse hover:shadow-lg transition-all duration-300 transform hover:scale-105">
        <i class="fas fa-shopping-cart mr-2"></i>
        Comprar Token NFC
        <i class="fas fa-arrow-right ml-2"></i>
    </a>
</div>
@endif