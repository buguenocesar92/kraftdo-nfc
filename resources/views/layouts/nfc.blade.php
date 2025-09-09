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
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        :root {
            --kraftdo-primary: #6366f1;
            --kraftdo-secondary: #8b5cf6;
            --kraftdo-accent: #06b6d4;
            --kraftdo-success: #10b981;
            --kraftdo-warning: #f59e0b;
            --kraftdo-error: #ef4444;
            --kraftdo-dark: #1f2937;
            --kraftdo-light: #f8fafc;
        }
        
        .kraftdo-gradient {
            background: linear-gradient(135deg, var(--kraftdo-primary) 0%, var(--kraftdo-secondary) 50%, var(--kraftdo-accent) 100%);
        }
        
        .kraftdo-glass {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .kraftdo-shadow {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .kraftdo-animate-float {
            animation: kraftdoFloat 6s ease-in-out infinite;
        }
        
        .kraftdo-animate-pulse-slow {
            animation: kraftdoPulse 4s ease-in-out infinite;
        }
        
        .kraftdo-animate-fade-in {
            animation: kraftdoFadeIn 0.8s ease-out forwards;
        }
        
        .kraftdo-animate-scale-in {
            animation: kraftdoScaleIn 0.6s ease-out forwards;
        }
        
        @keyframes kraftdoFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            25% { transform: translateY(-10px) rotate(1deg); }
            50% { transform: translateY(-20px) rotate(0deg); }
            75% { transform: translateY(-10px) rotate(-1deg); }
        }
        
        @keyframes kraftdoPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        
        @keyframes kraftdoFadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes kraftdoScaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        
        .kraftdo-bg-pattern {
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(6, 182, 212, 0.05) 0%, transparent 50%);
        }
    </style>
    
    @stack('styles')
</head>
<body class="kraftdo-gradient min-h-screen text-white font-['Inter'] antialiased kraftdo-bg-pattern" x-data="kraftdoApp()">
    
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
        <div class="kraftdo-glass rounded-full px-4 py-2 kraftdo-shadow">
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 kraftdo-gradient rounded-full flex items-center justify-center">
                    <i class="fas fa-bolt text-white text-xs"></i>
                </div>
                <span class="text-sm font-medium text-white/90">Powered by Kraftdo</span>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div x-data="kraftdoToast()" class="fixed top-4 right-4 z-50 space-y-2">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.show" 
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="max-w-sm w-full kraftdo-glass rounded-xl kraftdo-shadow p-4 border"
                 :class="toast.type === 'success' ? 'border-green-400' : toast.type === 'error' ? 'border-red-400' : 'border-blue-400'">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i :class="toast.type === 'success' ? 'fas fa-check-circle text-green-400' : 
                                   toast.type === 'error' ? 'fas fa-exclamation-circle text-red-400' : 
                                   'fas fa-info-circle text-blue-400'"></i>
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium text-white" x-text="toast.title"></p>
                        <p class="mt-1 text-sm text-white/80" x-text="toast.message" x-show="toast.message"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="removeToast(toast.id)" class="text-white/60 hover:text-white/80 focus:outline-none">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </template>
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
        
        function kraftdoToast() {
            return {
                toasts: [],
                
                addToast(type, title, message = '', duration = 5000) {
                    const id = Date.now();
                    const toast = { id, type, title, message, show: true };
                    this.toasts.push(toast);
                    
                    setTimeout(() => {
                        this.removeToast(id);
                    }, duration);
                },
                
                removeToast(id) {
                    const index = this.toasts.findIndex(toast => toast.id === id);
                    if (index > -1) {
                        this.toasts[index].show = false;
                        setTimeout(() => {
                            this.toasts.splice(index, 1);
                        }, 300);
                    }
                },
                
                success(title, message = '') {
                    this.addToast('success', title, message);
                },
                
                error(title, message = '') {
                    this.addToast('error', title, message);
                },
                
                info(title, message = '') {
                    this.addToast('info', title, message);
                }
            }
        }
        
        // Global toast function
        window.kraftdoToast = function(type, title, message = '') {
            const event = new CustomEvent('kraftdo-toast', {
                detail: { type, title, message }
            });
            document.dispatchEvent(event);
        };
        
        document.addEventListener('kraftdo-toast', (e) => {
            const { type, title, message } = e.detail;
            // Find toast component and trigger
            const toastEl = document.querySelector('[x-data*="kraftdoToast"]');
            if (toastEl && toastEl._x_dataStack) {
                toastEl._x_dataStack[0].addToast(type, title, message);
            }
        });
    </script>

    @stack('scripts')
</body>
</html>