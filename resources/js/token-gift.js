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
            });
            
            videoElement.addEventListener('touchmove', (e) => {
                if (e.touches.length !== 1) return;
                
                const deltaX = e.touches[0].clientX - touchStartX;
                const deltaY = e.touches[0].clientY - touchStartY;
                
                // Horizontal swipe for seeking
                if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 30) {
                    seeking = true;
                    const seekAmount = (deltaX / videoElement.clientWidth) * this.currentVideo.duration;
                    const newTime = Math.max(0, Math.min(this.currentVideo.duration, this.currentVideo.currentTime + seekAmount));
                    this.seekTo(newTime, videoId);
                }
                
                // Vertical swipe for volume
                if (Math.abs(deltaY) > Math.abs(deltaX) && Math.abs(deltaY) > 30) {
                    const volumeChange = -(deltaY / videoElement.clientHeight);
                    const newVolume = Math.max(0, Math.min(1, this.currentVideo.volume + volumeChange));
                    this.setVolume(newVolume, videoId);
                }
            });
            
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
            });
        }
        }));
    } else {
        console.warn('Alpine.js no está disponible aún');
    }
}

// Intentar registrar inmediatamente o esperar a que Alpine esté disponible
if (typeof window.Alpine !== 'undefined') {
    registerTokenGiftComponent();
} else {
    // Esperar a que Alpine esté disponible
    document.addEventListener('alpine:init', () => {
        registerTokenGiftComponent();
    });
}

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