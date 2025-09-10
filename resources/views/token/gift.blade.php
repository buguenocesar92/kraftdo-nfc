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

<body class="h-full gradient-bg" x-data="tokenGift()">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            
            {{-- Header Component --}}
            <x-token-gift.header />

            {{-- Main Card --}}
            <main class="bg-white rounded-2xl card-shadow p-8 animate-fade-in" style="animation-delay: 0.2s;" role="main">
                
                {{-- Recipient Information --}}
                <x-token-gift.recipient :content-gift="$contentGift" />

                {{-- Personal Message --}}
                <x-token-gift.message :content-gift="$contentGift" />

                {{-- Multimedia Content --}}
                @if($contentMultimedia)
                    <div class="space-y-6">
                        {{-- Video Player --}}
                        <h1 class="text-lg font-semibold text-gray-900">Video Especial</h1>
                        <x-token-gift.video-player :content-multimedia="$contentMultimedia" />

                        {{-- Audio Player --}}
                        <h1 class="text-lg font-semibold text-gray-900">Audio Especial</h1>
                        <x-token-gift.audio-player :content-multimedia="$contentMultimedia" />
                    </div>
                @endif

                {{-- Gallery --}}
                <x-token-gift.gallery :gallery-images="$galleryImages ?? []" />

                {{-- Footer --}}
                <x-token-gift.footer />
                
            </main>
        </div>
    </div>

    {{-- Modal Component --}}
    <x-token-gift.modal />

    {{-- Autoplay Permission Overlay --}}
    <div x-data="{ 
        showAutoplayOverlay: true,
        hasAudio: {{ $contentMultimedia && ($contentMultimedia->audio_url || $contentMultimedia->audio_file) ? 'true' : 'false' }},
        hasVideo: {{ $contentMultimedia && ($contentMultimedia->video_url || $contentMultimedia->video_file) ? 'true' : 'false' }}
    }" 
         x-init="console.log('Overlay init:', { hasAudio, hasVideo, showOverlay: hasVideo && !hasAudio }); showAutoplayOverlay = true;"
         x-show="showAutoplayOverlay" 
         class="fixed inset-0 flex items-center justify-center"
         style="z-index: 9999; display: block !important; background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);"
         x-transition>
        
        <!-- Overlay Content -->
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 text-center shadow-2xl">
            <!-- Icon -->
            <div class="mb-6">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
            </div>
            
            <!-- Title -->
            <h2 class="text-2xl font-bold text-gray-900 mb-4">
                ¡Experiencia Multimedia!
            </h2>
            
            <!-- Description -->
            <p class="text-gray-600 mb-6 leading-relaxed">
                Para brindarte la mejor experiencia, necesitamos activar la reproducción automática de videos.
            </p>
            
            <!-- Debug Info -->
            <div class="text-xs text-gray-400 mb-4" x-text="'Audio: ' + hasAudio + ', Video: ' + hasVideo"></div>
            
            <!-- Action Button -->
            <button x-on:click="showAutoplayOverlay = false; window.enableAutoplay()" 
                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold py-4 px-6 rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                <span class="flex items-center justify-center gap-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    Activar Reproducción
                </span>
            </button>
            
            <!-- Info Text -->
            <p class="text-xs text-gray-500 mt-4">
                Esto permitirá que los videos se reproduzcan automáticamente
            </p>
        </div>
    </div>

    <!-- JavaScript for Autoplay -->
    <script>
        window.enableAutoplay = function() {
            console.log('Starting video playback...');
            
            // Find and start playing the video immediately
            const video = document.querySelector('video[x-ref="videoElement"]');
            if (video) {
                console.log('Found video, starting playback');
                
                // Set video properties for better playback
                video.muted = false; // Can unmute since user interacted
                video.controls = true; // Show controls
                
                // Start playing the video
                video.play()
                    .then(() => {
                        console.log('Video started playing successfully');
                        
                        // Scroll to center the video in the viewport
                        scrollToVideo(video);
                    })
                    .catch(e => {
                        console.log('Video play failed:', e);
                        // Fallback: try with muted
                        video.muted = true;
                        video.play().then(() => {
                            // Still scroll even if muted
                            scrollToVideo(video);
                        }).catch(err => {
                            console.error('Even muted playback failed:', err);
                            // Scroll anyway to show the video
                            scrollToVideo(video);
                        });
                    });
            } else {
                console.log('No video element found');
            }
        }

        // Function to scroll to video and center it
        function scrollToVideo(video) {
            console.log('Scrolling to video...');
            
            // Get video container (the parent div that contains the video)
            const videoContainer = video.closest('.relative') || video.parentElement;
            
            if (videoContainer) {
                // Calculate position to center the video in viewport
                const rect = videoContainer.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                const containerHeight = rect.height;
                
                // Calculate offset to center the video
                const offsetTop = window.pageYOffset + rect.top;
                const centerOffset = (windowHeight - containerHeight) / 2;
                const scrollToPosition = Math.max(0, offsetTop - centerOffset);
                
                // Smooth scroll to the calculated position
                window.scrollTo({
                    top: scrollToPosition,
                    behavior: 'smooth'
                });
                
                console.log('Scrolled to video position:', scrollToPosition);
            } else {
                // Fallback: scroll to video element directly
                video.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center',
                    inline: 'nearest'
                });
                
                console.log('Used fallback scroll method');
            }
        }

        // Prevent scrolling when overlay is visible
        document.addEventListener('alpine:init', function() {
            // Setup scroll prevention
            const checkOverlay = () => {
                const hasOverlay = document.querySelector('[x-show="showAutoplayOverlay"]');
                const isVisible = hasOverlay && hasOverlay.style.display !== 'none' && 
                                 (!hasOverlay.__x || hasOverlay.__x.$data.showAutoplayOverlay !== false);
                
                if (isVisible) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = 'auto';
                }
            };
            
            // Check immediately and periodically
            checkOverlay();
            setInterval(checkOverlay, 100);
        });
    </script>
</body>
</html>