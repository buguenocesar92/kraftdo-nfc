/**
 * Profile Enhancements JavaScript
 * Advanced lazy loading, performance optimizations, and image positioning fixes
 */

// Advanced Lazy Loading Manager
class LazyLoadManager {
    constructor() {
        this.imageObserver = null;
        this.images = [];
        this.init();
    }

    init() {
        this.setupIntersectionObserver();
        this.findLazyImages();
        this.observeImages();
        this.addImageOptimizations();
    }

    setupIntersectionObserver() {
        const options = {
            root: null,
            rootMargin: '50px 0px 50px 0px',
            threshold: 0.01
        };

        this.imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadImage(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, options);
    }

    findLazyImages() {
        this.images = document.querySelectorAll('img[data-src], img.lazy-load');
    }

    observeImages() {
        this.images.forEach(img => {
            this.imageObserver.observe(img);
        });
    }

    loadImage(img) {
        // Add loading state
        img.classList.add('loading');
        
        // Add loading spinner
        this.addLoadingSpinner(img);
        
        // Create image loader
        const imageLoader = new Image();
        
        // Handle successful load
        imageLoader.onload = () => {
            this.handleImageLoad(img, imageLoader.src);
        };
        
        // Handle load error
        imageLoader.onerror = () => {
            this.handleImageError(img);
        };
        
        // Start loading the image
        const imageSrc = img.dataset.src || img.src;
        imageLoader.src = imageSrc;
    }

    addLoadingSpinner(img) {
        const container = img.closest('.relative') || img.parentElement;
        if (container && !container.querySelector('.image-loading-spinner')) {
            const spinner = document.createElement('div');
            spinner.className = 'image-loading-spinner';
            container.style.position = 'relative';
            container.appendChild(spinner);
        }
    }

    removeLoadingSpinner(img) {
        const container = img.closest('.relative') || img.parentElement;
        const spinner = container?.querySelector('.image-loading-spinner');
        if (spinner) {
            spinner.remove();
        }
    }

    handleImageLoad(img, src) {
        // Remove data-src and set actual src
        img.src = src;
        img.removeAttribute('data-src');
        
        // Update classes
        img.classList.remove('loading');
        img.classList.add('loaded');
        
        // Remove loading spinner
        this.removeLoadingSpinner(img);
        
        // Add fade-in effect
        img.style.opacity = '0';
        img.style.transition = 'opacity 0.4s ease-out';
        
        // Use requestAnimationFrame for smooth animation
        requestAnimationFrame(() => {
            img.style.opacity = '1';
        });

        // Dispatch custom event
        img.dispatchEvent(new CustomEvent('imageLoaded', {
            detail: { src: src }
        }));
    }

    handleImageError(img) {
        img.classList.remove('loading');
        img.classList.add('error');
        
        // Remove loading spinner
        this.removeLoadingSpinner(img);
        
        // Set fallback image based on type
        if (img.classList.contains('lazy-load')) {
            // Profile image fallback
            img.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PGNpcmNsZSBjeD0iMTAwIiBjeT0iNzUiIHI9IjMwIiBmaWxsPSIjZTVlN2ViIi8+PHBhdGggZD0ibTEwMCAxMDBjLTE2LjU2OSAwLTMwIDEzLjQzMS0zMCAzMGg2MGMwLTE2LjU2OS0xMy40MzEtMzAtMzAtMzB6IiBmaWxsPSIjZTVlN2ViIi8+PHRleHQgeD0iMTAwIiB5PSIxNTAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0iIzk5OTk5OSI+SW1hZ2VuIG5vIGRpc3BvbmlibGU8L3RleHQ+PC9zdmc+';
        } else {
            // General image fallback
            img.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iIGZpbGw9IiM5OTk5OTkiPkltYWdlbiBubyBkaXNwb25pYmxlPC90ZXh0Pjwvc3ZnPg==';
        }

        // Dispatch error event
        img.dispatchEvent(new CustomEvent('imageError', {
            detail: { originalSrc: img.dataset.src || img.src }
        }));
    }

