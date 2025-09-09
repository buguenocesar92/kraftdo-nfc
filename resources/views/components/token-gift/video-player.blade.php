{{-- Token Gift Video Player Component --}}
@props([
    'contentMultimedia' => null
])

@if($contentMultimedia && ($contentMultimedia->video_url || $contentMultimedia->video_file))
    <div class="bg-gray-50 rounded-xl p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">🎬 Video</h3>
        <div class="aspect-video overflow-hidden bg-black w-screen sm:w-full relative left-1/2 -translate-x-1/2 sm:left-0 sm:translate-x-0 sm:rounded-lg">
            @if($contentMultimedia->video_type === 'youtube' && $contentMultimedia->video_url)
                @php
                    $videoId = '';
                    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $contentMultimedia->video_url, $matches)) {
                        $videoId = $matches[1];
                    }
                @endphp
                @if($videoId)
                    <iframe 
                        class="w-full h-full" 
                        src="https://www.youtube.com/embed/{{ $videoId }}?rel=0" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                @endif
            @elseif($contentMultimedia->video_type === 'vimeo' && $contentMultimedia->video_url)
                @php
                    $videoId = '';
                    if (preg_match('/vimeo\.com\/(\d+)/', $contentMultimedia->video_url, $matches)) {
                        $videoId = $matches[1];
                    }
                @endphp
                @if($videoId)
                    <iframe 
                        class="w-full h-full" 
                        src="https://player.vimeo.com/video/{{ $videoId }}" 
                        frameborder="0" 
                        allow="autoplay; fullscreen; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                @endif
            @elseif($contentMultimedia->video_type === 'file_upload' && $contentMultimedia->video_file)
                <video controls class="w-full h-full">
                    <source src="{{ asset('storage/' . $contentMultimedia->video_file) }}" type="video/mp4">
                    Tu navegador no soporta el elemento video.
                </video>
            @elseif($contentMultimedia->video_type === 'direct' && $contentMultimedia->video_url)
                <video controls class="w-full h-full">
                    <source src="{{ $contentMultimedia->video_url }}" type="video/mp4">
                    Tu navegador no soporta el elemento video.
                </video>
            @endif
        </div>
    </div>
@endif