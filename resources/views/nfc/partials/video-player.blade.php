{{-- Video Player Component --}}
@php
$video = $video ?? [];
$videoType = $video['type'] ?? '';
$theme = $theme ?? [];
@endphp

@if($video['has_embed'] ?? false)
    {{-- Embedded video (YouTube, Vimeo) --}}
    <div class="aspect-video rounded-lg overflow-hidden shadow-lg">
        <iframe 
            src="{{ $video['embed_url'] }}" 
            frameborder="0" 
            allow="autoplay; encrypted-media" 
            allowfullscreen
            class="w-full h-full video-iframe"
            id="video-iframe">
        </iframe>
    </div>

@elseif($videoType === 'file_upload')
    {{-- Uploaded video file --}}
    <div class="{{ $theme['card_style'] ?? 'bg-white/90 backdrop-blur-md' }} rounded-3xl shadow-xl p-6 sm:p-8 mb-6">
        <h3 class="text-2xl sm:text-3xl font-bold text-center mb-6 bg-gradient-to-r {{ $theme['primary_gradient'] ?? 'from-pink-600 to-purple-600' }} bg-clip-text text-transparent">
            🎬 Video Especial
        </h3>
        <div class="video-container-public loading" id="gift-video-container-{{ uniqid() }}">
            <video 
                controls 
                class="video-player-public"
                id="video-player"
                data-video-enhanced="true"
                {{ (isset($video['autoplay']) && $video['autoplay']) ? 'autoplay' : '' }}
                {{ (isset($video['muted']) && $video['muted']) ? 'muted' : '' }}
                preload="metadata">
                <source src="{{ $video['url'] ?? '' }}" type="video/mp4">
                <source src="{{ $video['url'] ?? '' }}" type="video/webm">
                Tu navegador no soporta el elemento de video.
            </video>
        </div>
    </div>

@elseif($videoType === 'direct')
    {{-- Direct video --}}
    <div class="video-container-public loading" id="gift-video-container-{{ uniqid() }}">
        <video 
            controls 
            class="video-player-public"
            id="video-player-direct"
            data-video-enhanced="true"
            {{ (isset($video['autoplay']) && $video['autoplay']) ? 'autoplay' : '' }}
            {{ (isset($video['muted']) && $video['muted']) ? 'muted' : '' }}
            preload="metadata">
            <source src="{{ $video['url'] ?? '' }}" type="video/mp4">
            <source src="{{ $video['url'] ?? '' }}" type="video/webm">
            Tu navegador no soporta el elemento de video.
        </video>
    </div>

@elseif(in_array($videoType, ['instagram_reel', 'tiktok']))
    {{-- Social media links --}}
    <div class="bg-white rounded-lg p-6 shadow-lg text-center">
        <div class="text-4xl mb-4">
            {{ $videoType === 'instagram_reel' ? '📸' : '🎵' }}
        </div>
        <p class="text-gray-600 mb-4">¡Hay un {{ $videoType === 'instagram_reel' ? 'reel especial' : 'video divertido' }} para ti!</p>
        <a href="{{ $video['url'] ?? '' }}" target="_blank" 
            class="inline-flex items-center px-6 py-3 bg-gradient-to-r {{ $theme['primary_gradient'] ?? 'from-pink-500 to-rose-500' }} text-white rounded-full font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            <span class="mr-2">{{ $videoType === 'instagram_reel' ? '📱' : '🎬' }}</span>
            Ver {{ $videoType === 'instagram_reel' ? 'Reel' : 'Video' }}
        </a>
    </div>

@endif