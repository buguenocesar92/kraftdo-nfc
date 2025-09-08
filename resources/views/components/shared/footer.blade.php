@props([
    'content' => null,
    'theme' => 'default',
    'showAdminInfo' => false
])

<footer class="mt-8 py-6 border-t border-gray-200" 
        x-data="{ showDetails: false }"
        x-init="setTimeout(() => showDetails = true, 2000)">
    
    <div class="text-center">
        <!-- Main Footer Content -->
        <div x-show="showDetails" 
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0">
            
            @if($theme === 'gift')
                <!-- Gift Footer -->
                <div class="flex items-center justify-center text-gray-500 text-sm mb-2">
                    <i class="fas fa-heart text-red-400 mr-2 animate-pulse"></i>
                    <span>Creado con amor usando tecnología NFC</span>
                    <i class="fas fa-heart text-red-400 ml-2 animate-pulse"></i>
                </div>
                
            @elseif($theme === 'profile')
                <!-- Profile Footer -->
                <div class="flex items-center justify-center text-gray-500 text-sm mb-2">
                    <i class="fas fa-user-circle text-blue-400 mr-2"></i>
                    <span>Perfil digital creado con KraftDo NFC</span>
                </div>
                
            @elseif($theme === 'menu')
                <!-- Menu Footer -->
                <div class="flex items-center justify-center text-gray-500 text-sm mb-2">
                    <i class="fas fa-utensils text-orange-400 mr-2"></i>
                    <span>Menú digital por KraftDo NFC</span>
                </div>
                
            @else
                <!-- Default Footer -->
                <div class="flex items-center justify-center text-gray-500 text-sm mb-2">
                    <i class="fas fa-qrcode text-gray-400 mr-2"></i>
                    <span>Powered by KraftDo NFC Technology</span>
                </div>
            @endif

            <!-- Content ID for debugging (only if content exists) -->
            @if($content && ($showAdminInfo || config('app.debug')))
                <div class="text-xs text-gray-400 mb-2">
                    ID: {{ $content->content_id ?? 'N/A' }} 
                    @if(isset($content->status))
                        | Estado: {{ ucfirst($content->status) }}
                    @endif
                    @if(isset($content->published_at))
                        | Publicado: {{ $content->published_at->format('d/m/Y') }}
                    @endif
                </div>
            @endif

            <!-- Social Links if available -->
            @if($content && isset($content->data['company_social']) && is_array($content->data['company_social']))
                <div class="flex justify-center space-x-3 mb-2">
                    @foreach($content->data['company_social'] as $social)
                        @if(isset($social['url']) && $social['url'])
                            <a href="{{ $social['url'] }}" 
                               target="_blank" 
                               rel="noopener"
                               class="text-gray-400 hover:text-gray-600 transition-colors text-sm">
                                <i class="{{ $this->getSocialIcon($social['platform'] ?? 'web') }}"></i>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif

            <!-- Version and Copyright -->
            <div class="text-xs text-gray-400">
                <span>&copy; {{ date('Y') }} KraftDo</span>
                @if(config('app.env') === 'local')
                    <span class="ml-2">v{{ config('app.version', '1.0') }}</span>
                @endif
            </div>
        </div>

        <!-- Loading state -->
        <div x-show="!showDetails" class="text-gray-400 text-sm">
            <i class="fas fa-spinner fa-spin mr-2"></i>
            <span>Cargando...</span>
        </div>
    </div>

    <!-- Interactive Easter Egg -->
    @if($theme === 'gift')
        <div x-data="{ clicks: 0, showMessage: false }" 
             class="text-center mt-4">
            <button @click="clicks++; if(clicks >= 5) { showMessage = true; setTimeout(() => showMessage = false, 3000); clicks = 0; }"
                    class="text-xs text-gray-300 hover:text-gray-500 transition-colors focus:outline-none">
                ✨
            </button>
            
            <div x-show="showMessage" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="fixed bottom-4 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-pink-500 to-purple-500 text-white px-4 py-2 rounded-full text-sm shadow-lg z-50">
                ¡Gracias por usar KraftDo! 💖
            </div>
        </div>
    @endif
</footer>

@php
    // Helper function for social icons
    if (!function_exists('getSocialIcon')) {
        function getSocialIcon($platform) {
            return match($platform) {
                'instagram' => 'fab fa-instagram',
                'facebook' => 'fab fa-facebook-f',
                'twitter' => 'fab fa-twitter',
                'whatsapp' => 'fab fa-whatsapp',
                'linkedin' => 'fab fa-linkedin-in',
                'youtube' => 'fab fa-youtube',
                'tiktok' => 'fab fa-tiktok',
                'website' => 'fas fa-globe',
                default => 'fas fa-link',
            };
        }
    }
@endphp