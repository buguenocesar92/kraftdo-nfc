/**
 * Advanced Gallery Modal System
 * Handles photo gallery display with modal functionality
 * 
 * Features:
 * - Modal image viewer with navigation
 * - Keyboard navigation support
 * - Image counter and captions
 * - Smooth animations and transitions
 * - Touch/swipe support (future enhancement)
 */

class GalleryModal {
    constructor() {
        this.galleryData = [];
        this.currentImageIndex = 0;
        this.modal = null;
        this.modalImage = null;
        this.modalCaption = null;
        this.imageCounter = null;
        
        this.init();
    }
    
    init() {
        // Check if DOM is already ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.setupGalleryModal();
            });
        } else {
            // DOM is already ready, setup immediately
            this.setupGalleryModal();
        }
    }
    
    setupGalleryModal() {
        console.log('🔍 Setting up gallery modal...');
        
        // Get DOM elements
        const galleryItems = document.querySelectorAll('.gallery-item');
        this.modal = document.getElementById('galleryModal');
        this.modalImage = document.getElementById('modalImage');
        this.modalCaption = document.getElementById('modalCaption');
        this.imageCounter = document.getElementById('imageCounter');
        
        console.log('🔍 Found elements:', {
            galleryItems: galleryItems.length,
            modal: !!this.modal,
            modalImage: !!this.modalImage,
            modalCaption: !!this.modalCaption,
            imageCounter: !!this.imageCounter
        });
        
        if (!this.modal || !galleryItems.length) {
            console.warn('❌ Gallery modal elements not found - modal:', !!this.modal, 'galleryItems:', galleryItems.length);
            return;
        }
        
        this.setupGalleryItems(galleryItems);
        this.setupModalControls();
        this.setupKeyboardNavigation();
        
        console.log('✅ Gallery modal system initialized with', this.galleryData.length, 'images');
    }
    
    setupGalleryItems(galleryItems) {
        // Collect gallery data
        galleryItems.forEach((item, index) => {
            this.galleryData.push({
                url: item.dataset.url,
                caption: item.dataset.caption,
                index: index
            });
            
            // Add click listener to open modal
            item.addEventListener('click', (e) => {
                console.log('🔍 Gallery item clicked by GalleryModal!', index, item.dataset.url);
                e.preventDefault();
                e.stopPropagation();
                this.openModal(index);
            });
        });
    }
    
    setupModalControls() {
        const closeBtn = document.getElementById('closeModal');
        const prevBtn = document.getElementById('prevImage');
        const nextBtn = document.getElementById('nextImage');
        
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeModal());
        }
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.showPrevImage());
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.showNextImage());
        }
        
        // Close modal when clicking on background
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.closeModal();
            }
        });
    }
    
    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            if (!this.modal.classList.contains('hidden')) {
                switch(e.key) {
                    case 'Escape':
                        this.closeModal();
                        break;
                    case 'ArrowLeft':
                        this.showPrevImage();
                        break;
                    case 'ArrowRight':
                        this.showNextImage();
                        break;
                }
            }
        });
    }
    
    openModal(index) {
        console.log('🔍 Opening modal for image index:', index);
        console.log('🔍 Modal element:', this.modal);
        console.log('🔍 Gallery data:', this.galleryData[index]);
        
        this.currentImageIndex = index;
        this.updateModalContent();
        
        if (this.modal) {
            this.modal.classList.remove('hidden');
            this.modal.classList.add('flex');
            console.log('🔍 Modal should now be visible');
        } else {
            console.error('❌ Modal element not found!');
        }
        
        document.body.style.overflow = 'hidden';
        
        // Entry animation
        setTimeout(() => {
            this.modalImage.style.transform = 'scale(1)';
            this.modalImage.style.opacity = '1';
        }, 50);
    }
    
    closeModal() {
        this.modal.classList.add('hidden');
        this.modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
        this.modalImage.style.transform = 'scale(0.9)';
        this.modalImage.style.opacity = '0';
    }
    
    updateModalContent() {
        const currentImage = this.galleryData[this.currentImageIndex];
        
        if (!currentImage) {
            console.error('Image data not found for index:', this.currentImageIndex);
            return;
        }
        
        this.modalImage.src = currentImage.url;
        this.modalImage.alt = currentImage.caption;
        this.modalCaption.querySelector('p').textContent = currentImage.caption;
        this.imageCounter.textContent = `${this.currentImageIndex + 1} de ${this.galleryData.length}`;
        
        // Show/hide navigation buttons
        const prevBtn = document.getElementById('prevImage');
        const nextBtn = document.getElementById('nextImage');
        
        if (prevBtn && nextBtn) {
            prevBtn.style.display = this.galleryData.length > 1 ? 'flex' : 'none';
            nextBtn.style.display = this.galleryData.length > 1 ? 'flex' : 'none';
        }
        
        // Reset animation
        this.modalImage.style.transform = 'scale(0.9)';
        this.modalImage.style.opacity = '0';
    }
    
    showPrevImage() {
        this.currentImageIndex = this.currentImageIndex > 0 
            ? this.currentImageIndex - 1 
            : this.galleryData.length - 1;
        
        this.updateModalContent();
        this.animateImageIn();
    }
    
    showNextImage() {
        this.currentImageIndex = this.currentImageIndex < this.galleryData.length - 1 
            ? this.currentImageIndex + 1 
            : 0;
        
        this.updateModalContent();
        this.animateImageIn();
    }
    
    animateImageIn() {
        setTimeout(() => {
            this.modalImage.style.transform = 'scale(1)';
            this.modalImage.style.opacity = '1';
        }, 50);
    }
    
    // Method to add images dynamically (for future use)
    addImage(url, caption) {
        const newIndex = this.galleryData.length;
        this.galleryData.push({
            url: url,
            caption: caption,
            index: newIndex
        });
        
        console.log('📸 Added new image to gallery:', caption);
    }
    
    // Method to remove image (for future use)
    removeImage(index) {
        if (index >= 0 && index < this.galleryData.length) {
            this.galleryData.splice(index, 1);
            
            // Re-index remaining images
            this.galleryData.forEach((img, newIndex) => {
                img.index = newIndex;
            });
            
            // Adjust current index if necessary
            if (this.currentImageIndex >= this.galleryData.length) {
                this.currentImageIndex = Math.max(0, this.galleryData.length - 1);
            }
            
            console.log('📸 Removed image from gallery at index:', index);
        }
    }
    
    // Get current image data
    getCurrentImage() {
        return this.galleryData[this.currentImageIndex];
    }
    
    // Get total number of images
    getTotalImages() {
        return this.galleryData.length;
    }
    
    // Check if modal is open
    isModalOpen() {
        return !this.modal.classList.contains('hidden');
    }
}

// Export for use in gift.blade.php
window.GalleryModal = GalleryModal;

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 Gallery Modal - DOMContentLoaded, initializing...');
    console.log('🔍 Current page:', window.location.pathname);
    window.galleryModalInstance = new GalleryModal();
});

// Also try immediate initialization in case DOM is already ready
if (document.readyState === 'loading') {
    // Loading hasn't finished yet
    console.log('🔍 Gallery Modal - Document still loading, waiting for DOMContentLoaded');
} else {
    // `DOMContentLoaded` has already fired
    console.log('🔍 Gallery Modal - DOM already ready, initializing immediately');
    window.galleryModalInstance = new GalleryModal();
}