// Multimedia Audio Component JavaScript
// Generic audio player functionality for multimedia components

// Register Alpine.js component when available
function registerMultimediaAudioComponent() {
    if (typeof window.Alpine !== 'undefined') {
        window.Alpine.data('multimediaAudioPlayer', (config = {}) => ({
            // Audio configuration
            audioId: config.audioId || 'audio_' + Date.now(),
            audioSrc: config.audioSrc || '',
            audioType: config.audioType || 'direct',
            visualization: config.visualization || 'waveform',
            showMetadata: config.showMetadata || true,
            
            // Audio state
            isPlaying: false,
            isLoading: true,
            hasError: false,
            currentTime: 0,
            duration: 0,
            buffered: 0,
            volume: 1,
            isMuted: false,
            playbackRate: 1,
            isLooping: false,
            
            // Visualization data
            waveformData: Array.from({length: 60}, () => Math.random()),
            
            // Metadata
            metadata: {
                title: '',
                artist: '',
                album: '',
                artwork: null
            },
            
            init() {
                console.log('Multimedia Audio Player initialized:', {
                    audioId: this.audioId,
                    audioSrc: this.audioSrc,
                    audioType: this.audioType
                });
                
                this.$nextTick(() => {
                    this.initializeAudio();
                });
                
                // Generate initial waveform data
                this.generateWaveform();
            },
            
            initializeAudio() {
                const audio = this.$refs.audioElement;
                if (!audio) return;
                
                // Event listeners
                audio.addEventListener('loadedmetadata', () => {
                    this.duration = audio.duration;
                    this.isLoading = false;
                    
                    // Try to extract metadata
                    this.extractMetadata(audio);
                    
                    console.log('Audio metadata loaded:', {
                        duration: this.duration,
                        title: this.metadata.title
                    });
                });
                
                audio.addEventListener('timeupdate', () => {
                    this.currentTime = audio.currentTime;
                    this.updateVisualization();
                });
                
                audio.addEventListener('play', () => {
                    this.isPlaying = true;
                });
                
                audio.addEventListener('pause', () => {
                    this.isPlaying = false;
                });
                
                audio.addEventListener('ended', () => {
                    this.isPlaying = false;
                    if (!this.isLooping) {
                        this.currentTime = 0;
                    }
                });
                
                audio.addEventListener('volumechange', () => {
                    this.volume = audio.volume;
                    this.isMuted = audio.muted;
                });
                
                audio.addEventListener('progress', () => {
                    if (audio.buffered.length > 0) {
                        this.buffered = audio.buffered.end(audio.buffered.length - 1);
                    }
                });
                
                audio.addEventListener('loadstart', () => {
                    this.isLoading = true;
                    this.hasError = false;
                });
                
                audio.addEventListener('canplay', () => {
                    this.isLoading = false;
                });
                
                audio.addEventListener('error', (e) => {
                    this.hasError = true;
                    this.isLoading = false;
                    console.error('Audio error:', e);
                });
                
                // Set initial source
                if (this.audioSrc) {
                    audio.src = this.audioSrc;
                }
            },
            
            // Playback controls
            togglePlay() {
                const audio = this.$refs.audioElement;
                console.log('togglePlay called', { 
                    audio: !!audio, 
                    isPlaying: this.isPlaying, 
                    src: audio?.src 
                });
                
                if (!audio) {
                    console.error('Audio element not found');
                    return;
                }
                
                if (this.isPlaying) {
                    audio.pause();
                } else {
                    // Ensure audio source is set
                    if (!audio.src) {
                        console.log('Setting audio src:', this.audioSrc);
                        audio.src = this.audioSrc;
                    }
                    
                    this.isLoading = true;
                    audio.play()
                        .then(() => {
                            console.log('Audio started playing successfully');
                            this.isLoading = false;
                        })
                        .catch(e => {
                            console.error('Play failed:', e);
                            this.isLoading = false;
                            this.hasError = true;
                        });
                }
            },
            
            skip(seconds) {
                const audio = this.$refs.audioElement;
                if (audio) {
                    audio.currentTime = Math.max(0, Math.min(this.duration, audio.currentTime + seconds));
                }
            },
            
            seekToPosition(event) {
                const audio = this.$refs.audioElement;
                if (!audio || !this.duration) return;
                
                const rect = event.currentTarget.getBoundingClientRect();
                const clickX = event.clientX - rect.left;
                const progress = clickX / rect.width;
                audio.currentTime = progress * this.duration;
            },
            
            setVolume(value) {
                const audio = this.$refs.audioElement;
                if (audio) {
                    this.volume = parseFloat(value);
                    audio.volume = this.volume;
                    if (this.volume > 0) {
                        audio.muted = false;
                        this.isMuted = false;
                    }
                }
            },
            
            toggleMute() {
                const audio = this.$refs.audioElement;
                if (audio) {
                    audio.muted = !audio.muted;
                    this.isMuted = audio.muted;
                }
            },
            
            toggleLoop() {
                const audio = this.$refs.audioElement;
                if (audio) {
                    this.isLooping = !this.isLooping;
                    audio.loop = this.isLooping;
                }
            },
            
            setPlaybackRate(rate) {
                const audio = this.$refs.audioElement;
                if (audio) {
                    audio.playbackRate = rate;
                    this.playbackRate = rate;
                }
            },
            
            retryAudio() {
                const audio = this.$refs.audioElement;
                if (audio) {
                    this.hasError = false;
                    this.isLoading = true;
                    audio.load();
                }
            },
            
            // Visualization methods
            generateWaveform() {
                // Generate mock waveform data
                this.waveformData = Array.from({length: 60}, () => Math.random());
            },
            
            updateVisualization() {
                if (this.visualization === 'bars' && this.isPlaying) {
                    // Update bars visualization with random data
                    // In a real implementation, you would use Web Audio API
                    this.$nextTick(() => {
                        const bars = this.$el.querySelectorAll('[x-show="visualization === \'bars\'"] div');
                        bars.forEach(bar => {
                            if (this.isPlaying) {
                                const height = Math.random() * 70 + 10;
                                bar.style.height = height + 'px';
                            }
                        });
                    });
                }
            },
            
            // Metadata extraction
            extractMetadata(audio) {
                // Basic metadata extraction
                if (audio.title) {
                    this.metadata.title = audio.title;
                }
                
                // Try to extract from filename if no title
                if (!this.metadata.title && this.audioSrc) {
                    const filename = this.audioSrc.split('/').pop();
                    const nameWithoutExt = filename.replace(/\.[^/.]+$/, '');
                    this.metadata.title = nameWithoutExt.replace(/[-_]/g, ' ');
                }
                
                // In a real implementation, you might use:
                // - Web Audio API for more detailed analysis
                // - ID3 tag parsing libraries
                // - Server-side metadata extraction
            },
            
            // Utility methods
            formatTime(seconds) {
                if (isNaN(seconds)) return '0:00';
                const minutes = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return minutes + ':' + secs.toString().padStart(2, '0');
            },
            
            // Computed properties
            get progress() {
                return this.duration ? (this.currentTime / this.duration) * 100 : 0;
            },
            
            get bufferedPercent() {
                return this.duration ? (this.buffered / this.duration) * 100 : 0;
            }
        }));
        
        console.log('Multimedia Audio component registered');
    } else {
        console.warn('Alpine.js not available for Multimedia Audio component');
    }
}

// Auto-register when Alpine is available
document.addEventListener('alpine:init', () => {
    registerMultimediaAudioComponent();
});

// Fallback registration
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        if (typeof window.Alpine !== 'undefined') {
            registerMultimediaAudioComponent();
        }
    }, 100);
});

// Export for manual registration
if (typeof window !== 'undefined') {
    window.registerMultimediaAudioComponent = registerMultimediaAudioComponent;
}