{{-- Advanced Token Gift Video Player Component --}}
@props([
    'contentMultimedia' => null
])

@if($contentMultimedia && ($contentMultimedia->video_url || $contentMultimedia->video_file))
    @php
        $videoId = '';
        $videoType = '';
        $videoSrc = '';
        $thumbnailSrc = '';
        
        // Determine video type and extract ID
        if ($contentMultimedia->video_type === 'youtube' && $contentMultimedia->video_url) {
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $contentMultimedia->video_url, $matches)) {
                $videoId = $matches[1];
                $videoType = 'youtube';
                $videoSrc = "https://www.youtube.com/embed/{$videoId}?enablejsapi=1&rel=0&modestbranding=1";
                $thumbnailSrc = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
            }
        } elseif ($contentMultimedia->video_type === 'vimeo' && $contentMultimedia->video_url) {
            if (preg_match('/vimeo\.com\/(\d+)/', $contentMultimedia->video_url, $matches)) {
                $videoId = $matches[1];
                $videoType = 'vimeo';
                $videoSrc = "https://player.vimeo.com/video/{$videoId}?api=1";
                // Vimeo thumbnail would need API call
            }
        } elseif ($contentMultimedia->video_type === 'file_upload' && $contentMultimedia->video_file) {
            $videoId = 'local_' . md5($contentMultimedia->video_file);
            $videoType = 'html5';
            $videoSrc = asset('storage/' . $contentMultimedia->video_file);
        } elseif ($contentMultimedia->video_type === 'direct' && $contentMultimedia->video_url) {
            $videoId = 'direct_' . md5($contentMultimedia->video_url);
            $videoType = 'html5';
            $videoSrc = $contentMultimedia->video_url;
        }
    @endphp

    @if($videoId)
        <div class="bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 rounded-xl p-6" 
             x-data="{
                videoId: '{{ $videoId }}',
                videoType: '{{ $videoType }}',
                videoSrc: '{{ $videoSrc }}',
                thumbnailSrc: '{{ $thumbnailSrc }}',
                videoLoaded: false,
                videoError: false,
                showThumbnail: true,
                customControls: false,
                currentTime: 0,
                duration: 0,
                playing: false,
                muted: false,
                isVerticalVideo: false,
                videoWidth: 0,
                videoHeight: 0
             }"
             x-init="
                // Initialize video player when component loads
                $nextTick(() => {
                    if (videoType === 'html5') {
                        customControls = true;
                        
                        // Setup video event listeners
                        const video = $refs.videoElement;
                        if (video) {
                            video.addEventListener('timeupdate', () => {
                                currentTime = video.currentTime;
                            });
                            
                            video.addEventListener('loadedmetadata', () => {
                                duration = video.duration;
                                videoWidth = video.videoWidth;
                                videoHeight = video.videoHeight;
                                isVerticalVideo = video.videoHeight > video.videoWidth;
                                
                                // Auto-fullscreen for vertical videos
                                if (isVerticalVideo) {
                                    console.log('Vertical video detected:', videoWidth + 'x' + videoHeight);
                                }
                            });
                            
                            video.addEventListener('play', () => {
                                playing = true;
                            });
                            
                            video.addEventListener('pause', () => {
                                playing = false;
                            });
                            
                            video.addEventListener('volumechange', () => {
                                muted = video.muted;
                            });
                        }
                    }
                    // If no thumbnail, show video immediately and ensure it's ready
                    if (!thumbnailSrc) {
                        showThumbnail = false;
                        // Initialize video immediately
                        $nextTick(() => {
                            const video = $refs.videoElement;
                            if (video && videoType === 'html5') {
                                console.log('Initializing video without thumbnail');
                                console.log('Video element found:', !!video);
                                console.log('Video src attribute:', video.getAttribute('src'));
                                console.log('Video src property:', video.src);
                                console.log('VideoSrc variable:', videoSrc);
                                
                                // Ensure video src is properly set
                                if (!video.src || video.src === '' || video.src !== videoSrc) {
                                    video.src = videoSrc;
                                    console.log('Video src set/updated to:', videoSrc);
                                }
                                
                                // Preload metadata
                                video.preload = 'metadata';
                                video.load();
                                console.log('Video load() called, readyState:', video.readyState);
                            }
                        });
                    }
                });
             ">
            

            <!-- Video Container -->
            <div class="relative w-screen overflow-hidden bg-black left-1/2 -translate-x-1/2 sm:left-0 sm:translate-x-0 sm:w-full sm:rounded-lg"
                 :class="{
                     'aspect-video': !isVerticalVideo,
                     'aspect-[9/16] max-h-[70vh]': isVerticalVideo,
                 }">
                
                <!-- Loading State -->
                <div x-show="currentVideo.loading" 
                     class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center z-20">
                    <div class="text-center text-white">
                        <svg class="w-12 h-12 mx-auto mb-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-lg">Cargando video...</p>
                        <div class="mt-2 bg-gray-700 rounded-full h-1 w-48 mx-auto overflow-hidden">
                            <div class="bg-blue-500 h-full rounded-full animate-pulse" style="width: 60%"></div>
                        </div>
                    </div>
                </div>

                <!-- Error State -->
                <div x-show="currentVideo.error" 
                     class="absolute inset-0 bg-gradient-to-br from-red-900 to-red-700 flex items-center justify-center z-20">
                    <div class="text-center text-white p-6">
                        <svg class="w-16 h-16 mx-auto mb-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold mb-2">Error al cargar el video</h3>
                        <p class="text-red-200 mb-4" x-text="currentVideo.error"></p>
                        <button x-on:click="
                            currentVideo.error = null; 
                            currentVideo.loading = true;
                            $nextTick(() => {
                                if (videoType === 'html5') {
                                    $refs.videoElement.load();
                                } else {
                                    $refs.iframeElement.src = $refs.iframeElement.src;
                                }
                            });
                        " 
                                class="bg-red-600 hover:bg-red-500 px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reintentar
                        </button>
                    </div>
                </div>

                <!-- Thumbnail Preview (for lazy loading) -->
                <div x-show="showThumbnail && thumbnailSrc" 
                     class="absolute inset-0 bg-cover bg-center cursor-pointer group"
                     :style="`background-image: url('${thumbnailSrc}')`"
                     x-on:click="
                        showThumbnail = false;
                        $nextTick(() => {
                            if (videoType === 'html5') {
                                const video = $refs.videoElement;
                                if (video && !video.src) {
                                    video.src = videoSrc;
                                }
                                // Wait for video to be ready then play
                                setTimeout(() => {
                                    if (video) {
                                        video.play().catch(e => console.log('Autoplay prevented:', e));
                                    }
                                }, 100);
                            } else {
                                $refs.iframeElement.src = videoSrc;
                            }
                        });
                     ">
                    <!-- Play Button Overlay -->
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center group-hover:bg-opacity-60 transition-all">
                        <div class="bg-white bg-opacity-90 group-hover:bg-opacity-100 rounded-full p-6 group-hover:scale-110 transition-all shadow-2xl">
                            <svg class="w-12 h-12 text-gray-800 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Video Info Overlay -->
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                        <p class="text-white font-medium">Haz clic para reproducir</p>
                        <p class="text-gray-300 text-sm">{{ ucfirst($videoType) }} Video</p>
                    </div>
                </div>

                <!-- YouTube/Vimeo Iframe -->
                @if($videoType === 'youtube' || $videoType === 'vimeo')
                    <iframe x-ref="iframeElement"
                            class="w-full h-full"
                            :data-video-id="videoId"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                            x-intersect.once="
                                if (!showThumbnail) {
                                    $el.src = videoSrc;
                                    initVideoPlayer($el, videoId, videoType);
                                }
                            ">
                    </iframe>
                @endif

                <!-- HTML5 Video -->
                @if($videoType === 'html5')
                    <video x-ref="videoElement"
                           class="w-full h-full object-contain"
                           :class="{ 'cursor-pointer': !customControls }"
                           :controls="!customControls"
                           preload="metadata"
                           playsinline
                           src="{{ $videoSrc }}"
                           x-intersect.once="
                               if (!showThumbnail) {
                                   $el.src = videoSrc;
                                   $nextTick(() => {
                                       initVideoPlayer($el, videoId, videoType);
                                       setupVideoTouchGestures($el, videoId);
                                   });
                               }
                           "
                           x-on:click="
                               if (customControls) {
                                   if ($el.paused) {
                                       $el.play().catch(e => console.log('Play failed:', e));
                                   } else {
                                       $el.pause();
                                   }
                               }
                           ">
                        <source :src="videoSrc" type="video/mp4">
                        <p class="text-white text-center p-4">Tu navegador no soporta la reproducción de video.</p>
                    </video>
                @endif

                <!-- Custom Controls Overlay (for HTML5) -->
                <div x-show="customControls && videoType === 'html5'" 
                     class="absolute inset-0 group"
                     x-on:mousemove="controlsVisible = true; clearTimeout(controlsTimer); controlsTimer = setTimeout(() => controlsVisible = false, 3000)"
                     x-data="{ controlsVisible: true, controlsTimer: null }">
                    
                    <!-- Controls Bar -->
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-black to-transparent p-4 transition-opacity duration-300"
                         :class="{ 'opacity-0': !controlsVisible && playing, 'opacity-100': controlsVisible || !playing }"
                        
                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="relative bg-white bg-opacity-30 rounded-full h-1 cursor-pointer group/progress"
                                 x-on:click="
                                    if ($refs.videoElement && duration) {
                                        const rect = $el.getBoundingClientRect();
                                        const clickX = $event.clientX - rect.left;
                                        const progress = clickX / rect.width;
                                        $refs.videoElement.currentTime = progress * duration;
                                    }
                                 ">
                                <!-- Buffer Bar -->
                                <div class="absolute top-0 left-0 h-full bg-white bg-opacity-50 rounded-full transition-all"
                                     :style="`width: ${currentVideo.buffered && currentVideo.duration ? (currentVideo.buffered / currentVideo.duration) * 100 : 0}%`"></div>
                                
                                <!-- Progress Bar -->
                                <div class="absolute top-0 left-0 h-full bg-blue-500 rounded-full transition-all"
                                     :style="`width: ${duration ? (currentTime / duration) * 100 : 0}%`"></div>
                                
                                <!-- Progress Handle -->
                                <div class="absolute top-1/2 -translate-y-1/2 w-3 h-3 bg-blue-500 rounded-full opacity-0 group-hover/progress:opacity-100 transition-opacity"
                                     :style="`left: calc(${duration ? (currentTime / duration) * 100 : 0}% - 6px)`"></div>
                            </div>
                        </div>

                        <!-- Control Buttons -->
                        <div class="flex items-center text-white">
                            <div class="flex items-center space-x-4 flex-1">
                                <!-- Play/Pause -->
                                <button x-on:click="
                                    if ($refs.videoElement.paused) {
                                        $refs.videoElement.play();
                                    } else {
                                        $refs.videoElement.pause();
                                    }
                                " 
                                        class="hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                                    <svg x-show="!playing" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                    <svg x-show="playing" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                                    </svg>
                                </button>

                                <!-- Volume -->
                                <div class="flex items-center space-x-2 group/volume">
                                    <button x-on:click="$refs.videoElement.muted = !$refs.videoElement.muted" 
                                            class="hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                                        <svg x-show="!muted" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                                        </svg>
                                        <svg x-show="muted" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
                                        </svg>
                                    </button>
                                    
                                    <!-- Volume Slider -->
                                    <input type="range" 
                                           min="0" 
                                           max="1" 
                                           step="0.1"
                                           :value="$refs.videoElement ? $refs.videoElement.volume : 1"
                                           x-on:input="$refs.videoElement.volume = $event.target.value"
                                           class="w-20 opacity-0 group-hover/volume:opacity-100 transition-opacity accent-blue-500">
                                </div>
                            </div>

                            <!-- Time Display (absolute centered) -->
                            <div class="absolute left-1/2 transform -translate-x-1/2">
                                <div class="text-sm font-mono whitespace-nowrap">
                                    <span x-text="Math.floor(currentTime / 60) + ':' + Math.floor(currentTime % 60).toString().padStart(2, '0')">0:00</span><span class="text-gray-400"> / </span><span x-text="duration ? Math.floor(duration / 60) + ':' + Math.floor(duration % 60).toString().padStart(2, '0') : '0:00'">0:00</span>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 flex-1 justify-end">
                                <!-- Picture-in-Picture -->
                                <button x-show="'pictureInPictureEnabled' in document" 
                                        x-on:click="togglePictureInPicture(videoId)"
                                        :class="{ 'bg-blue-600': currentVideo.pip }"
                                        class="hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 7h-8v6h8V7zm2-4H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14z"/>
                                    </svg>
                                </button>

                                <!-- Fullscreen -->
                                <button x-on:click="toggleFullscreen(videoId)"
                                        :class="{ 'bg-blue-600': currentVideo.fullscreen }"
                                        class="hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                                    <svg x-show="!currentVideo.fullscreen" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                                    </svg>
                                    <svg x-show="currentVideo.fullscreen" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Center Play Button (when paused) -->
                    <div x-show="!playing" 
                         class="absolute inset-0 flex items-center justify-center cursor-pointer"
                         x-on:click="$refs.videoElement.play().catch(e => console.log('Play failed:', e))">
                        <div class="bg-black bg-opacity-60 hover:bg-opacity-80 rounded-full p-4 transition-all hover:scale-110">
                            <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif
@endif