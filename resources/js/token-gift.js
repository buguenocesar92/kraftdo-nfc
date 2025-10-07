// Token Gift Professional JavaScript with Alpine.js
// Este archivo se carga antes de Alpine.start() para registrar componentes

// Función que registra el componente cuando Alpine esté disponible
function registerTokenGiftComponent() {
    if (typeof window.Alpine !== 'undefined') {
        window.Alpine.data('tokenGift', () => ({
        // Gallery modal state
        modalOpen: false,
        currentImage: {
            src: '',
            alt: '',
            caption: '',
            loading: true,
            error: false
        },
        galleryImages: [],
        currentImageIndex: 0,
        
        // Gallery advanced features
        isSlideshow: false,
        slideshowInterval: null,
        zoomLevel: 1,
        imagePosition: { x: 0, y: 0 },
        isDragging: false,
        dragStart: { x: 0, y: 0 },
        
        // Loading states
        imagesLoaded: new Set(),
        preloadedImages: new Set(),
        
        // Video player state
        videoPlayers: new Map(),
        currentVideo: {
            id: null,
            playing: false,
            muted: false,
            volume: 1,
            currentTime: 0,
            duration: 0,
            buffered: 0,
            quality: 'auto',
            playbackRate: 1,
            fullscreen: false,
            pip: false,
            loading: false,
            error: null
        },
        
        init() {
            this.setupKeyboardHandlers();
            this.setupTouchHandlers();
            this.setupIntersectionObserver();
            this.setupVideoHandlers();
            this.loadVideoPreferences();
            console.log('Token Gift Alpine component initialized');
        },
        
        // Gallery Modal Functions
        openImageModal(src, alt = '', images = [], index = 0, caption = '') {
            this.currentImage = {
                src: src,
                alt: alt,
                caption: caption,
                loading: !this.imagesLoaded.has(src), // Solo loading si no está cargada
                error: false
            };
            this.galleryImages = images;
            this.currentImageIndex = index;
            this.modalOpen = true;
            this.zoomLevel = 1;
            this.imagePosition = { x: 0, y: 0 };
            document.body.style.overflow = 'hidden';
            
            // Solo cargar si no está en cache
            if (!this.imagesLoaded.has(src)) {
                this.loadImage(src);
            }
            // Preload adjacent images
            this.preloadAdjacentImages();
        },
        
        nextImage() {
            if (this.galleryImages.length > 0) {
                this.currentImageIndex = (this.currentImageIndex + 1) % this.galleryImages.length;
                this.updateCurrentImage();
                this.preloadAdjacentImages();
            }
        },
        
        prevImage() {
            if (this.galleryImages.length > 0) {
                this.currentImageIndex = this.currentImageIndex === 0 ? this.galleryImages.length - 1 : this.currentImageIndex - 1;
                this.updateCurrentImage();
                this.preloadAdjacentImages();
            }
        },
        
        updateCurrentImage() {
            const img = this.galleryImages[this.currentImageIndex];
            const isLoaded = this.imagesLoaded.has(img.src);
            
            this.currentImage = {
                src: img.src,
                alt: img.alt,
                caption: img.caption || '',
                loading: !isLoaded,
                error: false
            };
            this.zoomLevel = 1;
            this.imagePosition = { x: 0, y: 0 };
            
            // Solo cargar si no está en cache
            if (!isLoaded) {
                this.loadImage(img.src);
            }
        },
        
        closeImageModal() {
            this.modalOpen = false;
            document.body.style.overflow = 'auto';
            this.stopSlideshow();
            this.zoomLevel = 1;
            this.imagePosition = { x: 0, y: 0 };
            this.currentImage = { src: '', alt: '', caption: '', loading: false, error: false };
        },
        
        // Debug method to check cache state
        debugImageCache() {
            console.log('Images loaded cache:', Array.from(this.imagesLoaded));
            console.log('Current image:', this.currentImage);
        },
        
        // Method to clear cache if needed
        clearImageCache() {
            this.imagesLoaded.clear();
            this.preloadedImages.clear();
            console.log('Image cache cleared');
        },
        
        handleModalBackdrop(event) {
            if (event.target === event.currentTarget) {
                this.closeImageModal();
            }
        },
        
        setupKeyboardHandlers() {
            document.addEventListener('keydown', (e) => {
                // Gallery shortcuts (when modal is open)
                if (this.modalOpen) {
                    switch(e.key) {
                        case 'Escape':
                            this.closeImageModal();
                            break;
                        case 'ArrowLeft':
                            this.prevImage();
                            break;
                        case 'ArrowRight':
                            this.nextImage();
                            break;
                    }
                    return;
                }
                
                // Video shortcuts (when video is active)
                if (this.currentVideo.id && !e.target.matches('input, textarea, select')) {
                    switch(e.key) {
                        case ' ':
                        case 'k':
                            e.preventDefault();
                            this.toggleVideoPlayback(this.currentVideo.id);
                            break;
                        case 'f':
                            e.preventDefault();
                            this.toggleFullscreen(this.currentVideo.id);
                            break;
                        case 'm':
                            e.preventDefault();
                            this.toggleMute(this.currentVideo.id);
                            break;
                        case 'ArrowLeft':
                            e.preventDefault();
                            this.seekTo(Math.max(0, this.currentVideo.currentTime - 10), this.currentVideo.id);
                            break;
                        case 'ArrowRight':
                            e.preventDefault();
                            this.seekTo(Math.min(this.currentVideo.duration, this.currentVideo.currentTime + 10), this.currentVideo.id);
                            break;
                        case 'ArrowUp':
                            e.preventDefault();
                            this.setVolume(Math.min(1, this.currentVideo.volume + 0.1), this.currentVideo.id);
                            break;
                        case 'ArrowDown':
                            e.preventDefault();
                            this.setVolume(Math.max(0, this.currentVideo.volume - 0.1), this.currentVideo.id);
                            break;
                        case ',':
                            // Previous frame (when paused)
                            if (!this.currentVideo.playing) {
                                e.preventDefault();
                                this.seekTo(Math.max(0, this.currentVideo.currentTime - 1/30), this.currentVideo.id);
                            }
                            break;
                        case '.':
                            // Next frame (when paused)
                            if (!this.currentVideo.playing) {
                                e.preventDefault();
                                this.seekTo(Math.min(this.currentVideo.duration, this.currentVideo.currentTime + 1/30), this.currentVideo.id);
                            }
                            break;
                        case '0':
                        case '1':
                        case '2':
                        case '3':
                        case '4':
                        case '5':
                        case '6':
                        case '7':
                        case '8':
                        case '9':
                            // Seek to percentage
                            e.preventDefault();
                            const percentage = parseInt(e.key) * 10;
                            this.seekTo((percentage / 100) * this.currentVideo.duration, this.currentVideo.id);
                            break;
                    }
                }
            });
        },
        
        setupTouchHandlers() {
            let startX = 0;
            let startY = 0;
            let initialDistance = 0;
            let initialZoom = 1;
            
            document.addEventListener('touchstart', (e) => {
                if (!this.modalOpen) return;
                
                if (e.touches.length === 1) {
                    startX = e.touches[0].clientX;
                    startY = e.touches[0].clientY;
                } else if (e.touches.length === 2) {
                    // Pinch to zoom
                    const touch1 = e.touches[0];
                    const touch2 = e.touches[1];
                    initialDistance = Math.hypot(touch2.clientX - touch1.clientX, touch2.clientY - touch1.clientY);
                    initialZoom = this.zoomLevel;
                }
            });
            
            document.addEventListener('touchmove', (e) => {
                if (!this.modalOpen) return;
                
                if (e.touches.length === 2) {
                    e.preventDefault();
                    const touch1 = e.touches[0];
                    const touch2 = e.touches[1];
                    const currentDistance = Math.hypot(touch2.clientX - touch1.clientX, touch2.clientY - touch1.clientY);
                    const scale = currentDistance / initialDistance;
                    this.zoomLevel = Math.max(0.5, Math.min(3, initialZoom * scale));
                }
            });
            
            document.addEventListener('touchend', (e) => {
                if (!this.modalOpen) return;
                
                if (e.changedTouches.length === 1 && e.touches.length === 0) {
                    const endX = e.changedTouches[0].clientX;
                    const endY = e.changedTouches[0].clientY;
                    const deltaX = endX - startX;
                    const deltaY = endY - startY;
                    
                    // Solo si el movimiento horizontal es mayor que el vertical y no hay zoom
                    if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50 && this.zoomLevel <= 1) {
                        if (deltaX > 0) {
                            this.prevImage(); // Swipe derecha = imagen anterior
                        } else {
                            this.nextImage(); // Swipe izquierda = imagen siguiente
                        }
                    }
                }
            });
        },
        
        // Image loading and preloading
        loadImage(src) {
            if (this.imagesLoaded.has(src)) {
                // Si ya está cargada, actualizar estado inmediatamente
                if (this.currentImage.src === src) {
                    this.currentImage.loading = false;
                    this.currentImage.error = false;
                }
                return Promise.resolve();
            }
            
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => {
                    this.imagesLoaded.add(src);
                    // Actualizar estado solo si es la imagen actual
                    if (this.currentImage.src === src) {
                        this.currentImage.loading = false;
                        this.currentImage.error = false;
                    }
                    resolve();
                };
                img.onerror = () => {
                    // Actualizar estado solo si es la imagen actual
                    if (this.currentImage.src === src) {
                        this.currentImage.loading = false;
                        this.currentImage.error = true;
                    }
                    reject();
                };
                img.src = src;
            });
        },
        
        preloadAdjacentImages() {
            if (this.galleryImages.length <= 1) return;
            
            const nextIndex = (this.currentImageIndex + 1) % this.galleryImages.length;
            const prevIndex = this.currentImageIndex === 0 ? this.galleryImages.length - 1 : this.currentImageIndex - 1;
            
            [this.galleryImages[nextIndex], this.galleryImages[prevIndex]].forEach(img => {
                if (!this.preloadedImages.has(img.src)) {
                    this.preloadedImages.add(img.src);
                    this.loadImage(img.src);
                }
            });
        },
        
        // Slideshow functionality
        toggleSlideshow() {
            if (this.isSlideshow) {
                this.stopSlideshow();
            } else {
                this.startSlideshow();
            }
        },
        
        startSlideshow() {
            if (this.galleryImages.length <= 1) return;
            this.isSlideshow = true;
            this.slideshowInterval = setInterval(() => {
                this.nextImage();
            }, 3000);
        },
        
        stopSlideshow() {
            this.isSlideshow = false;
            if (this.slideshowInterval) {
                clearInterval(this.slideshowInterval);
                this.slideshowInterval = null;
            }
        },
        
        // Zoom functionality
        zoomIn() {
            this.zoomLevel = Math.min(3, this.zoomLevel * 1.2);
        },
        
        zoomOut() {
            this.zoomLevel = Math.max(0.5, this.zoomLevel / 1.2);
            if (this.zoomLevel <= 1) {
                this.imagePosition = { x: 0, y: 0 };
            }
        },
        
        resetZoom() {
            this.zoomLevel = 1;
            this.imagePosition = { x: 0, y: 0 };
        },
        
        // Share functionality
        async shareImage() {
            if (!navigator.share || !this.currentImage.src) return false;
            
            try {
                await navigator.share({
                    title: this.currentImage.alt || 'Imagen de galería',
                    text: this.currentImage.caption || '',
                    url: this.currentImage.src
                });
                return true;
            } catch (err) {
                console.error('Error sharing:', err);
                return false;
            }
        },
        
        // Download functionality
        downloadImage() {
            if (!this.currentImage.src) return;
            
            const link = document.createElement('a');
            link.href = this.currentImage.src;
            link.download = this.currentImage.alt || 'imagen.jpg';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        
        // Lazy loading with Intersection Observer
        setupIntersectionObserver() {
            if (!window.IntersectionObserver) return;
            
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        const src = img.dataset.src;
                        if (src && !img.src) {
                            img.src = src;
                            img.classList.remove('opacity-0');
                            img.classList.add('opacity-100', 'transition-opacity', 'duration-300');
                            this.observer.unobserve(img);
                        }
                    }
                });
            }, { threshold: 0.1 });
        },
        
        observeImage(img) {
            if (this.observer && img) {
                this.observer.observe(img);
            }
        },
        
        // ========================
        // VIDEO PLAYER METHODS
        // ========================
        
        // Setup video event handlers
        setupVideoHandlers() {
            // Fullscreen change events
            document.addEventListener('fullscreenchange', () => {
                this.currentVideo.fullscreen = !!document.fullscreenElement;
            });
            
            // Picture-in-picture events
            document.addEventListener('enterpictureinpicture', () => {
                this.currentVideo.pip = true;
            });
            
            document.addEventListener('leavepictureinpicture', () => {
                this.currentVideo.pip = false;
            });
            
            // Visibility change for video optimization
            document.addEventListener('visibilitychange', () => {
                if (document.hidden && this.currentVideo.playing) {
                    // Optionally pause video when tab is hidden
                    this.pauseCurrentVideo();
                }
            });
        },
        
        // Load video preferences from localStorage
        loadVideoPreferences() {
            const savedPrefs = localStorage.getItem('tokenGift_videoPrefs');
            if (savedPrefs) {
                const prefs = JSON.parse(savedPrefs);
                this.currentVideo.volume = prefs.volume || 1;
                this.currentVideo.muted = prefs.muted || false;
                this.currentVideo.playbackRate = prefs.playbackRate || 1;
                this.currentVideo.quality = prefs.quality || 'auto';
            }
        },
        
        // Save video preferences
        saveVideoPreferences() {
            const prefs = {
                volume: this.currentVideo.volume,
                muted: this.currentVideo.muted,
                playbackRate: this.currentVideo.playbackRate,
                quality: this.currentVideo.quality
            };
            localStorage.setItem('tokenGift_videoPrefs', JSON.stringify(prefs));
        },
        
        // Initialize video player
        initVideoPlayer(videoElement, videoId, videoType) {
            if (!videoElement) return;
            
            this.currentVideo.id = videoId;
            this.currentVideo.loading = false;
            this.currentVideo.error = null;
            
            // Store video element reference
            this.videoPlayers.set(videoId, {
                element: videoElement,
                type: videoType,
                initialized: true
            });
            
            // Apply saved preferences for HTML5 videos
            if (videoType === 'html5') {
                videoElement.volume = this.currentVideo.volume;
                videoElement.muted = this.currentVideo.muted;
                videoElement.playbackRate = this.currentVideo.playbackRate;
                
                // Add event listeners
                this.addVideoEventListeners(videoElement, videoId);
            }
            
            console.log(`Video player initialized: ${videoId} (${videoType})`);
        },
        
        // Add event listeners to HTML5 video
        addVideoEventListeners(video, videoId) {
            // Basic playback events
            video.addEventListener('loadstart', () => {
                this.currentVideo.loading = true;
                this.currentVideo.error = null;
            });
            
            video.addEventListener('loadedmetadata', () => {
                this.currentVideo.duration = video.duration;
                this.currentVideo.loading = false;
            });
            
            video.addEventListener('loadeddata', () => {
                this.currentVideo.loading = false;
            });
            
            video.addEventListener('canplay', () => {
                this.currentVideo.loading = false;
            });
            
            video.addEventListener('play', () => {
                this.currentVideo.playing = true;
                this.trackVideoEvent('play', videoId);
            });
            
            video.addEventListener('pause', () => {
                this.currentVideo.playing = false;
                this.trackVideoEvent('pause', videoId);
            });
            
            video.addEventListener('ended', () => {
                this.currentVideo.playing = false;
                this.trackVideoEvent('ended', videoId);
            });
            
            video.addEventListener('timeupdate', () => {
                this.currentVideo.currentTime = video.currentTime;
                this.trackVideoProgress(video.currentTime, video.duration, videoId);
            });
            
            video.addEventListener('volumechange', () => {
                this.currentVideo.volume = video.volume;
                this.currentVideo.muted = video.muted;
                this.saveVideoPreferences();
            });
            
            video.addEventListener('ratechange', () => {
                this.currentVideo.playbackRate = video.playbackRate;
                this.saveVideoPreferences();
            });
            
            video.addEventListener('progress', () => {
                if (video.buffered.length > 0) {
                    this.currentVideo.buffered = video.buffered.end(video.buffered.length - 1);
                }
            });
            
            video.addEventListener('error', (e) => {
                this.handleVideoError(e, videoId);
            });
            
            video.addEventListener('waiting', () => {
                this.currentVideo.loading = true;
            });
            
            video.addEventListener('playing', () => {
                this.currentVideo.loading = false;
            });
        },
        
        // Handle video errors
        handleVideoError(error, videoId) {
            console.error(`Video error for ${videoId}:`, error);
            
            const video = this.videoPlayers.get(videoId)?.element;
            let errorMessage = 'Error al reproducir el video';
            
            if (video && video.error) {
                switch (video.error.code) {
                    case 1:
                        errorMessage = 'Reproducción abortada por el usuario';
                        break;
                    case 2:
                        errorMessage = 'Error de red al cargar el video';
                        break;
                    case 3:
                        errorMessage = 'Error al decodificar el video';
                        break;
                    case 4:
                        errorMessage = 'Formato de video no soportado';
                        break;
                    default:
                        errorMessage = 'Error desconocido al reproducir el video';
                }
            }
            
            this.currentVideo.error = errorMessage;
            this.currentVideo.loading = false;
            this.currentVideo.playing = false;
            
            this.trackVideoEvent('error', videoId, { error: errorMessage });
        },
        
        // Play/Pause toggle
        toggleVideoPlayback(videoId) {
            const playerData = this.videoPlayers.get(videoId);
            if (!playerData) return;
            
            if (playerData.type === 'html5') {
                const video = playerData.element;
                if (this.currentVideo.playing) {
                    video.pause();
                } else {
                    this.playWithFallback(video, videoId);
                }
            } else if (playerData.type === 'youtube') {
                this.toggleYouTubeVideo(videoId);
            } else if (playerData.type === 'vimeo') {
                this.toggleVimeoVideo(videoId);
            }
        },
        
        // Play with autoplay policy handling
        async playWithFallback(video, videoId) {
            try {
                await video.play();
            } catch (error) {
                console.warn('Autoplay prevented:', error);
                
                if (error.name === 'NotAllowedError') {
                    // Autoplay was prevented, show user-friendly message
                    this.currentVideo.error = 'Haz clic en el video para reproducir';
                    
                    // Try to play muted
                    video.muted = true;
                    this.currentVideo.muted = true;
                    
                    try {
                        await video.play();
                        this.currentVideo.error = null;
                    } catch (mutedError) {
                        console.error('Even muted playback failed:', mutedError);
                    }
                }
            }
        },
        
        // Pause current video
        pauseCurrentVideo() {
            if (this.currentVideo.id) {
                const playerData = this.videoPlayers.get(this.currentVideo.id);
                if (playerData && playerData.type === 'html5') {
                    playerData.element.pause();
                }
            }
        },
        
        // Seek to specific time
        seekTo(time, videoId) {
            const playerData = this.videoPlayers.get(videoId);
            if (!playerData) return;
            
            if (playerData.type === 'html5') {
                playerData.element.currentTime = time;
            }
            // TODO: Implement for YouTube and Vimeo
        },
        
        // Set volume
        setVolume(volume, videoId) {
            const playerData = this.videoPlayers.get(videoId);
            if (!playerData) return;
            
            volume = Math.max(0, Math.min(1, volume));
            
            if (playerData.type === 'html5') {
                playerData.element.volume = volume;
                if (volume > 0) {
                    playerData.element.muted = false;
                }
            }
            
            this.currentVideo.volume = volume;
            this.currentVideo.muted = volume === 0;
            this.saveVideoPreferences();
        },
        
        // Toggle mute
        toggleMute(videoId) {
            const playerData = this.videoPlayers.get(videoId);
            if (!playerData) return;
            
            if (playerData.type === 'html5') {
                playerData.element.muted = !playerData.element.muted;
                this.currentVideo.muted = playerData.element.muted;
            }
            
            this.saveVideoPreferences();
        },
        
        // Set playback rate
        setPlaybackRate(rate, videoId) {
            const playerData = this.videoPlayers.get(videoId);
            if (!playerData) return;
            
            if (playerData.type === 'html5') {
                playerData.element.playbackRate = rate;
            }
            
            this.currentVideo.playbackRate = rate;
            this.saveVideoPreferences();
        },
        
        // Toggle fullscreen
        async toggleFullscreen(videoId) {
            const playerData = this.videoPlayers.get(videoId);
            if (!playerData) return;
            
            try {
                if (!document.fullscreenElement) {
                    await playerData.element.requestFullscreen();
                } else {
                    await document.exitFullscreen();
                }
            } catch (error) {
                console.error('Fullscreen error:', error);
            }
        },
        
        // Toggle picture-in-picture
        async togglePictureInPicture(videoId) {
            const playerData = this.videoPlayers.get(videoId);
            if (!playerData || playerData.type !== 'html5') return;
            
            try {
                if (document.pictureInPictureElement) {
                    await document.exitPictureInPicture();
                } else {
                    await playerData.element.requestPictureInPicture();
                }
            } catch (error) {
                console.error('Picture-in-picture error:', error);
            }
        },
        
        // Format time display
        formatTime(seconds) {
            if (isNaN(seconds)) return '0:00';
            
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = Math.floor(seconds % 60);
            
            if (hours > 0) {
                return `${hours}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            } else {
                return `${minutes}:${secs.toString().padStart(2, '0')}`;
            }
        },
        
        // Track video events for analytics
        trackVideoEvent(event, videoId, data = {}) {
            const eventData = {
                event: `video_${event}`,
                video_id: videoId,
                timestamp: Date.now(),
                ...data
            };
            
            console.log('Video event:', eventData);
            
            // Here you could send to analytics service
            // Example: gtag('event', event, eventData);
        },
        
        // Track video progress milestones
        trackVideoProgress(currentTime, duration, videoId) {
            if (!duration || duration === 0) return;
            
            const progress = (currentTime / duration) * 100;
            const milestones = [25, 50, 75, 100];
            
            milestones.forEach(milestone => {
                const key = `${videoId}_${milestone}`;
                if (progress >= milestone && !this.videoMilestones?.has(key)) {
                    if (!this.videoMilestones) {
                        this.videoMilestones = new Set();
                    }
                    this.videoMilestones.add(key);
                    this.trackVideoEvent('progress', videoId, { 
                        progress: milestone,
                        currentTime,
                        duration 
                    });
                }
            });
        },
        
        // YouTube player specific methods
        toggleYouTubeVideo(videoId) {
            // Implementation for YouTube iframe API
            const iframe = document.querySelector(`iframe[data-video-id="${videoId}"]`);
            if (iframe) {
                iframe.contentWindow.postMessage(
                    '{"event":"command","func":"' + (this.currentVideo.playing ? 'pauseVideo' : 'playVideo') + '","args":""}',
                    '*'
                );
            }
        },
        
        // Vimeo player specific methods
        toggleVimeoVideo(videoId) {
            // Implementation for Vimeo iframe API
            const iframe = document.querySelector(`iframe[data-video-id="${videoId}"]`);
            if (iframe) {
                iframe.contentWindow.postMessage(
                    '{"method":"' + (this.currentVideo.playing ? 'pause' : 'play') + '"}',
                    '*'
                );
            }
        },
        
        // Lazy load video iframe
        lazyLoadVideo(element, src, videoId, videoType) {
            // Create intersection observer for this specific video
            const videoObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Load the video
                        if (videoType === 'iframe') {
                            element.src = src;
                        } else {
                            element.load();
                        }
                        
                        // Initialize player after loading
                        setTimeout(() => {
                            this.initVideoPlayer(element, videoId, videoType);
                        }, 100);
                        
                        // Stop observing
                        videoObserver.unobserve(element);
                    }
                });
            }, { threshold: 0.1 });
            
            videoObserver.observe(element);
        },
        
        // Touch gestures for video
        setupVideoTouchGestures(videoElement, videoId) {
            let touchStartTime = 0;
            let touchStartX = 0;
            let touchStartY = 0;
            let seeking = false;
            
            videoElement.addEventListener('touchstart', (e) => {
                touchStartTime = Date.now();
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
                seeking = false;
            }, { passive: true });
            
            videoElement.addEventListener('touchmove', (e) => {
                if (e.touches.length !== 1) return;
                
                const deltaX = e.touches[0].clientX - touchStartX;
                const deltaY = e.touches[0].clientY - touchStartY;
                
                // Only prevent default if we're actually performing gestures
                const isHorizontalGesture = Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 30;
                const isVerticalGesture = Math.abs(deltaY) > Math.abs(deltaX) && Math.abs(deltaY) > 30;
                
                if (isHorizontalGesture || isVerticalGesture) {
                    e.preventDefault(); // Only prevent default when needed
                }
                
                // Horizontal swipe for seeking
                if (isHorizontalGesture) {
                    seeking = true;
                    const seekAmount = (deltaX / videoElement.clientWidth) * this.currentVideo.duration;
                    const newTime = Math.max(0, Math.min(this.currentVideo.duration, this.currentVideo.currentTime + seekAmount));
                    this.seekTo(newTime, videoId);
                }
                
                // Vertical swipe for volume
                if (isVerticalGesture) {
                    const volumeChange = -(deltaY / videoElement.clientHeight);
                    const newVolume = Math.max(0, Math.min(1, this.currentVideo.volume + volumeChange));
                    this.setVolume(newVolume, videoId);
                }
            }, { passive: false }); // We need non-passive here for preventDefault
            
            videoElement.addEventListener('touchend', (e) => {
                const touchDuration = Date.now() - touchStartTime;
                
                // Single tap to play/pause (if not seeking)
                if (touchDuration < 300 && !seeking) {
                    this.toggleVideoPlayback(videoId);
                }
                
                // Double tap for fullscreen
                if (touchDuration < 300 && e.detail === 2) {
                    this.toggleFullscreen(videoId);
                }
            }, { passive: true });
        }
        }));

        // Voice Selector Component
        window.Alpine.data('voiceSelector', () => ({
            showVoiceSelector: false,
            availableVoices: [],
            selectedVoice: null,

            initVoiceSelector() {
                this.loadVoices();
                speechSynthesis.addEventListener('voiceschanged', () => this.loadVoices());
            },

            loadVoices() {
                // Filter to show only Spanish (Spain) voices
                this.availableVoices = speechSynthesis.getVoices().filter(voice => 
                    voice.lang.startsWith('es-ES')
                );
                
                // If no es-ES voices found, fallback to any Spanish voice
                if (this.availableVoices.length === 0) {
                    console.log('No es-ES voices found, falling back to any Spanish voice');
                    this.availableVoices = speechSynthesis.getVoices().filter(voice => 
                        voice.lang.startsWith('es')
                    );
                }
                
                if (this.availableVoices.length > 0 && !this.selectedVoice) {
                    // Prioritize es-ES voices, prefer remote (cloud) voices for better quality
                    this.selectedVoice = this.availableVoices.find(v => v.lang === 'es-ES' && !v.localService) ||
                                       this.availableVoices.find(v => v.lang === 'es-ES') ||
                                       this.availableVoices[0];
                    window.selectedTTSVoice = this.selectedVoice;
                    console.log('Default Spanish voice selected:', this.selectedVoice.name, this.selectedVoice.lang, this.selectedVoice.localService ? '(Local)' : '(Remote)');
                } else if (this.availableVoices.length === 0) {
                    console.warn('No Spanish voices available in this browser');
                }
            },

            selectVoice(voice) {
                this.selectedVoice = voice;
                window.selectedTTSVoice = voice;
                console.log('Selected voice:', voice.name);
            },

            testVoice() {
                if (this.selectedVoice) {
                    const testText = 'Hola, esta es una prueba de voz.';
                    const utterance = new SpeechSynthesisUtterance(testText);
                    utterance.voice = this.selectedVoice;
                    utterance.lang = this.selectedVoice.lang;
                    utterance.rate = 0.9;
                    speechSynthesis.speak(utterance);
                }
            },

            playMessage() {
                if (window.readMessageAloud) {
                    window.readMessageAloud();
                }
            }
        }));

        // Autoplay Overlay Component
        window.Alpine.data('autoplayOverlay', (hasAudio, hasVideo) => ({
            showAutoplayOverlay: true,
            hasAudio: hasAudio,
            hasVideo: hasVideo,

            initOverlay() {
                console.log('=== OVERLAY COMPONENT INIT ===');
                console.log('Alpine component initializing with:', { hasAudio: this.hasAudio, hasVideo: this.hasVideo });
                console.log('DOM ready state:', document.readyState);
                console.log('Page load timing:', performance.now(), 'ms since page start');
                
                // Debug: Check actual elements in DOM
                const videoEl = document.querySelector('video[x-ref="videoElement"]');
                const iframeEl = document.querySelector('iframe[x-ref="iframeElement"], iframe[src*="youtube.com"], iframe[src*="vimeo.com"]');
                const audioEl = document.querySelector('audio[x-ref="audioElement"]');
                console.log('DOM Media Elements:', {
                    videoElement: !!videoEl,
                    iframeElement: !!iframeEl,
                    audioElement: !!audioEl,
                    calculatedHasVideo: !!(videoEl || iframeEl),
                    passedHasVideo: this.hasVideo,
                    passedHasAudio: this.hasAudio
                });
                
                // Always show overlay when there's video, regardless of audio
                this.showAutoplayOverlay = this.hasVideo;
                console.log('Overlay visibility set to:', this.showAutoplayOverlay);
                
                // Add a small delay to ensure DOM is fully ready
                setTimeout(() => {
                    console.log('Overlay component fully initialized');
                    console.log('Button element exists:', !!document.querySelector('[x-on\\:click="activateAutoplay()"]'));
                }, 100);
            },

            getTitle() {
                if (this.hasVideo && this.hasAudio) {
                    return '¡Ver Video Especial!';
                }
                return this.hasVideo ? '¡Activa tu Video!' : '¡Escucha tu Mensaje!';
            },

            getDescription() {
                if (this.hasVideo && this.hasAudio) {
                    return 'Disfruta del video especial que han preparado para ti. El audio estará disponible por separado.';
                }
                return this.hasVideo ? 
                    'Para brindarte la mejor experiencia, necesitamos activar la reproducción automática del video.' : 
                    'Te leeremos el mensaje personalizado en voz alta para una experiencia única.';
            },

            getButtonText() {
                if (this.hasVideo && this.hasAudio) {
                    return 'Ver Video Especial';
                }
                return this.hasVideo ? 'Activar Video' : 'Escuchar Mensaje';
            },

            activateAutoplay() {
                console.log('=== OVERLAY BUTTON CLICKED ===');
                console.log('Current state:', { hasVideo: this.hasVideo, hasAudio: this.hasAudio });
                console.log('Hiding overlay...');
                this.showAutoplayOverlay = false;
                
                // If we have both video and audio, prioritize VIDEO playback (not audio)
                if (this.hasVideo && this.hasAudio) {
                    console.log('🎬 BRANCH: Has both video and audio, going directly to video (skipping audio autoplay)...');
                    // Skip audio autoplay when video is present - let user decide
                    if (window.enableAutoplay) {
                        console.log('🎬 Calling window.enableAutoplay() for video priority...');
                        window.enableAutoplay();
                    }
                } else if (this.hasVideo) {
                    console.log('🎬 BRANCH: Has video only, calling enableAutoplay...');
                    if (window.enableAutoplay) {
                        window.enableAutoplay();
                    }
                } else if (this.hasAudio) {
                    console.log('🎵 BRANCH: Has audio only, calling enableAutoplay...');
                    if (window.enableAutoplay) {
                        window.enableAutoplay();
                    }
                } else {
                    console.log('📝 BRANCH: No media, calling enableAutoplay for TTS...');
                    if (window.enableAutoplay) {
                        window.enableAutoplay();
                    }
                }
                
                if (!window.enableAutoplay) {
                    console.error('❌ window.enableAutoplay not found!');
                }
            }
        }));
    } else {
        console.warn('Alpine.js no está disponible aún');
    }
}

// TTS Functions - moved from inline JS
window.enableAutoplay = function(skipDelay = false, retryCount = 0) {
    console.log('=== ENABLE AUTOPLAY CALLED ===');
    console.log('Call stack:', new Error().stack);
    console.log('Starting media playback...', { skipDelay, retryCount });
    console.log('Function called at:', performance.now(), 'ms since page start');
    console.log('Document ready state:', document.readyState);
    
    // Try to find and start video first (HTML5, YouTube, or Vimeo)
    let video = document.querySelector('video[x-ref="videoElement"]');
    let iframe = document.querySelector('iframe[x-ref="iframeElement"], iframe[src*="youtube.com"], iframe[src*="vimeo.com"]');
    let hasVideo = video || iframe;
    
    console.log('Video search results:', { 
        html5Video: !!video, 
        iframe: !!iframe, 
        hasAnyVideo: hasVideo 
    });
    
    // If no video found and we haven't exceeded max retries, wait for Alpine.js to render
    if (!hasVideo && retryCount < 5) {
        console.log(`No video elements found (attempt ${retryCount + 1}/5), waiting for Alpine.js...`);
        setTimeout(() => {
            window.enableAutoplay(true, retryCount + 1); // Recursive call with retry count
        }, skipDelay ? 50 : 200); // Shorter delay on retries
        return;
    }
    
    // If we found any kind of video (HTML5, YouTube, Vimeo), prioritize video
    if (hasVideo) {
        if (video) {
            console.log('Found HTML5 video, starting HTML5 video playback');
            // Handle HTML5 video (existing logic below)
        } else if (iframe) {
            console.log('Found iframe video (YouTube/Vimeo), attempting autoplay after user interaction...');
            // Scroll to the iframe video
            iframe.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            
            // Try to autoplay iframe video since user interacted with overlay
            setTimeout(() => {
                console.log('Attempting to autoplay iframe video...');
                const currentSrc = iframe.src;
                const videoId = iframe.getAttribute('data-video-id');
                
                console.log('Iframe details:', { src: currentSrc, dataVideoId: videoId });
                
                // Handle lazy-loaded YouTube iframe without src
                if (!currentSrc && videoId) {
                    console.log('Lazy-loaded YouTube iframe detected, using YouTube Player API...');
                    
                    // Load YouTube API if not already loaded
                    if (!window.YT) {
                        console.log('Loading YouTube API...');
                        const script = document.createElement('script');
                        script.src = 'https://www.youtube.com/iframe_api';
                        document.head.appendChild(script);
                        
                        // Wait for API to load then create player
                        window.onYouTubeIframeAPIReady = () => {
                            console.log('YouTube API loaded, creating player...');
                            createYouTubePlayer(iframe, videoId);
                        };
                    } else {
                        createYouTubePlayer(iframe, videoId);
                    }
                    return;
                }
                
                if (currentSrc.includes('youtube.com')) {
                    console.log('YouTube iframe with existing src detected...');
                    // Extract video ID from existing URL
                    const urlVideoId = currentSrc.match(/embed\/([^?&]+)/)?.[1];
                    if (urlVideoId) {
                        console.log('Extracted video ID from URL:', urlVideoId);
                        // Replace iframe with YouTube Player API
                        createYouTubePlayer(iframe, urlVideoId);
                    } else {
                        // Fallback to URL parameter method
                        console.log('Could not extract video ID, using URL parameters...');
                        if (!currentSrc.includes('autoplay=1')) {
                            const newSrc = currentSrc.includes('?') 
                                ? currentSrc + '&autoplay=1&mute=1&controls=1'
                                : currentSrc + '?autoplay=1&mute=1&controls=1';
                            console.log('Setting new YouTube src with autoplay:', newSrc);
                            iframe.src = newSrc;
                        }
                    }
                } else if (currentSrc.includes('vimeo.com')) {
                    console.log('Vimeo iframe detected, adding autoplay parameter...');
                    // Add autoplay parameter to Vimeo URL
                    if (!currentSrc.includes('autoplay=1')) {
                        const newSrc = currentSrc.includes('?') 
                            ? currentSrc + '&autoplay=1'
                            : currentSrc + '?autoplay=1';
                        console.log('Setting new Vimeo src with autoplay:', newSrc);
                        iframe.src = newSrc;
                    }
                } else {
                    console.log('Unknown iframe video type, using postMessage API...');
                    // Try using postMessage API for iframe communication
                    try {
                        iframe.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
                    } catch (e) {
                        console.log('PostMessage failed:', e);
                    }
                }
            }, 500); // Small delay to ensure scroll completes
            
            console.log('Video iframe autoplay initiated');
            return; // Exit early - iframe videos handle their own playback
        }
    }
    
    if (video) {
        console.log('Found video, starting video playback');
        console.log('Video readyState:', video.readyState);
        console.log('Video src:', video.src);
        console.log('Video currentSrc:', video.currentSrc);
        
        // Simplified play attempt - just try to play immediately
        const attemptPlay = () => {
            console.log('Attempting to play video immediately...');
            console.log('Video state:', {
                readyState: video.readyState,
                paused: video.paused,
                src: video.src,
                currentSrc: video.currentSrc
            });
            
            // Set video properties for better playback
            video.muted = false; // Can unmute since user interacted
            video.controls = true; // Show controls
            
            // Just try to play - let the browser handle loading
            video.play()
                .then(() => {
                    console.log('✅ Video.play() promise resolved');
                    console.log('Video actual state after play():', {
                        paused: video.paused,
                        currentTime: video.currentTime,
                        duration: video.duration,
                        readyState: video.readyState,
                        networkState: video.networkState,
                        ended: video.ended,
                        seeking: video.seeking
                    });
                    
                    // Double check if video is actually playing
                    setTimeout(() => {
                        console.log('Video state 500ms after play():', {
                            paused: video.paused,
                            currentTime: video.currentTime,
                            playing: !video.paused && !video.ended && video.readyState > 2
                        });
                        
                        if (video.paused) {
                            console.error('🚨 VIDEO IS STILL PAUSED despite promise resolving!');
                            console.log('Attempting force play again...');
                            video.play().catch(e => console.error('Force play failed:', e));
                        }
                    }, 500);
                    
                    // Scroll to center the video in the viewport
                    scrollToVideo(video);
                })
                .catch(e => {
                    console.log('Video play failed, trying muted:', e.name);
                    // Fallback: try with muted
                    video.muted = true;
                    return video.play();
                })
                .then(() => {
                    console.log('Video playing (possibly muted)');
                    console.log('Muted video state:', {
                        paused: video.paused,
                        muted: video.muted,
                        currentTime: video.currentTime
                    });
                    scrollToVideo(video);
                })
                .catch(err => {
                    console.error('All playback attempts failed:', err);
                    // Still scroll to show the video
                    scrollToVideo(video);
                });
        };

        // Add temporary event listeners to debug what's happening
        const debugEvents = ['play', 'pause', 'playing', 'waiting', 'stalled', 'suspend', 'error', 'canplay', 'canplaythrough'];
        debugEvents.forEach(eventName => {
            const listener = () => {
                console.log(`🎬 VIDEO EVENT: ${eventName}`, {
                    currentTime: video.currentTime,
                    paused: video.paused,
                    readyState: video.readyState,
                    networkState: video.networkState
                });
            };
            video.addEventListener(eventName, listener, { once: true });
        });
        
        // Start the attempt process
        attemptPlay();
        return; // Exit early if video found
    }
    
    // Only if no video at all (HTML5 or iframe), try audio fallback
    if (!hasVideo) {
        console.log('No video found after retries, checking for audio...');
        const audio = document.querySelector('audio[x-ref="audioElement"]');
        if (audio) {
            window.enableAutoplayAudio(audio);
            return; // Exit early if audio found
        }
        
        // If no video/audio found, use text-to-speech for the message
        console.log('No media elements found, starting text-to-speech');
        window.readMessageAloud();
    }
}

// YouTube Player API integration for reliable autoplay
function createYouTubePlayer(iframe, videoId) {
    console.log('Creating YouTube Player with API for video:', videoId);
    
    // Create a unique ID for the iframe if it doesn't have one
    const playerId = iframe.id || `youtube-player-${Date.now()}`;
    iframe.id = playerId;
    
    // Clear the iframe src to avoid conflicts
    iframe.src = '';
    
    try {
        const player = new YT.Player(playerId, {
            videoId: videoId,
            width: iframe.width || '100%',
            height: iframe.height || '315',
            playerVars: {
                autoplay: 1,
                mute: 1, // Start muted for reliable autoplay
                controls: 1,
                rel: 0,
                modestbranding: 1,
                enablejsapi: 1
            },
            events: {
                onReady: (event) => {
                    console.log('YouTube player ready, starting playback...');
                    event.target.playVideo();
                    
                    // Show message that video started muted
                    setTimeout(() => {
                        const messageEl = document.createElement('div');
                        messageEl.innerHTML = `
                            <div class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-75 text-white px-4 py-2 rounded-lg text-sm z-50 animate-fade-in">
                                🔇 Video iniciado sin sonido - haz clic en el altavoz para activar audio
                            </div>
                        `;
                        document.body.appendChild(messageEl);
                        setTimeout(() => messageEl.remove(), 4000);
                    }, 500);
                },
                onStateChange: (event) => {
                    console.log('YouTube player state change:', event.data);
                    if (event.data === YT.PlayerState.PLAYING) {
                        console.log('✅ YouTube video is now playing!');
                    } else if (event.data === YT.PlayerState.PAUSED) {
                        console.log('YouTube video paused');
                    } else if (event.data === YT.PlayerState.ENDED) {
                        console.log('YouTube video ended');
                    }
                },
                onError: (event) => {
                    console.error('YouTube player error:', event.data);
                    // Fallback to iframe method if API fails
                    console.log('Falling back to iframe method...');
                    iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&controls=1&rel=0`;
                }
            }
        });
        
        // Store player reference for later use
        window.youtubePlayer = player;
        
    } catch (error) {
        console.error('Failed to create YouTube Player:', error);
        // Fallback to iframe method
        console.log('Falling back to iframe method...');
        iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&controls=1&rel=0`;
    }
}

// Separate function for audio playback
window.enableAutoplayAudio = function(audio) {
    console.log('Found audio, starting audio playback');
    console.log('Audio readyState:', audio.readyState);
    console.log('Audio src:', audio.src);
    
    // Wait for audio to be ready before playing
    const attemptPlayAudio = (retryCount = 0) => {
        if (retryCount > 10) {
            console.error('Max retries reached for audio, giving up');
            return;
        }

        // Check if audio is ready
        if (audio.readyState < 3) { // HAVE_FUTURE_DATA
            console.log(`Audio not ready yet (readyState: ${audio.readyState}), waiting... (attempt ${retryCount + 1})`);
            setTimeout(() => attemptPlayAudio(retryCount + 1), 200);
            return;
        }

        console.log('Audio is ready, attempting to play...');
        
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
                    // Try one more time after a delay
                    if (retryCount < 3) {
                        console.log('Retrying audio playback after delay...');
                        setTimeout(() => attemptPlayAudio(retryCount + 1), 500);
                    } else {
                        console.log('Final audio fallback failed');
                    }
                });
            });
    };

    // Start the attempt process for audio
    attemptPlayAudio();
}

// Text-to-Speech functionality
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
        
        // Use selected voice if available, defaulting to es-ES
        if (window.selectedTTSVoice) {
            utterance.voice = window.selectedTTSVoice;
            utterance.lang = window.selectedTTSVoice.lang;
            console.log('Using selected voice:', window.selectedTTSVoice.name, window.selectedTTSVoice.lang);
        } else {
            // Default to Spanish (Spain) if no voice is selected
            utterance.lang = 'es-ES';
            console.log('Using default language: es-ES');
        }
        
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

// Global click event listener to debug button clicks
document.addEventListener('click', function(event) {
    if (event.target.closest('button')) {
        const button = event.target.closest('button');
        const buttonText = button.textContent.trim();
        
        // Only log clicks on relevant buttons
        if (buttonText.includes('Activar') || buttonText.includes('Video') || buttonText.includes('Multimedia')) {
            console.log('=== GLOBAL BUTTON CLICK DETECTED ===');
            console.log('Button text:', buttonText);
            console.log('Button HTML:', button.outerHTML);
            console.log('Alpine x-on:click attribute:', button.getAttribute('x-on:click'));
            console.log('Alpine data context:', button.__x ? !!button.__x : 'No Alpine context');
            console.log('Time:', performance.now(), 'ms since page start');
        }
    }
}, true); // Use capture phase to catch all clicks

// Scroll Reveal Animation System
class ScrollReveal {
    constructor() {
        this.elements = [];
        this.observer = null;
        this.init();
    }

    init() {
        // Create Intersection Observer for scroll reveals
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.revealElement(entry.target);
                    this.observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        // Find and observe all scroll reveal elements
        this.observeElements();

        // Setup parallax scroll effects
        this.setupParallax();
    }

    observeElements() {
        const selectors = [
            '.scroll-reveal',
            '.scroll-reveal-left',
            '.scroll-reveal-right',
            '.scroll-reveal-scale'
        ];

        selectors.forEach(selector => {
            document.querySelectorAll(selector).forEach(el => {
                this.observer.observe(el);
            });
        });
    }

    revealElement(element) {
        element.classList.add('revealed');
        
        // Add confetti for special elements
        if (element.classList.contains('special-reveal')) {
            setTimeout(() => createConfetti(), 200);
        }
    }

    setupParallax() {
        let ticking = false;
        
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    this.updateParallax();
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    updateParallax() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.parallax-element');
        
        parallaxElements.forEach((element, index) => {
            const rate = scrolled * -0.5;
            element.style.transform = `translateY(${rate}px)`;
        });
    }
}

// Initialize scroll reveal when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new ScrollReveal();
    
    // Add smooth scrolling behavior
    document.documentElement.style.scrollBehavior = 'smooth';
    
    // Initialize lazy loading for images
    initLazyLoading();
    
    // Add intersection observer for animations
    setupIntersectionAnimations();
});

// Lazy loading system
function initLazyLoading() {
    const lazyImages = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                
                // Validate data-src before loading
                if (!img.dataset.src || img.dataset.src === 'undefined' || img.dataset.src.includes('undefined')) {
                    console.warn('Invalid image source detected in token-gift.js:', img.dataset.src);
                    imageObserver.unobserve(img);
                    return;
                }
                
                img.src = img.dataset.src;
                img.classList.remove('opacity-0');
                img.classList.add('opacity-100');
                imageObserver.unobserve(img);
            }
        });
    });
    
    lazyImages.forEach(img => imageObserver.observe(img));
}

// Enhanced intersection animations
function setupIntersectionAnimations() {
    const animatedElements = document.querySelectorAll('[class*="animate-"]');
    
    const animationObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                
                // Add special effects for certain elements
                if (element.classList.contains('special-effect')) {
                    setTimeout(() => {
                        element.style.filter = 'drop-shadow(0 0 20px rgba(139, 92, 246, 0.5))';
                    }, 300);
                }
                
                animationObserver.unobserve(element);
            }
        });
    }, { threshold: 0.2 });
    
    animatedElements.forEach(el => animationObserver.observe(el));
}

// Intentar registrar inmediatamente o esperar a que Alpine esté disponible
console.log('=== ALPINE COMPONENT REGISTRATION ===');
console.log('Page load time:', performance.now(), 'ms');
console.log('Alpine available:', typeof window.Alpine !== 'undefined');
console.log('Document ready state:', document.readyState);

if (typeof window.Alpine !== 'undefined') {
    console.log('Alpine is available, registering components immediately...');
    registerTokenGiftComponent();
} else {
    console.log('Alpine not available, waiting for alpine:init event...');
    // Esperar a que Alpine esté disponible
    document.addEventListener('alpine:init', () => {
        console.log('=== ALPINE:INIT EVENT FIRED ===');
        console.log('Time:', performance.now(), 'ms');
        registerTokenGiftComponent();
    });
}

// Enhanced Gift Action Functions
window.shareGift = function() {
    const giftData = {
        title: '🎁 ¡Mira este regalo especial!',
        text: 'Alguien especial te ha enviado un regalo personalizado con KRAFTDO NFC',
        url: window.location.href
    };
    
    if (navigator.share) {
        navigator.share(giftData)
            .then(() => {
                showNotification('¡Regalo compartido exitosamente! 🎉', 'success');
                // Add confetti effect
                createConfetti();
            })
            .catch(err => console.error('Error sharing:', err));
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href)
            .then(() => {
                showNotification('¡Enlace copiado al portapapeles! 📋', 'success');
            })
            .catch(() => {
                showNotification('No se pudo compartir el regalo', 'error');
            });
    }
};

window.toggleFavorite = function() {
    const isFavorite = localStorage.getItem('gift_favorite') === 'true';
    const newState = !isFavorite;
    
    localStorage.setItem('gift_favorite', newState.toString());
    
    if (newState) {
        showNotification('❤️ ¡Regalo agregado a favoritos!', 'success');
        createHearts();
    } else {
        showNotification('💔 Regalo removido de favoritos', 'info');
    }
};

window.showQRCode = function() {
    const qrModal = document.createElement('div');
    qrModal.innerHTML = `
        <div class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4 animate-fade-in" onclick="this.remove()">
            <div class="bg-white rounded-2xl p-8 max-w-sm w-full text-center card-shadow animate-scale-in" onclick="event.stopPropagation()">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">📱 Código QR</h3>
                <div class="bg-gray-100 p-4 rounded-xl mb-4">
                    <div id="qr-code" class="flex items-center justify-center h-48">
                        <div class="text-gray-500">Generando código QR...</div>
                    </div>
                </div>
                <p class="text-gray-600 text-sm mb-4">Escanea para compartir este regalo</p>
                <button onclick="this.closest('.fixed').remove()" 
                        class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-6 py-2 rounded-full hover:from-purple-600 hover:to-pink-600 transition-all duration-300">
                    Cerrar
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(qrModal);
    
    // Generate QR code (you can integrate QR.js library here)
    setTimeout(() => {
        document.getElementById('qr-code').innerHTML = `
            <div class="w-32 h-32 bg-gradient-to-br from-purple-400 to-pink-400 rounded-lg flex items-center justify-center text-white font-bold">
                QR CODE<br>
                <small class="text-xs">Próximamente</small>
            </div>
        `;
    }, 500);
};

