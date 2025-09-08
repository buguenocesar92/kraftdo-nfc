{{-- Audio Player Component --}}
@php
$audio = $audio ?? [];
$theme = $theme ?? [];
$audioType = $audio['type'] ?? '';
$hasAutoplay = isset($audio['autoplay']) && $audio['autoplay'];
@endphp

@if(!empty($audio))
<div class="{{ $theme['card_style'] ?? 'bg-white/90 backdrop-blur-md' }} rounded-3xl shadow-xl p-6 sm:p-8">
    <div class="text-center mb-6">
        <h3 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r {{ $theme['primary_gradient'] ?? 'from-pink-600 to-purple-600' }} bg-clip-text text-transparent mb-2">
            🎵 Música Especial
        </h3>
        <p class="text-sm {{ $theme['accent_color'] ?? 'text-pink-600' }} font-medium">
            {{ $audio['title'] ?? 'Una canción pensada para ti' }}
        </p>
    </div>
    
    @if($audioType === 'file_upload' && !empty($audio['url']))
        {{-- Uploaded Audio File --}}
        @include('nfc.partials.audio-controls', ['audio' => $audio])
        
    @elseif($audioType === 'spotify' && !empty($audio['url']))
        {{-- Spotify Embed --}}
        @include('nfc.partials.spotify-player', ['audio' => $audio])
        
    @elseif($audioType === 'youtube_music' && !empty($audio['url']))
        {{-- YouTube Music Embed --}}
        @include('nfc.partials.youtube-music-player', ['audio' => $audio, 'theme' => $theme])
        
    @elseif($audioType === 'soundcloud' && !empty($audio['url']))
        {{-- SoundCloud Embed --}}
        @include('nfc.partials.soundcloud-player', ['audio' => $audio])
        
    @elseif($audioType === 'direct' && !empty($audio['url']))
        {{-- Direct Audio --}}
        @include('nfc.partials.direct-audio-player', ['audio' => $audio, 'theme' => $theme])
        
    @endif
</div>
@endif