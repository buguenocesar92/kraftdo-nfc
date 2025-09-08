{{-- Photo Gallery Component --}}
@php
$gallery = $gallery ?? [];
$theme = $theme ?? [];
@endphp

@if(!empty($gallery) && is_array($gallery) && count($gallery) > 0)
<div class="{{ $theme['card_style'] ?? 'bg-white/90 backdrop-blur-md' }} rounded-3xl shadow-xl p-6 sm:p-8">
    <h3 class="text-2xl sm:text-3xl font-bold text-center mb-6 bg-gradient-to-r {{ $theme['primary_gradient'] ?? 'from-pink-600 to-purple-600' }} bg-clip-text text-transparent">
        📸 Galería de Recuerdos
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @foreach($gallery as $index => $photo)
            @if(isset($photo['url']) && !empty($photo['url']))
                <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 cursor-pointer gallery-item" 
                     data-index="{{ $index }}" 
                     data-url="{{ $photo['url'] }}" 
                     data-caption="{{ $photo['caption'] ?? $photo['alt'] ?? 'Foto de recuerdo ' . ($index + 1) }}">
                    <img src="{{ $photo['url'] }}" 
                         alt="{{ $photo['caption'] ?? $photo['alt'] ?? 'Foto de recuerdo ' . ($index + 1) }}"
                         class="w-full h-48 sm:h-56 object-cover group-hover:scale-110 transition-transform duration-500"
                         loading="lazy"
                         onerror="this.parentElement.style.display='none'">
                    
                    @if(!empty($photo['caption']))
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-4">
                            <p class="text-white text-sm sm:text-base font-medium">{{ $photo['caption'] }}</p>
                        </div>
                    @endif
                    
                    <!-- Overlay decorativo -->
                    <div class="absolute inset-0 bg-gradient-to-br {{ $theme['primary_gradient'] ?? 'from-pink-500/0 to-purple-500/0' }} opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    
                    <!-- Indicador de click -->
                    <div class="absolute top-4 right-4 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <i class="fas fa-expand-alt text-gray-800 text-sm"></i>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Contador de fotos -->
    <div class="text-center mt-6">
        <p class="text-sm {{ $theme['accent_color'] ?? 'text-pink-600' }} font-medium">
            📷 {{ count($gallery) }} {{ count($gallery) === 1 ? 'foto' : 'fotos' }} especiales
        </p>
    </div>
</div>
@endif