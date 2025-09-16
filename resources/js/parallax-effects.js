/**
 * Parallax effects for scroll-based animations
 */
class ParallaxEffects {
    constructor() {
        this.scrollElements = [];
        this.isScrolling = false;
        this.init();
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    setup() {
        // Find all parallax elements
        this.scrollElements = document.querySelectorAll('[data-parallax]');
        
        if (this.scrollElements.length > 0) {
            this.bindEvents();
            this.updateParallax(); // Initial update
        }
    }

    bindEvents() {
        // Use passive listeners for better performance
        window.addEventListener('scroll', () => this.handleScroll(), { passive: true });
        window.addEventListener('resize', () => this.handleResize(), { passive: true });
    }

    handleScroll() {
        if (!this.isScrolling) {
            requestAnimationFrame(() => this.updateParallax());
            this.isScrolling = true;
        }
    }

    handleResize() {
        // Recalculate positions on resize
        this.updateParallax();
    }

    updateParallax() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = window.innerHeight;

        this.scrollElements.forEach(element => {
            const rect = element.getBoundingClientRect();
            const elementTop = rect.top + scrollTop;
            const elementHeight = rect.height;
            
            // Check if element is in viewport
            if (rect.bottom >= 0 && rect.top <= windowHeight) {
                const speed = parseFloat(element.getAttribute('data-parallax')) || 0.5;
                const direction = element.getAttribute('data-parallax-direction') || 'vertical';
                const yPos = -(scrollTop - elementTop) * speed;
                
                this.applyTransform(element, yPos, direction);
            }
        });

        this.isScrolling = false;
    }

    applyTransform(element, offset, direction) {
        let transform = '';
        
        switch (direction) {
            case 'vertical':
                transform = `translate3d(0, ${offset}px, 0)`;
                break;
            case 'horizontal':
                transform = `translate3d(${offset}px, 0, 0)`;
                break;
            case 'both':
                transform = `translate3d(${offset}px, ${offset}px, 0)`;
                break;
            case 'rotate':
                const rotation = offset * 0.1; // Subtle rotation
                transform = `translate3d(0, ${offset}px, 0) rotate(${rotation}deg)`;
                break;
            case 'scale':
                const scale = 1 + (offset * 0.001); // Subtle scaling
                transform = `translate3d(0, ${offset}px, 0) scale(${Math.max(0.5, Math.min(1.5, scale))})`;
                break;
            default:
                transform = `translate3d(0, ${offset}px, 0)`;
        }
        
        element.style.transform = transform;
    }

    // Method to add new parallax elements dynamically
    addElement(element, speed = 0.5, direction = 'vertical') {
        element.setAttribute('data-parallax', speed);
        element.setAttribute('data-parallax-direction', direction);
        this.scrollElements = document.querySelectorAll('[data-parallax]');
        this.updateParallax();
    }

    // Method to remove parallax effect from elements
    removeElement(element) {
        element.removeAttribute('data-parallax');
        element.removeAttribute('data-parallax-direction');
        element.style.transform = '';
        this.scrollElements = document.querySelectorAll('[data-parallax]');
    }

    // Enable/disable parallax effects (useful for performance)
    setEnabled(enabled) {
        if (enabled) {
            this.bindEvents();
        } else {
            window.removeEventListener('scroll', this.handleScroll);
            window.removeEventListener('resize', this.handleResize);
            
            // Reset all transforms
            this.scrollElements.forEach(element => {
                element.style.transform = '';
            });
        }
    }

    // Destroy the parallax instance
    destroy() {
        this.setEnabled(false);
        this.scrollElements = [];
    }

    // Static method for simple parallax setup
    static simple(selector, speed = 0.5) {
        const elements = document.querySelectorAll(selector);
        elements.forEach(element => {
            element.setAttribute('data-parallax', speed);
        });
        return new ParallaxEffects();
    }

    // Advanced parallax with custom easing
    static withEasing(selector, speed = 0.5, easing = 'linear') {
        const parallax = new ParallaxEffects();
        
        // Override updateParallax with easing
        const originalUpdate = parallax.updateParallax.bind(parallax);
        parallax.updateParallax = function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const windowHeight = window.innerHeight;
            const maxScroll = document.body.scrollHeight - windowHeight;
            const scrollProgress = scrollTop / maxScroll;
            
            let easedProgress;
            switch (easing) {
                case 'easeOut':
                    easedProgress = 1 - Math.pow(1 - scrollProgress, 3);
                    break;
                case 'easeIn':
                    easedProgress = Math.pow(scrollProgress, 3);
                    break;
                case 'easeInOut':
                    easedProgress = scrollProgress < 0.5 
                        ? 4 * scrollProgress * scrollProgress * scrollProgress
                        : 1 - Math.pow(-2 * scrollProgress + 2, 3) / 2;
                    break;
                default:
                    easedProgress = scrollProgress;
            }
            
            // Apply eased progress to parallax calculations
            originalUpdate();
            this.isScrolling = false;
        };
        
        return parallax;
    }
}

// Initialize parallax effects when DOM is ready
const parallaxEffects = new ParallaxEffects();

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ParallaxEffects;
}

// Global access
window.ParallaxEffects = ParallaxEffects;
window.parallaxEffects = parallaxEffects;