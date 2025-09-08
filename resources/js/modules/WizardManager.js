/**
 * Wizard Manager - State Pattern for wizard navigation
 */
class WizardManager {
    constructor(eventBus, validationService) {
        this.eventBus = eventBus;
        this.validationService = validationService;
        this.state = new WizardState();
        this.initialized = false;
        
        // Bind methods
        this.handleNext = this.handleNext.bind(this);
        this.handlePrevious = this.handlePrevious.bind(this);
        this.handleStepClick = this.handleStepClick.bind(this);
    }
    
    /**
     * Initialize wizard
     */
    init() {
        if (this.initialized) return;
        
        this.setupInitialState();
        this.bindEvents();
        this.setupValidationRules();
        this.updateUI();
        
        this.initialized = true;
        this.eventBus.emit('wizard:initialized', this.state);
    }
    
    /**
     * Setup initial wizard state
     */
    setupInitialState() {
        // Detect content type and total steps
        const contentTypeElement = document.querySelector('[data-content-type]');
        const contentType = contentTypeElement?.dataset.contentType || 'GIFT';
        
        this.state.contentType = contentType;
        this.state.totalSteps = contentType === 'PROFILE' ? 5 : 4;
        this.state.currentStep = 1;
        
        // Find step elements
        this.state.stepElements = document.querySelectorAll('.wizard-step-content');
        
        // Find navigation elements
        this.state.nextBtn = document.getElementById('next-step');
        this.state.prevBtn = document.getElementById('prev-step');
        this.state.submitBtn = document.getElementById('submit-form');
        this.state.previewBtn = document.getElementById('preview-with-save-btn');
        this.state.publishBtn = document.getElementById('publish-btn');
    }
    
    /**
     * Bind event listeners
     */
    bindEvents() {
        // Navigation buttons
        this.state.nextBtn?.addEventListener('click', this.handleNext);
        this.state.prevBtn?.addEventListener('click', this.handlePrevious);
        
        // Step indicators (allow direct navigation)
        document.querySelectorAll('.wizard-step').forEach(step => {
            step.addEventListener('click', this.handleStepClick);
        });
        
        // Form submission
        this.state.submitBtn?.addEventListener('click', this.handleSubmit.bind(this));
        this.state.previewBtn?.addEventListener('click', this.handlePreviewWithSave.bind(this));
    }
    
    /**
     * Setup validation rules based on content type
     */
    setupValidationRules() {
        const rules = this.validationService.getContentTypeRules(this.state.contentType);
        
        Object.keys(rules).forEach(fieldName => {
            this.validationService.setRules(fieldName, rules[fieldName]);
        });
        
        // Setup special validation for contact info (profile only)
        if (this.state.contentType === 'PROFILE') {
            this.validationService.addValidator('contactRequired', (fields) => {
                const email = fields['data[contact_info][email]'];
                const phone = fields['data[contact_info][phone]'];
                
                const hasEmail = email && String(email).trim();
                const hasPhone = phone && String(phone).trim();
                
                return {
                    isValid: hasEmail || hasPhone,
                    message: 'Debes proporcionar al menos un email o teléfono de contacto.'
                };
            });
        }
    }
    
    /**
     * Handle next step
     */
    handleNext() {
        if (!this.canAdvance()) return;
        
        const validationResult = this.validateCurrentStep();
        
        if (!validationResult.isValid) {
            this.showValidationErrors(validationResult.errors);
            return;
        }
        
        this.advance();
    }
    
    /**
     * Handle previous step
     */
    handlePrevious() {
        if (this.state.currentStep > 1) {
            this.goToStep(this.state.currentStep - 1);
        }
    }
    
    /**
     * Handle step indicator click
     */
    handleStepClick(event) {
        const targetStep = parseInt(event.currentTarget.dataset.step);
        
        // Allow navigation to completed steps or next step
        if (targetStep <= this.state.completedSteps + 1) {
            this.goToStep(targetStep);
        }
    }
    
    /**
     * Handle form submission
     */
    async handleSubmit(event) {
        event.preventDefault();
        
        const form = this.findWizardForm();
        if (!form) {
            console.error('Wizard form not found');
            return;
        }
        
        // Validate entire form
        const validationResult = this.validationService.validateForm(form);
        
        if (!validationResult.isValid) {
            this.showValidationErrors(validationResult.results);
            return;
        }
        
        // Submit form
        this.eventBus.emit('wizard:submitting');
        
        try {
            const ajaxService = window.app?.getModule('ajax');
            if (ajaxService) {
                const formData = new FormData(form);
                const result = await ajaxService.post(form.action, formData);
                
                this.eventBus.emit('wizard:submitted', result);
            } else {
                // Fallback to regular form submission
                form.submit();
            }
        } catch (error) {
            this.eventBus.emit('wizard:error', error);
        }
    }
    
