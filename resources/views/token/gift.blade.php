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

            {{-- Play Message Button --}}
            <div class="text-center mb-6">
                <button onclick="window.readMessageAloud()" 
                        class="inline-flex items-center gap-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    🔊 Reproducir Mensaje
                </button>
            </div>

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
                        @if($contentMultimedia && ($contentMultimedia->video_url || $contentMultimedia->video_file))
                        <h1 class="text-lg font-semibold text-gray-900">Video Especial</h1>
                        @endif
                        <x-token-gift.video-player :content-multimedia="$contentMultimedia" />

                        {{-- Audio Player --}}
                        @if($contentMultimedia && ($contentMultimedia->audio_url || $contentMultimedia->audio_file))
                        <h1 class="text-lg font-semibold text-gray-900">Audio Especial</h1>
                        @endif
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
         x-init="console.log('Overlay init:', { hasAudio, hasVideo, showOverlay: !hasVideo && !hasAudio }); showAutoplayOverlay = !hasVideo && !hasAudio;"
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
            <h2 class="text-2xl font-bold text-gray-900 mb-4" x-text="hasVideo || hasAudio ? '¡Experiencia Multimedia!' : '¡Escucha tu Mensaje!'">
                ¡Escucha tu Mensaje!
            </h2>
            
            <!-- Description -->
            <p class="text-gray-600 mb-6 leading-relaxed" x-text="hasVideo || hasAudio ? 'Para brindarte la mejor experiencia, necesitamos activar la reproducción automática del contenido multimedia.' : 'Te leeremos el mensaje personalizado en voz alta para una experiencia única.'">
                Te leeremos el mensaje personalizado en voz alta para una experiencia única.
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
                    Activar Multimedia
                </span>
            </button>
            
            <!-- Info Text -->
            <p class="text-xs text-gray-500 mt-4">
                Esto iniciará la reproducción del contenido multimedia disponible
            </p>
        </div>
    </div>

    <!-- JavaScript for Autoplay -->
    <script>
        window.enableAutoplay = function() {
            console.log('Starting media playback...');
            
            // Try to find and start video first
            const video = document.querySelector('video[x-ref="videoElement"]');
            if (video) {
                console.log('Found video, starting video playback');
                
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
                return; // Exit early if video found
            }
            
            // If no video, try to find and start audio
            const audio = document.querySelector('audio[x-ref="audioElement"]');
            if (audio) {
                console.log('Found audio, starting audio playback');
                
                // Set audio properties for better playback
                audio.muted = false; // Can unmute since user interacted
                audio.controls = true; // Show controls
                
                // Start playing the audio
                audio.play()
                    .then(() => {
                        console.log('Audio started playing successfully');
                    })
                    .catch(e => {
                        console.log('Audio play failed:', e);
                        // Fallback: try with muted
                        audio.muted = true;
                        audio.play().then(() => {
                            console.log('Audio started playing muted');
                        }).catch(err => {
                            console.error('Even muted audio playback failed:', err);
                        });
                    });
                return; // Exit early if audio found
            }
            
            // If no video/audio found, use text-to-speech for the message
            console.log('No media elements found, starting text-to-speech');
            window.readMessageAloud();
        }

        // Text-to-Speech functionality
        function tryTextToSpeech() {
            // Check if browser supports Speech Synthesis
            if (!('speechSynthesis' in window)) {
                console.log('Text-to-speech not supported in this browser');
                return;
            }

            // Collect all text content from the gift
            let fullText = '';
            
            // Get recipient info - more specific selector
            const recipientElement = document.querySelector('main h2, .max-w-2xl h2');
            if (recipientElement) {
                console.log('Found recipient:', recipientElement.textContent);
                fullText += recipientElement.textContent.trim() + '. ';
            }
            
            // Get sender info - look for p element that contains "De:"
            const allParagraphs = document.querySelectorAll('p');
            allParagraphs.forEach(p => {
                if (p.textContent.includes('De:')) {
                    console.log('Found sender:', p.textContent);
                    fullText += p.textContent.trim() + '. ';
                }
            });
            
            // Get the personal message section
            const messageSection = document.querySelector('.bg-gradient-to-r');
            if (messageSection) {
                const messageTitle = messageSection.querySelector('h3');
                const messageContent = messageSection.querySelector('.text-gray-700');
                
                if (messageTitle) {
                    console.log('Found message title:', messageTitle.textContent);
                    fullText += messageTitle.textContent.replace('💌', '').trim() + ': ';
                }
                
                if (messageContent) {
                    console.log('Found message content:', messageContent.textContent);
                    fullText += messageContent.textContent.trim();
                }
            }

            if (!fullText || fullText.length < 3) {
                console.log('No content found for text-to-speech');
                return;
            }

            console.log('Starting text-to-speech for full content:', fullText);

            // Create speech synthesis utterance
            const utterance = new SpeechSynthesisUtterance(fullText);
            
            // Configure speech settings
            utterance.lang = 'es-ES'; // Spanish
            utterance.rate = 0.9; // Slightly slower for clarity
            utterance.pitch = 1.0; // Normal pitch
            utterance.volume = 0.8; // Slightly quieter

            // Event handlers
            utterance.onstart = () => {
                console.log('Text-to-speech started');
            };

            utterance.onend = () => {
                console.log('Text-to-speech finished');
            };

            utterance.onerror = (error) => {
                console.error('Text-to-speech error:', error);
            };

            // Start speaking
            speechSynthesis.speak(utterance);
        }

        // Function to manually trigger text-to-speech (can be called from anywhere)
        window.readMessageAloud = function() {
            if (speechSynthesis.speaking) {
                speechSynthesis.cancel(); // Stop current speech
            }
            
            // Collect all text content from the gift
            let fullText = '';
            
            console.log('=== DEBUGGING TTS ===');
            
            // Get recipient info - try multiple selectors
            let recipientElement = document.querySelector('main h2');
            if (!recipientElement) {
                recipientElement = document.querySelector('.max-w-2xl h2');
            }
            if (!recipientElement) {
                recipientElement = document.querySelector('h2');
            }
            
            if (recipientElement) {
                console.log('Found recipient:', recipientElement.textContent);
                fullText += recipientElement.textContent.trim() + '. ';
            } else {
                console.log('No recipient element found');
            }
            
            // Get sender info - look for p element that contains "De:"
            const allParagraphs = document.querySelectorAll('p');
            console.log('Found paragraphs:', allParagraphs.length);
            allParagraphs.forEach((p, index) => {
                console.log(`Paragraph ${index}:`, p.textContent);
                if (p.textContent.includes('De:')) {
                    console.log('Found sender:', p.textContent);
                    fullText += p.textContent.trim() + '. ';
                }
            });
            
            // Get the personal message section - try multiple approaches
            let messageSection = document.querySelector('.bg-gradient-to-r');
            if (!messageSection) {
                messageSection = document.querySelector('[class*="gradient"]');
            }
            if (!messageSection) {
                messageSection = document.querySelector('.from-pink-50');
            }
            
            console.log('Message section found:', !!messageSection);
            console.log('All gradient elements:', document.querySelectorAll('[class*="gradient"]').length);
            
            if (messageSection) {
                const messageTitle = messageSection.querySelector('h3');
                const messageContent = messageSection.querySelector('.text-gray-700');
                
                console.log('Message title found:', !!messageTitle);
                console.log('Message content found:', !!messageContent);
                
                if (messageTitle) {
                    console.log('Message title text:', messageTitle.textContent);
                    fullText += messageTitle.textContent.replace('💌', '').trim() + ': ';
                }
                
                if (messageContent) {
                    console.log('Message content text:', messageContent.textContent);
                    fullText += messageContent.textContent.trim();
                }
            }
            
            // Always try fallback to ensure we get the message
            console.log('Trying fallback selectors anyway...');
            const allH3 = document.querySelectorAll('h3');
            const allTextElements = document.querySelectorAll('.text-gray-700');
            
            console.log('Found h3 elements:', allH3.length);
            console.log('Found .text-gray-700 elements:', allTextElements.length);
            
            allH3.forEach((h3, index) => {
                console.log(`H3 ${index}:`, h3.textContent);
                if (h3.textContent.includes('Mensaje') && !fullText.includes('Mensaje Personal')) {
                    fullText += h3.textContent.replace('💌', '').trim() + ': ';
                }
            });
            
            allTextElements.forEach((el, index) => {
                console.log(`Text element ${index}:`, el.textContent);
                const trimmedText = el.textContent.trim();
                
                // Skip if it's part of controls, navigation, or already included
                if (!el.closest('button') && !el.closest('nav') && 
                    trimmedText.length > 3 && // Cambié de 10 a 3 para incluir mensajes cortos
                    !trimmedText.includes('Para:') && 
                    !trimmedText.includes('De:') &&
                    !trimmedText.includes('Error') &&
                    !trimmedText.includes('Cargando') &&
                    !trimmedText.includes('Regalo creado') &&
                    !trimmedText.includes('Te leeremos') &&
                    !trimmedText.includes('Esto iniciará') &&
                    !fullText.includes(trimmedText)) {
                    console.log(`Adding text element ${index}: "${trimmedText}"`);
                    fullText += trimmedText + '. ';
                }
            });

            console.log('Final text to speak:', fullText);
            console.log('Text length:', fullText.length);

            if (fullText && fullText.length > 3) {
                console.log('Starting speech synthesis...');
                const utterance = new SpeechSynthesisUtterance(fullText);
                utterance.lang = 'es-ES';
                utterance.rate = 0.9;
                utterance.pitch = 1.0;
                utterance.volume = 0.8;
                
                utterance.onstart = () => console.log('TTS started');
                utterance.onend = () => console.log('TTS ended');
                utterance.onerror = (e) => console.error('TTS error:', e);
                
                speechSynthesis.speak(utterance);
            } else {
                console.log('No content to speak');
                alert('No se encontró contenido para reproducir');
            }
            
            console.log('=== END DEBUGGING ===');
        }

        // Function to scroll to video and center it
        function scrollToVideo(video) {
            scrollToMedia(video, 'video');
        }
        
        // Generic function to scroll to any media element and center it
        function scrollToMedia(mediaElement, mediaType = 'media') {
            console.log(`Scrolling to ${mediaType}...`);
            
            // Get media container (the parent div that contains the media)
            const mediaContainer = mediaElement.closest('.relative') || 
                                  mediaElement.closest('[class*="bg-gradient"]') || 
                                  mediaElement.parentElement;
            
            if (mediaContainer) {
                // Calculate position to center the media in viewport
                const rect = mediaContainer.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                const containerHeight = rect.height;
                
                // Calculate offset to center the media
                const offsetTop = window.pageYOffset + rect.top;
                const centerOffset = (windowHeight - containerHeight) / 2;
                const scrollToPosition = Math.max(0, offsetTop - centerOffset);
                
                // Smooth scroll to the calculated position
                window.scrollTo({
                    top: scrollToPosition,
                    behavior: 'smooth'
                });
                
                console.log(`Scrolled to ${mediaType} position:`, scrollToPosition);
            } else {
                // Fallback: scroll to media element directly
                mediaElement.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center',
                    inline: 'nearest'
                });
                
                console.log(`Used fallback scroll method for ${mediaType}`);
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