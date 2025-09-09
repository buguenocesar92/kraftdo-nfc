{{-- Token Gift Gallery Component --}}
@props([
    'galleryImages' => []
])

@if($galleryImages && count($galleryImages) > 0)
    <div class="bg-yellow-50 rounded-xl p-6" x-data="{
        images: [
            @foreach($galleryImages as $image)
            { src: '{{ $image->image_source }}', alt: '{{ $image->alt_text ?? '' }}', caption: '{{ $image->caption ?? '' }}' }{{ !$loop->last ? ',' : '' }}
            @endforeach
        ]
    }">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">📸 Galería de Imágenes</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($galleryImages as $index => $image)
                <div class="relative group cursor-pointer" 
                     x-on:click="openImageModal('{{ $image->image_source }}', '{{ $image->alt_text ?? '' }}', images, {{ $index }})">
                    <img src="{{ $image->image_source }}" 
                         alt="{{ $image->alt_text }}" 
                         class="w-full h-48 object-cover rounded-lg group-hover:opacity-80 transition-opacity">
                    @if($image->caption)
                        <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2 rounded-b-lg">
                            <p class="text-sm">{{ $image->caption }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif