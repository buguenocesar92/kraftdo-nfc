// Multimedia Components Bundle
// This file contains all multimedia components for Alpine.js

console.log('Loading Multimedia Components Bundle');

// Wait for Alpine to be available before registering components
document.addEventListener('alpine:init', () => {
    console.log('Registering multimedia components...');
    
    // Multimedia Gallery Component
    Alpine.data('multimediaGallery', (config = {}) => ({
    images: config.images || [],
    imageLoaded: {},
    imageError: {},
    modalOpen: false,
    currentImage: null,
    currentIndex: 0,
    zoomLevel: 1,
    isPanning: false,
    panX: 0,
    panY: 0,
    startX: 0,
    startY: 0,
    loading: false,
    imageLoaded: {},
    imageError: {},
    isSlideshow: false,
    slideshowInterval: null,
    stats: {
        total: 0,
        loaded: 0,
        errors: 0
    },

    // Computed properties
    get imagePosition() {
        return {
            x: this.panX,
            y: this.panY
        };
    },

    get galleryImages() {
        return this.images;
    },

    init() {
        this.stats.total = this.images.length;
        
        // Initialize image loading states
        this.images.forEach((image, index) => {
            this.imageLoaded[index] = false;
            this.imageError[index] = false;
        });

        // Setup keyboard event listener
        document.addEventListener('keydown', this.handleKeydown.bind(this));
    },

    handleImageLoad(index) {
        this.imageLoaded[index] = true;
        this.stats.loaded++;
    },

    handleImageError(index) {
        this.imageError[index] = true;
        this.stats.errors++;
    },

    openModal(image, index) {
        this.currentImage = {
            ...image,
            loading: false,
            error: false
        };
        this.currentIndex = index;
        this.modalOpen = true;
        this.resetZoom();
        document.body.classList.add('overflow-hidden');
    },

    closeModal() {
        this.modalOpen = false;
        this.currentImage = null;
        this.resetZoom();
        this.stopSlideshow();
        document.body.classList.remove('overflow-hidden');
    },

    nextImage() {
        if (this.currentIndex < this.images.length - 1) {
            this.currentIndex++;
            this.currentImage = {
                ...this.images[this.currentIndex],
                loading: false,
                error: false
            };
            this.resetZoom();
        }
    },

    prevImage() {
        if (this.currentIndex > 0) {
            this.currentIndex--;
            this.currentImage = {
                ...this.images[this.currentIndex],
                loading: false,
                error: false
            };
            this.resetZoom();
        }
    },

    resetZoom() {
        this.zoomLevel = 1;
        this.panX = 0;
        this.panY = 0;
        this.isPanning = false;
    },

    zoomIn() {
        this.zoomLevel = Math.min(this.zoomLevel * 1.5, 5);
    },

    zoomOut() {
        this.zoomLevel = Math.max(this.zoomLevel / 1.5, 0.5);
        if (this.zoomLevel === 1) {
            this.panX = 0;
            this.panY = 0;
        }
    },

    handleKeydown(event) {
        if (!this.modalOpen) return;
        
        switch (event.key) {
            case 'Escape':
                this.closeModal();
                break;
            case 'ArrowRight':
                this.nextImage();
                break;
            case 'ArrowLeft':
                this.prevImage();
                break;
            case '+':
            case '=':
                this.zoomIn();
                break;
            case '-':
                this.zoomOut();
                break;
        }
    },

    // Modal drag functionality
    handleModalBackdrop(event) {
        if (event.target === event.currentTarget) {
            this.closeModal();
        }
    },

    startDrag(event) {
        if (this.zoomLevel <= 1) return;
        
        this.isPanning = true;
        const clientX = event.touches ? event.touches[0].clientX : event.clientX;
        const clientY = event.touches ? event.touches[0].clientY : event.clientY;
        
        this.startX = clientX - this.panX;
        this.startY = clientY - this.panY;
        
        event.preventDefault();
    },

    drag(event) {
        if (!this.isPanning) return;
        
        const clientX = event.touches ? event.touches[0].clientX : event.clientX;
        const clientY = event.touches ? event.touches[0].clientY : event.clientY;
        
        this.panX = clientX - this.startX;
        this.panY = clientY - this.startY;
        
        event.preventDefault();
    },

    endDrag() {
        this.isPanning = false;
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
        this.isSlideshow = true;
        this.slideshowInterval = setInterval(() => {
            if (this.currentIndex < this.images.length - 1) {
                this.nextImage();
            } else {
                this.currentIndex = 0;
                this.currentImage = {
                    ...this.images[this.currentIndex],
                    loading: false,
                    error: false
                };
                this.resetZoom();
            }
        }, 3000);
    },

    stopSlideshow() {
        this.isSlideshow = false;
        if (this.slideshowInterval) {
            clearInterval(this.slideshowInterval);
            this.slideshowInterval = null;
        }
    },

    // Share functionality
    async shareImage() {
        if (navigator.share && this.currentImage) {
            try {
                await navigator.share({
                    title: this.currentImage.alt || 'Imagen de galería',
                    text: this.currentImage.caption || 'Compartir imagen',
                    url: this.currentImage.src
                });
            } catch (error) {
                console.log('Error sharing:', error);
            }
        }
    },

    // Download functionality
    downloadImage() {
        if (this.currentImage && this.currentImage.src) {
            const link = document.createElement('a');
            link.href = this.currentImage.src;
            link.download = this.currentImage.alt || `imagen-${this.currentIndex + 1}.jpg`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }
}));

// Multimedia Video Component
Alpine.data('multimediaVideo', (config = {}) => ({
    videoId: config.videoId || '',
    videoType: config.videoType || 'html5',
    videoSrc: config.videoSrc || '',
    thumbnailSrc: config.thumbnailSrc || '',
    isPlaying: false,
    currentTime: 0,
    duration: 0,
    volume: 1,
    isMuted: false,
    isFullscreen: false,
    isPip: false,
    loading: false,
    error: null,
    buffered: 0,
    progress: 0,
    showThumbnailOverlay: config.showThumbnail !== false,
    autoplay: config.autoplay || false,
    customControls: config.customControls !== false,
    isVerticalVideo: false,
    videoWidth: 0,
    videoHeight: 0,

    init() {
        this.$nextTick(() => {
            if (this.videoType === 'html5') {
                this.customControls = true;
                
                const video = this.$refs.videoElement;
                if (video) {
                    this.setupVideoEvents(video);
                    
                    // Auto-detect vertical videos
                    video.addEventListener('loadedmetadata', () => {
                        this.duration = video.duration;
                        this.videoWidth = video.videoWidth;
                        this.videoHeight = video.videoHeight;
                        this.isVerticalVideo = video.videoHeight > video.videoWidth;
                        
                        if (this.isVerticalVideo) {
                            console.log('Vertical video detected:', this.videoWidth + 'x' + this.videoHeight);
                        }
                    });
                }
            }
            
            // If no thumbnail, show video immediately
            if (!this.thumbnailSrc) {
                this.showThumbnailOverlay = false;
                this.initVideo();
            }
        });
    },

    initVideo() {
        this.$nextTick(() => {
            const video = this.$refs.videoElement;
            if (video && this.videoType === 'html5') {
                if (!video.src || video.src !== this.videoSrc) {
                    video.src = this.videoSrc;
                }
                video.preload = 'metadata';
                video.load();
            }
        });
    },

    setupVideoEvents(video) {
        video.addEventListener('timeupdate', () => {
            this.currentTime = video.currentTime;
            this.progress = this.duration ? (this.currentTime / this.duration) * 100 : 0;
        });

        video.addEventListener('progress', () => {
            if (video.buffered.length > 0) {
                this.buffered = (video.buffered.end(video.buffered.length - 1) / this.duration) * 100;
            }
        });

        video.addEventListener('play', () => {
            this.isPlaying = true;
        });

        video.addEventListener('pause', () => {
            this.isPlaying = false;
        });

        video.addEventListener('volumechange', () => {
            this.volume = video.volume;
            this.isMuted = video.muted;
        });

        video.addEventListener('error', () => {
            this.error = 'Error al cargar el video';
            this.loading = false;
        });

        video.addEventListener('loadstart', () => {
            this.loading = true;
        });

        video.addEventListener('canplaythrough', () => {
            this.loading = false;
        });

        // Picture-in-Picture events
        video.addEventListener('enterpictureinpicture', () => {
            this.isPip = true;
        });

        video.addEventListener('leavepictureinpicture', () => {
            this.isPip = false;
        });

        // Fullscreen events
        document.addEventListener('fullscreenchange', () => {
            this.isFullscreen = !!document.fullscreenElement;
        });
    },

    playVideo() {
        this.showThumbnailOverlay = false;
        this.$nextTick(() => {
            if (this.videoType === 'html5') {
                const video = this.$refs.videoElement;
                if (video && !video.src) {
                    video.src = this.videoSrc;
                }
                setTimeout(() => {
                    if (video) {
                        video.play().catch(e => console.log('Autoplay prevented:', e));
                    }
                }, 100);
            } else {
                const iframe = this.$refs.iframeElement;
                if (iframe) {
                    iframe.src = this.videoSrc + '&autoplay=1';
                }
            }
        });
    },

    togglePlayPause() {
        const video = this.$refs.videoElement;
        if (video) {
            if (video.paused) {
                video.play().catch(e => console.log('Play failed:', e));
            } else {
                video.pause();
            }
        }
    },

    seekTo(event) {
        const video = this.$refs.videoElement;
        if (video && this.duration) {
            const rect = event.currentTarget.getBoundingClientRect();
            const clickX = event.clientX - rect.left;
            const progress = clickX / rect.width;
            video.currentTime = progress * this.duration;
        }
    },

    setVolume(volume) {
        const video = this.$refs.videoElement;
        if (video) {
            video.volume = volume;
            this.volume = volume;
        }
    },

    toggleMute() {
        const video = this.$refs.videoElement;
        if (video) {
            video.muted = !video.muted;
            this.isMuted = video.muted;
        }
    },

    togglePictureInPicture() {
        const video = this.$refs.videoElement;
        if (video && 'pictureInPictureEnabled' in document) {
            if (document.pictureInPictureElement) {
                document.exitPictureInPicture();
            } else {
                video.requestPictureInPicture().catch(e => console.log('PiP failed:', e));
            }
        }
    },

    toggleFullscreen() {
        const container = this.$refs.videoElement?.parentElement || this.$el;
        if (document.fullscreenElement) {
            document.exitFullscreen();
        } else {
            container.requestFullscreen().catch(e => console.log('Fullscreen failed:', e));
        }
    },

    showControls() {
        // Method for showing controls on mouse movement
        this.controlsVisible = true;
        if (this.controlsTimer) {
            clearTimeout(this.controlsTimer);
        }
        this.controlsTimer = setTimeout(() => {
            this.controlsVisible = false;
        }, 3000);
    },

    retryVideo() {
        this.error = null;
        this.loading = true;
        this.$nextTick(() => {
            if (this.videoType === 'html5') {
                this.$refs.videoElement?.load();
            } else {
                const iframe = this.$refs.iframeElement;
                if (iframe) {
                    iframe.src = iframe.src;
                }
            }
        });
    },

    initIframeVideo() {
        if (!this.showThumbnailOverlay) {
            const iframe = this.$refs.iframeElement;
            if (iframe) {
                iframe.src = this.videoSrc;
            }
        }
    },

    initHTML5Video() {
        if (!this.showThumbnailOverlay) {
            const video = this.$refs.videoElement;
            if (video) {
                video.src = this.videoSrc;
                this.$nextTick(() => {
                    this.setupVideoEvents(video);
                });
            }
        }
    },

    formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${minutes}:${secs.toString().padStart(2, '0')}`;
    }
}));

// Multimedia Audio Component  
Alpine.data('multimediaAudio', (config = {}) => ({
    audio: config.audio || null,
    isPlaying: false,
    currentTime: 0,
    duration: 0,
    volume: 1,
    isMuted: false,
    loading: true,
    error: false,
    visualization: config.visualization || 'waveform',
    showMetadata: config.showMetadata !== false,

    init() {
        this.$nextTick(() => {
            const audioElement = this.$refs.audioElement;
            if (audioElement) {
                this.setupAudioEvents(audioElement);
                this.loading = false;
            }
        });
    },

    setupAudioEvents(audio) {
        audio.addEventListener('loadedmetadata', () => {
            this.duration = audio.duration;
        });

        audio.addEventListener('timeupdate', () => {
            this.currentTime = audio.currentTime;
        });

        audio.addEventListener('play', () => {
            this.isPlaying = true;
        });

        audio.addEventListener('pause', () => {
            this.isPlaying = false;
        });

        audio.addEventListener('volumechange', () => {
            this.volume = audio.volume;
            this.isMuted = audio.muted;
        });

        audio.addEventListener('error', () => {
            this.error = true;
            this.loading = false;
        });
    },

    togglePlay() {
        const audio = this.$refs.audioElement;
        if (audio.paused) {
            audio.play();
        } else {
            audio.pause();
        }
    },

    setCurrentTime(time) {
        const audio = this.$refs.audioElement;
        audio.currentTime = time;
    },

    setVolume(volume) {
        const audio = this.$refs.audioElement;
        audio.volume = volume;
        this.volume = volume;
    },

    toggleMute() {
        const audio = this.$refs.audioElement;
        audio.muted = !audio.muted;
        this.isMuted = audio.muted;
    },

    formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${minutes}:${secs.toString().padStart(2, '0')}`;
    }
}));

    console.log('All multimedia components registered successfully');
});