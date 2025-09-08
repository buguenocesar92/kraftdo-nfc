/**
 * Streaming Controls
 * Handles audio playback controls for various streaming services
 * 
 * Supports:
 * - Spotify integration
 * - YouTube Music
 * - SoundCloud
 * - Direct audio files
 * - Apple Music (future enhancement)
 */

class StreamingControls {
    constructor(config) {
        this.audioConfig = config.audio || {};
        this.hasAutoplay = config.hasAutoplay || false;
        this.init();
    }
    
    init() {
        if (this.hasAutoplay) {
            this.setupUniversalAutoplay();
        }
        
        this.setupAudioControls();
    }
    
    setupUniversalAutoplay() {
        console.log('🎵 Setting up universal autoplay for:', this.audioConfig.type);
        
        // Auto-activation on scroll
        let scrollActivated = false;
        window.addEventListener('scroll', () => {
            if (!scrollActivated && window.scrollY > 50) {
                scrollActivated = true;
                setTimeout(() => {
                    this.startAutoplay();
                }, 800);
            }
        });
        
        // Initialize system when ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                console.log('🎵 Streaming controls system loaded');
            });
        } else {
            console.log('🎵 Streaming controls system loaded');
        }
    }
    
    setupAudioControls() {
        // Setup file upload audio controls if present
        if (this.audioConfig.type === 'file_upload') {
            this.setupFileUploadControls();
        }
    }
    
    setupFileUploadControls() {
        document.addEventListener('DOMContentLoaded', () => {
            const audio = document.getElementById('background-audio');
            const toggleBtn = document.getElementById('main-audio-toggle');
            
            if (audio && toggleBtn) {
                audio.volume = 0.3;
                
                // Initial event listener - will be replaced by overlay system if overlay is present
                toggleBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    console.log('Main audio button clicked (fallback)');
                    
                    if (audio.paused) {
                        audio.play().catch(console.error);
                    } else {
                        audio.pause();
                    }
                });
                
                // Handle audio ended event
                audio.addEventListener('ended', () => {
                    if (!audio.loop) {
                        const audioIcon = document.getElementById('main-audio-icon');
                        const equalizer = document.getElementById('audio-equalizer');
                        const audioStatus = document.getElementById('audio-status');
                        
                        if (audioIcon) audioIcon.className = 'fas fa-play text-2xl';
                        if (equalizer) equalizer.classList.add('hidden');
                        if (audioStatus) audioStatus.textContent = 'Haz clic para reproducir';
                    }
                });
            }
        });
    }
    
    scrollToPlayer() {
        console.log('🎵 Scrolling to player...');
        
        let player = null;
        
        // Find player by type
        switch (this.audioConfig.type) {
            case 'spotify':
                player = document.querySelector('iframe[src*="spotify.com"]');
                break;
            case 'youtube_music':
                player = document.querySelector('iframe[src*="youtube.com"]');
                break;
            case 'soundcloud':
                player = document.querySelector('iframe[src*="soundcloud.com"]');
                break;
            case 'direct':
                player = document.querySelector('audio');
                break;
        }
        
        if (player) {
            // Smooth scroll to player
            player.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center',
                inline: 'center'
            });
            
            // Visual highlight effect
            player.style.cssText += `
                border: 4px solid #8B5CF6 !important;
                border-radius: 12px !important;
                box-shadow: 0 0 30px rgba(139, 92, 246, 0.6) !important;
                transform: scale(1.05);
                transition: all 0.8s ease;
            `;
            
            // Show service-specific message
            let message = '';
            switch (this.audioConfig.type) {
                case 'spotify':
                    message = '🎵 Haz clic en ▶️ en el reproductor de Spotify';
                    break;
                case 'youtube_music':
                    message = '🎵 Haz clic en ▶️ en YouTube Music para reproducir';
                    break;
                case 'soundcloud':
                    message = '🎵 Haz clic en ▶️ en SoundCloud para reproducir';
                    break;
                case 'direct':
                    message = '🎵 ¡Audio directo - puede reproducirse automáticamente!';
                    break;
            }
            
            this.showNotification(message, 'info');
            
            // Remove effects after 6 seconds
            setTimeout(() => {
                player.style.cssText += `
                    border: none !important;
                    box-shadow: none !important;
                    transform: scale(1);
                `;
            }, 6000);
        } else {
            this.showNotification('❌ No se encontró el reproductor', 'error');
        }
    }
    
    startAutoplay() {
        console.log('🎵 Starting autoplay system for:', this.audioConfig.type);
        
        switch (this.audioConfig.type) {
            case 'youtube_music':
                // YouTube Music has autoplay limitations
                break;
            case 'soundcloud':
                setTimeout(() => {
                    this.showNotification('🎵 SoundCloud listo. Es posible que requiera clic para reproducir', 'info');
                }, 1500);
                break;
            case 'direct':
                this.setupDirectAudioAutoplay();
                break;
            case 'spotify':
                setTimeout(() => {
                    this.showNotification('🎵 Spotify listo. Haz clic en ▶️ para reproducir', 'info');
                }, 2000);
                break;
        }
    }
    
    setupDirectAudioAutoplay() {
        let interactionEvents = ['click', 'scroll', 'touchstart', 'keydown'];
        
        const tryDirectAutoplay = () => {
            const audio = document.querySelector('audio');
            if (audio && audio.paused) {
                audio.play()
                    .then(() => {
                        this.showNotification('🎵 ¡Audio reproduciéndose automáticamente!', 'success');
                        // Remove listeners after successful playback
                        interactionEvents.forEach(event => {
                            document.removeEventListener(event, tryDirectAutoplay);
                        });
                    })
                    .catch(() => {
                        console.log('🎵 Autoplay blocked, waiting for more interaction');
                        this.showNotification('🎵 Haz clic en el reproductor para iniciar', 'info');
                    });
            }
        };
        
        // Listen for any interaction to activate
        interactionEvents.forEach(event => {
            document.addEventListener(event, tryDirectAutoplay, { once: true });
        });
        
        // Show initial message
        setTimeout(() => {
            this.showNotification('🎵 ¡Audio directo - verdadero autoplay disponible!', 'success');
        }, 1000);
    }
    
    showNotification(text, type = 'info') {
        // Remove previous notification
        const previous = document.getElementById('audio-notification');
        if (previous) previous.remove();
        
        // Colors by type
        const colors = {
            'success': 'linear-gradient(135deg, #10b981, #059669)',
            'info': 'linear-gradient(135deg, #3b82f6, #1d4ed8)',
            'error': 'linear-gradient(135deg, #ef4444, #dc2626)'
        };
        
        // Create notification
        const notification = document.createElement('div');
        notification.id = 'audio-notification';
        notification.innerHTML = `
            <div style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${colors[type] || colors.info};
                color: white;
                padding: 16px 24px;
                border-radius: 12px;
                box-shadow: 0 8px 24px rgba(0,0,0,0.2);
                z-index: 9999;
                font-family: system-ui;
                font-size: 14px;
                font-weight: 500;
                transform: translateX(100%);
                transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                max-width: 320px;
                backdrop-filter: blur(10px);
            ">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-music" style="margin-right: 10px; font-size: 16px;"></i>
                    <span>${text}</span>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate entry
        setTimeout(() => {
            notification.firstElementChild.style.transform = 'translateX(0)';
        }, 100);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.firstElementChild.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 400);
        }, 5000);
    }
}

// Export for use in gift.blade.php
window.StreamingControls = StreamingControls;