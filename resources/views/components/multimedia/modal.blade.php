{{-- Advanced Multimedia Modal Component --}}
@props([
    'theme' => [
        'background' => 'bg-black',
        'text' => 'text-white'
    ]
])

<div class="fixed inset-0 bg-black z-50" 
     x-show="modalOpen"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-on:click="handleModalBackdrop($event)"
     x-on:wheel="$event.preventDefault(); $event.deltaY > 0 ? zoomOut() : zoomIn()"
     style="display: none;"
     x-on:mousedown="startDrag($event)"
     x-on:mousemove="drag($event)"
     x-on:mouseup="endDrag()"
     x-on:mouseleave="endDrag()"
     x-on:touchstart="startDrag($event)"
     x-on:touchmove="drag($event)"
     x-on:touchend="endDrag()">
    
    <!-- Main Image Container -->
    <div class="absolute inset-0 flex items-center justify-center p-4 overflow-hidden">
        <!-- Loading State -->
        <div x-show="currentImage && currentImage.loading" 
             class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-75 z-20">
            <div class="text-center text-white">
                <svg class="w-12 h-12 mx-auto mb-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-lg">Cargando imagen...</p>
            </div>
        </div>
        
        <!-- Error State -->
        <div x-show="currentImage && currentImage.error" 
             class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-75 z-20">
            <div class="text-center text-white">
                <svg class="w-16 h-16 mx-auto mb-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold mb-2">Error al cargar la imagen</h3>
                <p class="text-gray-300">La imagen no pudo ser cargada</p>
                <button x-on:click="closeModal()" 
                        class="mt-4 bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
        
        <!-- Main Image -->
        <img class="max-w-none max-h-none object-contain transition-transform duration-200 select-none" 
             x-bind:src="currentImage ? currentImage.src : ''"
             x-bind:alt="currentImage ? currentImage.alt : ''"
             x-bind:style="`transform: scale(${zoomLevel}) translate(${imagePosition.x}px, ${imagePosition.y}px); cursor: ${zoomLevel > 1 ? 'grab' : 'default'};`"
             x-show="currentImage && (!currentImage.loading && !currentImage.error)"
             draggable="false">
    </div>

    <!-- Top Bar -->
    <div class="absolute top-0 left-0 right-0 bg-gradient-to-b from-black to-transparent p-4 z-30">
        <div class="flex items-center justify-between">
            <!-- Image Info -->
            <div class="text-white flex-1">
                <h3 class="text-lg font-semibold" x-text="(currentImage && currentImage.alt) || 'Imagen de galería'"></h3>
                <p class="text-sm text-gray-300" x-show="currentImage && currentImage.caption" x-text="currentImage && currentImage.caption"></p>
            </div>
            
            <!-- Close Button -->
            <button x-on:click="closeModal()" 
                    class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors ml-4"
                    aria-label="Cerrar modal">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Navigation Arrows -->
    <template x-if="images.length > 1">
        <div>
            <!-- Previous Button -->
            <button x-on:click="prevImage()" 
                    class="absolute left-4 top-1/2 -translate-y-1/2 text-white bg-black bg-opacity-40 hover:bg-opacity-70 rounded-full p-3 transition-all hover:scale-110 backdrop-blur-sm"
                    :class="currentIndex <= 0 ? 'opacity-50 cursor-not-allowed' : ''"
                    :disabled="currentIndex <= 0"
                    aria-label="Imagen anterior">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <!-- Next Button -->
            <button x-on:click="nextImage()" 
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-white bg-black bg-opacity-40 hover:bg-opacity-70 rounded-full p-3 transition-all hover:scale-110 backdrop-blur-sm"
                    :class="currentIndex >= images.length - 1 ? 'opacity-50 cursor-not-allowed' : ''"
                    :disabled="currentIndex >= images.length - 1"
                    aria-label="Imagen siguiente">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </template>

    <!-- Bottom Controls -->
    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4 z-30">
        <div class="flex items-center justify-center space-x-4">
            
            <!-- Position Indicator -->
            <div x-show="images.length > 1" 
                 class="bg-black bg-opacity-60 text-white px-3 py-1 rounded-full text-sm backdrop-blur-sm">
                <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
            </div>
            
            <!-- Zoom Controls -->
            <div class="flex items-center space-x-2 bg-black bg-opacity-60 rounded-full px-3 py-2 backdrop-blur-sm">
                <button x-on:click="zoomOut()" 
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-1 transition-colors"
                        aria-label="Alejar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path>
                    </svg>
                </button>
                
                <span class="text-white text-sm px-2" x-text="Math.round(zoomLevel * 100) + '%'"></span>
                
                <button x-on:click="zoomIn()" 
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-1 transition-colors"
                        aria-label="Acercar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                    </svg>
                </button>
                
                <button x-on:click="resetZoom()" 
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-1 transition-colors"
                        x-show="zoomLevel !== 1"
                        aria-label="Restablecer zoom">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Slideshow Control -->
            <template x-if="images.length > 1">
                <button x-on:click="toggleSlideshow()" 
                        class="text-white bg-black bg-opacity-60 hover:bg-opacity-80 rounded-full p-2 transition-colors backdrop-blur-sm"
                        :aria-label="isSlideshow ? 'Pausar presentación' : 'Iniciar presentación'">
                    <svg x-show="!isSlideshow" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12l4 4 4-4"></path>
                    </svg>
                    <svg x-show="isSlideshow" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </button>
            </template>
            
            <!-- Share Button -->
            <button x-on:click="shareImage()" 
                    x-show="navigator.share"
                    class="text-white bg-black bg-opacity-60 hover:bg-opacity-80 rounded-full p-2 transition-colors backdrop-blur-sm"
                    aria-label="Compartir imagen">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                </svg>
            </button>
            
            <!-- Download Button -->
            <button x-on:click="downloadImage()" 
                    class="text-white bg-black bg-opacity-60 hover:bg-opacity-80 rounded-full p-2 transition-colors backdrop-blur-sm"
                    aria-label="Descargar imagen">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            </button>
        </div>

        <!-- Thumbnails -->
        <template x-if="images.length > 1 && images.length <= 10">
            <div class="flex justify-center mt-4 space-x-2 overflow-x-auto pb-2">
                <template x-for="(image, index) in images" :key="index">
                    <button x-on:click="currentIndex = index; currentImage = images[currentIndex]; resetZoom()"
                            class="relative flex-shrink-0 w-12 h-12 rounded-lg overflow-hidden border-2 transition-all"
                            :class="currentIndex === index ? 'border-white scale-110' : 'border-transparent opacity-60 hover:opacity-100'">
                        <img :src="image.src" :alt="image.alt" class="w-full h-full object-cover">
                        <div x-show="currentIndex === index" 
                             class="absolute inset-0 bg-white bg-opacity-30"></div>
                    </button>
                </template>
            </div>
        </template>

        <!-- Keyboard Shortcuts Info -->
        <div class="hidden lg:flex justify-center mt-2 text-xs text-gray-400 space-x-4">
            <span>← → Navegar</span>
            <span>Esc Cerrar</span>
            <span>Rueda ratón Zoom</span>
        </div>
    </div>
</div>