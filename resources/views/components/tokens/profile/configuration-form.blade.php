@props([
    'token',
    'content'
])

<x-nfc.configuration-form :token="$token" :content="$content">
    <!-- Step 2: Información Personal (solo para PROFILE) -->
    <div class="wizard-step-content hidden" data-step="2">
        <h3 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-6">👤 Información Personal</h3>
        
        <x-tokens.profile.partials.personal-section :token="$token" :content="$content" />
        
        <div class="flex justify-between mt-8">
            <x-ui.button type="button" variant="secondary" onclick="previousStep()" icon="fa-arrow-left">
                Anterior
            </x-ui.button>
            <x-ui.button type="button" onclick="nextStep()" icon-right="fa-arrow-right">
                Siguiente
            </x-ui.button>
        </div>
    </div>

    <!-- Step 3: Información de Contacto (solo para PROFILE) -->
    <div class="wizard-step-content hidden" data-step="3">
        <h3 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-6">📞 Información de Contacto</h3>
        
        <x-tokens.profile.partials.contact-section :token="$token" :content="$content" />
        
        <div class="flex justify-between mt-8">
            <x-ui.button type="button" variant="secondary" onclick="previousStep()" icon="fa-arrow-left">
                Anterior
            </x-ui.button>
            <x-ui.button type="button" onclick="nextStep()" icon-right="fa-arrow-right">
                Siguiente
            </x-ui.button>
        </div>
    </div>

    <!-- Step 4: Redes Sociales (solo para PROFILE) -->
    <div class="wizard-step-content hidden" data-step="4">
        <h3 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-6">🌐 Redes Sociales</h3>
        
        <x-tokens.profile.partials.social-section :token="$token" :content="$content" />
        
        <div class="flex justify-between mt-8">
            <x-ui.button type="button" variant="secondary" onclick="previousStep()" icon="fa-arrow-left">
                Anterior
            </x-ui.button>
            <x-ui.button type="button" onclick="nextStep()" icon-right="fa-arrow-right">
                Siguiente
            </x-ui.button>
        </div>
    </div>

    <!-- Step 5: Diseño y Branding (solo para PROFILE) -->
    <div class="wizard-step-content hidden" data-step="5">
        <h3 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-6">🎨 Diseño y Branding</h3>
        
        <x-tokens.profile.partials.design-section :content="$content" />
        
        <div class="flex justify-between mt-8">
            <x-ui.button type="button" variant="secondary" onclick="previousStep()" icon="fa-arrow-left">
                Anterior
            </x-ui.button>
            <x-ui.button type="button" onclick="nextStep()" icon-right="fa-arrow-right">
                Siguiente
            </x-ui.button>
        </div>
    </div>

    <!-- Step 6: Revisión Final -->
    <div class="wizard-step-content hidden" data-step="6">
        <h3 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-6">✅ Revisión Final</h3>
        
        <x-tokens.profile.partials.review-section :token="$token" :content="$content" />
        
        <div class="flex justify-between mt-8">
            <x-ui.button type="button" variant="secondary" onclick="previousStep()" icon="fa-arrow-left">
                Anterior
            </x-ui.button>
            <x-ui.button type="submit" variant="success" icon="fa-save">
                Guardar Configuración
            </x-ui.button>
        </div>
    </div>
    
    <x-slot name="scripts">
        <script>
        console.log('🚀 SCRIPT INICIADO');
        alert('PRUEBA: JavaScript se está ejecutando');

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ JavaScript cargado correctamente');
    
    // Test básico
    const phoneInput = document.querySelector('input[name="data[contact_info][phone]"]');
    console.log('📞 Campo teléfono encontrado:', phoneInput ? 'SÍ' : 'NO');
    
    if (phoneInput) {
        console.log('🔗 Agregando event listeners...');
        phoneInput.addEventListener('input', function() {
            console.log('📝 Escribiendo en teléfono:', this.value);
        });
    }
    // Validación básica para WhatsApp solamente (no para teléfono principal)
    const whatsappInputs = document.querySelectorAll('input[name*="whatsapp"]');
    const phoneRegex = /^\+?[1-9]\d{1,14}$/;
    
    whatsappInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = this.value.trim();
            
            // Remover errores previos
            const formatErrorMsg = this.parentElement.querySelector('.phone-error-msg');
            if (formatErrorMsg) {
                formatErrorMsg.remove();
            }
            
            // Validar formato solo si hay valor
            if (value !== '' && !phoneRegex.test(value)) {
                this.classList.add('border-red-500');
                this.classList.remove('border-gray-300');
                
                // Mostrar mensaje de error de formato
                const errorMsg = document.createElement('p');
                errorMsg.className = 'text-red-500 text-xs mt-1 phone-error-msg';
                errorMsg.textContent = 'Formato: +56912345678 (formato internacional)';
                this.parentElement.appendChild(errorMsg);
            } else {
                this.classList.remove('border-red-500');
                this.classList.add('border-gray-300');
            }
        });
    });
    
    // Validación de contacto - teléfono requerido
    const emailInput = document.querySelector('input[name="data[contact_info][email]"]');
    const phoneInput = document.querySelector('input[name="data[contact_info][phone]"]');
    
    console.log('Email input found:', emailInput);
    console.log('Phone input found:', phoneInput);
    console.log('Phone regex defined:', phoneRegex);
    
    function validateContactInfo() {
        console.log('validateContactInfo called');
        let isValid = true;
        
        // Validar teléfono (obligatorio)
        if (phoneInput) {
            const phoneValue = phoneInput.value.trim();
            console.log('Phone value:', phoneValue);
            console.log('Phone regex test:', phoneRegex.test(phoneValue));
            
            if (phoneValue === '') {
                console.log('Phone is empty - showing required error');
                phoneInput.classList.add('border-red-500');
                showFieldError(phoneInput, 'El teléfono es obligatorio');
                isValid = false;
            } else if (!phoneRegex.test(phoneValue)) {
                console.log('Phone format invalid - showing format error');
                phoneInput.classList.add('border-red-500');
                showFieldError(phoneInput, 'Formato: +56912345678 (formato internacional)');
                isValid = false;
            } else {
                console.log('Phone is valid - removing errors');
                phoneInput.classList.remove('border-red-500');
                hideFieldError(phoneInput);
            }
        }
        
        console.log('validateContactInfo returning:', isValid);
        return isValid;
    }
    
    // Validación específica para el teléfono principal (requerido)
    if (phoneInput) {
        console.log('Adding event listeners to phone input');
        phoneInput.addEventListener('blur', function() {
            console.log('Phone blur event triggered');
            validateContactInfo();
        });
        phoneInput.addEventListener('input', function() {
            console.log('Phone input event triggered');
            validateContactInfo(); // Llamar a la validación completa en cada cambio
        });
    } else {
        console.log('Phone input not found - cannot add event listeners');
    }
    
    // Email no necesita validación especial, solo formato
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            // Solo remover errores si está válido, no validar teléfono
            if (this.value.trim() === '' || this.validity.valid) {
                this.classList.remove('border-red-500');
                hideFieldError(this);
            }
        });
    }
    
    // Validación para Step 2 - Información Personal (campos requeridos)
    const professionInput = document.querySelector('input[name="data[personal_info][profession]"]');
    const companyInput = document.querySelector('input[name="data[personal_info][company]"]');
    const bioTextarea = document.querySelector('textarea[name="data[personal_info][bio]"]');
    
    function validatePersonalInfo() {
        let isValid = true;
        
        // Validar profesión (requerida)
        if (professionInput) {
            const professionValue = professionInput.value.trim();
            if (professionValue === '') {
                professionInput.classList.add('border-red-500');
                showFieldError(professionInput, 'La profesión o cargo es obligatorio');
                isValid = false;
            } else {
                professionInput.classList.remove('border-red-500');
                hideFieldError(professionInput);
            }
        }
        
        // Validar empresa (requerida)
        if (companyInput) {
            const companyValue = companyInput.value.trim();
            if (companyValue === '') {
                companyInput.classList.add('border-red-500');
                showFieldError(companyInput, 'La empresa u organización es obligatoria');
                isValid = false;
            } else {
                companyInput.classList.remove('border-red-500');
                hideFieldError(companyInput);
            }
        }
        
        // Validar biografía (requerida, mínimo 20 caracteres)
        if (bioTextarea) {
            const bioValue = bioTextarea.value.trim();
            if (bioValue === '') {
                bioTextarea.classList.add('border-red-500');
                showFieldError(bioTextarea, 'La biografía es obligatoria');
                isValid = false;
            } else if (bioValue.length < 20) {
                bioTextarea.classList.add('border-red-500');
                showFieldError(bioTextarea, 'La biografía debe tener al menos 20 caracteres');
                isValid = false;
            } else {
                bioTextarea.classList.remove('border-red-500');
                hideFieldError(bioTextarea);
            }
        }
        
        return isValid;
    }
    
    // Función auxiliar para mostrar errores
    function showFieldError(field, message) {
        console.log('showFieldError called with message:', message);
        console.log('Field parent element:', field.parentElement);
        
        hideFieldError(field); // Remover error previo
        
        // Verificar si ya existe un mensaje de error de Laravel/Blade
        let existingError = field.parentElement.querySelector('p.text-red-500');
        console.log('Existing error element found:', existingError);
        
        if (existingError) {
            // Reutilizar el elemento existente
            existingError.textContent = message;
            existingError.className = 'text-red-500 text-xs mt-1 field-error-msg';
            existingError.style.display = 'block'; // Asegurar que esté visible
            console.log('Reused existing error element');
        } else {
            // Crear nuevo elemento si no existe
            const errorMsg = document.createElement('p');
            errorMsg.className = 'text-red-500 text-xs mt-1 field-error-msg';
            errorMsg.textContent = message;
            field.parentElement.appendChild(errorMsg);
            console.log('Created new error element');
        }
    }
    
    // Función auxiliar para ocultar errores
    function hideFieldError(field) {
        // Buscar tanto errores JavaScript como de Laravel
        const jsErrorMsg = field.parentElement.querySelector('.field-error-msg');
        const laravelErrorMsg = field.parentElement.querySelector('p.text-red-500:not(.field-error-msg)');
        
        if (jsErrorMsg) {
            jsErrorMsg.remove();
        }
        if (laravelErrorMsg) {
            laravelErrorMsg.style.display = 'none'; // Ocultar en lugar de eliminar
        }
    }
    
    // Validar campos de información personal en tiempo real
    if (professionInput) {
        professionInput.addEventListener('blur', validatePersonalInfo);
        professionInput.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.remove('border-red-500');
                hideFieldError(this);
            }
        });
    }
    
    if (companyInput) {
        companyInput.addEventListener('blur', validatePersonalInfo);
        companyInput.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.remove('border-red-500');
                hideFieldError(this);
            }
        });
    }
    
    if (bioTextarea) {
        bioTextarea.addEventListener('blur', validatePersonalInfo);
        bioTextarea.addEventListener('input', function() {
            const value = this.value.trim();
            if (value.length >= 20) {
                this.classList.remove('border-red-500');
                hideFieldError(this);
            }
        });
    }
    
    // Interceptar navegación entre steps para validar
    window.nextStep = function() {
        const currentStep = document.querySelector('.wizard-step-content:not(.hidden)');
        const stepNumber = currentStep ? currentStep.getAttribute('data-step') : null;
        
        // Validar Step 2 (Información Personal) antes de continuar
        if (stepNumber === '2') {
            if (!validatePersonalInfo()) {
                alert('Por favor, completa los campos obligatorios de información personal antes de continuar.');
                return false;
            }
        }
        
        // Validar Step 3 (Información de Contacto) antes de continuar  
        if (stepNumber === '3') {
            if (!validateContactInfo()) {
                alert('Por favor, proporciona un número de teléfono válido antes de continuar.');
                return false;
            }
        }
        
        // Continuar con la navegación normal si las validaciones pasan
        return originalNextStep();
    };
    
    // Guardar referencia a la función original nextStep
    const originalNextStep = window.nextStep || function() {
        const currentStep = document.querySelector('.wizard-step-content:not(.hidden)');
        const nextStepNumber = parseInt(currentStep.getAttribute('data-step')) + 1;
        const nextStep = document.querySelector(`[data-step="${nextStepNumber}"]`);
        
        if (nextStep) {
            currentStep.classList.add('hidden');
            nextStep.classList.remove('hidden');
            
            // Actualizar indicador de paso
            const currentIndicator = document.querySelector(`.step-indicator.active`);
            const nextIndicator = document.querySelector(`.step-indicator:nth-child(${nextStepNumber})`);
            
            if (currentIndicator) currentIndicator.classList.remove('active');
            if (nextIndicator) nextIndicator.classList.add('active');
        }
    };
    
    // Validación antes de enviar el formulario
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validar información personal
            if (!validatePersonalInfo()) {
                isValid = false;
            }
            
            // Validar contacto requerido
            if (!validateContactInfo()) {
                isValid = false;
            }
            
            // Validar formato de teléfonos
            const allPhoneInputs = document.querySelectorAll('input[name*="phone"], input[name*="whatsapp"]');
            allPhoneInputs.forEach(input => {
                const value = input.value.trim();
                if (value !== '' && !phoneRegex.test(value)) {
                    input.classList.add('border-red-500');
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Por favor, corrige los errores en el formulario antes de continuar.');
                return false;
            }
        });
    }
});
        </script>
    </x-slot>
</x-nfc.configuration-form>