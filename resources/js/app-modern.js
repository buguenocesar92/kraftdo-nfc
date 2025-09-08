/**
 * Modern Frontend Architecture Entry Point
 * 
 * This file initializes the new modular frontend architecture
 * following SOLID principles and modern design patterns.
 */

// Import all required modules and services
import './core/App.js';
import './services/AjaxService.js';
import './services/ValidationService.js';
import './services/NotificationSystem.js';
import './modules/WizardManager.js';
import './modules/UploadManager.js';
import './modules/MultimediaManager.js';

/**
 * Application Initialization
 * 
 * SOLID Principles Applied:
 * - Single Responsibility: Each class has one clear purpose
 * - Open/Closed: New modules can be added without modifying core
 * - Liskov Substitution: All services implement predictable interfaces
 * - Interface Segregation: Services expose only necessary methods
 * - Dependency Inversion: Core depends on abstractions, not concretions
 */
class ModernFrontendApp {
    constructor() {
        this.isReady = false;
        this.readyCallbacks = [];
    }
    
    /**
     * Initialize the application when DOM is ready
     */
    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.start());
        } else {
            this.start();
        }
    }
    
    /**
     * Start the application
     */
    start() {
        try {
            // Initialize global app instance
            window.app = new KraftdoApp();
            
            // Set up global error handling
            this.setupErrorHandling();
            
            // Set up global utilities
            this.setupUtilities();
            
            // Mark as ready
            this.isReady = true;
            
            // Execute ready callbacks
            this.readyCallbacks.forEach(callback => {
                try {
                    callback();
                } catch (error) {
                    console.error('Error in ready callback:', error);
                }
            });
            
            console.log('🎉 Modern Frontend Architecture initialized successfully!');
            
        } catch (error) {
            console.error('❌ Failed to initialize Modern Frontend Architecture:', error);
            
            // Fallback to legacy behavior if available
            this.fallbackToLegacy();
        }
    }
    
    /**
     * Setup global error handling
     */
    setupErrorHandling() {
        window.addEventListener('error', (event) => {
            console.error('Global JS Error:', event.error);
            
            if (window.app?.getModule('notifications')) {
                window.app.getModule('notifications').error(
                    'Ha ocurrido un error inesperado. Por favor, recarga la página.'
                );
            }
        });
        
        window.addEventListener('unhandledrejection', (event) => {
            console.error('Unhandled Promise Rejection:', event.reason);
            
            if (window.app?.getModule('notifications')) {
                window.app.getModule('notifications').error(
                    'Error en la aplicación. Por favor, inténtalo nuevamente.'
                );
            }
        });
    }
    
    /**
     * Setup global utilities and helpers
     */
    setupUtilities() {
        // CSRF Token helper
        window.getCsrfToken = () => {
            return window.app?.getConfig('csrfToken') || 
                   document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        };
        
        // Global notification helpers
        window.showNotification = (message, type = 'info', options = {}) => {
            const notifications = window.app?.getModule('notifications');
            if (notifications) {
                return notifications.show(message, type, options);
            }
        };
        
        // Content publishing helper (maintains backward compatibility)
        // Only define if not already defined by legacy system
        if (!window.publishContent) {
            window.publishContent = () => {
            const notifications = window.app?.getModule('notifications');
            
            if (notifications) {
                const confirmed = notifications.confirm(
                    '¿Estás seguro de que quieres publicar este contenido? Una vez publicado, solo podrás hacer modificaciones limitadas y no podrás volver a modo borrador.'
                );
                
                if (confirmed) {
                    this.executeContentPublication();
                }
            } else {
                // Fallback to browser confirm
                const confirmed = confirm(
                    '¿Estás seguro de que quieres publicar este contenido? Una vez publicado, solo podrás hacer modificaciones limitadas y no podrás volver a modo borrador.'
                );
                
                if (confirmed) {
                    this.executeContentPublication();
                }
            }
            };
        }
        
        // Form validation helper
        window.validateForm = (formElement) => {
            const validator = window.app?.getModule('validator');
            if (validator && formElement) {
                return validator.validateForm(formElement);
            }
            return { isValid: true, results: {} };
        };
        
        // File upload helpers
        window.handleFileUpload = (file, type, onProgress) => {
            const uploads = window.app?.getModule('uploads');
            if (uploads) {
                const uploader = uploads.getUploader(`${type}-upload-area`);
                if (uploader) {
                    return uploader.processFile(file);
                }
            }
        };
    }
    
    /**
     * Execute content publication
     */
    executeContentPublication() {
        const contentId = this.extractContentId();
        if (!contentId) {
            console.error('Could not determine content ID for publication');
            return;
        }
        
        const ajax = window.app?.getModule('ajax');
        if (ajax) {
            // Use modern AJAX service
            ajax.post(`/content/${contentId}/publish`, {}, {
                headers: {
                    'X-CSRF-TOKEN': window.getCsrfToken()
                }
            }).then(response => {
                if (response.success) {
                    window.showNotification('¡Contenido publicado exitosamente!', 'success');
                    
                    // Redirect if URL provided
                    if (response.redirect_url) {
                        setTimeout(() => {
                            window.location.href = response.redirect_url;
                        }, 1500);
                    }
                }
            }).catch(error => {
                window.showNotification('Error al publicar el contenido: ' + error.message, 'error');
            });
        } else {
            // Fallback to form submission
            this.createPublishForm(contentId);
        }
    }
    
    /**
     * Extract content ID from current page context
     */
    extractContentId() {
        // Try different methods to get content ID
        const urlMatch = window.location.pathname.match(/\/content\/(\d+)/);
        if (urlMatch) return urlMatch[1];
        
        const metaContent = document.querySelector('meta[name="content-id"]');
        if (metaContent) return metaContent.getAttribute('content');
        
        const contentElement = document.querySelector('[data-content-id]');
        if (contentElement) return contentElement.dataset.contentId;
        
        return null;
    }
    
    /**
     * Create and submit publish form (fallback)
     */
    createPublishForm(contentId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/content/${contentId}/publish`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = window.getCsrfToken();
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
    
    /**
     * Fallback to legacy behavior
     */
    fallbackToLegacy() {
        console.warn('🔄 Falling back to legacy frontend behavior');
        
        // Re-enable legacy scripts if they exist
        const legacyScripts = document.querySelectorAll('script[data-legacy]');
        legacyScripts.forEach(script => {
            script.disabled = false;
        });
    }
    
    /**
     * Register callback to execute when app is ready
     */
    ready(callback) {
        if (this.isReady) {
            callback();
        } else {
            this.readyCallbacks.push(callback);
        }
    }
    
    /**
     * Check if modern architecture is available and working
     */
    isModernArchitectureAvailable() {
        return this.isReady && 
               window.app && 
               typeof window.app.getModule === 'function';
    }
}

// Initialize the modern frontend app
const modernApp = new ModernFrontendApp();
modernApp.init();

// Expose for external use
window.modernApp = modernApp;

// Export for module systems
export default modernApp;