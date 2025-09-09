{{-- Token Gift Modal Component --}}

<div class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" 
     x-show="modalOpen"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-on:click="handleModalBackdrop($event)"
     style="display: none;">
    <div class="max-w-4xl max-h-full relative">
        <img class="max-w-full max-h-full object-contain rounded-lg" 
             x-bind:src="currentImage.src" 
             x-bind:alt="currentImage.alt">
        
        <!-- Botón cerrar -->
        <button x-on:click="closeImageModal()" 
                class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-75 transition-colors"
                aria-label="Cerrar modal">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <!-- Botón anterior -->
        <button x-show="galleryImages.length > 1" 
                x-on:click="prevImage()" 
                class="absolute left-4 top-1/2 -translate-y-1/2 text-white bg-black bg-opacity-50 rounded-full p-3 hover:bg-opacity-75 transition-colors"
                aria-label="Imagen anterior">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        
        <!-- Botón siguiente -->
        <button x-show="galleryImages.length > 1" 
                x-on:click="nextImage()" 
                class="absolute right-4 top-1/2 -translate-y-1/2 text-white bg-black bg-opacity-50 rounded-full p-3 hover:bg-opacity-75 transition-colors"
                aria-label="Imagen siguiente">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
        
        <!-- Indicador de posición -->
        <div x-show="galleryImages.length > 1" 
             class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
            <span x-text="currentImageIndex + 1"></span> / <span x-text="galleryImages.length"></span>
        </div>
    </div>
</div>