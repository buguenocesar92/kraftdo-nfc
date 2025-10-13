/**
 * Validation Service - Strategy Pattern for field validation
 */
class ValidationService {
    constructor(eventBus) {
        this.eventBus = eventBus;
        this.validators = new Map();
        this.rules = new Map();
        this.initDefaultValidators();
    }
    
    /**
     * Initialize default validators
     */
    initDefaultValidators() {
        // Required validator
        this.addValidator('required', (value, options = {}) => {
            const isValid = value !== null && value !== undefined && String(value).trim() !== '';
            return {
                isValid,
                message: options.message || 'Este campo es obligatorio.'
            };
        });
        
        // Email validator
        this.addValidator('email', (value, options = {}) => {
            if (!value) return { isValid: true, message: '' }; // Skip if empty
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const isValid = emailRegex.test(value);
            return {
                isValid,
                message: options.message || 'Formato de email inválido.'
            };
        });
        
        // Phone validator
        this.addValidator('phone', (value, options = {}) => {
            if (!value) return { isValid: true, message: '' }; // Skip if empty
            
            const phoneRegex = /^\+?[1-9][\d\s\-\(\)]{7,20}$/;
            const isValid = phoneRegex.test(value);
            return {
                isValid,
                message: options.message || 'Formato inválido. Ejemplos: +56912345678, +1 (721) 168-5477'
            };
        });
        
        // URL validator
        this.addValidator('url', (value, options = {}) => {
            if (!value) return { isValid: true, message: '' }; // Skip if empty
            
            try {
                new URL(value);
                return { isValid: true, message: '' };
            } catch {
                return {
                    isValid: false,
                    message: options.message || 'URL inválida.'
                };
            }
        });
        
        // Min length validator
        this.addValidator('minLength', (value, options = {}) => {
            if (!value) return { isValid: true, message: '' }; // Skip if empty
            
            const minLength = options.value || 1;
            const isValid = String(value).length >= minLength;
            return {
                isValid,
                message: options.message || `Mínimo ${minLength} caracteres requeridos.`
            };
        });
        
        // Max length validator
        this.addValidator('maxLength', (value, options = {}) => {
            if (!value) return { isValid: true, message: '' }; // Skip if empty
            
            const maxLength = options.value || 255;
            const isValid = String(value).length <= maxLength;
            return {
                isValid,
                message: options.message || `Máximo ${maxLength} caracteres permitidos.`
            };
        });
        
        // File type validator
        this.addValidator('fileType', (file, options = {}) => {
            if (!file) return { isValid: true, message: '' };
            
            const allowedTypes = options.types || [];
            const isValid = allowedTypes.some(type => {
                if (type.includes('/')) {
                    return file.type === type;
                } else {
                    return file.name.toLowerCase().endsWith(`.${type}`);
                }
            });
            
            return {
                isValid,
                message: options.message || `Tipo de archivo no permitido. Permitidos: ${allowedTypes.join(', ')}`
            };
        });
        
        // File size validator
        this.addValidator('fileSize', (file, options = {}) => {
            if (!file) return { isValid: true, message: '' };
            
            const maxSize = options.value || (10 * 1024 * 1024); // 10MB default
            const isValid = file.size <= maxSize;
            
            return {
                isValid,
                message: options.message || `Archivo demasiado grande. Máximo ${this.formatFileSize(maxSize)}.`
            };
        });
        
        // Custom content type validators
        this.addValidator('giftFrom', (value, options = {}) => {
            const isValid = value && String(value).trim().length > 0;
            return {
                isValid,
                message: options.message || 'El nombre de quien envía el regalo es obligatorio.'
            };
        });
        
        this.addValidator('giftTo', (value, options = {}) => {
            const isValid = value && String(value).trim().length > 0;
            return {
                isValid,
                message: options.message || 'El nombre de quien recibe el regalo es obligatorio.'
            };
        });
        
        this.addValidator('loveMessage', (value, options = {}) => {
            const isValid = value && String(value).trim().length > 0;
            return {
                isValid,
                message: options.message || 'El mensaje especial es obligatorio para los regalos.'
            };
        });
    }
    
    /**
     * Add a custom validator
     */
    addValidator(name, validator) {
        this.validators.set(name, validator);
        this.eventBus.emit('validator:added', { name });
    }
    
    /**
     * Get a validator
     */
    getValidator(name) {
        return this.validators.get(name);
    }
    
    /**
     * Set validation rules for a field
     */
    setRules(fieldName, rules) {
        this.rules.set(fieldName, rules);
    }
    
    /**
     * Get validation rules for a field
     */
    getRules(fieldName) {
        return this.rules.get(fieldName) || [];
    }
    
    /**
     * Validate a single field
     */
    validateField(fieldName, value, rules = null) {
        const fieldRules = rules || this.getRules(fieldName);
        const errors = [];
        
        for (const rule of fieldRules) {
            const { validator, options = {} } = this.parseRule(rule);
            const validatorFn = this.getValidator(validator);
            
            if (!validatorFn) {
                console.warn(`Validator ${validator} not found for field ${fieldName}`);
                continue;
            }
            
            const result = validatorFn(value, options);
            
            if (!result.isValid) {
                errors.push(result.message);
                break; // Stop on first error
            }
        }
        
        this.eventBus.emit('field:validated', { fieldName, value, errors, isValid: errors.length === 0 });
        
        return {
            isValid: errors.length === 0,
            errors
        };
    }
    
    /**
     * Validate multiple fields
     */
    validateFields(fields) {
        const results = {};
        let isFormValid = true;
        
        Object.keys(fields).forEach(fieldName => {
            const result = this.validateField(fieldName, fields[fieldName]);
            results[fieldName] = result;
            
            if (!result.isValid) {
                isFormValid = false;
            }
        });
        
        this.eventBus.emit('form:validated', { results, isValid: isFormValid });
        
        return {
            isValid: isFormValid,
            results
        };
    }
    
    /**
     * Validate form element
     */
    validateForm(formElement) {
        const formData = new FormData(formElement);
        const fields = {};
        
        // Extract form data
        for (let [key, value] of formData.entries()) {
            fields[key] = value;
        }
        
        return this.validateFields(fields);
    }
    
    /**
     * Parse validation rule string/object
     */
    parseRule(rule) {
        if (typeof rule === 'string') {
            return { validator: rule };
        }
        
        if (typeof rule === 'object') {
            const { validator, ...options } = rule;
            return { validator, options };
        }
        
        throw new Error(`Invalid rule format: ${rule}`);
    }
    
    /**
     * Format file size for display
     */
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    /**
     * Create validation rules for content types
     */
    getContentTypeRules(contentType) {
        const baseRules = {
            title: ['required', { validator: 'minLength', value: 3 }],
            description: [{ validator: 'maxLength', value: 1000 }]
        };
        
        switch (contentType) {
            case 'GIFT':
                return {
                    ...baseRules,
                    'data[from]': ['giftFrom'],
                    'data[to]': ['giftTo'],
                    'data[love_message]': ['loveMessage']
                };
                
            case 'PROFILE':
                return {
                    ...baseRules,
                    'data[contact_info][phone]': ['phone'],
                    'data[contact_info][email]': ['email']
                };
                
            case 'BUSINESS':
                return {
                    ...baseRules,
                    'data[business_info][name]': ['required']
                };
                
            case 'EVENT':
                return {
                    ...baseRules,
                    'data[event_info][name]': ['required'],
                    'data[event_info][date]': ['required']
                };
                
            default:
                return baseRules;
        }
    }
}

window.ValidationService = ValidationService;