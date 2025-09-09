{{-- Token Gift Gallery Component --}}
@props([
    'galleryImages' => []
])

@if($galleryImages && count($galleryImages) > 0)
    <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-yellow-50 rounded-xl p-6" x-data="{
        images: [
            @foreach($galleryImages as $image)
            { src: '{{ $image->image_source }}', alt: '{{ $image->alt_text ?? '' }}', caption: '{{ $image->caption ?? '' }}' }{{ !$loop->last ? ',' : '' }}
            @endforeach
        ],
        imageLoaded: {},
        imageError: {}
    }">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <span class="text-2xl">📸</span>
                <span>Galería de Imágenes</span>
                <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded-full">{{ count($galleryImages) }}</span>
            </h3>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($galleryImages as $index => $image)
                <div class="relative group cursor-pointer transform transition-all duration-300 hover:scale-105" 
                     x-on:click="openImageModal('{{ $image->image_source }}', '{{ $image->alt_text ?? '' }}', images, {{ $index }}, '{{ $image->caption ?? '' }}')">
                    
                    <!-- Skeleton Loader -->
                    <div class="absolute inset-0 bg-gray-200 rounded-lg animate-pulse z-10"
                         x-show="!imageLoaded['img_{{ $index }}'] && !imageError['img_{{ $index }}']">
                        <div class="flex items-center justify-center h-48">
                            <svg class="w-8 h-8 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Error State -->
                    <div class="absolute inset-0 bg-red-50 rounded-lg border-2 border-red-200 flex items-center justify-center z-10"
                         x-show="imageError['img_{{ $index }}']">
                        <div class="text-center p-4">
                            <svg class="w-12 h-12 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-red-600">Error al cargar</p>
                        </div>
                    </div>
                    
                    <!-- Main Image -->
                    <img data-src="{{ $image->image_source }}" 
                         alt="{{ $image->alt_text }}" 
                         class="w-full h-48 object-cover rounded-lg opacity-0 transition-all duration-500 group-hover:scale-110"
                         x-intersect.once="
                            $el.src = $el.dataset.src;
                            $el.onload = () => {
                                imageLoaded['img_{{ $index }}'] = true;
                                $el.classList.remove('opacity-0');
                                $el.classList.add('opacity-100');
                            };
                            $el.onerror = () => {
                                imageError['img_{{ $index }}'] = true;
                            };
                         ">
                    
                    <!-- Hover Overlay -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <div class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                            <div class="bg-white bg-opacity-90 rounded-full p-3 shadow-lg">
                                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Caption -->
                    @if($image->caption)
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-black to-transparent text-white p-3 rounded-b-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <p class="text-sm font-medium">{{ $image->caption }}</p>
                        </div>
                    @endif
                    
                    <!-- Image Counter Badge -->
                    <div class="absolute top-2 right-2 bg-black bg-opacity-60 text-white text-xs px-2 py-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        {{ $index + 1 }}/{{ count($galleryImages) }}
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Gallery Stats -->
        <div class="mt-4 text-center text-sm text-gray-600">
            <span class="inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Haz clic en cualquier imagen para ver en grande
            </span>
        </div>
    </div>
@else
    <!-- Empty State -->
    <div class="bg-gray-50 rounded-xl p-8 text-center">
        <div class="text-gray-400 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-700 mb-2">No hay imágenes en la galería</h3>
        <p class="text-gray-500">Las imágenes aparecerán aquí cuando se agreguen a la galería.</p>
    </div>
@endif