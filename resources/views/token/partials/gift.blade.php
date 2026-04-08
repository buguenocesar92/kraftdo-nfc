{{--
    Partial: Regalo NFC — mobile-first, Tailwind
    Variables: $token, $dynamicContent, $contentGift, $contentMultimedia
--}}
@php
    use App\Helpers\ThemeHelper;
    use Illuminate\Support\Facades\Cache;

    $themeName   = $contentMultimedia?->settings['theme'] ?? 'love';
    $themeConfig = Cache::remember("theme_config:{$themeName}", 3600, fn() => ThemeHelper::getThemeConfig($themeName));

    $colors     = $themeConfig['colors'] ?? [];
    $gradientBg = $colors['gradient_bg'] ?? '#ec4899, #f43f5e, #be185d';
    $recipient  = $contentGift?->recipient_name ?? '';
    $sender     = $contentGift?->sender_name    ?? '';
    $message    = $contentGift?->message        ?? '';

    $galleryImages = $contentMultimedia?->galleryImages ?? collect();

    // Video
    $videoSrc = '';
    $isHtml5  = false;
    $isEmbed  = false;
    $embedSrc = '';

    if ($contentMultimedia) {
        if (in_array($contentMultimedia->video_type, ['file_upload', null]) && $contentMultimedia->video_file) {
            $videoSrc = asset('storage/' . $contentMultimedia->video_file);
            $isHtml5  = true;
        } elseif ($contentMultimedia->video_type === 'direct' && $contentMultimedia->video_url) {
            $videoSrc = $contentMultimedia->video_url;
            $isHtml5  = true;
        } elseif ($contentMultimedia->video_type === 'youtube' && $contentMultimedia->video_url) {
            preg_match('/(?:youtube\.com.*[?&]v=|youtu\.be\/)([^"&?\/ ]{11})/', $contentMultimedia->video_url, $m);
            if (!empty($m[1])) {
                $embedSrc = "https://www.youtube.com/embed/{$m[1]}?rel=0";
                $isEmbed  = true;
            }
        }
    }
@endphp

<div class="min-h-screen pb-8"
     style="background: linear-gradient(135deg, {{ $gradientBg }});">
    <div class="max-w-md mx-auto px-4 pt-6 space-y-5">

        {{-- ── HEADER ── --}}
        <div class="text-center text-white pt-4 pb-2">
            <div class="text-5xl mb-3">{{ $themeConfig['header_icon'] ?? '🎁' }}</div>
            <h1 class="text-3xl font-bold drop-shadow">
                {{ $themeConfig['name'] ?? 'Regalo' }} Especial
            </h1>
            <p class="text-white/80 text-sm mt-1">¡Tienes un regalo personalizado!</p>
        </div>

        {{-- ── MAIN CARD ── --}}
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="p-6 sm:p-8 space-y-5">

                {{-- Destinatario --}}
                @if($recipient)
                    <div class="text-center">
                        <p class="text-sm text-gray-500 mb-1">Este regalo es para</p>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $recipient }}</h2>
                        @if($sender)
                            <p class="text-sm text-gray-500 mt-1">De parte de <span class="font-semibold text-gray-700">{{ $sender }}</span></p>
                        @endif
                    </div>
                @endif

                {{-- Foto principal (primera imagen de galería) --}}
                @if($galleryImages->isNotEmpty())
                    @php $firstImage = $galleryImages->first(); @endphp
                    @if($firstImage && $firstImage->image_path)
                        <div class="rounded-2xl overflow-hidden">
                            <img src="{{ asset('storage/' . $firstImage->image_path) }}"
                                 alt="Imagen del regalo"
                                 class="w-full h-56 sm:h-72 object-cover">
                        </div>
                    @endif
                @endif

                {{-- Mensaje --}}
                @if($message)
                    <div class="bg-gradient-to-br from-pink-50 to-purple-50 rounded-2xl p-5 relative">
                        <div class="absolute top-2 left-3 text-4xl text-pink-200 font-serif leading-none">"</div>
                        <p class="text-gray-700 leading-relaxed text-base italic px-4 pt-2">{{ $message }}</p>
                        <div class="absolute bottom-1 right-3 text-4xl text-purple-200 font-serif leading-none rotate-180">"</div>
                    </div>
                @endif

                {{-- Video principal --}}
                @if($isHtml5 && $videoSrc)
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Video</p>
                        <video src="{{ $videoSrc }}"
                               controls playsinline preload="metadata"
                               class="w-full rounded-2xl max-h-72 object-contain bg-black">
                            Tu navegador no soporta video HTML5.
                        </video>
                    </div>
                @elseif($isEmbed && $embedSrc)
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Video</p>
                        <div class="aspect-video rounded-2xl overflow-hidden">
                            <iframe src="{{ $embedSrc }}" class="w-full h-full"
                                    frameborder="0" allowfullscreen
                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"></iframe>
                        </div>
                    </div>
                @endif

                {{-- Galería adicional --}}
                @if($galleryImages->count() > 1)
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Galería</p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($galleryImages->skip(1) as $img)
                                @if($img->image_path)
                                    <img src="{{ asset('storage/' . $img->image_path) }}"
                                         alt="Imagen"
                                         class="w-full h-28 object-cover rounded-xl">
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-white/60 pb-2">Powered by <span class="font-semibold">KraftDo</span></p>

    </div>
</div>
