{{-- Welcome Header Component --}}
<header class="welcome-header">
    <div class="welcome-nav">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="welcome-brand">
                    <i class="fas fa-microchip mr-3"></i>
                    KRAFTDO NFC
                </h1>
                <p class="welcome-tagline">Tecnología NFC Inteligente para el Futuro</p>
            </div>
            @if (Route::has('login'))
                <nav class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="welcome-nav-btn-primary">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="welcome-nav-btn">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Iniciar Sesión
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="welcome-nav-btn-primary">
                                <i class="fas fa-user-plus mr-2"></i>
                                Registrarse
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </div>
</header>