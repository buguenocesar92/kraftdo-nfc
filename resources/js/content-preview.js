/**
 * Content Preview JavaScript
 * Handles background audio control and video enhancements for preview pages
 */

// Background Audio Control for Preview
function setupBackgroundAudioControl() {
    const audio = document.getElementById('background-audio');
    const mainToggleBtn = document.getElementById('main-audio-toggle');
    const mainAudioIcon = document.getElementById('main-audio-icon');
    const equalizer = document.getElementById('audio-equalizer');
    const audioStatus = document.getElementById('audio-status');
    
    if (!audio || !mainToggleBtn) return;
    
    let isPlaying = false;
    let hasTriedAutoplay = false;

    // Set initial audio properties
    audio.volume = 0.3;
    
    // Function to start audio
    function startAudio() {
        console.log('Attempting to start audio...');
        return audio.play().then(() => {
            isPlaying = true;
            if (mainAudioIcon) mainAudioIcon.className = 'fas fa-pause text-2xl';
            if (equalizer) equalizer.classList.remove('hidden');
            if (audioStatus) audioStatus.textContent = '♪ Reproduciendo tu canción especial ♪';
            console.log('Audio started successfully');
        }).catch(error => {
            console.log('Audio play failed:', error.message);
            isPlaying = false;
            if (mainAudioIcon) mainAudioIcon.className = 'fas fa-play text-2xl';
            if (equalizer) equalizer.classList.add('hidden');
            if (audioStatus) audioStatus.textContent = 'Haz clic para reproducir';
        });
    }

    // Function to stop audio
    function stopAudio() {
        audio.pause();
        audio.currentTime = 0;
        isPlaying = false;
        if (mainAudioIcon) mainAudioIcon.className = 'fas fa-play text-2xl';
        if (equalizer) equalizer.classList.add('hidden');
        if (audioStatus) audioStatus.textContent = 'Haz clic para reproducir';
    }

    // Toggle audio playback
    if (mainToggleBtn) {
        mainToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Main audio toggle clicked, isPlaying:', isPlaying);
            
            if (isPlaying) {
                stopAudio();
            } else {
                startAudio();
            }
        });
    }

    // Function to try autoplay
    function tryAutoplay() {
        if (!hasTriedAutoplay) {
            hasTriedAutoplay = true;
            console.log('Trying autoplay...');
            startAudio();
        }
    }

    // Auto-start on valid user interactions only
    const validInteractions = ['click', 'keydown', 'touchstart'];
    
    function handleValidInteraction(e) {
        console.log('Valid user interaction detected:', e.type);
        startAudio().then(() => {
            if (isPlaying) {
                // Remove all event listeners after successful start
                validInteractions.forEach(event => {
                    document.removeEventListener(event, handleValidInteraction);
                });
            }
        });
    }

    // Add listeners for valid interaction types only
    validInteractions.forEach(event => {
        document.addEventListener(event, handleValidInteraction, { once: true });
    });

    // Show message to user to click if autoplay fails
    setTimeout(() => {
        if (!isPlaying) {
            console.log('Autoplay not working - user needs to click');
            // Show message
            const message = document.getElementById('audio-message');
            if (message) {
                message.classList.remove('hidden');
                // Hide message after 5 seconds
                setTimeout(() => {
                    message.classList.add('hidden');
                }, 5000);
            }
        }
    }, 1000);

    // Handle audio events
    audio.addEventListener('loadstart', function() {
        console.log('Audio loading started');
    });

    audio.addEventListener('canplaythrough', function() {
        console.log('Audio can play through');
        if (!hasTriedAutoplay) {
            tryAutoplay();
        }
    });

    // Handle audio errors
    audio.addEventListener('error', function(e) {
        console.error('Audio error:', e);
        if (mainAudioIcon) {
            mainAudioIcon.className = 'fas fa-exclamation-triangle text-lg text-red-500';
        }
    });

    // Debug: log audio element details
    console.log('Audio element:', audio);
    console.log('Audio src:', audio.src);
}

