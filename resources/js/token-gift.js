// Token Gift Professional JavaScript with Alpine.js
// Este archivo se carga antes de Alpine.start() para registrar componentes

// Función que registra el componente cuando Alpine esté disponible
function registerTokenGiftComponent() {
    if (typeof window.Alpine !== 'undefined') {
        window.Alpine.data('tokenGift', () => ({
        // Gallery modal state
        modalOpen: false,
        currentImage: {
            src: '',
            alt: ''
        },
        galleryImages: [],
        currentImageIndex: 0,
        
        init() {
            this.setupKeyboardHandlers();
            this.setupTouchHandlers();
            console.log('Token Gift Alpine component initialized');
        },
        
        // Gallery Modal Functions
        openImageModal(src, alt = '', images = [], index = 0) {
            this.currentImage.src = src;
            this.currentImage.alt = alt;
            this.galleryImages = images;
            this.currentImageIndex = index;
            this.modalOpen = true;
            document.body.style.overflow = 'hidden';
        },
        
        nextImage() {
            if (this.galleryImages.length > 0) {
                this.currentImageIndex = (this.currentImageIndex + 1) % this.galleryImages.length;
                const nextImg = this.galleryImages[this.currentImageIndex];
                this.currentImage.src = nextImg.src;
                this.currentImage.alt = nextImg.alt;
            }
        },
        
        prevImage() {
            if (this.galleryImages.length > 0) {
                this.currentImageIndex = this.currentImageIndex === 0 ? this.galleryImages.length - 1 : this.currentImageIndex - 1;
                const prevImg = this.galleryImages[this.currentImageIndex];
                this.currentImage.src = prevImg.src;
                this.currentImage.alt = prevImg.alt;
            }
        },
        
        closeImageModal() {
            this.modalOpen = false;
            document.body.style.overflow = 'auto';
            this.currentImage = { src: '', alt: '' };
        },
        
        handleModalBackdrop(event) {
            if (event.target === event.currentTarget) {
                this.closeImageModal();
            }
        },
        
        setupKeyboardHandlers() {
            document.addEventListener('keydown', (e) => {
                if (!this.modalOpen) return;
                
                switch(e.key) {
                    case 'Escape':
                        this.closeImageModal();
                        break;
                    case 'ArrowLeft':
                        this.prevImage();
                        break;
                    case 'ArrowRight':
                        this.nextImage();
                        break;
                }
            });
        },
        
        setupTouchHandlers() {
            let startX = 0;
            let startY = 0;
            
            document.addEventListener('touchstart', (e) => {
                if (!this.modalOpen) return;
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
            });
            
            document.addEventListener('touchend', (e) => {
                if (!this.modalOpen) return;
                
                const endX = e.changedTouches[0].clientX;
                const endY = e.changedTouches[0].clientY;
                const deltaX = endX - startX;
                const deltaY = endY - startY;
                
                // Solo si el movimiento horizontal es mayor que el vertical
                if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
                    if (deltaX > 0) {
                        this.prevImage(); // Swipe derecha = imagen anterior
                    } else {
                        this.nextImage(); // Swipe izquierda = imagen siguiente
                    }
                }
            });
        }
        }));
    } else {
        console.warn('Alpine.js no está disponible aún');
    }
}

// Intentar registrar inmediatamente o esperar a que Alpine esté disponible
if (typeof window.Alpine !== 'undefined') {
    registerTokenGiftComponent();
} else {
    // Esperar a que Alpine esté disponible
    document.addEventListener('alpine:init', () => {
        registerTokenGiftComponent();
    });
}

// Utility functions
window.TokenUtils = {
    // Format time duration
    formatDuration(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    },
    
    // Copy text to clipboard
    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (err) {
            console.error('Failed to copy:', err);
            return false;
        }
    },
    
    // Share content using Web Share API
    async shareContent(data) {
        if (navigator.share) {
            try {
                await navigator.share(data);
                return true;
            } catch (err) {
                console.error('Failed to share:', err);
                return false;
            }
        }
        return false;
    }
};