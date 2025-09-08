/**
 * Main Application Class
 * Implements Module Pattern and acts as central coordinator
 */
class KraftdoApp {
    constructor() {
        this.modules = new Map();
        this.config = {
            debug: window.APP_DEBUG || false,
            apiEndpoint: window.API_ENDPOINT || '/api',
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        };
        this.isInitialized = false;
        
        this.logger = new Logger(this.config.debug);
        this.eventBus = new EventBus();
        
        this.init();
    }
    
    /**
     * Initialize the application
     */
    init() {
        if (this.isInitialized) {
            this.logger.warn('Application already initialized');
            return;
        }
        
        this.logger.info('🚀 Initializing KraftdoApp');
        
        // Initialize core modules
        this.initCoreModules();
        
        // Auto-discover and initialize page-specific modules
        this.discoverModules();
        
        this.isInitialized = true;
        this.eventBus.emit('app:initialized');
        
        this.logger.info('✅ KraftdoApp initialized successfully');
    }
    
    /**
     * Initialize core modules that are always needed
     */
    initCoreModules() {
        this.registerModule('ajax', new AjaxService(this.config, this.eventBus));
        this.registerModule('notifications', new NotificationSystem(this.eventBus));
        this.registerModule('validator', new ValidationService(this.eventBus));
    }
    
    /**
     * Auto-discover modules based on page context
     */
    discoverModules() {
        // Check for wizard presence
        if (document.querySelector('.wizard-step-content')) {
            this.registerModule('wizard', new WizardManager(this.eventBus, this.getModule('validator')));
        }
        
        // Check for upload components
        if (document.querySelector('[id$="-upload-area"]')) {
            this.registerModule('uploads', new UploadManager(this.eventBus, this.getModule('ajax')));
        }
        
        // Check for multimedia components
        if (document.querySelector('[name*="multimedia"]')) {
            this.registerModule('multimedia', new MultimediaManager(this.eventBus));
        }
        
        // Check for form validation
        if (document.querySelector('form[data-validate]')) {
            this.registerModule('formValidator', new FormValidationManager(this.eventBus, this.getModule('validator')));
        }
    }
    
    /**
     * Register a module
     */
    registerModule(name, module) {
        if (this.modules.has(name)) {
            this.logger.warn(`Module ${name} already exists, replacing...`);
        }
        
        this.modules.set(name, module);
        this.logger.info(`📦 Registered module: ${name}`);
        
        // Initialize module if it has init method
        if (typeof module.init === 'function') {
            module.init();
        }
        
        this.eventBus.emit('module:registered', { name, module });
    }
    
    /**
     * Get a module
     */
    getModule(name) {
        if (!this.modules.has(name)) {
            this.logger.error(`Module ${name} not found`);
            return null;
        }
        return this.modules.get(name);
    }
    
    /**
     * Check if module exists
     */
    hasModule(name) {
        return this.modules.has(name);
    }
    
    /**
     * Get configuration
     */
    getConfig(key = null) {
        if (key === null) {
            return this.config;
        }
        return this.config[key];
    }
    
    /**
     * Destroy application
     */
    destroy() {
        this.logger.info('🔥 Destroying KraftdoApp');
        
        // Destroy all modules
        this.modules.forEach((module, name) => {
            if (typeof module.destroy === 'function') {
                module.destroy();
            }
            this.logger.info(`🗑️ Destroyed module: ${name}`);
        });
        
        this.modules.clear();
        this.eventBus.removeAllListeners();
        this.isInitialized = false;
        
        this.logger.info('✅ KraftdoApp destroyed');
    }
}

/**
 * Logger utility
 */
class Logger {
    constructor(debug = false) {
        this.debug = debug;
        this.prefix = '[KraftdoApp]';
    }
    
    info(message, data = null) {
        if (!this.debug) return;
        console.log(`${this.prefix} ℹ️ ${message}`, data || '');
    }
    
    warn(message, data = null) {
        if (!this.debug) return;
        console.warn(`${this.prefix} ⚠️ ${message}`, data || '');
    }
    
    error(message, data = null) {
        console.error(`${this.prefix} ❌ ${message}`, data || '');
    }
    
    debug(message, data = null) {
        if (!this.debug) return;
        console.debug(`${this.prefix} 🐛 ${message}`, data || '');
    }
}

/**
 * Event Bus for module communication
 */
class EventBus {
    constructor() {
        this.listeners = new Map();
    }
    
    on(event, callback) {
        if (!this.listeners.has(event)) {
            this.listeners.set(event, []);
        }
        this.listeners.get(event).push(callback);
        
        // Return unsubscribe function
        return () => this.off(event, callback);
    }
    
    off(event, callback) {
        if (!this.listeners.has(event)) return;
        
        const callbacks = this.listeners.get(event);
        const index = callbacks.indexOf(callback);
        if (index > -1) {
            callbacks.splice(index, 1);
        }
        
        if (callbacks.length === 0) {
            this.listeners.delete(event);
        }
    }
    
    emit(event, data = null) {
        if (!this.listeners.has(event)) return;
        
        this.listeners.get(event).forEach(callback => {
            try {
                callback(data);
            } catch (error) {
                console.error(`Error in event listener for ${event}:`, error);
            }
        });
    }
    
    removeAllListeners() {
        this.listeners.clear();
    }
}

// Make classes available globally
window.KraftdoApp = KraftdoApp;
window.Logger = Logger;
window.EventBus = EventBus;