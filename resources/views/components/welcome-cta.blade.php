{{-- Welcome CTA Section Component --}}
<div class="welcome-cta">
    <h2 class="welcome-cta-title">
        ¿Listo para <span class="bg-gradient-to-r from-kraftdo-blue to-kraftdo-green bg-clip-text text-transparent">Revolucionar</span> tu Forma de Conectar?
    </h2>
    <p class="welcome-cta-description">
        Únete a miles de profesionales que ya están utilizando la tecnología NFC para expandir sus redes y hacer crecer sus negocios.
    </p>
    
    @auth
        <a href="{{ url('/dashboard') }}" class="welcome-cta-btn">
            <i class="fas fa-tachometer-alt mr-3"></i>
            Acceder al Dashboard
        </a>
    @else
        <a href="{{ route('register') }}" class="welcome-cta-btn">
            <i class="fas fa-rocket mr-3"></i>
            Comenzar Gratis Hoy
        </a>
    @endauth
    
    <div class="mt-6 text-gray-500 text-sm">
        <i class="fas fa-shield-alt mr-2"></i>
        Sin compromisos • Configuración en 2 minutos • Soporte 24/7
    </div>
</div>