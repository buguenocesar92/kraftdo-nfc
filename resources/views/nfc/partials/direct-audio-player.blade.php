{{-- Direct Audio Player Component --}}
<div class="bg-gradient-to-r {{ $theme['secondary_gradient'] ?? 'from-pink-100 to-purple-100' }} rounded-2xl p-6">
    <audio controls class="w-full" 
           {{ (isset($audio['autoplay']) && $audio['autoplay']) ? 'autoplay' : '' }} 
           {{ (isset($audio['loop']) && $audio['loop']) ? 'loop' : '' }}>
        <source src="{{ $audio['url'] ?? '' }}" type="audio/mpeg">
        Tu navegador no soporta el elemento de audio.
    </audio>
</div>