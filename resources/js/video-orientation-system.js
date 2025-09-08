/**
 * Video Orientation System
 * Handles video container sizing and orientation detection
 * 
 * Features:
 * - Automatic orientation detection (landscape, portrait, square)
 * - Responsive video container sizing
 * - Loading state management
 * - Mobile-optimized layouts
 */

class VideoOrientationSystem {
    constructor() {
        this.videoContainers = [];
        this.init();
    }
    
    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.setupVideoOrientations();
        });
    }
    
    setupVideoOrientations() {
        // Setup video orientation for all public videos
        const videoContainers = document.querySelectorAll('.video-container-public');
        
        videoContainers.forEach(container => {
            const videoElement = container.querySelector('video[data-video-enhanced="true"]');
            if (videoElement) {
                console.log('🎬 Setting up orientation for public video:', container.id);
                this.setupPublicVideoOrientation(videoElement, container);
                this.videoContainers.push({ container, videoElement });
            }
        });
        
        console.log('🎬 Video orientation system initialized for', this.videoContainers.length, 'videos');
    }
    
    setupPublicVideoOrientation(videoElement, containerElement) {
        const applyOrientation = () => {
            const videoWidth = videoElement.videoWidth;
            const videoHeight = videoElement.videoHeight;
            
            console.log('🎬 Public video dimensions:', { 
                width: videoWidth, 
                height: videoHeight,
                container: containerElement.id 
            });
            
            if (videoWidth && videoHeight) {
                const aspectRatio = videoWidth / videoHeight;
                
                // Remove existing orientation classes
                containerElement.classList.remove('landscape', 'portrait', 'square', 'loading');
                
                if (aspectRatio > 1.3) {
                    // Horizontal/Landscape video
                    containerElement.classList.add('landscape');
                    console.log('🎬 Applied landscape orientation to public video');
                } else if (aspectRatio < 0.8) {
                    // Vertical/Portrait video
                    containerElement.classList.add('portrait');
                    console.log('🎬 Applied portrait orientation to public video');
                } else {
                    // Square or near-square video
                    containerElement.classList.add('square');
                    console.log('🎬 Applied square orientation to public video');
                }
                
                // Add data attributes for CSS targeting
                containerElement.dataset.aspectRatio = aspectRatio.toFixed(2);
                containerElement.dataset.orientation = aspectRatio > 1.3 ? 'landscape' : 
                                                      aspectRatio < 0.8 ? 'portrait' : 'square';
                                                      
                // Also add to video element
                videoElement.dataset.aspectRatio = aspectRatio.toFixed(2);
                videoElement.dataset.orientation = containerElement.dataset.orientation;
            }
        };
        
        // Apply orientation when metadata is loaded
        if (videoElement.videoWidth && videoElement.videoHeight) {
            // Video dimensions already available
            applyOrientation();
        } else {
            // Wait for metadata to load
            const onLoadedMetadata = () => {
                console.log('🎬 Public video metadata loaded, applying orientation');
                applyOrientation();
                videoElement.removeEventListener('loadedmetadata', onLoadedMetadata);
            };
            
            videoElement.addEventListener('loadedmetadata', onLoadedMetadata);
            
            // Fallback timeout
            setTimeout(() => {
                if (!videoElement.videoWidth || !videoElement.videoHeight) {
                    console.warn('🎬 Public video dimensions not available after timeout, using default');
                    containerElement.classList.remove('loading');
                    containerElement.classList.add('landscape'); // Default fallback
                }
            }, 5000);
        }
    }
    
    // Method to manually apply orientation to a video
    applyOrientationToVideo(videoElement, containerElement) {
        this.setupPublicVideoOrientation(videoElement, containerElement);
    }
    
    // Method to get video orientation info
    getVideoInfo(containerElement) {
        return {
            aspectRatio: containerElement.dataset.aspectRatio,
            orientation: containerElement.dataset.orientation,
            classes: Array.from(containerElement.classList)
        };
    }
    
    // Method to update all video orientations (useful for dynamic content)
    refreshAllOrientations() {
        this.videoContainers.forEach(({ container, videoElement }) => {
            this.setupPublicVideoOrientation(videoElement, container);
        });
    }
    
    // Method to add new video container dynamically
    addVideoContainer(containerElement, videoElement) {
        this.setupPublicVideoOrientation(videoElement, containerElement);
        this.videoContainers.push({ container: containerElement, videoElement });
    }
    
    // Method to remove video container
    removeVideoContainer(containerElement) {
        this.videoContainers = this.videoContainers.filter(
            ({ container }) => container !== containerElement
        );
    }
    
    // Static method to inject CSS styles
    static injectStyles() {
        const styles = `
            <style>
            .video-container-public {
                position: relative;
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
                border-radius: 0.5rem;
                overflow: hidden;
                background-color: #000;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 200px;
            }

            .video-player-public {
                width: 100%;
                height: auto;
                max-height: 70vh;
                object-fit: contain;
                border-radius: 0.5rem;
            }

            /* Horizontal videos (landscape) */
            .video-container-public.landscape {
                aspect-ratio: 16/9;
                max-height: 400px;
            }

            .video-container-public.landscape .video-player-public {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            /* Vertical videos (portrait) */
            .video-container-public.portrait {
                max-width: 400px;
                max-height: 70vh;
            }

            .video-container-public.portrait .video-player-public {
                width: 100%;
                height: auto;
                max-height: 70vh;
                object-fit: contain;
            }

            /* Square videos */
            .video-container-public.square {
                aspect-ratio: 1/1;
                max-width: 400px;
                max-height: 400px;
            }

            .video-container-public.square .video-player-public {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            /* Mobile responsive */
            @media (max-width: 768px) {
                .video-container-public {
                    max-width: 100%;
                }
                
                .video-container-public.portrait {
                    max-width: 100%;
                    max-height: 60vh;
                }
                
                .video-container-public.landscape {
                    max-height: 300px;
                }
            }

            /* Loading state */
            .video-container-public.loading {
                background: linear-gradient(45deg, #f0f0f0 25%, transparent 25%), 
                            linear-gradient(-45deg, #f0f0f0 25%, transparent 25%),
                            linear-gradient(45deg, transparent 75%, #f0f0f0 75%), 
                            linear-gradient(-45deg, transparent 75%, #f0f0f0 75%);
                background-size: 20px 20px;
                background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            }
            </style>
        `;
        
        document.head.insertAdjacentHTML('beforeend', styles);
    }
}

// Auto-inject styles when script loads
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        VideoOrientationSystem.injectStyles();
    });
} else {
    VideoOrientationSystem.injectStyles();
}

// Export for use in gift.blade.php
window.VideoOrientationSystem = VideoOrientationSystem;