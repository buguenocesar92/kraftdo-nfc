/**
 * Audio Overlay System
 * Handles mandatory audio activation overlay and controls
 * 
 * This system:
 * 1. Shows an overlay that blocks all interaction until audio is activated
 * 2. Handles different types of audio/video content
 * 3. Manages streaming services (Spotify, YouTube Music, SoundCloud)
 * 4. Provides fallback video activation when no audio is present
 */

class AudioOverlaySystem {
    constructor(config = null) {
        // Get config from hidden inputs if not provided
        if (config) {
            this.audioConfig = config.audio || {};
            this.videoConfig = config.video || {};
            this.theme = config.theme || {};
        } else {
            this.audioConfig = {
                type: document.getElementById('audio-type')?.value || '',
                url: document.getElementById('audio-url')?.value || '',
                autoplay: document.getElementById('audio-autoplay')?.value === '1'
            };
            this.videoConfig = {
                type: document.getElementById('video-type')?.value || '',
                url: document.getElementById('video-url')?.value || ''
            };
            this.theme = {
                primary_gradient: document.getElementById('theme-primary-gradient')?.value || '',
                accent_color: document.getElementById('theme-accent-color')?.value || ''
            };
        }
        
        this.hasAudio = this.audioConfig.type && this.audioConfig.url;
        this.hasVideo = this.videoConfig.type && this.videoConfig.url;
        
        this.init();
    }
    
    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.setupOverlay();
            });
        } else {
            this.setupOverlay();
        }
    }
    
    setupOverlay() {
        const audioOverlay = document.getElementById('audio-overlay');
        const activateBtn = document.getElementById('activate-audio-btn');
        
        if (!audioOverlay || !activateBtn) {
            console.warn('Audio overlay elements not found');
            return;
        }
        
        // Block all interaction when page loads
        document.body.classList.add('audio-overlay-active');
        
        this.setupOverlayEventListeners(audioOverlay, activateBtn);
    }
    
    setupOverlayEventListeners(audioOverlay, activateBtn) {
        // Prevent clicks on transparent overlay area
        audioOverlay.addEventListener('click', (e) => {
            if (e.target === audioOverlay) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
        
        // Prevent clicks on modal except button
        const audioModal = document.getElementById('audio-modal');
        if (audioModal) {
            audioModal.addEventListener('click', (e) => {
                if (e.target !== activateBtn && !activateBtn.contains(e.target)) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });
        }
        
        // Main activation button
        activateBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            this.activateContent(audioOverlay);
        });
    }
    
    activateContent(audioOverlay) {
        // Hide overlay with animation
        audioOverlay.style.opacity = '0';
        audioOverlay.style.transform = 'scale(0.9)';
        
        setTimeout(() => {
            audioOverlay.style.display = 'none';
            
            // Unblock page interaction
            document.body.classList.remove('audio-overlay-active');
            
            // Activate appropriate content
            this.activatePlayback();
        }, 300);
    }
    
    activatePlayback() {
        // If no audio but has video, activate video
        if (!this.hasAudio && this.hasVideo) {
            console.log('🎬 No audio, activating video type:', this.videoConfig.type);
            this.activateVideoPlayback();
            return;
        }
        
        console.log('🎵 Activating audio playback type:', this.audioConfig.type);
        
        switch (this.audioConfig.type) {
            case 'file_upload':
                this.activateFileUploadAudio();
                break;
            case 'youtube_music':
                this.activateYouTubeMusicAudio();
                break;
            case 'spotify':
                this.activateSpotifyAudio();
                break;
            case 'soundcloud':
                this.activateSoundCloudAudio();
                break;
            default:
                console.warn('Unknown audio type:', this.audioConfig.type);
        }
    }
    
    activateFileUploadAudio() {
        const audioElement = document.getElementById('background-audio');
        const mainAudioToggle = document.getElementById('main-audio-toggle');
        const audioIcon = document.getElementById('main-audio-icon');
        const equalizer = document.getElementById('audio-equalizer');
        const audioStatus = document.getElementById('audio-status');
        
        if (audioElement && audioElement.paused) {
            audioElement.play().then(() => {
                console.log('🎵 Audio activated from overlay successfully');
                
                // Update UI
                if (audioIcon) audioIcon.className = 'fas fa-pause text-2xl';
                if (equalizer) equalizer.classList.remove('hidden');
                if (audioStatus) audioStatus.textContent = '♪ Reproduciendo tu canción especial ♪';
                if (mainAudioToggle) mainAudioToggle.classList.remove('animate-pulse');
                
                this.showAudioActivatedNotification();
                this.setupFileUploadAudioControls(audioElement, mainAudioToggle, audioIcon, equalizer, audioStatus);
                
            }).catch(error => {
                console.error('🎵 Error activating audio from overlay:', error);
            });
        }
    }
    
    setupFileUploadAudioControls(audioElement, mainAudioToggle, audioIcon, equalizer, audioStatus) {
        if (mainAudioToggle) {
            // Replace existing listeners
            const newToggle = mainAudioToggle.cloneNode(true);
            mainAudioToggle.parentNode.replaceChild(newToggle, mainAudioToggle);
            
            newToggle.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('Main audio button clicked after overlay');
                
                if (audioElement.paused) {
                    audioElement.play().then(() => {
                        audioIcon.className = 'fas fa-pause text-2xl';
                        equalizer.classList.remove('hidden');
                        audioStatus.textContent = '♪ Reproduciendo tu canción especial ♪';
                    }).catch(console.error);
                } else {
                    audioElement.pause();
                    audioIcon.className = 'fas fa-play text-2xl';
                    equalizer.classList.add('hidden');
                    audioStatus.textContent = 'Haz clic para reproducir';
                }
            });
        }
    }
    
    activateYouTubeMusicAudio() {
        console.log('🎵 Activating YouTube Music');
        
        const youtubeFrame = document.querySelector('#audio-iframe, .audio-iframe');
        if (youtubeFrame) {
            console.log('🎵 YouTube iframe found, activating autoplay');
            
            // Modify src to activate autoplay
            let currentSrc = youtubeFrame.src;
            if (!currentSrc.includes('autoplay=1')) {
                if (currentSrc.includes('autoplay=0')) {
                    currentSrc = currentSrc.replace('autoplay=0', 'autoplay=1');
                } else {
                    currentSrc += (currentSrc.includes('?') ? '&' : '?') + 'autoplay=1';
                }
                
                console.log('🎵 Changing URL from:', youtubeFrame.src);
                console.log('🎵 To:', currentSrc);
                
                youtubeFrame.src = currentSrc;
            }
            
            this.updateAudioUIForStreamingService(true, 'YouTube Music iniciado');
            this.showAudioActivatedNotification();
            this.setupStreamingServiceControl('youtube_music', youtubeFrame);
        }
    }
    
    activateSpotifyAudio() {
        console.log('🎵 Activating Spotify');
        
        const spotifyFrame = document.querySelector('iframe[src*="spotify.com"]');
        if (spotifyFrame) {
            console.log('🎵 Spotify iframe found');
            
            this.updateAudioUIForStreamingService(true, 'Spotify listo - Haz clic en ▶️');
            this.showAudioActivatedNotification();
            this.setupStreamingServiceControl('spotify', spotifyFrame);
        }
    }
    
    activateSoundCloudAudio() {
        console.log('🎵 Activating SoundCloud');
        
        const soundcloudFrame = document.querySelector('iframe[src*="soundcloud.com"]');
        if (soundcloudFrame) {
            console.log('🎵 SoundCloud iframe found');
            
            // Try to activate autoplay
            let currentSrc = soundcloudFrame.src;
            if (!currentSrc.includes('auto_play=true')) {
                if (currentSrc.includes('auto_play=false')) {
                    currentSrc = currentSrc.replace('auto_play=false', 'auto_play=true');
                } else {
                    currentSrc += (currentSrc.includes('?') ? '&' : '?') + 'auto_play=true';
                }
                soundcloudFrame.src = currentSrc;
            }
            
            this.updateAudioUIForStreamingService(true, 'SoundCloud iniciado');
            this.showAudioActivatedNotification();
            this.setupStreamingServiceControl('soundcloud', soundcloudFrame);
        }
    }
    
    activateVideoPlayback() {
        console.log('🎬 Activating video playback type:', this.videoConfig.type);
        
        switch (this.videoConfig.type) {
            case 'file_upload':
                this.activateFileUploadVideo();
                break;
            case 'youtube':
            case 'direct':
            case 'vimeo':
                this.activateEmbeddedVideo();
                break;
            default:
                this.activateGenericVideo();
        }
    }
    
    activateFileUploadVideo() {
        const videoElement = document.querySelector('video');
        if (videoElement) {
            // Smooth scroll to video first
            videoElement.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center',
                inline: 'nearest'
            });
            
            // Small delay for scroll to finish
            setTimeout(() => {
                videoElement.muted = false; // Video with audio enabled
                videoElement.play().then(() => {
                    console.log('🎬 Video activated from overlay successfully with audio');
                    this.showVideoActivatedNotification('¡Video reproduciéndose con audio! 🔊');
                }).catch(error => {
                    console.error('🎬 Error activating video from overlay:', error);
                    // Fallback without audio
                    videoElement.muted = true;
                    videoElement.play().then(() => {
                        console.log('🎬 Video activated without audio (fallback)');
                        this.showVideoActivatedNotification('Video reproduciéndose - haz clic para activar audio 🔊');
                    }).catch(error2 => {
                        console.error('🎬 Error activating video even without audio:', error2);
                        this.showVideoActivatedNotification('Video listo - haz clic en ▶️ para reproducir');
                    });
                });
            }, 500);
        }
    }
    
    activateEmbeddedVideo() {
        const iframe = document.querySelector('#video-iframe') || document.querySelector('video');
        if (iframe) {
            console.log('🎬 Embedded video found');
            
            // If it's a YouTube video iframe, activate autoplay and audio
            if (iframe.id === 'video-iframe' && iframe.src && iframe.src.includes('youtube.com')) {
                this.activateYouTubeVideo(iframe);
            } else if (iframe.src && iframe.src.includes('vimeo.com')) {
                this.activateVimeoVideo(iframe);
            } else {
                this.showVideoActivatedNotification('Video listo - haz clic en ▶️ para reproducir');
            }
            
            // Smooth scroll to video
            iframe.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center',
                inline: 'nearest'
            });
            
            this.addVideoHighlight(iframe);
        }
    }
    
    activateYouTubeVideo(iframe) {
        let currentSrc = iframe.src;
        let srcModified = false;
        
        // Activate autoplay if not activated
        if (currentSrc.includes('autoplay=0')) {
            currentSrc = currentSrc.replace('autoplay=0', 'autoplay=1');
            srcModified = true;
            console.log('🎬 Autoplay activated for YouTube');
        } else if (!currentSrc.includes('autoplay=1')) {
            currentSrc += (currentSrc.includes('?') ? '&' : '?') + 'autoplay=1';
            srcModified = true;
            console.log('🎬 Autoplay added for YouTube');
        }
        
        // Activate audio (change mute=1 to mute=0)
        if (currentSrc.includes('mute=1')) {
            currentSrc = currentSrc.replace('mute=1', 'mute=0');
            srcModified = true;
            console.log('🎬 Audio activated for YouTube');
        }
        
        // Only reload iframe if there were changes
        if (srcModified) {
            iframe.src = currentSrc;
            console.log('🎬 YouTube iframe updated:', currentSrc);
            this.showVideoActivatedNotification('¡Video de YouTube reproduciéndose automáticamente! 🎬');
        } else {
            this.showVideoActivatedNotification('Video de YouTube listo - debería reproducirse automáticamente');
        }
    }
    
    activateVimeoVideo(iframe) {
        let currentSrc = iframe.src;
        let srcModified = false;
        
        // Activate autoplay if not activated
        if (currentSrc.includes('autoplay=0')) {
            currentSrc = currentSrc.replace('autoplay=0', 'autoplay=1');
            srcModified = true;
            console.log('🎬 Autoplay activated for Vimeo');
        } else if (!currentSrc.includes('autoplay=1')) {
            currentSrc += (currentSrc.includes('?') ? '&' : '?') + 'autoplay=1';
            srcModified = true;
            console.log('🎬 Autoplay added for Vimeo');
        }
        
        if (srcModified) {
            iframe.src = currentSrc;
            console.log('🎬 Vimeo iframe updated:', currentSrc);
            this.showVideoActivatedNotification('¡Video de Vimeo reproduciéndose automáticamente! 🎬');
        } else {
            this.showVideoActivatedNotification('Video de Vimeo listo - debería reproducirse automáticamente');
        }
    }
    
    activateGenericVideo() {
        console.log('🎬 Generic video type not specifically handled:', this.videoConfig.type);
        
        // Look for any video element on the page
        const videoElement = document.querySelector('video') || 
                           document.querySelector('#video-iframe') || 
                           document.querySelector('iframe[src*="soundcloud"]') ||
                           document.querySelector('iframe');
        
        if (videoElement) {
            console.log('🎬 Video element found, scrolling');
            
            // Scroll to found element
            videoElement.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center',
                inline: 'nearest'
            });
            
            this.showVideoActivatedNotification('Video listo - ve más abajo para reproducir');
        } else {
            this.showVideoActivatedNotification('Video disponible - busca el reproductor en la página');
        }
    }
    
    addVideoHighlight(iframe) {
        // Visual effect to draw attention
        iframe.style.cssText += `
            border: 3px solid #8B5CF6 !important;
            border-radius: 12px !important;
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.5) !important;
            transform: scale(1.02);
            transition: all 0.6s ease;
        `;
        
        // Remove effects after 4 seconds
        setTimeout(() => {
            iframe.style.cssText += `
                border: none !important;
                box-shadow: none !important;
                transform: scale(1);
            `;
        }, 4000);
    }
    
    updateAudioUIForStreamingService(isPlaying, statusMessage) {
        const audioIcon = document.getElementById('main-audio-icon');
        const audioStatus = document.getElementById('audio-status');
        const audioEqualizer = document.getElementById('audio-equalizer');
        const mainAudioToggle = document.getElementById('main-audio-toggle');
        
        if (audioIcon) {
            audioIcon.className = isPlaying ? 'fas fa-pause text-2xl' : 'fas fa-play text-2xl';
        }
        
        if (audioStatus) {
            audioStatus.textContent = statusMessage || (isPlaying ? '♪ Reproduciendo...' : 'Haz clic para reproducir');
        }
        
        if (audioEqualizer) {
            if (isPlaying) {
                audioEqualizer.classList.remove('hidden');
            } else {
                audioEqualizer.classList.add('hidden');
            }
        }
        
        if (mainAudioToggle) {
            mainAudioToggle.classList.remove('animate-pulse');
        }
    }
    
    setupStreamingServiceControl(serviceType, iframe) {
        const mainAudioToggle = document.getElementById('main-audio-toggle');
        let isPlaying = true; // Assume playing after overlay
        
        if (mainAudioToggle) {
            // Remove existing listeners
            const newToggle = mainAudioToggle.cloneNode(true);
            mainAudioToggle.parentNode.replaceChild(newToggle, mainAudioToggle);
            
            newToggle.addEventListener('click', (e) => {
                e.preventDefault();
                console.log(`Main button clicked for ${serviceType}`);
                
                // For streaming services, toggle visual state
                // Real control is done by user in iframe
                isPlaying = !isPlaying;
                
                if (isPlaying) {
                    this.updateAudioUIForStreamingService(true, this.getStreamingMessage(serviceType, true));
                } else {
                    this.updateAudioUIForStreamingService(false, this.getStreamingMessage(serviceType, false));
                }
            });
        }
    }
    
    getStreamingMessage(serviceType, isPlaying) {
        const messages = {
            'youtube_music': {
                playing: '♪ YouTube Music - Usa los controles del reproductor ♪',
                paused: 'YouTube Music - Haz clic en ▶️ para reproducir'
            },
            'spotify': {
                playing: '♪ Spotify - Usa los controles del reproductor ♪',
                paused: 'Spotify - Haz clic en ▶️ para reproducir'
            },
            'soundcloud': {
                playing: '♪ SoundCloud - Usa los controles del reproductor ♪',
                paused: 'SoundCloud - Haz clic en ▶️ para reproducir'
            }
        };
        
        const serviceMessages = messages[serviceType] || messages['youtube_music'];
        return isPlaying ? serviceMessages.playing : serviceMessages.paused;
    }
    
    showAudioActivatedNotification() {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 z-40 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300';
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-music mr-2"></i>
                <span>¡Música activada! 🎵</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    showVideoActivatedNotification(customMessage = null) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 z-40 bg-purple-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300';
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-play mr-2"></i>
                <span>${customMessage || '¡Video activado! 🎬'}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Remove after 4 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }
}

// Export for use in gift.blade.php
window.AudioOverlaySystem = AudioOverlaySystem;

// Auto-initialize if hidden inputs are present
document.addEventListener('DOMContentLoaded', function() {
    const audioType = document.getElementById('audio-type')?.value;
    const videoType = document.getElementById('video-type')?.value;
    
    // Only auto-initialize if we have audio or video data and no existing instance
    if ((audioType || videoType) && !window.audioOverlaySystemInstance) {
        console.log('🎵 Initializing Audio Overlay System from hidden inputs');
        window.audioOverlaySystemInstance = new AudioOverlaySystem();
    } else if (audioType || videoType) {
        console.log('🎵 Audio/Video data found but system already initialized');
    } else {
        console.log('🎵 No audio/video data found in hidden inputs');
    }
    
    // Debug information
    if (audioType || videoType) {
        console.log('🎵 Audio config:', { type: audioType, hasUrl: !!document.getElementById('audio-url')?.value });
        console.log('🎬 Video config:', { type: videoType, hasUrl: !!document.getElementById('video-url')?.value });
    }
});