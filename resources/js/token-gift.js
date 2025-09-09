// Token Gift Professional JavaScript with Alpine.js

document.addEventListener('alpine:init', () => {
    // Main Token Gift Component
    Alpine.data('tokenGift', () => ({
        // Gallery modal state
        modalOpen: false,
        currentImage: {
            src: '',
            alt: ''
        },
        
        init() {
            this.setupKeyboardHandlers();
        },
        
        // Gallery Modal Functions
        openImageModal(src, alt = '') {
            this.currentImage.src = src;
            this.currentImage.alt = alt;
            this.modalOpen = true;
            document.body.style.overflow = 'hidden';
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
                if (e.key === 'Escape' && this.modalOpen) {
                    this.closeImageModal();
                }
            });
        }
    }));
});

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