    /**
     * Handle preview with save
     */
    async handlePreviewWithSave(event) {
        event.preventDefault();
        
        const form = this.findWizardForm();
        if (!form) return;
        
        // Add preview redirect flag
        const previewInput = document.createElement('input');
        previewInput.type = 'hidden';
        previewInput.name = 'redirect_to_preview';
        previewInput.value = '1';
        form.appendChild(previewInput);
        
        form.submit();
    }
    
    /**
     * Validate current step
     */
    validateCurrentStep() {
        const currentStepElement = this.getCurrentStepElement();
        if (!currentStepElement) {
            return { isValid: true, errors: {} };
        }
        
        // Get required fields in current step
        const requiredFields = currentStepElement.querySelectorAll(
            'input[data-required="true"], textarea[data-required="true"], select[data-required="true"]'
        );
        
        const fields = {};
        const errors = {};
        
        requiredFields.forEach(field => {
            const fieldName = field.name;
            const fieldValue = field.type === 'file' ? field.files[0] : field.value;
            
            fields[fieldName] = fieldValue;
            
            // Skip social media fields for validation
            if (this.isSocialMediaField(fieldName)) {
                return;
            }
            
            const validationResult = this.validationService.validateField(fieldName, fieldValue);
            
            if (!validationResult.isValid) {
                errors[fieldName] = validationResult.errors;
                this.markFieldAsInvalid(field, validationResult.errors[0]);
            } else {
                this.markFieldAsValid(field);
            }
        });
        
        // Special validation for profile contact step
        if (this.state.contentType === 'PROFILE' && this.state.currentStep === 3) {
            const contactValidation = this.validationService.getValidator('contactRequired');
            if (contactValidation) {
                const result = contactValidation(fields);
                if (!result.isValid) {
                    errors['contact_required'] = [result.message];
                    
                    // Mark both email and phone fields as invalid
                    const emailField = currentStepElement.querySelector('input[name="data[contact_info][email]"]');
                    const phoneField = currentStepElement.querySelector('input[name="data[contact_info][phone]"]');
                    
                    if (emailField) this.markFieldAsInvalid(emailField, result.message);
                    if (phoneField) this.markFieldAsInvalid(phoneField, '');
                }
            }
        }
        
        // Special validation for multimedia step
        if (this.state.contentType === 'GIFT' && this.state.currentStep === 3) {
            const multimediaErrors = this.validateMultimedia(currentStepElement);
            Object.assign(errors, multimediaErrors);
        }
        
        return {
            isValid: Object.keys(errors).length === 0,
            errors
        };
    }
    
    /**
     * Validate multimedia components
     */
    validateMultimedia(stepElement) {
        const errors = {};
        
        // Validate audio
        const audioType = stepElement.querySelector('select[name="multimedia[audio][type]"]')?.value;
        if (audioType === 'file_upload') {
            const audioPreview = stepElement.querySelector('#audio-preview');
            const audioUrl = stepElement.querySelector('input[name="multimedia[audio][url]"]')?.value;
            
            const hasFile = audioPreview && !audioPreview.classList.contains('hidden');
            const hasUrl = audioUrl && audioUrl.trim();
            
            if (!hasFile && !hasUrl) {
                errors['multimedia_audio'] = ['Debes subir un archivo de audio o proporcionar una URL.'];
            }
        }
        
        // Validate video
        const videoType = stepElement.querySelector('select[name="multimedia[video][type]"]')?.value;
        if (videoType === 'file_upload') {
            const videoPreview = stepElement.querySelector('#video-preview');
            const videoUrl = stepElement.querySelector('input[name="multimedia[video][url]"]')?.value;
            
            const hasFile = videoPreview && !videoPreview.classList.contains('hidden');
            const hasUrl = videoUrl && videoUrl.trim();
            
            if (!hasFile && !hasUrl) {
                errors['multimedia_video'] = ['Debes subir un archivo de video o proporcionar una URL.'];
            }
        }
        
        return errors;
    }
    
    /**
     * Check if can advance to next step
     */
    canAdvance() {
        return this.state.currentStep < this.state.totalSteps;
    }
    
