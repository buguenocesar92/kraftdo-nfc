/**
 * Multimedia system for handling audio/video playback and gallery modals
 */
class MultimediaSystem {
    constructor() {
        this.audioOverlay = null;
        this.currentAudio = null;
        this.galleryModal = null;
        this.currentGalleryIndex = 0;
        this.galleryImages = [];
        this.init();
    }

    init() {
        this.createAudioOverlay();
        this.createGalleryModal();
        this.setupEventListeners();
    }

    createAudioOverlay() {
        this.audioOverlay = document.createElement('div');
        this.audioOverlay.className = 'audio-overlay hidden';
        this.audioOverlay.innerHTML = `
            <div class="audio-content">
                <button class="audio-close-btn" onclick="multimediaSystem.closeAudio()">&times;</button>
                <div class="audio-title">Reproduciendo Audio</div>
                <div class="audio-description">Toca para cerrar cuando termine</div>
                <audio class="audio-player" controls autoplay>
                    Tu navegador no soporta el elemento de audio.
                </audio>
            </div>
        `;
        document.body.appendChild(this.audioOverlay);
    }

    createGalleryModal() {
        this.galleryModal = document.createElement('div');
        this.galleryModal.className = 'gallery-modal hidden';
        this.galleryModal.innerHTML = `
            <div class="gallery-backdrop" onclick="multimediaSystem.closeGallery()"></div>
            <div class="gallery-content">
                <button class="gallery-close" onclick="multimediaSystem.closeGallery()">&times;</button>
                <button class="gallery-prev" onclick="multimediaSystem.previousImage()">&lt;</button>
                <img class="gallery-image" src="" alt="Gallery Image">
                <button class="gallery-next" onclick="multimediaSystem.nextImage()">&gt;</button>
                <div class="gallery-counter">
                    <span class="gallery-current">1</span> / <span class="gallery-total">1</span>
                </div>
            </div>
        `;
        document.body.appendChild(this.galleryModal);
    }

    setupEventListeners() {
        // Handle audio button clicks
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('audio-btn')) {
                const audioSrc = e.target.getAttribute('data-audio-src');
                if (audioSrc) {
                    this.playAudio(audioSrc);
                }
            }

            // Handle gallery image clicks
            if (e.target.classList.contains('gallery-trigger')) {
                const images = JSON.parse(e.target.getAttribute('data-gallery-images') || '[]');
                const startIndex = parseInt(e.target.getAttribute('data-start-index')) || 0;
                this.openGallery(images, startIndex);
            }
        });

        // Handle keyboard events
        document.addEventListener('keydown', (e) => {
            if (this.galleryModal && !this.galleryModal.classList.contains('hidden')) {
                if (e.key === 'Escape') {
                    this.closeGallery();
                } else if (e.key === 'ArrowLeft') {
                    this.previousImage();
                } else if (e.key === 'ArrowRight') {
                    this.nextImage();
                }
            }

            if (this.audioOverlay && !this.audioOverlay.classList.contains('hidden')) {
                if (e.key === 'Escape') {
                    this.closeAudio();
                }
            }
        });

        // Handle audio ended event
        document.addEventListener('ended', (e) => {
            if (e.target === this.currentAudio) {
                this.closeAudio();
            }
        });
    }

    playAudio(audioSrc) {
        const audioPlayer = this.audioOverlay.querySelector('.audio-player');
        audioPlayer.src = audioSrc;
        this.currentAudio = audioPlayer;
        
        this.audioOverlay.classList.remove('hidden');
        document.body.classList.add('audio-overlay-active');
        
        // Auto-play the audio
        audioPlayer.play().catch(error => {
            console.log('Audio autoplay prevented:', error);
        });
    }

    closeAudio() {
        if (this.currentAudio) {
            this.currentAudio.pause();
            this.currentAudio.currentTime = 0;
        }
        
        this.audioOverlay.classList.add('hidden');
        document.body.classList.remove('audio-overlay-active');
    }

    openGallery(images, startIndex = 0) {
        this.galleryImages = images;
        this.currentGalleryIndex = startIndex;
        
        if (images.length > 0) {
            this.updateGalleryImage();
            this.galleryModal.classList.remove('hidden');
            document.body.classList.add('gallery-active');
        }
    }

    closeGallery() {
        this.galleryModal.classList.add('hidden');
        document.body.classList.remove('gallery-active');
        this.galleryImages = [];
        this.currentGalleryIndex = 0;
    }

    nextImage() {
        if (this.galleryImages.length > 0) {
            this.currentGalleryIndex = (this.currentGalleryIndex + 1) % this.galleryImages.length;
            this.updateGalleryImage();
        }
    }

    previousImage() {
        if (this.galleryImages.length > 0) {
            this.currentGalleryIndex = this.currentGalleryIndex === 0 
                ? this.galleryImages.length - 1 
                : this.currentGalleryIndex - 1;
            this.updateGalleryImage();
        }
    }

    updateGalleryImage() {
        if (this.galleryImages.length > 0) {
            const currentImage = this.galleryImages[this.currentGalleryIndex];
            const galleryImage = this.galleryModal.querySelector('.gallery-image');
            const currentCounter = this.galleryModal.querySelector('.gallery-current');
            const totalCounter = this.galleryModal.querySelector('.gallery-total');
            
            galleryImage.src = currentImage.url || currentImage;
            galleryImage.alt = currentImage.alt || `Image ${this.currentGalleryIndex + 1}`;
            
            currentCounter.textContent = this.currentGalleryIndex + 1;
            totalCounter.textContent = this.galleryImages.length;
            
            // Show/hide navigation buttons
            const prevBtn = this.galleryModal.querySelector('.gallery-prev');
            const nextBtn = this.galleryModal.querySelector('.gallery-next');
            
            if (this.galleryImages.length <= 1) {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
            } else {
                prevBtn.style.display = 'block';
                nextBtn.style.display = 'block';
            }
        }
    }

    // Static helper methods for backward compatibility
    static playAudio(audioSrc) {
        if (window.multimediaSystem) {
            window.multimediaSystem.playAudio(audioSrc);
        }
    }

    static openGallery(images, startIndex = 0) {
        if (window.multimediaSystem) {
            window.multimediaSystem.openGallery(images, startIndex);
        }
    }
}

// Initialize the multimedia system
document.addEventListener('DOMContentLoaded', () => {
    window.multimediaSystem = new MultimediaSystem();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = MultimediaSystem;
}