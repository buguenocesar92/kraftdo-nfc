/**
 * AJAX Service - Centralized HTTP communications
 * Implements Service Pattern for API calls
 */
class AjaxService {
    constructor(config, eventBus) {
        this.config = config;
        this.eventBus = eventBus;
        this.defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        
        if (config.csrfToken) {
            this.defaultOptions.headers['X-CSRF-TOKEN'] = config.csrfToken;
        }
    }
    
    /**
     * Generic request method
     */
    async request(url, options = {}) {
        const finalOptions = this.mergeOptions(options);
        
        this.eventBus.emit('ajax:before', { url, options: finalOptions });
        
        try {
            const response = await fetch(url, finalOptions);
            const data = await this.handleResponse(response);
            
            this.eventBus.emit('ajax:success', { url, data, response });
            return data;
            
        } catch (error) {
            this.eventBus.emit('ajax:error', { url, error });
            throw error;
        }
    }
    
    /**
     * GET request
     */
    async get(url, options = {}) {
        return this.request(url, { ...options, method: 'GET' });
    }
    
    /**
     * POST request
     */
    async post(url, data = null, options = {}) {
        const postOptions = { 
            ...options, 
            method: 'POST',
            body: this.prepareBody(data, options)
        };
        
        return this.request(url, postOptions);
    }
    
    /**
     * PUT request
     */
    async put(url, data = null, options = {}) {
        const putOptions = { 
            ...options, 
            method: 'PUT',
            body: this.prepareBody(data, options)
        };
        
        return this.request(url, putOptions);
    }
    
    /**
     * DELETE request
     */
    async delete(url, options = {}) {
        return this.request(url, { ...options, method: 'DELETE' });
    }
    
    /**
     * Upload files with progress tracking
     */
    async upload(url, formData, onProgress = null) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            
            // Setup progress tracking
            if (onProgress && typeof onProgress === 'function') {
                xhr.upload.onprogress = (event) => {
                    if (event.lengthComputable) {
                        const percentComplete = (event.loaded / event.total) * 100;
                        onProgress(percentComplete);
                    }
                };
            }
            
            // Setup completion handler
            xhr.onload = () => {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        this.eventBus.emit('upload:success', { url, data });
                        resolve(data);
                    } catch (error) {
                        reject(new Error('Invalid JSON response'));
                    }
                } else {
                    const error = new Error(`Upload failed: ${xhr.status}`);
                    this.eventBus.emit('upload:error', { url, error });
                    reject(error);
                }
            };
            
            // Setup error handler
            xhr.onerror = () => {
                const error = new Error('Network error');
                this.eventBus.emit('upload:error', { url, error });
                reject(error);
            };
            
            // Setup request
            xhr.open('POST', url);
            
            // Add CSRF token if available
            if (this.config.csrfToken) {
                xhr.setRequestHeader('X-CSRF-TOKEN', this.config.csrfToken);
            }
            
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            this.eventBus.emit('upload:start', { url });
            xhr.send(formData);
        });
    }
    
    /**
     * Merge request options with defaults
     */
    mergeOptions(options) {
        const merged = { ...this.defaultOptions, ...options };
        
        // Merge headers
        if (options.headers) {
            merged.headers = { ...this.defaultOptions.headers, ...options.headers };
        }
        
        return merged;
    }
    
    /**
     * Prepare request body based on content type
     */
    prepareBody(data, options) {
        if (!data) return null;
        
        // If FormData, return as is
        if (data instanceof FormData) {
            // Remove Content-Type header to let browser set it with boundary
            if (options.headers) {
                delete options.headers['Content-Type'];
            }
            return data;
        }
        
        // If object, stringify as JSON
        if (typeof data === 'object') {
            return JSON.stringify(data);
        }
        
        // Otherwise return as string
        return String(data);
    }
    
    /**
     * Handle response based on content type
     */
    async handleResponse(response) {
        const contentType = response.headers.get('Content-Type') || '';
        
        if (!response.ok) {
            let errorMessage = `HTTP ${response.status}: ${response.statusText}`;
            
            try {
                if (contentType.includes('application/json')) {
                    const errorData = await response.json();
                    errorMessage = errorData.message || errorData.error || errorMessage;
                }
            } catch (e) {
                // Use default error message
            }
            
            throw new Error(errorMessage);
        }
        
        // Handle different response types
        if (contentType.includes('application/json')) {
            return await response.json();
        } else if (contentType.includes('text/')) {
            return await response.text();
        } else {
            return await response.blob();
        }
    }
    
    /**
     * Create form data from object
     */
    createFormData(data) {
        const formData = new FormData();
        
        const appendValue = (key, value) => {
            if (value === null || value === undefined) {
                return;
            }
            
            if (value instanceof File || value instanceof Blob) {
                formData.append(key, value);
            } else if (typeof value === 'object' && !(value instanceof Date)) {
                // Handle nested objects/arrays
                Object.keys(value).forEach(subKey => {
                    appendValue(`${key}[${subKey}]`, value[subKey]);
                });
            } else {
                formData.append(key, String(value));
            }
        };
        
        Object.keys(data).forEach(key => {
            appendValue(key, data[key]);
        });
        
        return formData;
    }
    
    /**
     * Utility method to handle Laravel validation errors
     */
    handleValidationErrors(error, formElement = null) {
        if (error.message && error.message.includes('422')) {
            this.eventBus.emit('validation:errors', { error, formElement });
            return true;
        }
        return false;
    }
}

window.AjaxService = AjaxService;