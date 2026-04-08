{{--
    Partial: Regalo NFC — replica de GiftPreview.tsx
    Variables: $token, $dynamicContent, $contentGift, $contentMultimedia
--}}
@php
    use App\Helpers\ThemeHelper;
    use Illuminate\Support\Facades\Cache;

    $themeKey         = $contentMultimedia?->settings['theme'] ?? 'love';
    $themeConfig      = Cache::remember("theme_config:{$themeKey}", 3600,
        fn() => ThemeHelper::getThemeConfig($themeKey)
    );
    $themeDisplayName = $themeConfig['name']  ?? 'Amor';
    $themeEmoji       = $themeConfig['emoji'] ?? '💕';

    $title     = $dynamicContent?->title       ?? 'Regalo Especial';
    $message   = $contentGift?->message        ?? $dynamicContent?->description ?? '';
    $sender    = $contentGift?->sender_name    ?? '';
    $recipient = $contentGift?->recipient_name ?? '';

    // Video
    $videoSrc = '';
    $isHtml5  = false;
    $isEmbed  = false;
    $embedSrc = '';

    if ($contentMultimedia) {
        if (in_array($contentMultimedia->video_type, ['file_upload', null])
            && $contentMultimedia->video_file) {
            $videoSrc = asset('storage/' . $contentMultimedia->video_file);
            $isHtml5  = true;
        } elseif ($contentMultimedia->video_type === 'direct'
            && $contentMultimedia->video_url) {
            $videoSrc = $contentMultimedia->video_url;
            $isHtml5  = true;
        } elseif ($contentMultimedia->video_type === 'youtube'
            && $contentMultimedia->video_url) {
            preg_match('/(?:youtube\.com.*[?&]v=|youtu\.be\/)([^"&?\/ ]{11})/',
                $contentMultimedia->video_url, $m);
            if (!empty($m[1])) {
                $embedSrc = "https://www.youtube.com/embed/{$m[1]}?rel=0";
                $isEmbed  = true;
            }
        }
    }

    // Audio
    $audioSrc = '';
    if ($contentMultimedia) {
        if ($contentMultimedia->audio_type === 'file_upload'
            && $contentMultimedia->audio_file) {
            $audioSrc = asset('storage/' . $contentMultimedia->audio_file);
        } elseif ($contentMultimedia->audio_url) {
            $audioSrc = $contentMultimedia->audio_url;
        }
    }

    $galleryImages = $contentMultimedia?->galleryImages ?? collect();
@endphp

<div
    x-data="{
        showOverlay: true,
        isVisible: false,
        videoPlaying: false,
        isIOS: /iPad|iPhone|iPod/.test(navigator.userAgent) ||
               (navigator.userAgent.includes('Mac') && 'ontouchend' in document),
        openGift() {
            this.showOverlay = false;
            @if($isHtml5 && $videoSrc)
            this.$nextTick(() => {
                setTimeout(() => {
                    const v = this.$refs.mainVideo;
                    if (!v) return;
                    v.addEventListener('ended', () => {
                        if (document.fullscreenElement) document.exitFullscreen().catch(() => {});
                    });
                    if (this.isIOS) {
                        if (v.paused) v.play().catch(() => {});
                        if (v.webkitEnterFullscreen) v.webkitEnterFullscreen();
                    } else {
                        v.play().catch(() => {});
                        const req = v.requestFullscreen || v.webkitRequestFullscreen
                                 || v.mozRequestFullScreen || v.msRequestFullscreen;
                        if (req) req.call(v).catch(() => {});
                    }
                }, 500);
            });
            @elseif($audioSrc)
            this.$nextTick(() => {
                setTimeout(() => {
                    const a = document.querySelector('audio');
                    if (a) a.play().catch(() => {});
                }, 500);
            });
            @endif
        }
    }"
    x-init="setTimeout(() => isVisible = true, 100)"
    class="min-h-screen flex items-start lg:items-center justify-center p-0 lg:p-4 relative"
