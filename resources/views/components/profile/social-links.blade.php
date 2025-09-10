{{-- Profile Social Links Component --}}
@props([
    'socialLinks' => []
])

@if($socialLinks && count($socialLinks) > 0)
    <div class="mb-6">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 text-center">
            Sígueme en
        </h3>
        <div class="grid grid-cols-2 gap-2">
            @foreach($socialLinks as $link)
                @php
                    $platform = $link->platform_info;
                    $url = $link->url ?: ($platform['base_url'] . $link->username);
                @endphp
                <a href="{{ $url }}" target="_blank" rel="noopener"
                   class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors text-sm">
                    <span class="text-lg">
                        @switch($link->platform)
                            @case('instagram') 📷 @break
                            @case('linkedin') 💼 @break
                            @case('twitter') 🐦 @break
                            @case('facebook') 📘 @break
                            @case('tiktok') 🎵 @break
                            @case('youtube') 📹 @break
                            @case('github') 💻 @break
                            @default 🌐 @break
                        @endswitch
                    </span>
                    <span class="text-gray-700 truncate">
                        {{ $link->username ?: $platform['name'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>
@endif