/**
 * Application Initializer
 * Handles global initialization of all JavaScript modules based on hidden input data
 */

class AppInitializer {
    constructor() {
        this.initializedModules = new Set();
        this.init();
    }

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initializeModules());
        } else {
            this.initializeModules();
        }
    }

    initializeModules() {
        console.log('🚀 App Initializer: Starting module initialization');
        
        // Initialize Audio Overlay System
        this.initializeAudioOverlaySystem();
        
        // Initialize QR Code Generator
        this.initializeQRCodeGenerator();
        
        // Initialize Streaming Controls
        this.initializeStreamingControls();
        
        // Initialize Gallery Modal
        this.initializeGalleryModal();
        
        // Initialize Video Orientation System
        this.initializeVideoOrientationSystem();
        
        // Initialize Design Preview
        this.initializeDesignPreview();
        
        console.log('🚀 App Initializer: Completed initialization for modules:', Array.from(this.initializedModules));
    }

    initializeAudioOverlaySystem() {
        const audioType = document.getElementById('audio-type')?.value;
        const videoType = document.getElementById('video-type')?.value;
        
        if ((audioType || videoType) && window.AudioOverlaySystem && !window.audioOverlaySystemInstance) {
            try {
                window.audioOverlaySystemInstance = new window.AudioOverlaySystem();
                this.initializedModules.add('AudioOverlaySystem');
                console.log('✅ AudioOverlaySystem initialized');
            } catch (error) {
                console.error('❌ Failed to initialize AudioOverlaySystem:', error);
            }
        }
    }

    initializeQRCodeGenerator() {
        const qrUrl = document.getElementById('qr-url')?.value;
        const qrContainer = document.getElementById('qrcode');
        
        if ((qrUrl || qrContainer?.getAttribute('data-url')) && window.QRCodeGenerator && !window.qrCodeGeneratorInstance) {
            try {
                window.qrCodeGeneratorInstance = new window.QRCodeGenerator();
                this.initializedModules.add('QRCodeGenerator');
                console.log('✅ QRCodeGenerator initialized');
            } catch (error) {
                console.error('❌ Failed to initialize QRCodeGenerator:', error);
            }
        }
    }

    initializeStreamingControls() {
        const audioType = document.getElementById('audio-type')?.value;
        const audioAutoplay = document.getElementById('audio-autoplay')?.value === '1';
        
        if (audioType && audioAutoplay && window.StreamingControls && !window.streamingControlsInstance) {
            try {
                const audioUrl = document.getElementById('audio-url')?.value || '';
                window.streamingControlsInstance = new window.StreamingControls({
                    audio: { type: audioType, url: audioUrl, autoplay: audioAutoplay },
                    hasAutoplay: true
                });
                this.initializedModules.add('StreamingControls');
                console.log('✅ StreamingControls initialized');
            } catch (error) {
                console.error('❌ Failed to initialize StreamingControls:', error);
            }
        }
    }

    initializeGalleryModal() {
        const galleryImagesData = document.getElementById('gallery-images')?.value;
        
        if (galleryImagesData && window.GalleryModalAdvanced && !window.galleryModalInstance) {
            try {
                const galleryImages = JSON.parse(galleryImagesData);
                if (galleryImages.length > 0) {
                    window.galleryModalInstance = new window.GalleryModalAdvanced(galleryImages);
                    this.initializedModules.add('GalleryModalAdvanced');
                    console.log('✅ GalleryModalAdvanced initialized');
                }
            } catch (error) {
                console.error('❌ Failed to initialize GalleryModalAdvanced:', error);
            }
        }
    }

    initializeVideoOrientationSystem() {
        const videoElements = document.querySelectorAll('video[data-video-enhanced="true"]');
        
        if (videoElements.length > 0 && window.VideoOrientationSystem && !window.videoOrientationSystemInstance) {
            try {
                window.videoOrientationSystemInstance = new window.VideoOrientationSystem();
                this.initializedModules.add('VideoOrientationSystem');
                console.log('✅ VideoOrientationSystem initialized');
            } catch (error) {
                console.error('❌ Failed to initialize VideoOrientationSystem:', error);
            }
        }
    }

    initializeDesignPreview() {
        const previewContainer = document.querySelector('.design-preview');
        const colorInputs = document.querySelectorAll('input[type="color"]');
        
        if ((previewContainer || colorInputs.length > 0) && window.DesignPreview && !window.designPreviewInstance) {
            try {
                window.designPreviewInstance = new window.DesignPreview();
                this.initializedModules.add('DesignPreview');
                console.log('✅ DesignPreview initialized');
            } catch (error) {
                console.error('❌ Failed to initialize DesignPreview:', error);
            }
        }
    }

    // Method to manually initialize a specific module
    initializeModule(moduleName) {
        const initMethod = `initialize${moduleName}`;
        if (typeof this[initMethod] === 'function') {
            this[initMethod]();
        } else {
            console.warn(`Unknown module: ${moduleName}`);
        }
    }

    // Method to check if a module is initialized
    isModuleInitialized(moduleName) {
        return this.initializedModules.has(moduleName);
    }

    // Method to get list of initialized modules
    getInitializedModules() {
        return Array.from(this.initializedModules);
    }
}

// Create global instance
window.appInitializer = new AppInitializer();

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AppInitializer;
}