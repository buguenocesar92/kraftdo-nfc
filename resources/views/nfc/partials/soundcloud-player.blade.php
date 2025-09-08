{{-- SoundCloud Player Component --}}
@php
// Convertir URL de SoundCloud a embed
$embedUrl = str_replace('soundcloud.com/', 'w.soundcloud.com/player/?url=https%3A//soundcloud.com/', $audio['url'] ?? '');
$embedUrl .= '&color=%23ff5500&auto_play=' . (isset($audio['autoplay']) && $audio['autoplay'] ? 'true' : 'false');
$embedUrl .= '&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true&visual=true';

if (!str_contains($embedUrl, 'w.soundcloud.com')) {
    $embedUrl = 'https://w.soundcloud.com/player/?url=' . urlencode($audio['url'] ?? '') . '&color=%23ff5500&auto_play=' . (isset($audio['autoplay']) && $audio['autoplay'] ? 'true' : 'false');
}
@endphp

<div class="rounded-2xl overflow-hidden shadow-lg">
    <iframe width="100%" 
            height="166" 
            scrolling="no" 
            frameborder="no" 
            allow="autoplay" 
            src="{{ $embedUrl }}"
            class="rounded-2xl">
    </iframe>
</div>