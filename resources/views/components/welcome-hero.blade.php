{{-- Welcome Hero Section Component --}}
<div class="welcome-hero">
    <h2 class="welcome-hero-title">
        El Futuro de la 
        <span class="bg-gradient-to-r from-kraftdo-blue to-kraftdo-green bg-clip-text text-transparent">Conectividad NFC</span>
    </h2>
    <p class="welcome-hero-subtitle">
        Transforma la manera en que compartes información con tarjetas NFC inteligentes. 
        Conecta el mundo físico con el digital de forma instantánea y elegante.
    </p>
    @auth
        <a href="{{ url('/dashboard') }}" class="welcome-hero-btn-primary">
            <i class="fas fa-tachometer-alt mr-3"></i>
            Ir al Dashboard
        </a>
    @else
        <div class="space-x-4">
            <a href="{{ route('register') }}" class="welcome-hero-btn-primary">
                <i class="fas fa-rocket mr-3"></i>
                Comenzar Ahora
            </a>
            <a href="{{ route('login') }}" class="welcome-hero-btn-secondary">
                <i class="fas fa-sign-in-alt mr-3"></i>
                Iniciar Sesión
            </a>
        </div>
    @endauth
</div>