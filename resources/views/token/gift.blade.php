{{-- 
    Professional Token Gift View
    
    Modular implementation with external CSS/JS and Alpine.js components
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Regalo personalizado NFC - {{ $contentGift?->recipient_name ?? 'Regalo especial' }}">
    
    {{-- Page Title --}}
    <title>{{ $contentGift?->recipient_name ? $contentGift->recipient_name . ' - Regalo NFC' : 'Regalo NFC' }}</title>
    
    {{-- External Assets --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    {{-- Vite Assets --}}
    @vite([
        'resources/css/app.css', 
        'resources/css/token-gift.css',
        'resources/js/app.js',
        'resources/js/token-gift.js'
    ])
</head>

<body class="h-full gradient-bg" x-data="tokenGift()" style="background: linear-gradient(135deg, {{ $theme['colors']['gradient_bg'] ?? '#ec4899, #f43f5e, #be185d' }});">  
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            
            {{-- Header Component --}}
            <x-token-gift.header :theme="$theme" />

            {{-- Voice Selector Component --}}
            <x-token-gift.voice-selector />

            {{-- Enhanced Main Card --}}
            <main class="bg-white rounded-3xl card-shadow hover:card-float p-8 animate-slide-up relative overflow-hidden" style="animation-delay: 0.4s;" role="main">
                <!-- Subtle background decoration -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-purple-50 via-pink-50 to-transparent rounded-full blur-3xl opacity-30 -z-10"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-blue-50 via-indigo-50 to-transparent rounded-full blur-2xl opacity-40 -z-10"></div>
                
                {{-- Recipient Information --}}
                <x-token-gift.recipient :content-gift="$contentGift" :theme="$theme" />

                {{-- Personal Message --}}
                <x-token-gift.message :content-gift="$contentGift" :theme="$theme" />

                {{-- Multimedia Content --}}
                @if($contentMultimedia)
                    <div class="space-y-6">
                        {{-- Video Player --}}
                        @if($contentMultimedia && ($contentMultimedia->video_url || $contentMultimedia->video_file))
                        <!-- Enhanced Video Title -->
                        <div class="section-title-container flex items-center justify-center gap-3 mb-6 animate-fade-in animate-delay-600">
                            <div class="flex-1 section-divider" style="color: {{ $theme['colors']['accent'] ?? 'rgb(252 165 165)' }};"></div>
                            <div class="section-title-badge flex items-center gap-3 px-6 py-3 bg-gradient-to-r {{ $theme['colors']['section_bg'] ?? 'from-red-50 to-pink-50' }} rounded-full border {{ $theme['colors']['section_border'] ?? 'border-red-200' }} shadow-sm hover:shadow-lg transition-all duration-300">
                                <span class="text-2xl sm:text-2xl icon-bounce gift-emoji">{{ $theme['video_icon'] ?? '🎬' }}</span>
                                <h2 class="text-lg sm:text-xl font-bold text-transparent bg-gradient-to-r {{ $theme['colors']['section_text'] ?? 'from-red-600 via-pink-600 to-purple-600' }} bg-clip-text whitespace-nowrap">
                                    Video Especial
                                </h2>
                                <span class="text-2xl sm:text-2xl icon-bounce gift-emoji" style="animation-delay: 0.3s;">{{ $theme['video_icon'] ?? '🎭' }}</span>
                            </div>
                            <div class="flex-1 section-divider" style="color: {{ $theme['colors']['accent'] ?? 'rgb(252 165 165)' }};"></div>
                        </div>
                        @endif
                        <x-token-gift.video-player :content-multimedia="$contentMultimedia" />

                        {{-- Audio Player --}}
                        @if($contentMultimedia && ($contentMultimedia->audio_url || $contentMultimedia->audio_file))
                        <!-- Enhanced Audio Title -->
                        <div class="section-title-container flex items-center justify-center gap-3 mb-6 animate-fade-in animate-delay-700">
                            <div class="flex-1 section-divider" style="color: {{ $theme['colors']['accent'] ?? 'rgb(134 239 172)' }};"></div>
                            <div class="section-title-badge flex items-center gap-3 px-6 py-3 bg-gradient-to-r {{ $theme['colors']['section_bg'] ?? 'from-green-50 to-emerald-50' }} rounded-full border {{ $theme['colors']['section_border'] ?? 'border-green-200' }} shadow-sm hover:shadow-lg transition-all duration-300">
                                <span class="text-2xl sm:text-2xl icon-bounce gift-emoji">{{ $theme['audio_icon'] ?? '🎵' }}</span>
                                <h2 class="text-lg sm:text-xl font-bold text-transparent bg-gradient-to-r {{ $theme['colors']['section_text'] ?? 'from-green-600 via-emerald-600 to-teal-600' }} bg-clip-text whitespace-nowrap">
                                    Audio Especial
                                </h2>
                                <span class="text-2xl sm:text-2xl icon-bounce gift-emoji" style="animation-delay: 0.4s;">{{ $theme['audio_icon'] ?? '🎧' }}</span>
                            </div>
                            <div class="flex-1 section-divider" style="color: {{ $theme['colors']['accent'] ?? 'rgb(134 239 172)' }};"></div>
                        </div>
                        @endif
                        <x-token-gift.audio-player :content-multimedia="$contentMultimedia" />
                    </div>
                @endif

                {{-- Gallery --}}
                @if($galleryImages && count($galleryImages) > 0)
                    <!-- Enhanced Gallery Title -->
                    <div class="section-title-container flex items-center justify-center gap-3 mb-8 animate-fade-in animate-delay-800">
                        <div class="flex-1 section-divider" style="color: {{ $theme['colors']['accent'] ?? 'rgb(196 181 253)' }};"></div>
                        <div class="section-title-badge flex items-center gap-3 px-6 py-4 bg-gradient-to-r {{ $theme['colors']['section_bg'] ?? 'from-purple-50 to-pink-50' }} rounded-full border {{ $theme['colors']['section_border'] ?? 'border-purple-200' }} shadow-sm hover:shadow-lg relative overflow-hidden">
                            <!-- Background decoration with theme colors -->
                            <div class="absolute inset-0 bg-gradient-to-r {{ $theme['colors']['section_bg'] ?? 'from-purple-100/50 to-pink-100/50' }} blur-xl opacity-50"></div>
                            
                            <!-- Content -->
                            <div class="relative z-10 flex flex-col sm:flex-row items-center gap-2 sm:gap-3">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <span class="text-xl sm:text-2xl icon-bounce gift-emoji">{{ $theme['gallery_icon'] ?? '📸' }}</span>
                                    <h2 class="text-lg sm:text-xl font-bold text-transparent bg-gradient-to-r {{ $theme['colors']['section_text'] ?? 'from-purple-600 via-pink-600 to-indigo-600' }} bg-clip-text text-center sm:text-left">
                                        Galería de Imágenes
                                    </h2>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="bg-gradient-to-r {{ $theme['colors']['primary'] ?? 'from-purple-500 to-pink-500' }} text-white text-sm font-bold px-3 py-1 rounded-full shadow-lg">
                                        {{ count($galleryImages) }}
                                    </div>
                                    <span class="text-xl sm:text-2xl icon-bounce gift-emoji" style="animation-delay: 0.5s;">{{ $theme['gallery_icon'] ?? '🖼️' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 section-divider" style="color: {{ $theme['colors']['accent'] ?? 'rgb(196 181 253)' }};"></div>
                    </div>
                @endif
                
                <x-token-gift.gallery :gallery-images="$galleryImages ?? []" />

                {{-- Footer --}}
                <x-token-gift.footer />
                
            </main>
        </div>
    </div>

    {{-- Modal Component --}}
    <x-token-gift.modal />

    {{-- Autoplay Overlay Component --}}
    <x-token-gift.autoplay-overlay :content-multimedia="$contentMultimedia" />

</body>
</html>