// Notification system
window.showNotification = function(message, type = 'info') {
    const notification = document.createElement('div');
    const bgColor = {
        success: 'from-green-500 to-emerald-500',
        error: 'from-red-500 to-rose-500',
        info: 'from-blue-500 to-indigo-500'
    };
    
    notification.innerHTML = `
        <div class="fixed top-4 right-4 z-50 bg-gradient-to-r ${bgColor[type]} text-white px-6 py-3 rounded-full shadow-lg animate-slide-up">
            ${message}
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.firstElementChild.style.transform = 'translateX(400px)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
};

// Fun effects
window.createConfetti = function() {
    for (let i = 0; i < 50; i++) {
        createConfettiPiece();
    }
};

function createConfettiPiece() {
    const confetti = document.createElement('div');
    const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffeaa7'];
    
    confetti.style.cssText = `
        position: fixed;
        width: 10px;
        height: 10px;
        background: ${colors[Math.floor(Math.random() * colors.length)]};
        top: -10px;
        left: ${Math.random() * window.innerWidth}px;
        z-index: 1000;
        pointer-events: none;
        border-radius: 2px;
        animation: confettiFall ${Math.random() * 2 + 1}s linear forwards;
    `;
    
    document.body.appendChild(confetti);
    
    setTimeout(() => confetti.remove(), 3000);
}

window.createHearts = function() {
    for (let i = 0; i < 20; i++) {
        createHeart();
    }
};

function createHeart() {
    const heart = document.createElement('div');
    const heartEmojis = ['❤️', '💕', '💖', '💝', '🥰'];
    
    heart.innerHTML = heartEmojis[Math.floor(Math.random() * heartEmojis.length)];
    heart.style.cssText = `
        position: fixed;
        font-size: 20px;
        top: ${window.innerHeight}px;
        left: ${Math.random() * window.innerWidth}px;
        z-index: 1000;
        pointer-events: none;
        animation: heartFloat ${Math.random() * 2 + 2}s ease-out forwards;
    `;
    
    document.body.appendChild(heart);
    setTimeout(() => heart.remove(), 4000);
}

// Add CSS animations for effects
const styleSheet = document.createElement('style');
styleSheet.innerHTML = `
    @keyframes confettiFall {
        to {
            transform: translateY(100vh) rotate(720deg);
        }
    }
    
    @keyframes heartFloat {
        to {
            transform: translateY(-100vh);
            opacity: 0;
        }
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
`;
document.head.appendChild(styleSheet);

// Utility functions
window.TokenUtils = {
    // Format time duration
    formatDuration(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    },
    
    // Copy text to clipboard
    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (err) {
            console.error('Failed to copy:', err);
            return false;
        }
    },
    
    // Share content using Web Share API
    async shareContent(data) {
        if (navigator.share) {
            try {
                await navigator.share(data);
                return true;
            } catch (err) {
                console.error('Failed to share:', err);
                return false;
            }
        }
        return false;
    }
};