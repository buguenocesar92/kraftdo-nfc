{{-- Gallery Modal Component --}}
@if(isset($gallery) && !empty($gallery) && is_array($gallery) && count($gallery) > 0)
<div id="galleryModal" class="fixed inset-0 bg-black/90 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="relative max-w-6xl max-h-full w-full h-full flex items-center justify-center">
        <!-- Botón cerrar -->
        <button id="closeModal" class="absolute top-4 right-4 z-10 w-12 h-12 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-all duration-300">
            <i class="fas fa-times text-xl"></i>
        </button>
        
        <!-- Botón anterior -->
        <button id="prevImage" class="absolute left-4 top-1/2 transform -translate-y-1/2 z-10 w-12 h-12 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-all duration-300">
            <i class="fas fa-chevron-left text-xl"></i>
        </button>
        
        <!-- Botón siguiente -->
        <button id="nextImage" class="absolute right-4 top-1/2 transform -translate-y-1/2 z-10 w-12 h-12 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-all duration-300">
            <i class="fas fa-chevron-right text-xl"></i>
        </button>
        
        <!-- Contenedor de imagen -->
        <div class="w-full h-full flex items-center justify-center p-4 md:p-8">
            <div class="relative w-full h-full flex items-center justify-center">
                <img id="modalImage" src="" alt="" class="max-w-[90vw] max-h-[80vh] w-auto h-auto object-contain rounded-2xl shadow-2xl">
                <div id="modalCaption" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4 md:p-6 rounded-b-2xl">
                    <p class="text-white text-sm md:text-lg font-medium text-center"></p>
                </div>
            </div>
        </div>
        
        <!-- Contador de fotos -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-white/20 backdrop-blur-md rounded-full px-4 py-2">
            <span id="imageCounter" class="text-white text-sm font-medium"></span>
        </div>
    </div>
</div>
@endif