    addImageOptimizations() {
        // Add responsive image attributes
        this.images.forEach(img => {
            // Add decoding async if not present
            if (!img.hasAttribute('decoding')) {
                img.setAttribute('decoding', 'async');
            }
            
            // Add loading lazy if not present
            if (!img.hasAttribute('loading')) {
                img.setAttribute('loading', 'lazy');
            }
        });

        // Preload critical images
        this.preloadCriticalImages();
    }

    preloadCriticalImages() {
        // Preload profile image if it's in viewport
        const profileImg = document.querySelector('.lazy-load');
        if (profileImg && this.isInViewport(profileImg)) {
            this.loadImage(profileImg);
        }
    }

    isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    // Public method to manually load an image
    forceLoad(img) {
        if (img && img.dataset.src) {
            this.loadImage(img);
        }
    }

    // Public method to add new images to lazy loading
    addImage(img) {
        if (img && (img.dataset.src || img.classList.contains('lazy-load'))) {
            this.imageObserver.observe(img);
        }
    }

    // Public method to refresh all images
    refresh() {
        this.findLazyImages();
        this.observeImages();
    }
}

// Performance optimizations
class PerformanceOptimizer {
    constructor() {
        this.init();
    }

    init() {
        this.optimizeRendering();
        this.addWebPSupport();
        this.implementCriticalResourceHints();
    }

    optimizeRendering() {
        // Add transform3d to trigger hardware acceleration
        document.querySelectorAll('.lazy-load, .animate-fade-in, .transition-all').forEach(el => {
            el.style.transform += ' translateZ(0)';
            el.style.backfaceVisibility = 'hidden';
        });
    }

    addWebPSupport() {
        // Check WebP support
        this.supportsWebP().then(supported => {
            if (supported) {
                document.documentElement.classList.add('webp-support');
            } else {
                document.documentElement.classList.add('no-webp-support');
            }
        });
    }

    supportsWebP() {
        return new Promise(resolve => {
            const webP = new Image();
            webP.onload = webP.onerror = () => {
                resolve(webP.height === 2);
            };
            webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
        });
    }

    implementCriticalResourceHints() {
        // Add preconnect hints for external resources
        this.addPreconnectHint('https://cdnjs.cloudflare.com');
        
        // Add dns-prefetch for common domains
        this.addDNSPrefetch('fonts.googleapis.com');
        this.addDNSPrefetch('fonts.gstatic.com');
    }

    addPreconnectHint(url) {
        const link = document.createElement('link');
        link.rel = 'preconnect';
        link.href = url;
        document.head.appendChild(link);
    }

    addDNSPrefetch(domain) {
        const link = document.createElement('link');
        link.rel = 'dns-prefetch';
        link.href = '//' + domain;
        document.head.appendChild(link);
    }
}

// Fix profile image visibility during scroll
class ProfileImageFixer {
    constructor() {
        this.profileImage = null;
        this.init();
    }

    init() {
        this.profileImage = document.querySelector('.profile-image-container');
        if (this.profileImage) {
            this.addScrollHandler();
            this.ensureVisibility();
        }
    }

    addScrollHandler() {
        let ticking = false;
        
        const handleScroll = () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    this.ensureVisibility();
                    ticking = false;
                });
                ticking = true;
            }
        };

        window.addEventListener('scroll', handleScroll, { passive: true });
        window.addEventListener('resize', handleScroll, { passive: true });
    }

    ensureVisibility() {
        if (this.profileImage) {
            // Force repaint to prevent disappearing issues
            this.profileImage.style.transform = 'translateX(-50%) translateZ(0)';
            
            // Ensure z-index is maintained
            this.profileImage.style.zIndex = '50';
            
            // Force hardware acceleration
            this.profileImage.style.willChange = 'transform';
            this.profileImage.style.backfaceVisibility = 'hidden';
        }
    }

    // Method to manually refresh positioning
    refresh() {
        this.ensureVisibility();
    }
}

