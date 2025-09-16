<!DOCTYPE html>
<html lang="es" x-data="{ theme: 'light' }" :class="theme">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kraftdo NFC')</title>
    
    <!-- Kraftdo Branding -->
    <meta name="description" content="@yield('description', 'Tecnología NFC inteligente by Kraftdo')">
    <meta name="author" content="Kraftdo">
    
    <!-- Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    
    <!-- Alpine.js with plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    
    
    @stack('styles')
</head>
<body class="min-h-screen text-white font-kraftdo antialiased" style="background: linear-gradient(135deg, #2A3441 0%, #3B4A6B 100%) !important; background-attachment: fixed !important; background-image: radial-gradient(circle at 25% 25%, rgba(74, 144, 226, 0.15) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(0, 255, 127, 0.12) 0%, transparent 50%), radial-gradient(circle at 50% 50%, rgba(50, 255, 50, 0.08) 0%, transparent 50%), radial-gradient(circle at 10% 80%, rgba(59, 74, 107, 0.1) 0%, transparent 50%) !important;" x-data="kraftdoApp()">
    
    <!-- Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none" x-show="showDecorations">
        <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full kraftdo-animate-float"></div>
        <div class="absolute top-32 right-16 w-16 h-16 bg-white/20 rounded-full kraftdo-animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-20 left-20 w-24 h-24 bg-white/10 rounded-full kraftdo-animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-32 right-10 w-12 h-12 bg-white/15 rounded-full kraftdo-animate-float" style="animation-delay: 0.5s;"></div>
        <div class="absolute top-1/2 left-1/4 text-6xl opacity-5 kraftdo-animate-float" x-text="backgroundEmoji"></div>
        <div class="absolute top-1/3 right-1/4 text-4xl opacity-10 kraftdo-animate-float" style="animation-delay: 1.5s;">✨</div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 min-h-screen">
        @yield('content')
    </div>

    <!-- Kraftdo Branding Footer -->
    <div class="fixed bottom-4 right-4 z-20" x-show="showBranding">
        <div class="kraftdo-glass rounded-full px-4 py-2 kraftdo-brand-shadow border border-white/20">
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 kraftdo-gradient rounded-full flex items-center justify-center animate-pulse">
                    <span class="text-white font-black text-xs">K</span>
                </div>
                <span class="text-sm font-bold text-white/95 tracking-wide">KRAFTDO</span>
                <span class="text-xs text-white/60">NFC</span>
            </div>
        </div>
    </div>

    <!-- Toast Notifications (simplified) -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2">
        <!-- Toasts will be inserted here via JavaScript -->
    </div>

    <!-- Loading Overlay -->
    <div x-show="loading" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center kraftdo-gradient"
         style="display: none;">
        <div class="kraftdo-glass rounded-3xl p-8 kraftdo-shadow">
            <div class="flex flex-col items-center space-y-4">
                <div class="w-16 h-16 border-4 border-white/30 border-t-white rounded-full animate-spin"></div>
                <p class="text-white font-medium" x-text="loadingMessage"></p>
            </div>
        </div>
    </div>

    <script>
        // Alpine.js Global Components
        function kraftdoApp() {
            return {
                showDecorations: true,
                showBranding: true,
                backgroundEmoji: '🎯',
                loading: false,
                loadingMessage: 'Cargando...',
                
                init() {
                    // Initialize any global functionality
                    this.setupGlobalEvents();
                },
                
                setupGlobalEvents() {
                    // Handle form submissions with loading states
                    document.addEventListener('submit', (e) => {
                        if (e.target.dataset.loading !== 'false') {
                            this.setLoading(true, 'Procesando...');
                        }
                    });
                },
                
                setLoading(state, message = 'Cargando...') {
                    this.loading = state;
                    this.loadingMessage = message;
                },
                
                setBrandingEmoji(emoji) {
                    this.backgroundEmoji = emoji;
                }
            }
        }
        
        // Simplified toast system
        window.kraftdoToast = function(type, title, message = '') {
            console.log(`[${type.toUpperCase()}] ${title}${message ? ': ' + message : ''}`);
            
            // Simple alert for now - can be enhanced later
            if (type === 'error') {
                alert(`Error: ${title}${message ? '\n' + message : ''}`);
            }
        };
    </script>

    @stack('scripts')
</body>
</html>