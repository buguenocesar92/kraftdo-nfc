{{-- Gift Multimedia Section Component --}}
@props([
    'multimedia' => [],
    'theme' => []
])

<!-- Video Section -->
@if(isset($multimedia['video']) && !empty($multimedia['video']))
    <div class="gift-section">
        @include('nfc.partials.video-player', ['video' => $multimedia['video'], 'theme' => $theme])
    </div>
@endif

<!-- Photo Gallery -->
@if(isset($multimedia['gallery']) && !empty($multimedia['gallery']))
    <div class="gift-section">
        @include('nfc.partials.photo-gallery', ['gallery' => $multimedia['gallery'], 'theme' => $theme])
    </div>
@endif

<!-- Social Content -->
@if(isset($multimedia['social']) && !empty($multimedia['social']))
    @include('nfc.partials.social-links', ['social' => $multimedia['social'], 'theme' => $theme])
@endif

<!-- Audio Overlay -->
@include('nfc.partials.audio-overlay', ['multimedia' => $multimedia, 'theme' => $theme])

<!-- Audio Section -->
@if(isset($multimedia['audio']) && !empty($multimedia['audio']))
    <div class="gift-section">
        @include('nfc.partials.audio-player', ['audio' => $multimedia['audio'], 'theme' => $theme])
    </div>
@endif