// Multimedia Gallery Component JavaScript
// Generic gallery functionality for multimedia components

// Register Alpine.js component when available
function registerMultimediaGalleryComponent() {
    if (typeof window.Alpine !== 'undefined') {
        window.Alpine.data('multimediaGallery', (config = {}) => ({
            // Gallery state
            images: config.images || [],
            imageLoaded: {},
            imageError: {},
            
            // Modal state
            modalOpen: false,
            currentImage: {
                src: '',
                alt: '',
                caption: '',
                loading: true,
                error: false,
                id: null
            },
            currentImageIndex: 0,
            
            // Advanced features
            zoomLevel: 1,
            imagePosition: { x: 0, y: 0 },
            isDragging: false,
            dragStart: { x: 0, y: 0 },
            
            // Slideshow
            isSlideshow: false,
            slideshowInterval: null,
            
            init() {
                console.log('Multimedia Gallery initialized with', this.images.length, 'images');
                
                // Keyboard navigation
                document.addEventListener('keydown', (e) => {
                    if (!this.modalOpen) return;
                    
                    switch(e.key) {
                        case 'Escape':
                            this.closeModal();
                            break;
                        case 'ArrowLeft':
                            this.previousImage();
                            break;
                        case 'ArrowRight':
                            this.nextImage();
                            break;
                        case ' ':
                            e.preventDefault();
                            this.toggleSlideshow();
                            break;
                    }
                });
                
                // Prevent body scroll when modal is open
                this.$watch('modalOpen', (value) => {
                    if (value) {
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow = '';
                    }
                });
            },
            
            // Modal methods
            openModal(src, alt = '', index = 0, caption = '') {
                console.log('Opening modal for image:', src, 'at index:', index);
                this.currentImageIndex = index;
                this.currentImage = {
                    src: src,
                    alt: alt,
                    caption: caption,
                    loading: true,
                    error: false,
                    id: this.images[index]?.id || index
                };
                this.modalOpen = true;
                this.resetZoom();
                
                // Preload image
                const img = new Image();
                img.onload = () => {
                    this.currentImage.loading = false;
                };
                img.onerror = () => {
                    this.currentImage.loading = false;
                    this.currentImage.error = true;
                };
                img.src = src;
                
                // Preload adjacent images
                this.preloadAdjacentImages();
            },
            
            closeModal() {
                this.modalOpen = false;
                this.stopSlideshow();
                this.resetZoom();
            },
            
            // Navigation methods
            nextImage() {
                if (this.images.length <= 1) return;
                const nextIndex = (this.currentImageIndex + 1) % this.images.length;
                const nextImage = this.images[nextIndex];
                this.openModal(nextImage.src, nextImage.alt, nextIndex, nextImage.caption);
            },
            
            previousImage() {
                if (this.images.length <= 1) return;
                const prevIndex = (this.currentImageIndex - 1 + this.images.length) % this.images.length;
                const prevImage = this.images[prevIndex];
                this.openModal(prevImage.src, prevImage.alt, prevIndex, prevImage.caption);
            },
            
            // Zoom methods
            zoomIn() {
                this.zoomLevel = Math.min(3, this.zoomLevel + 0.25);
            },
            
            zoomOut() {
                this.zoomLevel = Math.max(0.5, this.zoomLevel - 0.25);
            },
            
            resetZoom() {
                this.zoomLevel = 1;
                this.imagePosition = { x: 0, y: 0 };
            },
            
            // Drag methods
            startDrag(event) {
                if (this.zoomLevel <= 1) return;
                
                this.isDragging = true;
                const clientX = event.clientX || event.touches[0]?.clientX;
                const clientY = event.clientY || event.touches[0]?.clientY;
                
                this.dragStart = {
                    x: clientX - this.imagePosition.x,
                    y: clientY - this.imagePosition.y
                };
            },
            
            drag(event) {
                if (!this.isDragging || this.zoomLevel <= 1) return;
                
                event.preventDefault();
                const clientX = event.clientX || event.touches[0]?.clientX;
                const clientY = event.clientY || event.touches[0]?.clientY;
                
                this.imagePosition = {
                    x: clientX - this.dragStart.x,
                    y: clientY - this.dragStart.y
                };
            },
            
            endDrag() {
                this.isDragging = false;
            },
            
            // Slideshow methods
            toggleSlideshow() {
                if (this.isSlideshow) {
                    this.stopSlideshow();
                } else {
                    this.startSlideshow();
                }
            },
            
            startSlideshow() {
                if (this.images.length <= 1) return;
                
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
            
            // Utility methods
            handleModalBackdrop(event) {
                if (event.target === event.currentTarget) {
                    this.closeModal();
                }
            },
            
            preloadAdjacentImages() {
                if (this.images.length <= 1) return;
                
                // Preload next image
                const nextIndex = (this.currentImageIndex + 1) % this.images.length;
                const nextImg = new Image();
                nextImg.src = this.images[nextIndex].src;
                
                // Preload previous image
                const prevIndex = (this.currentImageIndex - 1 + this.images.length) % this.images.length;
                const prevImg = new Image();
                prevImg.src = this.images[prevIndex].src;
            },
            
            // Computed properties
            get galleryImages() {
                return this.images;
            }
        }));
        
        console.log('Multimedia Gallery component registered');
    } else {
        console.warn('Alpine.js not available for Multimedia Gallery component');
    }
}

// Auto-register when Alpine is available
document.addEventListener('alpine:init', () => {
    registerMultimediaGalleryComponent();
});

// Fallback registration
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        if (typeof window.Alpine !== 'undefined') {
            registerMultimediaGalleryComponent();
        }
    }, 100);
});

// Export for manual registration
if (typeof window !== 'undefined') {
    window.registerMultimediaGalleryComponent = registerMultimediaGalleryComponent;
}