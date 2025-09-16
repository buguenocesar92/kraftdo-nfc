{{-- YouTube Music Player Component --}}
@php
$embedType = 'direct_link';
$embedUrl = '';
$autoplayParam = isset($audio['autoplay']) && $audio['autoplay'] ? '1' : '0';

// Detectar diferentes tipos de URLs de YouTube Music
if (preg_match('/music\.youtube\.com\/watch\?v=([^&\n?#]+)/', $audio['url'] ?? '', $matches)) {
    $youtubeId = $matches[1];
    $embedType = 'video';
    $embedUrl = "https://www.youtube.com/embed/{$youtubeId}?autoplay={$autoplayParam}&mute=0&loop=" . (isset($audio['loop']) && $audio['loop'] ? '1' : '0');
} elseif (preg_match('/music\.youtube\.com\/playlist\?list=([^&\n?#]+)/', $audio['url'] ?? '', $matches)) {
    $playlistId = $matches[1];
    $embedType = 'playlist';
    $embedUrl = "https://www.youtube.com/embed/videoseries?list={$playlistId}&autoplay={$autoplayParam}";
}
@endphp

@if($embedType === 'video' || $embedType === 'playlist')
    <div class="rounded-2xl overflow-hidden shadow-lg">
        <iframe src="{{ $embedUrl }}" 
                width="100%" 
                height="200" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                class="rounded-2xl audio-iframe"
                id="audio-iframe">
        </iframe>
    </div>
@else
    <!-- Para canales y artistas -->
    <div class="text-center">
        <div class="text-4xl mb-4">🎵</div>
        <p class="{{ $theme['accent_color'] ?? 'text-pink-600' }} mb-4">Contenido musical especial</p>
        <a href="{{ $audio['url'] ?? '' }}" target="_blank" 
           class="inline-flex items-center px-8 py-4 bg-gradient-to-r {{ $theme['primary_gradient'] ?? 'from-pink-500 to-rose-500' }} text-white rounded-full font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            <i class="fab fa-youtube mr-3 text-xl"></i>
            Escuchar Música
        </a>
    </div>
@endif