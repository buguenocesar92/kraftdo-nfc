{{-- Audio Overlay Component --}}
@php
$multimedia = $multimedia ?? [];
$theme = $theme ?? [];
$hasAudio = isset($multimedia['audio']) && !empty($multimedia['audio']);
$hasVideo = isset($multimedia['video']) && !empty($multimedia['video']);
$shouldShow = $hasAudio || (!$hasAudio && $hasVideo);
@endphp

@if($shouldShow)
<div id="audio-overlay" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center" style="pointer-events: all;">
    <div id="audio-modal" class="bg-white rounded-3xl shadow-2xl p-8 max-w-md mx-4 text-center transform scale-100 transition-all duration-300">
        <div class="mb-6">
            <div class="w-20 h-20 bg-gradient-to-r {{ $theme['primary_gradient'] ?? 'from-pink-500 to-purple-600' }} rounded-full flex items-center justify-center mx-auto mb-4">
                @if($hasAudio)
                    <i class="fas fa-music text-white text-3xl"></i>
                @else
                    <i class="fas fa-play text-white text-3xl"></i>
                @endif
            </div>
            
            @if($hasAudio)
                <h3 class="text-2xl font-bold text-gray-800 mb-2">🎵 Música Especial</h3>
                <p class="text-gray-600">
                    Este regalo incluye una banda sonora especial. ¡Haz clic para activar la música y vivir la experiencia completa!
                </p>
            @else
                <h3 class="text-2xl font-bold text-gray-800 mb-2">🎬 Video Especial</h3>
                <p class="text-gray-600">
                    Este regalo incluye un video especial. ¡Haz clic para activar el video y vivir la experiencia completa!
                </p>
            @endif
        </div>
        
        <button id="activate-audio-btn" class="w-full bg-gradient-to-r {{ $theme['primary_gradient'] ?? 'from-pink-500 to-purple-600' }} text-white py-4 px-6 rounded-2xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            <i class="fas fa-play mr-2"></i>
            @if($hasAudio)
                Activar Música
            @else
                Activar Video
            @endif
        </button>
        
        <p class="text-xs text-gray-500 mt-4">
            <i class="fas fa-info-circle mr-1"></i>
            @if($hasAudio)
                La música se reproducirá en segundo plano mientras navegas
            @else
                El video se activará para que puedas disfrutar del contenido
            @endif
        </p>
    </div>
</div>
@endif