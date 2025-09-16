{{-- Social Links Component --}}
@php
$social = $social ?? [];
$theme = $theme ?? [];
@endphp

@if(!empty($social))
<div class="mb-8">
    <h3 class="text-xl font-bold text-center mb-4 text-gray-800">📱 Contenido Especial</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($social as $platform => $url)
            @if($url)
                <a href="{{ $url }}" target="_blank" 
                   class="block bg-white rounded-lg p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <div class="text-center">
                        <div class="text-3xl mb-2">
                            @switch($platform)
                                @case('instagram_reel')
                                    📸
                                    @break
                                @case('tiktok')
                                    🎵
                                    @break
                                @case('youtube_shorts')
                                    🎬
                                    @break
                                @default
                                    📱
                            @endswitch
                        </div>
                        <p class="font-semibold text-gray-800 capitalize">
                            {{ str_replace('_', ' ', $platform) }}
                        </p>
                    </div>
                </a>
            @endif
        @endforeach
    </div>
</div>
@endif