// Multimedia Video Component JavaScript
// Generic video player functionality for multimedia components

// Register Alpine.js component when available
function registerMultimediaVideoComponent() {
    if (typeof window.Alpine !== 'undefined') {
        window.Alpine.data('multimediaVideoPlayer', (config = {}) => ({
            // Video configuration
            videoId: config.videoId || 'video_' + Date.now(),
            videoType: config.videoType || 'html5',
            videoSrc: config.videoSrc || '',
            thumbnailSrc: config.thumbnailSrc || '',
            autoplay: config.autoplay || false,
            customControls: config.customControls || true,
            
            // Video state
            showThumbnailState: config.showThumbnail || false,
            loading: false,
            error: null,
            playing: false,
            muted: false,
            volume: 1,
            currentTime: 0,
            duration: 0,
            buffered: 0,
            fullscreen: false,
            pip: false,
            playbackRate: 1,
            
            // UI state
            controlsVisible: true,
            controlsTimer: null,
            isVerticalVideo: false,
            videoWidth: 0,
            videoHeight: 0,
            
            init() {
                console.log('Multimedia Video Player initialized:', {
                    videoId: this.videoId,
                    videoType: this.videoType,
                    videoSrc: this.videoSrc
                });
                
                this.$nextTick(() => {
                    this.initializeVideo();
                });
                
                // Fullscreen event listeners
                document.addEventListener('fullscreenchange', () => {
                    this.fullscreen = !!document.fullscreenElement;
                });
                
                // Picture-in-Picture support detection
                this.supportsPiP = 'pictureInPictureEnabled' in document;
            },
            
            initializeVideo() {
                if (this.videoType === 'html5') {
                    this.initializeHTML5Video();
                } else if (this.videoType === 'youtube' || this.videoType === 'vimeo') {
                    this.initializeEmbeddedVideo();
                }
                
                if (this.autoplay && !this.showThumbnailState) {
                    this.playVideo();
                }
            },
            
            initializeHTML5Video() {
                const video = this.$refs.videoElement;
                if (!video) return;
                
                // Event listeners
                video.addEventListener('loadedmetadata', () => {
                    this.duration = video.duration;
                    this.videoWidth = video.videoWidth;
                    this.videoHeight = video.videoHeight;
                    this.isVerticalVideo = video.videoHeight > video.videoWidth;
                    this.loading = false;
                    
                    console.log('Video metadata loaded:', {
                        duration: this.duration,
                        dimensions: `${this.videoWidth}x${this.videoHeight}`,
                        isVertical: this.isVerticalVideo
                    });
                });
                
                video.addEventListener('timeupdate', () => {
                    this.currentTime = video.currentTime;
                });
                
                video.addEventListener('play', () => {
                    this.playing = true;
                });
                
                video.addEventListener('pause', () => {
                    this.playing = false;
                });
                
                video.addEventListener('ended', () => {
                    this.playing = false;
                });
                
                video.addEventListener('volumechange', () => {
                    this.volume = video.volume;
                    this.muted = video.muted;
                });
                
                video.addEventListener('progress', () => {
                    if (video.buffered.length > 0) {
                        this.buffered = (video.buffered.end(video.buffered.length - 1) / video.duration) * 100;
                    }
                });
                
                video.addEventListener('loadstart', () => {
                    this.loading = true;
                    this.error = null;
                });
                
                video.addEventListener('canplay', () => {
                    this.loading = false;
                });
                
                video.addEventListener('error', (e) => {
                    this.loading = false;
                    this.error = 'Error al cargar el video';
                    console.error('Video error:', e);
                });
                
                // Set initial source if not showing thumbnail
                if (!this.showThumbnailState && this.videoSrc) {
                    video.src = this.videoSrc;
                }
            },
            
            initializeEmbeddedVideo() {
                // For YouTube/Vimeo, we'll set the src when playing
                console.log('Embedded video initialized:', this.videoType);
            },
            
            // Playback controls
            playVideo() {
                this.showThumbnailState = false;
                this.loading = true;
                
                this.$nextTick(() => {
                    if (this.videoType === 'html5') {
                        const video = this.$refs.videoElement;
                        if (video) {
                            if (!video.src) {
                                video.src = this.videoSrc;
                            }
                            
                            video.play()
                                .then(() => {
                                    console.log('Video started playing');
                                    this.loading = false;
                                })
                                .catch(e => {
                                    console.error('Play failed:', e);
                                    this.loading = false;
                                    this.error = 'No se pudo reproducir el video';
                                });
                        }
                    } else {
                        // Set iframe source for embedded videos
                        const iframe = this.$refs.iframeElement;
                        if (iframe && !iframe.src) {
                            iframe.src = this.videoSrc;
                        }
                        this.loading = false;
                    }
                });
            },
            
            togglePlay() {
                if (this.videoType === 'html5') {
                    const video = this.$refs.videoElement;
                    if (!video) return;
                    
                    if (video.paused) {
                        video.play();
                    } else {
                        video.pause();
                    }
                }
            },
            
            toggleMute() {
                if (this.videoType === 'html5') {
                    const video = this.$refs.videoElement;
                    if (video) {
                        video.muted = !video.muted;
                    }
                }
            },
            
            setVolume(value) {
                if (this.videoType === 'html5') {
                    const video = this.$refs.videoElement;
                    if (video) {
                        video.volume = parseFloat(value);
                        if (video.volume > 0) {
                            video.muted = false;
                        }
                    }
                }
            },
            
            seekTo(event) {
                if (this.videoType === 'html5') {
                    const video = this.$refs.videoElement;
                    if (!video || !this.duration) return;
                    
                    const rect = event.currentTarget.getBoundingClientRect();
                    const clickX = event.clientX - rect.left;
                    const progress = clickX / rect.width;
                    video.currentTime = progress * this.duration;
                }
            },
            
            // Advanced controls
            toggleFullscreen() {
                if (!document.fullscreenElement) {
                    const container = this.$el.querySelector('.relative');
                    if (container.requestFullscreen) {
                        container.requestFullscreen();
                    }
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    }
                }
            },
            
            togglePiP() {
                if (this.videoType === 'html5') {
                    const video = this.$refs.videoElement;
                    if (!video) return;
                    
                    if (document.pictureInPictureElement) {
                        document.exitPictureInPicture();
                    } else {
                        video.requestPictureInPicture()
                            .then(() => {
                                this.pip = true;
                            })
                            .catch(err => {
                                console.error('PiP failed:', err);
                            });
                    }
                }
            },
            
            retryVideo() {
                this.error = null;
                this.loading = true;
                
                if (this.videoType === 'html5') {
                    const video = this.$refs.videoElement;
                    if (video) {
                        video.load();
                    }
                } else {
                    const iframe = this.$refs.iframeElement;
                    if (iframe) {
                        iframe.src = iframe.src;
                    }
                }
            },
            
            // UI methods
            showControls() {
                this.controlsVisible = true;
                if (this.controlsTimer) {
                    clearTimeout(this.controlsTimer);
                }
                
                if (this.playing) {
                    this.controlsTimer = setTimeout(() => {
                        this.controlsVisible = false;
                    }, 3000);
                }
            },
            
            hideControls() {
                if (this.playing) {
                    this.controlsTimer = setTimeout(() => {
                        this.controlsVisible = false;
                    }, 1000);
                }
            },
            
            // Utility methods
            formatTime(seconds) {
                if (isNaN(seconds)) return '0:00';
                const minutes = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return minutes + ':' + secs.toString().padStart(2, '0');
            },
            
            get progress() {
                return this.duration ? (this.currentTime / this.duration) * 100 : 0;
            }
        }));
        
        console.log('Multimedia Video component registered');
    } else {
        console.warn('Alpine.js not available for Multimedia Video component');
    }
}

// Auto-register when Alpine is available
document.addEventListener('alpine:init', () => {
    registerMultimediaVideoComponent();
});

// Fallback registration
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        if (typeof window.Alpine !== 'undefined') {
            registerMultimediaVideoComponent();
        }
    }, 100);
});

// Export for manual registration
if (typeof window !== 'undefined') {
    window.registerMultimediaVideoComponent = registerMultimediaVideoComponent;
}