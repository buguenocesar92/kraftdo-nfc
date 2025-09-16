/**
 * CSRF token helper utilities for Laravel
 */
class CSRFHelpers {
    constructor() {
        this.token = null;
        this.init();
    }

    init() {
        this.refreshToken();
    }

    /**
     * Get the current CSRF token from the meta tag
     * @returns {string|null} The CSRF token or null if not found
     */
    getToken() {
        if (this.token) {
            return this.token;
        }

        // Try to get from meta tag first
        const metaToken = document.querySelector('meta[name="csrf-token"]');
        if (metaToken) {
            this.token = metaToken.getAttribute('content');
            return this.token;
        }

        // Try to get from hidden input (common in forms)
        const inputToken = document.querySelector('input[name="_token"]');
        if (inputToken) {
            this.token = inputToken.value;
            return this.token;
        }

        // Try to get from Laravel's global window object
        if (window.Laravel && window.Laravel.csrfToken) {
            this.token = window.Laravel.csrfToken;
            return this.token;
        }

        console.warn('CSRF token not found in meta tag, input field, or Laravel global object');
        return null;
    }

    /**
     * Refresh the CSRF token from the DOM
     */
    refreshToken() {
        this.token = null;
        return this.getToken();
    }

    /**
     * Get CSRF token as an object for use in AJAX requests
     * @returns {Object} Object with _token property
     */
    getTokenObject() {
        const token = this.getToken();
        return token ? { _token: token } : {};
    }

    /**
     * Get CSRF token as headers object for fetch/axios requests
     * @returns {Object} Headers object with X-CSRF-TOKEN
     */
    getHeaders() {
        const token = this.getToken();
        return token ? { 'X-CSRF-TOKEN': token } : {};
    }

    /**
     * Add CSRF token to form data
     * @param {FormData} formData - The FormData object to add token to
     * @returns {FormData} The modified FormData object
     */
    addToFormData(formData) {
        const token = this.getToken();
        if (token) {
            formData.append('_token', token);
        }
        return formData;
    }

    /**
     * Add CSRF token to a regular object (for JSON requests)
     * @param {Object} data - The data object to add token to
     * @returns {Object} The modified data object
     */
    addToData(data) {
        const token = this.getToken();
        if (token) {
            data._token = token;
        }
        return data;
    }

    /**
     * Create a fetch request with CSRF token included
     * @param {string} url - The URL to fetch
     * @param {Object} options - Fetch options
     * @returns {Promise} Fetch promise with CSRF token included
     */
    fetch(url, options = {}) {
        const token = this.getToken();
        
        if (token) {
            // Add CSRF token to headers
            options.headers = {
                ...options.headers,
                'X-CSRF-TOKEN': token
            };

            // If sending JSON data, ensure Content-Type is set
            if (options.body && typeof options.body === 'string') {
                options.headers['Content-Type'] = 'application/json';
            }
        }

        return fetch(url, options);
    }

    /**
     * Create a XMLHttpRequest with CSRF token included
     * @param {string} method - HTTP method
     * @param {string} url - The URL
     * @param {Object} data - Data to send
     * @returns {Promise} Promise that resolves with the response
     */
    xhr(method, url, data = null) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open(method, url, true);

            // Set CSRF token header
            const token = this.getToken();
            if (token) {
                xhr.setRequestHeader('X-CSRF-TOKEN', token);
            }

            // Handle JSON data
            if (data && typeof data === 'object' && !(data instanceof FormData)) {
                xhr.setRequestHeader('Content-Type', 'application/json');
                data = JSON.stringify(this.addToData(data));
            }

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        resolve(response);
                    } catch (e) {
                        resolve(xhr.responseText);
                    }
                } else {
                    reject(new Error(`HTTP ${xhr.status}: ${xhr.statusText}`));
                }
            };

            xhr.onerror = function() {
                reject(new Error('Network error'));
            };

            xhr.send(data);
        });
    }

    /**
     * Set up all forms on the page to include CSRF token
     */
    setupForms() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            this.setupForm(form);
        });
    }

    /**
     * Set up a specific form to include CSRF token
     * @param {HTMLFormElement} form - The form element
     */
    setupForm(form) {
        const token = this.getToken();
        if (!token) return;

        // Check if form already has a CSRF token
        let tokenInput = form.querySelector('input[name="_token"]');
        
        if (!tokenInput) {
            tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            form.appendChild(tokenInput);
        }
        
        tokenInput.value = token;
    }

    /**
     * Validate that CSRF token exists and is not empty
     * @returns {boolean} True if token is valid
     */
    isValid() {
        const token = this.getToken();
        return token !== null && token.length > 0;
    }

    /**
     * Handle CSRF token mismatch (419 errors)
     * @param {Function} callback - Function to call when token needs refresh
     */
    onTokenMismatch(callback) {
        // Monitor for 419 status codes (CSRF token mismatch)
        const originalFetch = window.fetch;
        window.fetch = (...args) => {
            return originalFetch(...args).then(response => {
                if (response.status === 419) {
                    this.refreshToken();
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
                return response;
            });
        };
    }
}

// Global CSRF helper instance
const csrfHelpers = new CSRFHelpers();

// Global function for backward compatibility
window.getCSRFToken = function() {
    return csrfHelpers.getToken();
};

// Setup forms automatically when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        csrfHelpers.setupForms();
    });
} else {
    csrfHelpers.setupForms();
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CSRFHelpers;
}

// Global access
window.CSRFHelpers = CSRFHelpers;
window.csrfHelpers = csrfHelpers;