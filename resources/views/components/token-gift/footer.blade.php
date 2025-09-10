{{-- Enhanced Token Gift Footer Component --}}
@props([
    'text' => 'Regalo creado con ❤️ usando KRAFTDO NFC'
])

<div class="mt-8 pt-8 border-t border-gradient-to-r from-gray-200 via-purple-200 to-pink-200 text-center animate-fade-in animate-delay-800">
    
    <!-- Action Buttons -->
    <div class="flex flex-wrap justify-center gap-3 mb-6">
        <!-- Share Button -->
        <button onclick="shareGift()" 
                class="group btn-animated inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium py-3 px-6 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300"
                aria-label="Compartir este regalo con otras personas"
                role="button"
                tabindex="0">
            <svg class="w-5 h-5 icon-rotate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
            </svg>
            <span>Compartir Regalo</span>
        </button>
        
        <!-- Favorite Button -->
        <button onclick="toggleFavorite()" 
                class="group btn-animated inline-flex items-center gap-2 bg-gradient-to-r from-pink-500 to-red-500 text-white font-medium py-3 px-6 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-pink-300"
                x-data="{ isFavorite: false }"
                aria-label="Marcar este regalo como favorito"
                role="button"
                tabindex="0">
            <svg class="w-5 h-5 icon-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <span>Me Gusta</span>
        </button>
        
        <!-- QR Code Button -->
        <button onclick="showQRCode()" 
                class="group btn-animated inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-teal-500 text-white font-medium py-3 px-6 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-300"
                aria-label="Mostrar código QR para compartir fácilmente"
                role="button"
                tabindex="0">
            <svg class="w-5 h-5 icon-rotate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
            </svg>
            <span>Código QR</span>
        </button>
    </div>
    
    <!-- Enhanced footer text -->
    <div class="relative">
        <p class="text-gray-500 text-sm font-medium">{!! $text !!}</p>
        <div class="flex justify-center mt-2">
            <div class="w-16 h-0.5 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full"></div>
        </div>
    </div>
    
    <!-- Sparkle decorations -->
    <div class="absolute inset-0 pointer-events-none">
        <span class="absolute top-2 left-1/4 text-yellow-400 text-lg animate-pulse">✨</span>
        <span class="absolute bottom-2 right-1/4 text-pink-400 text-sm animate-pulse" style="animation-delay: 0.5s;">⭐</span>
    </div>
</div>