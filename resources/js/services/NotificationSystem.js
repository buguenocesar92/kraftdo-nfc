/**
 * Notification System - Observer Pattern for user feedback
 */
class NotificationSystem {
    constructor(eventBus) {
        this.eventBus = eventBus;
        this.container = null;
        this.notifications = new Map();
        this.nextId = 1;
        
        this.init();
    }
    
    /**
     * Initialize notification system
     */
    init() {
        this.createContainer();
        this.bindEvents();
        this.eventBus.emit('notifications:initialized');
    }
    
    /**
     * Create notification container
     */
    createContainer() {
        // Check if container already exists
        this.container = document.getElementById('notification-container');
        
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.id = 'notification-container';
            this.container.className = 'fixed top-4 right-4 z-50 space-y-2 max-w-sm w-full';
            document.body.appendChild(this.container);
        }
    }
    
    /**
     * Bind event listeners
     */
    bindEvents() {
        // Listen for notification requests
        this.eventBus.on('notification:show', (data) => {
            this.show(data.message, data.type, data.options);
        });
        
        this.eventBus.on('notification:hide', (data) => {
            if (data.id) {
                this.hide(data.id);
            } else {
                this.hideAll();
            }
        });
        
        // Listen for AJAX events
        this.eventBus.on('ajax:success', () => {
            // Could show success notifications for certain operations
        });
        
        this.eventBus.on('ajax:error', (data) => {
            this.error('Error en la operación: ' + data.error.message);
        });
        
        // Listen for validation events
        this.eventBus.on('wizard:validationErrors', () => {
            this.warning('Por favor, corrige los errores del formulario.');
        });
        
        // Listen for upload events
        this.eventBus.on('file:uploaded', (data) => {
            this.success(`Archivo ${data.file.name} subido exitosamente.`);
        });
        
        this.eventBus.on('file:upload-error', (data) => {
            this.error(`Error al subir ${data.file.name}: ${data.error.message}`);
        });
    }
    
    /**
     * Show notification
     */
    show(message, type = 'info', options = {}) {
        const id = this.nextId++;
        const notification = this.createNotification(id, message, type, options);
        
        this.container.appendChild(notification);
        this.notifications.set(id, notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full', 'opacity-0');
            notification.classList.add('translate-x-0', 'opacity-100');
        }, 10);
        
        // Auto-remove after duration
        const duration = options.duration || this.getDefaultDuration(type);
        if (duration > 0) {
            setTimeout(() => this.hide(id), duration);
        }
        
        this.eventBus.emit('notification:shown', { id, message, type });
        
        return id;
    }
    
    /**
     * Create notification element
     */
    createNotification(id, message, type, options) {
        const notification = document.createElement('div');
        notification.id = `notification-${id}`;
        notification.className = `
            transform translate-x-full opacity-0 transition-all duration-300 ease-in-out
            bg-white border-l-4 rounded-lg shadow-lg p-4 flex items-start space-x-3
            ${this.getTypeClasses(type)}
        `.trim();
        
        // Icon
        const icon = document.createElement('div');
        icon.className = 'flex-shrink-0';
        icon.innerHTML = this.getTypeIcon(type);
        
        // Content
        const content = document.createElement('div');
        content.className = 'flex-1 min-w-0';
        
        const messageEl = document.createElement('p');
        messageEl.className = 'text-sm font-medium text-gray-900';
        messageEl.textContent = message;
        content.appendChild(messageEl);
        
        if (options.subtitle) {
            const subtitle = document.createElement('p');
            subtitle.className = 'text-sm text-gray-500 mt-1';
            subtitle.textContent = options.subtitle;
            content.appendChild(subtitle);
        }
        
        // Close button
        const closeBtn = document.createElement('button');
        closeBtn.className = 'flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors';
        closeBtn.innerHTML = '<i class="fas fa-times text-sm"></i>';
        closeBtn.onclick = () => this.hide(id);
        
        notification.appendChild(icon);
        notification.appendChild(content);
        notification.appendChild(closeBtn);
        
        return notification;
    }
    
    /**
     * Get CSS classes for notification type
     */
    getTypeClasses(type) {
        const classes = {
            success: 'border-green-400 bg-green-50',
            error: 'border-red-400 bg-red-50',
            warning: 'border-yellow-400 bg-yellow-50',
            info: 'border-blue-400 bg-blue-50'
        };
        
        return classes[type] || classes.info;
    }
    
    /**
     * Get icon for notification type
     */
    getTypeIcon(type) {
        const icons = {
            success: '<i class="fas fa-check-circle text-green-400 text-lg"></i>',
            error: '<i class="fas fa-exclamation-circle text-red-400 text-lg"></i>',
            warning: '<i class="fas fa-exclamation-triangle text-yellow-400 text-lg"></i>',
            info: '<i class="fas fa-info-circle text-blue-400 text-lg"></i>'
        };
        
        return icons[type] || icons.info;
    }
    
    /**
     * Get default duration for notification type
     */
    getDefaultDuration(type) {
        const durations = {
            success: 4000, // 4 seconds
            error: 6000,   // 6 seconds
            warning: 5000, // 5 seconds
            info: 4000     // 4 seconds
        };
        
        return durations[type] || 4000;
    }
    
    /**
     * Hide notification
     */
    hide(id) {
        const notification = this.notifications.get(id);
        if (!notification) return;
        
        // Animate out
        notification.classList.remove('translate-x-0', 'opacity-100');
        notification.classList.add('translate-x-full', 'opacity-0');
        
        // Remove from DOM after animation
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            this.notifications.delete(id);
        }, 300);
        
        this.eventBus.emit('notification:hidden', { id });
    }
    
    /**
     * Hide all notifications
     */
    hideAll() {
        this.notifications.forEach((notification, id) => {
            this.hide(id);
        });
    }
    
    /**
     * Convenience methods
     */
    success(message, options = {}) {
        return this.show(message, 'success', options);
    }
    
    error(message, options = {}) {
        return this.show(message, 'error', options);
    }
    
    warning(message, options = {}) {
        return this.show(message, 'warning', options);
    }
    
    info(message, options = {}) {
        return this.show(message, 'info', options);
    }
    
    /**
     * Show loading notification
     */
    loading(message = 'Cargando...', options = {}) {
        const loadingOptions = {
            ...options,
            duration: 0, // Don't auto-hide
            subtitle: options.subtitle || 'Por favor espera...'
        };
        
        return this.show(message, 'info', loadingOptions);
    }
    
    /**
     * Update existing notification
     */
    update(id, message, type = null, options = {}) {
        const notification = this.notifications.get(id);
        if (!notification) return;
        
        // Update message
        const messageEl = notification.querySelector('p.font-medium');
        if (messageEl) {
            messageEl.textContent = message;
        }
        
        // Update subtitle if provided
        if (options.subtitle) {
            let subtitleEl = notification.querySelector('p.text-gray-500');
            if (!subtitleEl) {
                subtitleEl = document.createElement('p');
                subtitleEl.className = 'text-sm text-gray-500 mt-1';
                messageEl.parentNode.appendChild(subtitleEl);
            }
            subtitleEl.textContent = options.subtitle;
        }
        
        // Update type if provided
        if (type) {
            // Remove old type classes
            notification.className = notification.className.replace(
                /border-(green|red|yellow|blue)-400 bg-(green|red|yellow|blue)-50/g,
                ''
            );
            
            // Add new type classes
            notification.className += ' ' + this.getTypeClasses(type);
            
            // Update icon
            const iconEl = notification.querySelector('div:first-child');
            if (iconEl) {
                iconEl.innerHTML = this.getTypeIcon(type);
            }
        }
        
        this.eventBus.emit('notification:updated', { id, message, type });
    }
    
    /**
     * Show progress notification
     */
    progress(message, percentage = 0, options = {}) {
        const progressOptions = {
            ...options,
            duration: 0,
            subtitle: `${Math.round(percentage)}% completado`
        };
        
        const id = options.id || this.show(message, 'info', progressOptions);
        
        if (options.id) {
            this.update(id, message, 'info', progressOptions);
        }
        
        return id;
    }
    
    /**
     * Show confirmation dialog (using browser confirm for now)
     */
    confirm(message, callback, options = {}) {
        const result = confirm(message);
        if (callback) {
            callback(result);
        }
        return result;
    }
    
    /**
     * Destroy notification system
     */
    destroy() {
        this.hideAll();
        
        if (this.container && this.container.parentNode) {
            this.container.parentNode.removeChild(this.container);
        }
        
        this.notifications.clear();
        this.eventBus.emit('notifications:destroyed');
    }
}

window.NotificationSystem = NotificationSystem;