    /**
     * Advance to next step
     */
    advance() {
        if (this.canAdvance()) {
            this.goToStep(this.state.currentStep + 1);
        }
    }
    
    /**
     * Go to specific step
     */
    goToStep(stepNumber) {
        const previousStep = this.state.currentStep;
        this.state.currentStep = stepNumber;
        
        // Update completed steps
        if (stepNumber > this.state.completedSteps) {
            this.state.completedSteps = stepNumber - 1;
        }
        
        this.updateUI();
        this.scrollToWizardTop();
        
        this.eventBus.emit('wizard:stepChanged', {
            from: previousStep,
            to: stepNumber,
            state: this.state
        });
    }
    
    /**
     * Update wizard UI
     */
    updateUI() {
        this.updateStepContent();
        this.updateNavigation();
        this.updateStepIndicators();
    }
    
    /**
     * Update step content visibility
     */
    updateStepContent() {
        this.state.stepElements.forEach(element => {
            element.classList.add('hidden');
        });
        
        const currentElement = this.getCurrentStepElement();
        if (currentElement) {
            currentElement.classList.remove('hidden');
        }
    }
    
    /**
     * Update navigation buttons
     */
    updateNavigation() {
        const { currentStep, totalSteps } = this.state;
        
        // Previous button
        if (this.state.prevBtn) {
            this.state.prevBtn.classList.toggle('hidden', currentStep === 1);
        }
        
        // Next button
        if (this.state.nextBtn) {
            this.state.nextBtn.classList.toggle('hidden', currentStep === totalSteps);
        }
        
        // Final step buttons
        const isLastStep = currentStep === totalSteps;
        
        if (this.state.submitBtn) {
            this.state.submitBtn.classList.toggle('hidden', !isLastStep);
        }
        
        if (this.state.previewBtn) {
            this.state.previewBtn.classList.toggle('hidden', !isLastStep);
        }
        
        if (this.state.publishBtn) {
            this.state.publishBtn.classList.toggle('hidden', !isLastStep);
        }
    }
    
    /**
     * Update step indicators
     */
    updateStepIndicators() {
        const { currentStep, totalSteps } = this.state;
        
        // Update desktop indicators
        for (let i = 1; i <= totalSteps; i++) {
            const step = document.getElementById(`desktop-step-${i}`);
            const label = document.getElementById(`desktop-label-${i}`);
            const connector = document.getElementById(`desktop-connector-${i}`);
            
            if (step) {
                const isActive = i === currentStep;
                const isCompleted = i <= this.state.completedSteps;
                
                step.classList.toggle('active', isActive);
                step.classList.toggle('kraftdo-gradient', isCompleted || isActive);
                step.classList.toggle('text-white', isCompleted || isActive);
                step.classList.toggle('bg-gray-200', !isCompleted && !isActive);
                step.classList.toggle('text-gray-500', !isCompleted && !isActive);
            }
            
            if (label) {
                const isCompleted = i <= currentStep;
                label.classList.toggle('text-kraftdo-blue', isCompleted);
                label.classList.toggle('text-gray-500', !isCompleted);
            }
            
            if (connector) {
                const isCompleted = i < currentStep;
                connector.classList.toggle('bg-gradient-to-r', isCompleted);
                connector.classList.toggle('from-kraftdo-blue/50', isCompleted);
                connector.classList.toggle('to-kraftdo-green/50', isCompleted);
                connector.classList.toggle('bg-gray-200', !isCompleted);
            }
        }
        
        // Update mobile indicators
        this.updateMobileIndicators();
    }
    
    /**
     * Update mobile step indicators
     */
    updateMobileIndicators() {
        const { currentStep, totalSteps } = this.state;
        
        // Update mobile elements
        const elements = {
            title: document.getElementById('mobile-step-title'),
            percentage: document.getElementById('mobile-step-percentage'),
            progressBar: document.getElementById('mobile-progress-bar'),
            currentStep: document.getElementById('mobile-current-step'),
            stepName: document.getElementById('mobile-step-name')
        };
        
        const percentage = Math.round((currentStep / totalSteps) * 100);
        
        if (elements.title) elements.title.textContent = `Paso ${currentStep} de ${totalSteps}`;
        if (elements.percentage) elements.percentage.textContent = `${percentage}%`;
        if (elements.progressBar) elements.progressBar.style.width = `${percentage}%`;
        if (elements.currentStep) elements.currentStep.textContent = currentStep;
        
        if (elements.stepName) {
            const stepNames = this.getStepNames();
            elements.stepName.textContent = stepNames[currentStep] || '';
        }
        
        // Update mobile step dots
        for (let i = 1; i <= totalSteps; i++) {
            const indicator = document.getElementById(`mobile-indicator-${i}`);
            if (indicator) {
                const isCompleted = i <= currentStep;
                indicator.classList.toggle('kraftdo-gradient', isCompleted);
                indicator.classList.toggle('bg-gray-200', !isCompleted);
            }
        }
    }
    