// Enhanced Video Monitoring for Preview
function setupVideoEnhancements(videoElement) {
    let stallCount = 0;
    let lastCurrentTime = 0;
    let recoveryAttempts = 0;
    const maxRecoveryAttempts = 3;
    
    // Setup orientation detection for preview videos
    setupVideoOrientationForPreview(videoElement);
    
    // Monitor for stalls during playback
    let stallMonitor = null;
    
    const startStallMonitoring = () => {
        if (stallMonitor) clearInterval(stallMonitor);
        
        stallMonitor = setInterval(() => {
            if (!videoElement.paused && !videoElement.ended) {
                const currentTime = videoElement.currentTime;
                
                if (currentTime === lastCurrentTime) {
                    stallCount++;
                    console.warn(`📼 Video stall detected in preview (count: ${stallCount})`);
                    
                    if (stallCount >= 3) {
                        console.error('📼 Video appears stuck in preview, attempting recovery');
                        attemptVideoRecovery();
                    }
                } else {
                    stallCount = 0;
                }
                
                lastCurrentTime = currentTime;
            }
        }, 2000);
    };
    
    const stopStallMonitoring = () => {
        if (stallMonitor) {
            clearInterval(stallMonitor);
            stallMonitor = null;
        }
    };
    
    const attemptVideoRecovery = () => {
        if (recoveryAttempts >= maxRecoveryAttempts) {
            console.error('📼 Max recovery attempts reached in preview');
            return;
        }
        
        recoveryAttempts++;
        console.log(`📼 Attempting video recovery in preview (attempt ${recoveryAttempts})`);
        
        const currentTime = videoElement.currentTime;
        const wasPlaying = !videoElement.paused;
        
        // Reload video
        const originalSrc = videoElement.src;
        videoElement.src = '';
        videoElement.load();
        
        setTimeout(() => {
            videoElement.src = originalSrc;
            videoElement.load();
            
            videoElement.addEventListener('canplay', function onCanPlay() {
                videoElement.removeEventListener('canplay', onCanPlay);
                
                // Restore position
                if (currentTime > 0) {
                    videoElement.currentTime = currentTime;
                }
                
                // Resume playing if needed
                if (wasPlaying) {
                    videoElement.play().catch(e => {
                        console.error('📼 Failed to resume playback in preview:', e);
                    });
                }
            });
        }, 1000);
        
        stallCount = 0;
    };
    
    // Enhanced event listeners
    videoElement.addEventListener('play', () => {
        console.log('📼 Preview video: Play started');
        startStallMonitoring();
    });
    
    videoElement.addEventListener('pause', () => {
        console.log('📼 Preview video: Paused');
        stopStallMonitoring();
    });
    
    videoElement.addEventListener('waiting', () => {
        console.log('📼 Preview video: Waiting for data');
        
        // Set timeout for recovery if waiting too long
        setTimeout(() => {
            if (videoElement.readyState < 3) {
                console.warn('📼 Preview video still waiting, attempting recovery');
                attemptVideoRecovery();
            }
        }, 10000);
    });
    
    videoElement.addEventListener('error', (e) => {
        console.error('📼 Preview video error:', e);
        const errorCode = videoElement.error?.code;
        
        // Attempt recovery for network errors
        if (errorCode === 2) {
            setTimeout(() => {
                attemptVideoRecovery();
            }, 2000);
        }
    });
    
    videoElement.addEventListener('stalled', () => {
        console.warn('📼 Preview video: Network stalled');
        setTimeout(() => {
            if (videoElement.readyState < 3) {
                attemptVideoRecovery();
            }
        }, 5000);
    });
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        stopStallMonitoring();
    });
}

function setupVideoOrientationForPreview(videoElement) {
    console.log('🎬 Setting up video orientation for preview');
    
    const applyOrientation = () => {
        const videoWidth = videoElement.videoWidth;
        const videoHeight = videoElement.videoHeight;
        
        console.log('🎬 Preview video dimensions:', { width: videoWidth, height: videoHeight });
        
        if (videoWidth && videoHeight) {
            const aspectRatio = videoWidth / videoHeight;
            
            // Apply dynamic styles based on orientation
            if (aspectRatio > 1.3) {
                // Landscape video
                videoElement.style.width = '100%';
                videoElement.style.height = 'auto';
                videoElement.style.maxHeight = '400px';
                videoElement.style.objectFit = 'contain';
                console.log('🎬 Applied landscape styling to preview video');
            } else if (aspectRatio < 0.8) {
                // Portrait video
                videoElement.style.width = 'auto';
                videoElement.style.height = '60vh';
                videoElement.style.maxWidth = '400px';
                videoElement.style.objectFit = 'contain';
                videoElement.style.margin = '0 auto';
                videoElement.style.display = 'block';
                console.log('🎬 Applied portrait styling to preview video');
            } else {
                // Square video
                videoElement.style.width = '400px';
                videoElement.style.height = '400px';
                videoElement.style.objectFit = 'contain';
                videoElement.style.margin = '0 auto';
                videoElement.style.display = 'block';
                console.log('🎬 Applied square styling to preview video');
            }
            
            // Add data attributes
            videoElement.dataset.aspectRatio = aspectRatio.toFixed(2);
            videoElement.dataset.orientation = aspectRatio > 1.3 ? 'landscape' : 
                                              aspectRatio < 0.8 ? 'portrait' : 'square';
        }
    };
    
    // Apply orientation when metadata loads
    if (videoElement.videoWidth && videoElement.videoHeight) {
        applyOrientation();
    } else {
        videoElement.addEventListener('loadedmetadata', () => {
            console.log('🎬 Preview video metadata loaded, applying orientation');
            applyOrientation();
        });
    }
}

// Initialize content preview
function initContentPreview() {
    document.addEventListener('DOMContentLoaded', function() {
        // Setup background audio control if available
        setupBackgroundAudioControl();
        
        // Find all video elements and enhance them
        const videoElements = document.querySelectorAll('video[data-video-enhanced="true"]');
        
        videoElements.forEach(videoElement => {
            console.log('🎬 Enhancing video element in preview:', videoElement);
            setupVideoEnhancements(videoElement);
        });
    });
}

// Auto-initialize
initContentPreview();