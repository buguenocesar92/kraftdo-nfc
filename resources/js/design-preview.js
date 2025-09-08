/**
 * Live design preview updates for token customization
 */
class DesignPreview {
    constructor() {
        this.previewContainer = null;
        this.colorInputs = [];
        this.init();
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupPreview());
        } else {
            this.setupPreview();
        }
    }

    setupPreview() {
        this.previewContainer = document.querySelector('.design-preview');
        this.colorInputs = document.querySelectorAll('input[type="color"]');
        
        if (this.previewContainer) {
            this.setupColorPickers();
            this.setupFormInputs();
            this.updatePreview();
        }
    }

    setupColorPickers() {
        this.colorInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                this.updatePreviewColor(e.target);
            });

            input.addEventListener('change', (e) => {
                this.updatePreviewColor(e.target);
            });
        });
    }

    setupFormInputs() {
        // Listen for text input changes
        const textInputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], textarea');
        textInputs.forEach(input => {
            input.addEventListener('input', () => {
                this.updatePreviewText();
            });
        });

        // Listen for file uploads (profile images)
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.updatePreviewImage(e.target);
            });
        });

        // Listen for select changes
        const selectInputs = document.querySelectorAll('select');
        selectInputs.forEach(select => {
            select.addEventListener('change', () => {
                this.updatePreview();
            });
        });
    }

    updatePreviewColor(colorInput) {
        const colorName = colorInput.name || colorInput.id;
        const colorValue = colorInput.value;
        
        // Update CSS custom properties in preview
        if (this.previewContainer) {
            this.previewContainer.style.setProperty(`--${colorName}`, colorValue);
            
            // Handle specific color mappings
            switch (colorName) {
                case 'primary_color':
                    this.previewContainer.style.setProperty('--primary', colorValue);
                    this.updateElementsWithClass('.bg-primary', { backgroundColor: colorValue });
                    this.updateElementsWithClass('.text-primary', { color: colorValue });
                    this.updateElementsWithClass('.border-primary', { borderColor: colorValue });
                    break;
                    
                case 'secondary_color':
                    this.previewContainer.style.setProperty('--secondary', colorValue);
                    this.updateElementsWithClass('.bg-secondary', { backgroundColor: colorValue });
                    this.updateElementsWithClass('.text-secondary', { color: colorValue });
                    break;
                    
                case 'accent_color':
                    this.previewContainer.style.setProperty('--accent', colorValue);
                    this.updateElementsWithClass('.bg-accent', { backgroundColor: colorValue });
                    this.updateElementsWithClass('.text-accent', { color: colorValue });
                    break;
                    
                case 'background_color':
                    this.previewContainer.style.setProperty('--background', colorValue);
                    this.updateElementsWithClass('.bg-background', { backgroundColor: colorValue });
                    break;
                    
                case 'text_color':
                    this.previewContainer.style.setProperty('--text', colorValue);
                    this.updateElementsWithClass('.text-preview', { color: colorValue });
                    break;
            }
        }

        // Trigger custom event
        const colorChangeEvent = new CustomEvent('previewColorChanged', {
            detail: {
                colorName: colorName,
                colorValue: colorValue,
                input: colorInput
            }
        });
        document.dispatchEvent(colorChangeEvent);
    }

    updateElementsWithClass(className, styles) {
        const elements = this.previewContainer.querySelectorAll(className);
        elements.forEach(element => {
            Object.assign(element.style, styles);
        });
    }

    updatePreviewText() {
        const textMappings = {
            'name': '.preview-name',
            'title': '.preview-title',
            'description': '.preview-description',
            'company': '.preview-company',
            'position': '.preview-position',
            'email': '.preview-email',
            'phone': '.preview-phone',
            'website': '.preview-website'
        };

        Object.entries(textMappings).forEach(([inputName, selector]) => {
            const input = document.querySelector(`input[name="${inputName}"], textarea[name="${inputName}"]`);
            const previewElement = this.previewContainer?.querySelector(selector);
            
            if (input && previewElement) {
                previewElement.textContent = input.value || previewElement.getAttribute('data-placeholder') || '';
            }
        });
    }

    updatePreviewImage(fileInput) {
        const file = fileInput.files[0];
        const previewImg = this.previewContainer?.querySelector('.preview-image');
        
        if (file && previewImg) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    updatePreview() {
        this.updatePreviewText();
        
        // Update any dynamic styles based on current form values
        this.colorInputs.forEach(input => {
            this.updatePreviewColor(input);
        });

        // Trigger custom event for full preview update
        const previewUpdateEvent = new CustomEvent('previewUpdated', {
            detail: {
                container: this.previewContainer
            }
        });
        document.dispatchEvent(previewUpdateEvent);
    }

    // Utility methods for external access
    setPreviewColor(colorName, colorValue) {
        const colorInput = document.querySelector(`input[name="${colorName}"], input[id="${colorName}"]`);
        if (colorInput) {
            colorInput.value = colorValue;
            this.updatePreviewColor(colorInput);
        }
    }

    getPreviewColors() {
        const colors = {};
        this.colorInputs.forEach(input => {
            const colorName = input.name || input.id;
            colors[colorName] = input.value;
        });
        return colors;
    }

    resetPreview() {
        // Reset to default colors and text
        const defaultColors = {
            primary_color: '#3B82F6',
            secondary_color: '#10B981',
            accent_color: '#F59E0B',
            background_color: '#FFFFFF',
            text_color: '#1F2937'
        };

        Object.entries(defaultColors).forEach(([colorName, colorValue]) => {
            this.setPreviewColor(colorName, colorValue);
        });

        this.updatePreview();
    }

    // Static methods for global access
    static getInstance() {
        if (!window.designPreview) {
            window.designPreview = new DesignPreview();
        }
        return window.designPreview;
    }
}

// Initialize design preview
window.designPreview = new DesignPreview();

// Global functions for backward compatibility
window.updatePreviewColor = function(colorInput) {
    DesignPreview.getInstance().updatePreviewColor(colorInput);
};

window.updatePreview = function() {
    DesignPreview.getInstance().updatePreview();
};

window.setPreviewColor = function(colorName, colorValue) {
    DesignPreview.getInstance().setPreviewColor(colorName, colorValue);
};

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DesignPreview;
}