// Accessibility enhancements
class AccessibilityManager {
    constructor() {
        this.init();
    }

    init() {
        this.addKeyboardSupport();
        this.addAriaLabels();
        this.addFocusManagement();
    }

    addKeyboardSupport() {
        // Add keyboard navigation for interactive elements
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                const target = e.target;
                if (target.classList.contains('lazy-load') && target.dataset.src) {
                    window.lazyLoader?.forceLoad(target);
                }
            }
        });
    }

    addAriaLabels() {
        // Add proper ARIA labels to lazy loaded images
        document.querySelectorAll('.lazy-load').forEach(img => {
            if (!img.hasAttribute('aria-label') && img.alt) {
                img.setAttribute('aria-label', img.alt);
            }
        });
    }

    addFocusManagement() {
        // Ensure proper focus management for dynamically loaded content
        document.addEventListener('imageLoaded', (e) => {
            const img = e.target;
            if (document.activeElement === img) {
                // Announce to screen readers that image has loaded
                const announcement = document.createElement('div');
                announcement.className = 'sr-only';
                announcement.setAttribute('aria-live', 'polite');
                announcement.textContent = 'Imagen cargada correctamente';
                document.body.appendChild(announcement);
                
                setTimeout(() => {
                    announcement.remove();
                }, 1000);
            }
        });
    }
}

// Error handling and debugging utilities
class ProfileDebugger {
    constructor() {
        this.init();
    }

    init() {
        this.setupErrorHandling();
        this.addDebugMethods();
    }

    setupErrorHandling() {
        // Global error handler for image loading issues
        window.addEventListener('error', (e) => {
            if (e.target.tagName === 'IMG') {
                console.warn('Image failed to load:', e.target.src);
                this.logImageError(e.target);
            }
        }, true);
    }

    logImageError(img) {
        const errorData = {
            src: img.src,
            dataSrc: img.dataset.src,
            alt: img.alt,
            className: img.className,
            timestamp: new Date().toISOString()
        };
        
        console.log('Image Error Details:', errorData);
    }

    addDebugMethods() {
        // Add global debugging methods
        window.profileDebug = {
            refreshImages: () => window.lazyLoader?.refresh(),
            fixImagePosition: () => window.profileImageFixer?.refresh(),
            checkImageStatus: () => {
                const images = document.querySelectorAll('img');
                images.forEach(img => {
                    console.log(`Image: ${img.src}, Complete: ${img.complete}, Natural dimensions: ${img.naturalWidth}x${img.naturalHeight}`);
                });
            },
            getPerformanceMetrics: () => {
                if (performance.getEntriesByType) {
                    const images = performance.getEntriesByType('resource').filter(entry => 
                        entry.initiatorType === 'img'
                    );
                    console.table(images);
                }
            }
        };
    }
}

// Initialize all managers when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const lazyLoader = new LazyLoadManager();
    const optimizer = new PerformanceOptimizer();
    const imageFixer = new ProfileImageFixer();
    const accessibilityManager = new AccessibilityManager();
    const profileDebugger = new ProfileDebugger();
    
    // Make managers globally accessible
    window.lazyLoader = lazyLoader;
    window.profileImageFixer = imageFixer;
    window.performanceOptimizer = optimizer;
    window.accessibilityManager = accessibilityManager;
});

// Handle visibility change for performance
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        // Resume lazy loading when page becomes visible
        if (window.lazyLoader) {
            window.lazyLoader.findLazyImages();
            window.lazyLoader.observeImages();
        }
        
        // Refresh image positioning
        if (window.profileImageFixer) {
            window.profileImageFixer.refresh();
        }
    }
});

// Handle page unload to clean up resources
window.addEventListener('beforeunload', () => {
    // Clean up any active observers
    if (window.lazyLoader?.imageObserver) {
        window.lazyLoader.imageObserver.disconnect();
    }
});

// Export for module usage if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        LazyLoadManager,
        PerformanceOptimizer,
        ProfileImageFixer,
        AccessibilityManager,
        ProfileDebugger
    };
}