/**
 * Video Orientation Support for Public Views
 * Shared JavaScript for detecting and applying video orientations
 */

// Main initialization function
function initializeVideoOrientation() {
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🎬 Initializing video orientation support for public views');
        
        // Setup video orientation for all public videos
        const videoContainers = document.querySelectorAll('.video-container-public');
        
        videoContainers.forEach(container => {
            const videoElement = container.querySelector('video[data-video-enhanced="true"]');
            if (videoElement) {
                console.log('🎬 Setting up orientation for public video:', container.id);
                setupPublicVideoOrientation(videoElement, container);
            }
        });
    });
}

// Setup orientation for a specific video
function setupPublicVideoOrientation(videoElement, containerElement) {
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

// Auto-initialize when script loads
initializeVideoOrientation();