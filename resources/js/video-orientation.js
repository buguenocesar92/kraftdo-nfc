/**
 * Video orientation detection and handling
 */
class VideoOrientationHandler {
    constructor() {
        this.init();
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupVideoHandlers());
        } else {
            this.setupVideoHandlers();
        }
    }

    setupVideoHandlers() {
        const videos = document.querySelectorAll('video[data-orientation-detect="true"]');
        
        videos.forEach(video => {
            video.addEventListener('loadedmetadata', () => {
                this.handleVideoOrientation(video);
            });

            // Also handle if metadata is already loaded
            if (video.readyState >= 1) {
                this.handleVideoOrientation(video);
            }
        });
    }

    handleVideoOrientation(video) {
        const width = video.videoWidth;
        const height = video.videoHeight;
        
        if (width && height) {
            // Remove existing orientation classes
            video.classList.remove('portrait', 'landscape', 'square');
            
            // Add appropriate orientation class
            if (height > width) {
                video.classList.add('portrait');
                video.setAttribute('data-orientation', 'portrait');
            } else if (width > height) {
                video.classList.add('landscape');  
                video.setAttribute('data-orientation', 'landscape');
            } else {
                video.classList.add('square');
                video.setAttribute('data-orientation', 'square');
            }

            // Trigger custom event for other scripts to listen to
            const orientationEvent = new CustomEvent('videoOrientationDetected', {
                detail: {
                    video: video,
                    width: width,
                    height: height,
                    orientation: video.getAttribute('data-orientation')
                }
            });
            
            document.dispatchEvent(orientationEvent);
        }
    }

    // Static method to manually trigger orientation detection
    static detectOrientation(video) {
        const handler = new VideoOrientationHandler();
        handler.handleVideoOrientation(video);
    }
}

// Initialize the handler
const videoOrientationHandler = new VideoOrientationHandler();

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = VideoOrientationHandler;
}

// Global function for backward compatibility
window.detectVideoOrientation = function(video) {
    VideoOrientationHandler.detectOrientation(video);
};