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
            alt: '',
            caption: '',
            loading: true,
            error: false
        },
        galleryImages: [],
        currentImageIndex: 0,
        
        // Gallery advanced features
        isSlideshow: false,
        slideshowInterval: null,
        zoomLevel: 1,
        imagePosition: { x: 0, y: 0 },
        isDragging: false,
        dragStart: { x: 0, y: 0 },
        
        // Loading states
        imagesLoaded: new Set(),
        preloadedImages: new Set(),
        
        init() {
            this.setupKeyboardHandlers();
            this.setupTouchHandlers();
            this.setupIntersectionObserver();
            console.log('Token Gift Alpine component initialized');
        },
        
        // Gallery Modal Functions
        openImageModal(src, alt = '', images = [], index = 0, caption = '') {
            this.currentImage = {
                src: src,
                alt: alt,
                caption: caption,
                loading: true,
                error: false
            };
            this.galleryImages = images;
            this.currentImageIndex = index;
            this.modalOpen = true;
            this.zoomLevel = 1;
            this.imagePosition = { x: 0, y: 0 };
            document.body.style.overflow = 'hidden';
            
            // Preload current image
            this.loadImage(src);
            // Preload adjacent images
            this.preloadAdjacentImages();
        },
        
        nextImage() {
            if (this.galleryImages.length > 0) {
                this.currentImageIndex = (this.currentImageIndex + 1) % this.galleryImages.length;
                this.updateCurrentImage();
                this.preloadAdjacentImages();
            }
        },
        
        prevImage() {
            if (this.galleryImages.length > 0) {
                this.currentImageIndex = this.currentImageIndex === 0 ? this.galleryImages.length - 1 : this.currentImageIndex - 1;
                this.updateCurrentImage();
                this.preloadAdjacentImages();
            }
        },
        
        updateCurrentImage() {
            const img = this.galleryImages[this.currentImageIndex];
            this.currentImage = {
                src: img.src,
                alt: img.alt,
                caption: img.caption || '',
                loading: !this.imagesLoaded.has(img.src),
                error: false
            };
            this.zoomLevel = 1;
            this.imagePosition = { x: 0, y: 0 };
            
            if (!this.imagesLoaded.has(img.src)) {
                this.loadImage(img.src);
            }
        },
        
        closeImageModal() {
            this.modalOpen = false;
            document.body.style.overflow = 'auto';
            this.stopSlideshow();
            this.zoomLevel = 1;
            this.imagePosition = { x: 0, y: 0 };
            this.currentImage = { src: '', alt: '', caption: '', loading: false, error: false };
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
            let initialDistance = 0;
            let initialZoom = 1;
            
            document.addEventListener('touchstart', (e) => {
                if (!this.modalOpen) return;
                
                if (e.touches.length === 1) {
                    startX = e.touches[0].clientX;
                    startY = e.touches[0].clientY;
                } else if (e.touches.length === 2) {
                    // Pinch to zoom
                    const touch1 = e.touches[0];
                    const touch2 = e.touches[1];
                    initialDistance = Math.hypot(touch2.clientX - touch1.clientX, touch2.clientY - touch1.clientY);
                    initialZoom = this.zoomLevel;
                }
            });
            
            document.addEventListener('touchmove', (e) => {
                if (!this.modalOpen) return;
                
                if (e.touches.length === 2) {
                    e.preventDefault();
                    const touch1 = e.touches[0];
                    const touch2 = e.touches[1];
                    const currentDistance = Math.hypot(touch2.clientX - touch1.clientX, touch2.clientY - touch1.clientY);
                    const scale = currentDistance / initialDistance;
                    this.zoomLevel = Math.max(0.5, Math.min(3, initialZoom * scale));
                }
            });
            
            document.addEventListener('touchend', (e) => {
                if (!this.modalOpen) return;
                
                if (e.changedTouches.length === 1 && e.touches.length === 0) {
                    const endX = e.changedTouches[0].clientX;
                    const endY = e.changedTouches[0].clientY;
                    const deltaX = endX - startX;
                    const deltaY = endY - startY;
                    
                    // Solo si el movimiento horizontal es mayor que el vertical y no hay zoom
                    if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50 && this.zoomLevel <= 1) {
                        if (deltaX > 0) {
                            this.prevImage(); // Swipe derecha = imagen anterior
                        } else {
                            this.nextImage(); // Swipe izquierda = imagen siguiente
                        }
                    }
                }
            });
        },
        
        // Image loading and preloading
        loadImage(src) {
            if (this.imagesLoaded.has(src)) return Promise.resolve();
            
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => {
                    this.imagesLoaded.add(src);
                    if (this.currentImage.src === src) {
                        this.currentImage.loading = false;
                        this.currentImage.error = false;
                    }
                    resolve();
                };
                img.onerror = () => {
                    if (this.currentImage.src === src) {
                        this.currentImage.loading = false;
                        this.currentImage.error = true;
                    }
                    reject();
                };
                img.src = src;
            });
        },
        
        preloadAdjacentImages() {
            if (this.galleryImages.length <= 1) return;
            
            const nextIndex = (this.currentImageIndex + 1) % this.galleryImages.length;
            const prevIndex = this.currentImageIndex === 0 ? this.galleryImages.length - 1 : this.currentImageIndex - 1;
            
            [this.galleryImages[nextIndex], this.galleryImages[prevIndex]].forEach(img => {
                if (!this.preloadedImages.has(img.src)) {
                    this.preloadedImages.add(img.src);
                    this.loadImage(img.src);
                }
            });
        },
        
        // Slideshow functionality
        toggleSlideshow() {
            if (this.isSlideshow) {
                this.stopSlideshow();
            } else {
                this.startSlideshow();
            }
        },
        
        startSlideshow() {
            if (this.galleryImages.length <= 1) return;
            this.isSlideshow = true;
            this.slideshowInterval = setInterval(() => {
                this.nextImage();
            }, 3000);
        },
        
        stopSlideshow() {
            this.isSlideshow = false;
            if (this.slideshowInterval) {
                clearInterval(this.slideshowInterval);
                this.slideshowInterval = null;
            }
        },
        
        // Zoom functionality
        zoomIn() {
            this.zoomLevel = Math.min(3, this.zoomLevel * 1.2);
        },
        
        zoomOut() {
            this.zoomLevel = Math.max(0.5, this.zoomLevel / 1.2);
            if (this.zoomLevel <= 1) {
                this.imagePosition = { x: 0, y: 0 };
            }
        },
        
        resetZoom() {
            this.zoomLevel = 1;
            this.imagePosition = { x: 0, y: 0 };
        },
        
        // Share functionality
        async shareImage() {
            if (!navigator.share || !this.currentImage.src) return false;
            
            try {
                await navigator.share({
                    title: this.currentImage.alt || 'Imagen de galería',
                    text: this.currentImage.caption || '',
                    url: this.currentImage.src
                });
                return true;
            } catch (err) {
                console.error('Error sharing:', err);
                return false;
            }
        },
        
        // Download functionality
        downloadImage() {
            if (!this.currentImage.src) return;
            
            const link = document.createElement('a');
            link.href = this.currentImage.src;
            link.download = this.currentImage.alt || 'imagen.jpg';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        
        // Lazy loading with Intersection Observer
        setupIntersectionObserver() {
            if (!window.IntersectionObserver) return;
            
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        const src = img.dataset.src;
                        if (src && !img.src) {
                            img.src = src;
                            img.classList.remove('opacity-0');
                            img.classList.add('opacity-100', 'transition-opacity', 'duration-300');
                            this.observer.unobserve(img);
                        }
                    }
                });
            }, { threshold: 0.1 });
        },
        
        observeImage(img) {
            if (this.observer && img) {
                this.observer.observe(img);
            }
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