@props(['token', 'content' => null])

<!-- Modern Wizard Navigation Buttons -->
<div class="flex flex-col sm:flex-row sm:justify-between mt-8 wizard-navigation bg-white/50 backdrop-blur-sm rounded-2xl p-4 sm:p-6 border border-kraftdo-green/20 gap-4" data-content-type="{{ $token->content_type }}">
    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
        <a href="{{ route('dashboard') }}" 
           class="bg-gradient-to-r from-kraftdo-navy to-kraftdo-navy/80 text-white px-4 sm:px-8 py-3 rounded-xl hover:shadow-lg transition-all duration-200 transform hover:scale-105 font-semibold text-center text-sm sm:text-base">
            <i class="fas fa-times mr-2"></i> <span class="hidden sm:inline">Cancelar</span><span class="sm:hidden">Cancelar</span>
        </a>
        <button type="button" id="prev-step" 
                class="bg-gradient-to-r from-kraftdo-blue to-kraftdo-green text-white px-4 sm:px-8 py-3 rounded-xl hover:shadow-lg transition-all duration-200 transform hover:scale-105 font-semibold hidden text-center text-sm sm:text-base">
            <i class="fas fa-chevron-left mr-2"></i> <span class="hidden sm:inline">Anterior</span><span class="sm:hidden">Anterior</span>
        </button>
    </div>
    
    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
        <button type="button" id="next-step" 
                class="bg-gradient-to-r from-kraftdo-blue to-kraftdo-green text-white px-4 sm:px-8 py-3 rounded-xl hover:shadow-lg transition-all duration-200 transform hover:scale-105 font-semibold text-center text-sm sm:text-base">
            <span class="hidden sm:inline">Siguiente</span><span class="sm:hidden">Siguiente</span> <i class="fas fa-chevron-right ml-2"></i>
        </button>
        <button type="button" id="preview-with-save-btn"
                class="bg-gradient-to-r from-kraftdo-navy to-kraftdo-blue text-white px-4 sm:px-8 py-3 rounded-xl hover:shadow-lg transition-all duration-200 transform hover:scale-105 font-semibold text-center text-sm sm:text-base hidden">
            <i class="fas fa-eye mr-2"></i> <span class="hidden sm:inline">Vista Previa</span><span class="sm:hidden">Vista Previa</span>
        </button>
        <button type="button" id="submit-form"
                class="bg-gradient-to-r from-kraftdo-blue to-kraftdo-green text-white px-4 sm:px-8 py-3 rounded-xl hover:shadow-lg transition-all duration-200 transform hover:scale-105 font-semibold hidden text-center text-sm sm:text-base">
            <i class="fas fa-save mr-2"></i> <span class="hidden sm:inline">Guardar Configuración</span><span class="sm:hidden">Guardar</span>
        </button>
        
        @if($content && $content->isDraft())
        <!-- Botón Publicar (solo en último paso y si es borrador) -->
        <button type="button" id="publish-btn" 
                onclick="publishContent()"
                class="bg-gradient-to-r from-kraftdo-green to-kraftdo-lime text-white px-4 sm:px-8 py-3 rounded-xl hover:shadow-lg transition-all duration-200 transform hover:scale-105 font-semibold hidden text-center text-sm sm:text-base">
            <i class="fas fa-rocket mr-2"></i> <span class="hidden sm:inline">Publicar Ahora</span><span class="sm:hidden">Publicar</span>
        </button>
        @endif
    </div>
</div>

{{-- Modern Architecture Script --}}
@push('scripts')
<script type="module">
/**
 * Modern Wizard Navigation - Clean Implementation
 * 
 * This script uses the modern frontend architecture instead of
 * the previous 650+ lines of spaghetti code.
 */

// Wait for modern architecture to be ready
if (window.modernApp) {
    window.modernApp.ready(() => {
        initializeModernWizard();
    });
} else {
    // Fallback if modern architecture is not available
    console.warn('Modern architecture not available, using fallback');
    document.addEventListener('DOMContentLoaded', initializeLegacyFallback);
}

function initializeModernWizard() {
    const app = window.app;
    
    if (!app) {
        console.error('KraftdoApp not initialized');
        return;
    }
    
    // The WizardManager module handles all the complex logic
    const wizard = app.getModule('wizard');
    if (!wizard) {
        console.error('WizardManager module not available');
        return;
    }
    
    // Listen for wizard events
    app.eventBus.on('wizard:stepChanged', (data) => {
        console.log(`Wizard step changed from ${data.from} to ${data.to}`);
    });
    
    app.eventBus.on('wizard:validationErrors', (errors) => {
        console.log('Validation errors:', errors);
    });
    
    app.eventBus.on('wizard:submitted', (result) => {
        app.getModule('notifications')?.success('Configuración guardada exitosamente');
        
        if (result.redirect_url) {
            setTimeout(() => {
                window.location.href = result.redirect_url;
            }, 1500);
        }
    });
    
    app.eventBus.on('wizard:error', (error) => {
        app.getModule('notifications')?.error('Error al guardar: ' + error.message);
    });
    
    console.log('✅ Modern wizard navigation initialized');
}

function initializeLegacyFallback() {
    console.log('🔄 Initializing legacy fallback for wizard navigation');
    
    // Basic wizard functionality without all the complexity
    let currentStep = 1;
    const contentType = document.querySelector('[data-content-type]')?.dataset.contentType || 'GIFT';
    const totalSteps = contentType === 'PROFILE' ? 5 : 4;
    
    const nextBtn = document.getElementById('next-step');
    const prevBtn = document.getElementById('prev-step');
    const submitBtn = document.getElementById('submit-form');
    
    function updateWizardUI() {
        // Show/hide step content
        document.querySelectorAll('.wizard-step-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        const currentContent = document.querySelector(`.wizard-step-content[data-step="${currentStep}"]`);
        if (currentContent) {
            currentContent.classList.remove('hidden');
        }
        
        // Update buttons
        prevBtn?.classList.toggle('hidden', currentStep === 1);
        nextBtn?.classList.toggle('hidden', currentStep === totalSteps);
        submitBtn?.classList.toggle('hidden', currentStep !== totalSteps);
        
        const previewBtn = document.getElementById('preview-with-save-btn');
        const publishBtn = document.getElementById('publish-btn');
        
        previewBtn?.classList.toggle('hidden', currentStep !== totalSteps);
        publishBtn?.classList.toggle('hidden', currentStep !== totalSteps);
        
        // Scroll to top
        const currentElement = document.querySelector(`.wizard-step-content[data-step="${currentStep}"]`);
        if (currentElement) {
            currentElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    
    function basicValidation() {
        const currentContent = document.querySelector(`.wizard-step-content[data-step="${currentStep}"]`);
        if (!currentContent) return true;
        
        const requiredFields = currentContent.querySelectorAll('input[data-required="true"], textarea[data-required="true"]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                isValid = false;
            } else {
                field.classList.remove('border-red-500');
            }
        });
        
        return isValid;
    }
    
    // Event listeners
    nextBtn?.addEventListener('click', () => {
        if (basicValidation() && currentStep < totalSteps) {
            currentStep++;
            updateWizardUI();
        }
    });
    
    prevBtn?.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateWizardUI();
        }
    });
    
    submitBtn?.addEventListener('click', () => {
        const form = document.querySelector('form[method="POST"][enctype="multipart/form-data"]');
        if (form && basicValidation()) {
            form.submit();
        }
    });
    
    // Initialize
    updateWizardUI();
    
    console.log('✅ Legacy wizard fallback initialized');
}
</script>
@endpush