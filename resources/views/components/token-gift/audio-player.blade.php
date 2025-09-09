{{-- Token Gift Audio Player Component --}}
@props([
    'contentMultimedia' => null
])

@if($contentMultimedia && ($contentMultimedia->audio_url || $contentMultimedia->audio_file))
    <div class="bg-blue-50 rounded-xl p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">🎵 Audio</h3>
        <div class="bg-white rounded-lg p-4">
            @if($contentMultimedia->audio_type === 'file_upload' && $contentMultimedia->audio_file)
                <audio controls class="w-full">
                    <source src="{{ asset('storage/' . $contentMultimedia->audio_file) }}" type="audio/mpeg">
                    Tu navegador no soporta el elemento audio.
                </audio>
            @elseif($contentMultimedia->audio_type === 'direct' && $contentMultimedia->audio_url)
                <audio controls class="w-full">
                    <source src="{{ $contentMultimedia->audio_url }}" type="audio/mpeg">
                    Tu navegador no soporta el elemento audio.
                </audio>
            @elseif(in_array($contentMultimedia->audio_type, ['youtube_music', 'spotify', 'soundcloud']) && $contentMultimedia->audio_url)
                <div class="text-center">
                    <a href="{{ $contentMultimedia->audio_url }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-blue-500 text-white font-semibold rounded-lg hover:from-green-600 hover:to-blue-600 transition-colors">
                        🎧 Escuchar en {{ ucfirst(str_replace('_', ' ', $contentMultimedia->audio_type)) }}
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif