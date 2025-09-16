// KraftDo NFC Application JavaScript with Alpine.js

// Global Alpine.js stores
document.addEventListener('alpine:init', () => {
    // Modal management store
    Alpine.store('modal', {
        active: null,
        
        open(modal) {
            this.active = modal;
            document.body.classList.add('overflow-hidden');
        },
        
        close() {
            this.active = null;
            document.body.classList.remove('overflow-hidden');
        },
        
        isActive(modal) {
            return this.active === modal;
        }
    });
    
    // Gallery management store
    Alpine.store('gallery', {
        isOpen: false,
        currentIndex: 0,
        images: [],
        
        get currentImage() {
            return this.images[this.currentIndex] || '';
        },
        
        open(index = 0, images = []) {
            this.images = images;
            this.currentIndex = index;
            this.isOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        
        close() {
            this.isOpen = false;
            this.images = [];
            this.currentIndex = 0;
            document.body.classList.remove('overflow-hidden');
        },
        
        next() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
        },
        
        previous() {
            this.currentIndex = this.currentIndex === 0 ? this.images.length - 1 : this.currentIndex - 1;
        }
    });
    
    // Audio management store
    Alpine.store('audio', {
        isPlaying: false,
        currentTrack: null,
        audioElement: null,
        volume: 0.3,
        
        init(audioUrl, autoplay = false) {
            if (this.audioElement) {
                this.audioElement.pause();
            }
            
            this.audioElement = new Audio(audioUrl);
            this.audioElement.loop = true;
            this.audioElement.volume = this.volume;
            this.currentTrack = audioUrl;
            
            // Handle autoplay with user interaction
            if (autoplay) {
                this.setupAutoplay();
            }
            
            return this;
        },
        
        setupAutoplay() {
            // Attempt autoplay after user interaction
            const playAfterInteraction = () => {
                this.play();
                document.removeEventListener('click', playAfterInteraction);
                document.removeEventListener('touchstart', playAfterInteraction);
            };
            
            document.addEventListener('click', playAfterInteraction);
            document.addEventListener('touchstart', playAfterInteraction);
        },
        
        play() {
            if (this.audioElement) {
                this.audioElement.play().then(() => {
                    this.isPlaying = true;
                }).catch(e => {
                    console.log('Autoplay prevented:', e);
                });
            }
        },
        
        pause() {
            if (this.audioElement) {
                this.audioElement.pause();
                this.isPlaying = false;
            }
        },
        
        toggle() {
            if (this.isPlaying) {
                this.pause();
            } else {
                this.play();
            }
        },
        
        setVolume(volume) {
            this.volume = Math.max(0, Math.min(1, volume));
            if (this.audioElement) {
                this.audioElement.volume = this.volume;
            }
        }
    });
    
    // Animation utilities store
    Alpine.store('animations', {
        observeElements() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                        entry.target.classList.remove('animate-on-scroll');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '50px'
            });
            
            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
        },
        
        parallaxEffect() {
            let ticking = false;
            
            const updateParallax = () => {
                const scrolled = window.pageYOffset;
                const parallaxElements = document.querySelectorAll('[data-parallax]');
                
                parallaxElements.forEach(element => {
                    const speed = element.dataset.parallax || 0.5;
                    const yPos = -(scrolled * speed);
                    element.style.transform = `translateY(${yPos}px)`;
                });
                
                ticking = false;
            };
            
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    requestAnimationFrame(updateParallax);
                    ticking = true;
                }
            });
        }
    });
});