    /**
     * Get step names based on content type
     */
    getStepNames() {
        if (this.state.contentType === 'PROFILE') {
            return {
                1: 'Info Básica',
                2: 'Personal',
                3: 'Contacto',
                4: 'Redes Sociales',
                5: 'Diseño'
            };
        } else {
            return {
                1: 'Info Básica',
                2: 'Tipo Regalo',
                3: 'Multimedia',
                4: 'Diseño'
            };
        }
    }
    
    /**
     * Get current step element
     */
    getCurrentStepElement() {
        return document.querySelector(`.wizard-step-content[data-step="${this.state.currentStep}"]`);
    }
    
    /**
     * Find wizard form
     */
    findWizardForm() {
        return document.querySelector('form[method="POST"][enctype="multipart/form-data"]');
    }
    
    /**
     * Check if field is social media field
     */
    isSocialMediaField(fieldName) {
        const socialFields = [
            'data[social_links][linkedin]', 'data[social_links][twitter]',
            'data[social_links][instagram]', 'data[social_links][facebook]',
            'data[social_links][youtube]', 'data[social_links][tiktok]',
            'data[social_links][telegram]', 'data[social_links][discord]',
            'data[social_links][snapchat]', 'data[social_links][threads]',
            'data[social_links][github]', 'data[social_links][spotify]'
        ];
        
        return socialFields.includes(fieldName);
    }
    
    /**
     * Mark field as invalid
     */
    markFieldAsInvalid(field, message) {
        field.classList.add('border-red-500');
        
        // Remove existing error message
        const existingError = field.parentNode.querySelector('.wizard-error-message');
        if (existingError) existingError.remove();
        
        if (message) {
            // Add error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'wizard-error-message text-red-500 text-sm mt-1 font-medium';
            errorDiv.textContent = message;
            field.parentNode.insertBefore(errorDiv, field.nextSibling);
        }
    }
    
    /**
     * Mark field as valid
     */
    markFieldAsValid(field) {
        field.classList.remove('border-red-500');
        
        // Remove error message
        const errorMessage = field.parentNode.querySelector('.wizard-error-message');
        if (errorMessage) errorMessage.remove();
    }
    
    /**
     * Show validation errors
     */
    showValidationErrors(errors) {
        this.eventBus.emit('wizard:validationErrors', errors);
        
        // Focus first invalid field
        const firstInvalidField = document.querySelector('.border-red-500');
        if (firstInvalidField) {
            firstInvalidField.focus();
        }
    }
    
    /**
     * Scroll to wizard top
     */
    scrollToWizardTop() {
        const currentStepElement = this.getCurrentStepElement();
        
        if (currentStepElement) {
            currentStepElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
                inline: 'nearest'
            });
            
            // Focus first input after scroll
            setTimeout(() => {
                const firstInput = currentStepElement.querySelector(
                    'input:not([type="hidden"]):not([disabled]), textarea:not([disabled]), select:not([disabled])'
                );
                if (firstInput) firstInput.focus();
            }, 400);
        }
    }
    
    /**
     * Destroy wizard
     */
    destroy() {
        // Remove event listeners
        this.state.nextBtn?.removeEventListener('click', this.handleNext);
        this.state.prevBtn?.removeEventListener('click', this.handlePrevious);
        
        document.querySelectorAll('.wizard-step').forEach(step => {
            step.removeEventListener('click', this.handleStepClick);
        });
        
        this.initialized = false;
        this.eventBus.emit('wizard:destroyed');
    }
}

/**
 * Wizard State - holds all wizard state data
 */
class WizardState {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 4;
        this.completedSteps = 0;
        this.contentType = 'GIFT';
        
        // UI Elements
        this.stepElements = null;
        this.nextBtn = null;
        this.prevBtn = null;
        this.submitBtn = null;
        this.previewBtn = null;
        this.publishBtn = null;
    }
}

window.WizardManager = WizardManager;
window.WizardState = WizardState;