>

    {{-- ── OVERLAY: Sobre con regalo ── --}}
    <div
        x-show="showOverlay"
        x-transition:leave="transition duration-700 ease-in"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-40 flex items-center justify-center bg-white/95 backdrop-blur-sm"
    >
        <div class="text-center px-6">

            {{-- Sobre SVG --}}
            <div class="relative mb-8 transform hover:scale-105 transition-transform duration-300">
                <svg width="200" height="140" viewBox="0 0 200 140"
                     class="mx-auto drop-shadow-2xl">
                    {{-- Cuerpo del sobre --}}
                    <rect x="10" y="30" width="180" height="100" rx="8"
                          fill="#f8fafc" stroke="#e2e8f0" stroke-width="2"/>
                    {{-- Solapa --}}
                    <path d="M10 30 L100 85 L190 30 Z"
                          fill="#f1f5f9" stroke="#e2e8f0" stroke-width="2"/>
                    {{-- Moño rosa --}}
                    <ellipse cx="100" cy="50" rx="15" ry="8"  fill="#ec4899"/>
                    <ellipse cx="85"  cy="45" rx="8"  ry="12" fill="#be185d"/>
                    <ellipse cx="115" cy="45" rx="8"  ry="12" fill="#be185d"/>
                    {{-- Destellos --}}
                    <circle cx="50"  cy="60" r="2"   fill="#fbbf24" opacity="0.8"/>
                    <circle cx="150" cy="70" r="1.5" fill="#fbbf24" opacity="0.6"/>
                    <circle cx="170" cy="50" r="1"   fill="#fbbf24" opacity="0.7"/>
                    <circle cx="30"  cy="80" r="1.5" fill="#fbbf24" opacity="0.5"/>
                </svg>

                {{-- Corazón flotante --}}
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 animate-bounce">
                    💝
                </div>
            </div>

            {{-- Título --}}
            <h2 class="text-2xl md:text-3xl font-bold mb-4 text-gray-900">
                ¡Tienes un regalo especial!
            </h2>

            {{-- Subtítulo --}}
            <p class="text-sm md:text-base mb-8 max-w-md mx-auto text-gray-600">
                @if($sender)
                    {{ $sender }} te ha enviado
                @else
                    Alguien especial te ha enviado
                @endif
                un regalo lleno de amor y recuerdos únicos
            </p>

            {{-- Botón Abrir --}}
            <button
                @click="openGift()"
                class="relative inline-flex items-center justify-center px-8 py-4 text-lg font-medium text-white bg-gradient-to-r from-pink-500 to-purple-600 rounded-full shadow-lg hover:from-pink-600 hover:to-purple-700 hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-pink-300/50 animate-bounce"
            >
                <span class="mr-2">🎁</span>
                Abrir Regalo
            </button>

            {{-- Destellos flotantes --}}
            <div class="absolute top-1/4 left-1/4 animate-pulse pointer-events-none">
                <span class="text-2xl">✨</span>
            </div>
            <div class="absolute top-1/3 right-1/4 animate-bounce pointer-events-none" style="animation-delay:.5s">
                <span class="text-xl">💫</span>
            </div>
            <div class="absolute bottom-1/3 left-1/3 animate-pulse pointer-events-none" style="animation-delay:1s">
                <span class="text-lg">⭐</span>
            </div>
            <div class="absolute top-1/2 right-1/3 animate-bounce pointer-events-none" style="animation-delay:1.5s">
                <span class="text-base">🌟</span>
            </div>
        </div>
    </div>

    {{-- ── TARJETA PRINCIPAL ── --}}
    <div
        class="relative w-full h-screen lg:h-auto lg:max-w-4xl xl:max-w-4xl mx-auto transition-all duration-1000 ease-out"
        :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
    >
        <div class="h-full lg:h-auto rounded-none lg:rounded-3xl overflow-hidden bg-white shadow-2xl flex flex-col">

            {{-- ── HEADER gradiente (azul→morado→rosa) ── --}}
            <div class="relative h-32 sm:h-40 md:h-48 lg:h-52 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-500 flex items-center justify-center flex-shrink-0">

                {{-- Círculo con ícono de regalo --}}
                <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-full bg-white shadow-lg flex items-center justify-center transition-transform duration-300 hover:scale-110 hover:rotate-12">
                    {{-- Lucide Gift (SVG inline) --}}
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 text-gray-700"
                         viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 12 20 22 4 22 4 12"/>
                        <rect x="2" y="7" width="20" height="5"/>
                        <line x1="12" y1="22" x2="12" y2="7"/>
                        <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
                        <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
                    </svg>
                </div>

                {{-- Círculos decorativos --}}
                <div class="absolute top-4 right-4 w-3 h-3 bg-white/30 rounded-full"></div>
                <div class="absolute top-8 right-8 w-2 h-2 bg-white/20 rounded-full"></div>
            </div>

            {{-- ── CONTENIDO ── --}}
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 md:p-8 lg:p-10 space-y-4 sm:space-y-6">

                {{-- Título + badge del tema + mensaje --}}
                <div class="text-center">
                    <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-2 sm:mb-3">
                        {{ $title }}
                    </h1>

                    <div class="mb-3 sm:mb-4">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-gray-100 text-gray-700 border border-gray-200">
                            {{ $themeEmoji }} {{ $themeDisplayName }}
                        </span>
                    </div>

                    @if($message)
                        <p class="text-sm md:text-base lg:text-lg leading-relaxed text-gray-600 mb-3 sm:mb-4">
                            {{ $message }}
                        </p>
                    @endif
                </div>

                {{-- ── INFO CARDS: De / Para ── --}}
                @if($sender || $recipient)
                    <div class="space-y-2 sm:space-y-3">

                        @if($sender)
                            <div class="flex items-center p-3 md:p-4 lg:p-5 rounded-xl bg-gray-50 hover:shadow-md transition-all duration-300 hover:scale-105">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 lg:w-14 lg:h-14 rounded-lg bg-blue-100 flex items-center justify-center mr-3 md:mr-4 flex-shrink-0 transition-transform duration-300 hover:rotate-6">
                                    {{-- Lucide Heart --}}
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 text-blue-600"
                                         viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                    </svg>
                                </div>
                                <div class="text-sm md:text-base lg:text-lg font-medium text-gray-900">
                                    De: {{ $sender }}
                                </div>
                            </div>
                        @endif

                        @if($recipient)
                            <div class="flex items-center p-3 md:p-4 lg:p-5 rounded-xl bg-gray-50 hover:shadow-md transition-all duration-300 hover:scale-105">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 lg:w-14 lg:h-14 rounded-lg bg-purple-100 flex items-center justify-center mr-3 md:mr-4 flex-shrink-0 transition-transform duration-300 hover:rotate-6">
                                    {{-- Lucide User --}}
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 text-purple-600"
                                         viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </div>
                                <div class="text-sm md:text-base lg:text-lg font-medium text-gray-900">
                                    Para: {{ $recipient }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ── MULTIMEDIA ── --}}
                @if(($isHtml5 && $videoSrc) || ($isEmbed && $embedSrc) || $audioSrc || $galleryImages->isNotEmpty())
                    <div class="space-y-3 sm:space-y-4 md:space-y-6">

                        {{-- Video HTML5 --}}
                        @if($isHtml5 && $videoSrc)
                            <div class="space-y-2 sm:space-y-3">
                                <h4 class="text-sm md:text-base font-medium text-gray-700 flex items-center">
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 bg-green-100 rounded-lg flex items-center justify-center mr-2 md:mr-3 flex-shrink-0">
                                        <span class="text-xs sm:text-sm md:text-base">📹</span>
                                    </div>
                                    Videos
                                </h4>
                                <div class="bg-gray-900 overflow-hidden rounded-xl">
                                    <div class="relative aspect-video w-full">
                                        <video
                                            x-ref="mainVideo"
                                            src="{{ $videoSrc }}"
                                            controls
                                            preload="metadata"
                                            :playsinline="!isIOS"
                                            @play="videoPlaying = true"
                                            @pause="videoPlaying = false"
                                            @ended="videoPlaying = false"
                                            class="w-full h-full object-cover"
                                        ></video>
                                        {{-- Hint para iOS --}}
                                        <div
                                            x-show="isIOS && !videoPlaying"
                                            class="absolute inset-0 flex items-center justify-center bg-black/30 pointer-events-none"
                                        >
                                            <div class="bg-black/80 text-white px-4 py-2 rounded-lg text-sm text-center animate-pulse">
                                                <div class="text-2xl mb-1">📱</div>
                                                <div>Toca ▶️ para ver en pantalla completa</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        {{-- Video YouTube embed --}}
                        @elseif($isEmbed && $embedSrc)
                            <div class="space-y-2 sm:space-y-3">
                                <h4 class="text-sm md:text-base font-medium text-gray-700 flex items-center">
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-green-100 rounded-lg flex items-center justify-center mr-2 flex-shrink-0">
                                        <span class="text-xs sm:text-sm">📹</span>
                                    </div>
                                    Videos
                                </h4>
                                <div class="bg-gray-900 rounded-xl overflow-hidden">
                                    <div class="aspect-video">
                                        <iframe
                                            src="{{ $embedSrc }}"
                                            class="w-full h-full"
                                            frameborder="0"
                                            allowfullscreen
                                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                        ></iframe>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Audio --}}
                        @if($audioSrc)
                            <div class="space-y-2 sm:space-y-3">
                                <h4 class="text-sm md:text-base font-medium text-gray-700 flex items-center">
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-2 md:mr-3 flex-shrink-0">
                                        <span class="text-xs sm:text-sm md:text-base">🎵</span>
                                    </div>
                                    Audio
                                </h4>
                                <div class="bg-gray-50 rounded-xl overflow-hidden p-4">
                                    <audio
                                        src="{{ $audioSrc }}"
                                        controls
                                        preload="metadata"
                                        class="w-full"
                                    ></audio>
                                </div>
                            </div>
                        @endif

                        {{-- Galería --}}
                        @if($galleryImages->isNotEmpty())
                            <div class="space-y-2 sm:space-y-3">
                                <h4 class="text-sm md:text-base font-medium text-gray-700 flex items-center">
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 bg-pink-100 rounded-lg flex items-center justify-center mr-2 md:mr-3 flex-shrink-0">
                                        <span class="text-xs sm:text-sm md:text-base">📸</span>
                                    </div>
                                    Fotos
                                </h4>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($galleryImages as $img)
                                        @if($img->image_path)
                                            <img
                                                src="{{ asset('storage/' . $img->image_path) }}"
                                                alt="{{ $img->alt_text ?? 'Imagen del regalo' }}"
                                                class="w-full h-36 sm:h-44 object-cover rounded-xl hover:scale-105 transition-transform duration-300"
                                                loading="lazy"
                                            >
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                @endif

            </div>

            {{-- ── FOOTER ── --}}
            <div class="flex-shrink-0 px-4 py-4 sm:py-6 text-center border-t border-gray-100">
                <p class="text-sm text-gray-600 font-medium mb-1">
                    Un regalo hecho con ❤️ especialmente para ti
                </p>
                <p class="text-xs text-gray-400">
                    Creado con tecnología NFC by KraftDo
                </p>
            </div>

        </div>
    </div>

</div>