// Alpine.js Components
window.nfcComponents = {
    // Gift component
    gift() {
        return {
            loading: true,
            showDetails: false,
            imageLoaded: false,
            
            init() {
                // Simulate loading
                setTimeout(() => {
                    this.loading = false;
                    this.initializeAnimations();
                }, 1000);
            },
            
            initializeAnimations() {
                Alpine.store('animations').observeElements();
                Alpine.store('animations').parallaxEffect();
            },
            
            toggleDetails() {
                this.showDetails = !this.showDetails;
            },
            
            openImageModal(imageSrc) {
                Alpine.store('modal').open('image-modal');
                // Pass image to modal content
                this.$dispatch('set-modal-image', imageSrc);
            }
        };
    },
    
    // Gallery component
    gallery() {
        return {
            images: [],
            
            init() {
                // Get images from data attribute or props
                this.images = this.$el.dataset.images ? 
                    JSON.parse(this.$el.dataset.images) : [];
            },
            
            openGallery(index = 0) {
                Alpine.store('gallery').open(index, this.images);
            }
        };
    },
    
    // Audio player component
    audioPlayer() {
        return {
            trackUrl: null,
            
            init() {
                this.trackUrl = this.$el.dataset.track;
                if (this.trackUrl) {
                    Alpine.store('audio').init(this.trackUrl, true);
                }
            },
            
            get isPlaying() {
                return Alpine.store('audio').isPlaying;
            },
            
            get currentTrack() {
                return Alpine.store('audio').currentTrack;
            },
            
            toggle() {
                Alpine.store('audio').toggle();
            },
            
            setVolume(volume) {
                Alpine.store('audio').setVolume(volume / 100);
            }
        };
    },
    
    // Social sharing component
    socialShare() {
        return {
            url: window.location.href,
            title: document.title,
            
            shareOnFacebook() {
                const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(this.url)}`;
                this.openShareWindow(shareUrl);
            },
            
            shareOnTwitter() {
                const shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(this.url)}&text=${encodeURIComponent(this.title)}`;
                this.openShareWindow(shareUrl);
            },
            
            shareOnWhatsApp() {
                const shareUrl = `https://wa.me/?text=${encodeURIComponent(this.title + ' ' + this.url)}`;
                this.openShareWindow(shareUrl);
            },
            
            copyToClipboard() {
                navigator.clipboard.writeText(this.url).then(() => {
                    this.showNotification('¡Enlace copiado al portapapeles!');
                });
            },
            
            openShareWindow(url) {
                window.open(url, '_blank', 'width=600,height=400');
            },
            
            showNotification(message) {
                this.$dispatch('show-notification', message);
            }
        };
    },
    
    // Notification system
    notifications() {
        return {
            notifications: [],
            
            init() {
                this.$watch('notifications', () => {
                    // Auto-remove notifications after 3 seconds
                    this.notifications.forEach((notification, index) => {
                        if (!notification.timeout) {
                            notification.timeout = setTimeout(() => {
                                this.remove(index);
                            }, 3000);
                        }
                    });
                });
            },
            
            add(message, type = 'info') {
                this.notifications.push({
                    id: Date.now(),
                    message,
                    type,
                    timeout: null
                });
            },
            
            remove(index) {
                if (this.notifications[index]) {
                    clearTimeout(this.notifications[index].timeout);
                    this.notifications.splice(index, 1);
                }
            }
        };
    }
};

// Global event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        // Gallery navigation
        if (Alpine.store('gallery').isOpen) {
            switch(e.key) {
                case 'ArrowRight':
                    e.preventDefault();
                    Alpine.store('gallery').next();
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    Alpine.store('gallery').previous();
                    break;
                case 'Escape':
                    e.preventDefault();
                    Alpine.store('gallery').close();
                    break;
            }
        }
        
        // Modal navigation
        if (Alpine.store('modal').active && e.key === 'Escape') {
            e.preventDefault();
            Alpine.store('modal').close();
        }
        
        // Audio controls
        if (e.key === ' ' && e.target === document.body) {
            e.preventDefault();
            Alpine.store('audio').toggle();
        }
    });
    
    // Custom event listeners
    window.addEventListener('show-notification', function(e) {
        // Find notification component and add notification
        const notificationComponent = document.querySelector('[x-data*="notifications"]');
        if (notificationComponent && notificationComponent._x_dataStack) {
            notificationComponent._x_dataStack[0].add(e.detail);
        }
    });
    
    // Prevent context menu on images for better UX
    document.addEventListener('contextmenu', function(e) {
        if (e.target.tagName === 'IMG' && e.target.closest('.gallery-image')) {
            e.preventDefault();
        }
    });
    
    // Handle offline/online status
    window.addEventListener('online', function() {
        console.log('Connection restored');
    });
    
    window.addEventListener('offline', function() {
        console.log('Connection lost');
    });
});

// Utility functions
window.nfcUtils = {
    // Format date
    formatDate(date, locale = 'es-ES') {
        return new Intl.DateTimeFormat(locale, {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }).format(new Date(date));
    },
    
    // Format currency
    formatCurrency(amount, currency = 'EUR', locale = 'es-ES') {
        return new Intl.NumberFormat(locale, {
            style: 'currency',
            currency: currency
        }).format(amount);
    },
    
    // Debounce function
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    // Throttle function
    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },
    
    // Generate random ID
    generateId(prefix = 'nfc') {
        return `${prefix}-${Math.random().toString(36).substr(2, 9)}`;
    }
};