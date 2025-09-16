{{-- Autoplay Permission Overlay Component --}}
@props([
    'contentMultimedia' => null
])

<div x-data="autoplayOverlay({{ $contentMultimedia && ($contentMultimedia->audio_url || $contentMultimedia->audio_file) ? 'true' : 'false' }}, {{ $contentMultimedia && ($contentMultimedia->video_url || $contentMultimedia->video_file) ? 'true' : 'false' }})" 
     x-init="initOverlay()"
     x-show="showAutoplayOverlay" 
     class="fixed inset-0 flex items-center justify-center"
     style="z-index: 9999; display: block !important; background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);"
     x-transition>
    
    <!-- Overlay Content -->
    <div class="bg-white rounded-2xl p-8 max-w-md mx-4 text-center shadow-2xl">
        <!-- Icon -->
        <div class="mb-6">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            </div>
        </div>
        
        <!-- Title -->
        <h2 class="text-2xl font-bold text-gray-900 mb-4" x-text="getTitle()">
            ¡Escucha tu Mensaje!
        </h2>
        
        <!-- Description -->
        <p class="text-gray-600 mb-6 leading-relaxed" x-text="getDescription()">
            Te leeremos el mensaje personalizado en voz alta para una experiencia única.
        </p>
        
        <!-- Debug Info -->
        <div class="text-xs text-gray-400 mb-4" x-text="'Audio: ' + hasAudio + ', Video: ' + hasVideo"></div>
        
        <!-- Action Button - Enhanced -->
        <button x-on:click="activateAutoplay()" 
                class="w-full btn-animated bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold py-4 px-6 rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg focus:outline-none focus:ring-4 focus:ring-purple-300"
                :aria-label="hasVideo ? 'Activar reproducción de video' : 'Activar reproducción de audio'"
                role="button"
                tabindex="0"
                autofocus>
            <span class="flex items-center justify-center gap-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
                <span x-text="getButtonText()">Activar Multimedia</span>
            </span>
        </button>
        
        <!-- Info Text -->
        <p class="text-xs text-gray-500 mt-4">
            Esto iniciará la reproducción del contenido multimedia disponible
        </p>
    </div>
</div>