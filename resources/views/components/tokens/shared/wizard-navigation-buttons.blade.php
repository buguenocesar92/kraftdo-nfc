@props(['token', 'content' => null])

<!-- Wizard Navigation Buttons -->
<div class="flex flex-col sm:flex-row sm:justify-between mt-8 wizard-navigation bg-white/50 backdrop-blur-sm rounded-2xl p-4 sm:p-6 border border-kraftdo-green/20 gap-4">
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
        <button type="button" id="save-only-btn"
                class="bg-gradient-to-r from-kraftdo-blue to-kraftdo-green text-white px-4 sm:px-8 py-3 rounded-xl hover:shadow-lg transition-all duration-200 transform hover:scale-105 font-semibold text-center text-sm sm:text-base hidden">
            <i class="fas fa-save mr-2"></i> <span class="hidden sm:inline">Guardar Cambios</span><span class="sm:hidden">Guardar</span>
        </button>
        <button type="button" id="preview-with-save-btn"
                class="bg-gradient-to-r from-kraftdo-navy to-kraftdo-blue text-white px-4 sm:px-8 py-3 rounded-xl hover:shadow-lg transition-all duration-200 transform hover:scale-105 font-semibold text-center text-sm sm:text-base hidden">
            <i class="fas fa-eye mr-2"></i> <span class="hidden sm:inline">Vista Previa</span><span class="sm:hidden">Vista Previa</span>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const isProfile = '{{ $token->content_type }}' === 'PROFILE';
    const totalSteps = isProfile ? 5 : 4;
    
    const nextBtn = document.getElementById('next-step');
    const prevBtn = document.getElementById('prev-step');
    
    // Función para hacer scroll hacia arriba del wizard
    function scrollToWizardTop() {
        // Buscar el step actual visible
        const currentStepContent = document.querySelector(`.wizard-step-content[data-step="${currentStep}"]`);
        
        if (currentStepContent) {
            // Scroll hacia el paso actual
            currentStepContent.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start',
                inline: 'nearest'
            });
            
            // Pequeño delay para enfocar el primer input después del scroll
            setTimeout(() => {
                const firstInput = currentStepContent.querySelector('input:not([type="hidden"]):not([disabled]), textarea:not([disabled]), select:not([disabled])');
                if (firstInput) {
                    firstInput.focus();
                }
            }, 400);
        } else {
            // Fallback: buscar el formulario principal del wizard
            const wizardForm = document.querySelector('form[enctype="multipart/form-data"]');
            if (wizardForm) {
                wizardForm.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start',
                    inline: 'nearest'
                });
            } else {
                // Último fallback: scroll hacia arriba de la página
                window.scrollTo({ 
                    top: 0, 
                    behavior: 'smooth' 
                });
            }
        }
    }
    
    function updateWizard() {
        // Update step content visibility
        document.querySelectorAll('.wizard-step-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        const currentStepContent = document.querySelector(`.wizard-step-content[data-step="${currentStep}"]`);
        if (currentStepContent) {
            currentStepContent.classList.remove('hidden');
        }
        
        
        // Update step navigation
        updateStepNavigation();
        
        // Update navigation buttons
        if (currentStep === 1) {
            prevBtn.classList.add('hidden');
        } else {
            prevBtn.classList.remove('hidden');
        }
        
        const saveOnlyBtn = document.getElementById('save-only-btn');
        const previewBtn = document.getElementById('preview-with-save-btn');
        const publishBtn = document.getElementById('publish-btn');
        
        if (currentStep === totalSteps) {
            nextBtn.classList.add('hidden');
            if (saveOnlyBtn) saveOnlyBtn.classList.remove('hidden');
            if (previewBtn) previewBtn.classList.remove('hidden');
            if (publishBtn) publishBtn.classList.remove('hidden');
        } else {
            nextBtn.classList.remove('hidden');
            if (saveOnlyBtn) saveOnlyBtn.classList.add('hidden');
            if (previewBtn) previewBtn.classList.add('hidden');
            if (publishBtn) publishBtn.classList.add('hidden');
        }
    }
    
    function updateStepNavigation() {
        // Update desktop step navigation
        for (let i = 1; i <= totalSteps; i++) {
            const desktopStep = document.getElementById(`desktop-step-${i}`);
            const desktopLabel = document.getElementById(`desktop-label-${i}`);
            const desktopConnector = document.getElementById(`desktop-connector-${i}`);
            
            if (desktopStep) {
                // Update step circle
                if (i <= currentStep) {
                    desktopStep.classList.remove('bg-gray-200', 'text-gray-500');
                    desktopStep.classList.add('kraftdo-gradient', 'text-white');
                } else {
                    desktopStep.classList.remove('kraftdo-gradient', 'text-white');
                    desktopStep.classList.add('bg-gray-200', 'text-gray-500');
                }
                
                // Update active class
                if (i === currentStep) {
                    desktopStep.classList.add('active');
                } else {
                    desktopStep.classList.remove('active');
                }
            }
            
            if (desktopLabel) {
                // Update label color
                if (i <= currentStep) {
                    desktopLabel.classList.remove('text-gray-500');
                    desktopLabel.classList.add('text-kraftdo-blue');
                } else {
                    desktopLabel.classList.remove('text-kraftdo-blue');
                    desktopLabel.classList.add('text-gray-500');
                }
            }
            
            if (desktopConnector) {
                // Update connector
                if (i < currentStep) {
                    desktopConnector.classList.remove('bg-gray-200');
                    desktopConnector.classList.add('bg-gradient-to-r', 'from-kraftdo-blue/50', 'to-kraftdo-green/50');
                } else {
                    desktopConnector.classList.remove('bg-gradient-to-r', 'from-kraftdo-blue/50', 'to-kraftdo-green/50');
                    desktopConnector.classList.add('bg-gray-200');
                }
            }
        }
        
        // Update mobile step navigation elements
        const mobileStepTitle = document.getElementById('mobile-step-title');
        const mobileStepPercentage = document.getElementById('mobile-step-percentage');
        const mobileProgressBar = document.getElementById('mobile-progress-bar');
        const mobileCurrentStep = document.getElementById('mobile-current-step');
        const mobileStepName = document.getElementById('mobile-step-name');
        
        if (mobileStepTitle) {
            mobileStepTitle.textContent = `Paso ${currentStep} de ${totalSteps}`;
        }
        
        if (mobileStepPercentage) {
            const percentage = Math.round((currentStep / totalSteps) * 100);
            mobileStepPercentage.textContent = `${percentage}%`;
        }
        
        if (mobileProgressBar) {
            const percentage = (currentStep / totalSteps) * 100;
            mobileProgressBar.style.width = `${percentage}%`;
        }
        
        if (mobileCurrentStep) {
            mobileCurrentStep.textContent = currentStep;
        }
        
        if (mobileStepName) {
            let stepNames;
            if (isProfile) {
                stepNames = {
                    1: 'Info Básica',
                    2: 'Personal',
                    3: 'Contacto',
                    4: 'Redes Sociales',
                    5: 'Diseño'
                };
            } else {
                stepNames = {
                    1: 'Info Básica',
                    2: 'Tipo Regalo',
                    3: 'Multimedia', 
                    4: 'Diseño'
                };
            }
            mobileStepName.textContent = stepNames[currentStep] || '';
        }
        
        // Update mobile step indicators
        for (let i = 1; i <= totalSteps; i++) {
            const indicator = document.getElementById(`mobile-indicator-${i}`);
            if (indicator) {
                if (i <= currentStep) {
                    indicator.classList.remove('bg-gray-200');
                    indicator.classList.add('kraftdo-gradient');
                } else {
                    indicator.classList.remove('kraftdo-gradient');
                    indicator.classList.add('bg-gray-200');
                }
            }
        }
    }
    
    function validateCurrentStep() {
        console.log('🔍 Starting validation for step:', currentStep);
        const currentStepContent = document.querySelector(`.wizard-step-content[data-step="${currentStep}"]`);
        if (!currentStepContent) {
            console.log('❌ No step content found for step:', currentStep);
            return true;
        }
        console.log('✅ Step content found:', currentStepContent);
        console.log('🏷️ Step content classes:', currentStepContent.className);
        console.log('🏷️ Step content data-step:', currentStepContent.getAttribute('data-step'));
        
        const requiredInputs = currentStepContent.querySelectorAll('input[data-required="true"], textarea[data-required="true"], select[data-required="true"]');
        console.log('📝 Found required inputs:', requiredInputs.length);
        
        // Debug: Log each required input found
        requiredInputs.forEach((input, index) => {
            console.log(`📝 Required input ${index + 1}:`, input.name, 'Value:', input.value);
        });
        
        // Filtrar campos de redes sociales que no deben ser obligatorios
        const socialMediaFields = [
            'data[social_links][linkedin]',
            'data[social_links][twitter]', 
            'data[social_links][instagram]',
            'data[social_links][facebook]',
            'data[social_links][youtube]',
            'data[social_links][tiktok]',
            'data[social_links][telegram]',
            'data[social_links][discord]',
            'data[social_links][snapchat]',
            'data[social_links][threads]',
            'data[social_links][github]',
            'data[social_links][spotify]'
        ];
        
        let isValid = true;
        let firstInvalidInput = null;
        
        // Limpiar errores previos (excepto para campos de menú que se validan por separado)
        currentStepContent.querySelectorAll('.wizard-error-message').forEach(error => {
            // Solo remover errores que no sean del contenedor de menú
            const menuContainer = currentStepContent.querySelector('#menuItemsContainer');
            if (!menuContainer || !menuContainer.contains(error)) {
                error.remove();
            }
        });
        requiredInputs.forEach(input => {
            // Solo limpiar campos que no sean de menú
            if (!input.name || !input.name.match(/^data\[menu_items\]\[\d+\]\[.*\]$/)) {
                input.classList.remove('border-red-500');
            }
        });
        
        requiredInputs.forEach(input => {
            // Determinar mensaje de error específico según el campo
            const fieldName = input.name;
            const fieldLabel = getFieldLabel(input);
            
            // Saltar validación para campos de redes sociales
            if (socialMediaFields.includes(fieldName)) {
                return;
            }
            
            // Saltar validación para campos de platos del menú (se validan por separado)
            if (fieldName && fieldName.match(/^data\[menu_items\]\[\d+\]\[.*\]$/)) {
                return;
            }
            
            const value = input.value.trim();
            let errorMessage = '';
            
            if (!value) {
                isValid = false;
                input.classList.add('border-red-500');
                
                if (!firstInvalidInput) {
                    firstInvalidInput = input;
                }
                
                if (fieldName === 'title') {
                    errorMessage = 'El título es obligatorio.';
                } else if (fieldName === 'description') {
                    errorMessage = 'La descripción es obligatoria.';
                } else if (fieldName === 'data[from]') {
                    errorMessage = 'El nombre de quien envía el regalo es obligatorio.';
                } else if (fieldName === 'data[to]') {
                    errorMessage = 'El nombre de quien recibe el regalo es obligatorio.';
                } else if (fieldName === 'data[love_message]') {
                    errorMessage = 'El mensaje especial es obligatorio para los regalos.';
                } else if (fieldName === 'data[contact_info][phone]') {
                    // Teléfono es obligatorio para PROFILE
                    errorMessage = 'El teléfono es obligatorio.';
                } else if (fieldName === 'data[contact_info][whatsapp]') {
                    // WhatsApp es obligatorio para PROFILE
                    errorMessage = 'El WhatsApp es obligatorio.';
                } else if (fieldName === 'data[contact_info][email]') {
                    // Email es opcional, no agregar error
                    isValid = true; // No marcar como inválido
                    input.classList.remove('border-red-500');
                    return; // Salir sin agregar error
                } else if (fieldName === 'data[restaurant_info][address]') {
                    errorMessage = 'La dirección del restaurante es obligatoria.';
                } else if (fieldName === 'data[restaurant_info][phone]') {
                    errorMessage = 'El teléfono del restaurante es obligatorio.';
                } else if (fieldName === 'data[restaurant_info][hours]') {
                    errorMessage = 'Los horarios de atención son obligatorios.';
                } else if (fieldName.match(/^data\[menu_items\]\[\d+\]\[name\]$/)) {
                    errorMessage = 'El nombre del plato es obligatorio.';
                } else if (fieldName.match(/^data\[menu_items\]\[\d+\]\[price\]$/)) {
                    errorMessage = 'El precio del plato es obligatorio.';
                } else if (fieldName.match(/^data\[menu_items\]\[\d+\]\[description\]$/)) {
                    errorMessage = 'La descripción del plato es obligatoria.';
                } else {
                    errorMessage = `El campo ${fieldLabel} es obligatorio.`;
                }
                
                if (errorMessage) {
                    showFieldError(input, errorMessage);
                }
            } else {
                // Validar formato de teléfono si tiene valor
                if (fieldName === 'data[contact_info][phone]' && value) {
                    // Regex que acepta formatos: +1234567890, +1 (234) 567-890, +1-234-567-890, etc.
                    const phoneRegex = /^\+?[1-9][\d\s\-\(\)]{7,20}$/;
                    if (!phoneRegex.test(value)) {
                        isValid = false;
                        input.classList.add('border-red-500');
                        showFieldError(input, 'Formato inválido. Ejemplos: +56912345678, +1 (721) 168-5477');
                        
                        if (!firstInvalidInput) {
                            firstInvalidInput = input;
                        }
                        return;
                    }
                }
                
                // Validar formato de WhatsApp si tiene valor
                if (fieldName === 'data[contact_info][whatsapp]' && value) {
                    // Mismo regex que teléfono
                    const phoneRegex = /^\+?[1-9][\d\s\-\(\)]{7,20}$/;
                    if (!phoneRegex.test(value)) {
                        isValid = false;
                        input.classList.add('border-red-500');
                        showFieldError(input, 'Formato inválido. Ejemplos: +56912345678, +1 (721) 168-5477');
                        
                        if (!firstInvalidInput) {
                            firstInvalidInput = input;
                        }
                        return;
                    }
                }
                
                // Validar formato de teléfono del restaurante si tiene valor
                if (fieldName === 'data[restaurant_info][phone]' && value) {
                    const phoneRegex = /^\+?[1-9][\d\s\-\(\)]{7,20}$/;
                    if (!phoneRegex.test(value)) {
                        isValid = false;
                        input.classList.add('border-red-500');
                        showFieldError(input, 'Formato inválido. Ejemplos: +56912345678, +1 (721) 168-5477');
                        
                        if (!firstInvalidInput) {
                            firstInvalidInput = input;
                        }
                        return;
                    }
                }
                
                // Validar formato de WhatsApp del restaurante si tiene valor
                if (fieldName === 'data[restaurant_info][whatsapp]' && value) {
                    const phoneRegex = /^\+?[1-9][\d\s\-\(\)]{7,20}$/;
                    if (!phoneRegex.test(value)) {
                        isValid = false;
                        input.classList.add('border-red-500');
                        showFieldError(input, 'Formato inválido. Ejemplos: +56912345678, +1 (721) 168-5477');
                        
                        if (!firstInvalidInput) {
                            firstInvalidInput = input;
                        }
                        return;
                    }
                }
                
                // Validar precio de platos del menú
                if (fieldName.match(/^data\[menu_items\]\[\d+\]\[price\]$/) && value) {
                    const price = parseFloat(value);
                    if (isNaN(price) || price < 0) {
                        isValid = false;
                        input.classList.add('border-red-500');
                        showFieldError(input, 'El precio debe ser un número mayor o igual a 0.');
                        
                        if (!firstInvalidInput) {
                            firstInvalidInput = input;
                        }
                        return;
                    }
                }
                
                // Validar longitud mínima si está especificada
                const minLength = input.getAttribute('data-minlength');
                if (minLength && value.length < parseInt(minLength)) {
                    isValid = false;
                    input.classList.add('border-red-500');
                    showFieldError(input, `Mínimo ${minLength} caracteres requeridos`);
                    
                    if (!firstInvalidInput) {
                        firstInvalidInput = input;
                    }
                    return;
                }
                
                input.classList.remove('border-red-500');
            }
        });
        
        // Validar campos opcionales que tengan valor (para formato)
        const optionalFields = currentStepContent.querySelectorAll('input:not([data-required="true"]), textarea:not([data-required="true"])');
        optionalFields.forEach(input => {
            const fieldName = input.name;
            const value = input.value.trim();
            
            // Solo validar si tiene valor
            if (value) {
                // Validar formato de WhatsApp del restaurante
                if (fieldName === 'data[restaurant_info][whatsapp]') {
                    const phoneRegex = /^\+?[1-9][\d\s\-\(\)]{7,20}$/;
                    if (!phoneRegex.test(value)) {
                        isValid = false;
                        input.classList.add('border-red-500');
                        showFieldError(input, 'Formato inválido. Ejemplos: +56912345678, +1 (721) 168-5477');
                        
                        if (!firstInvalidInput) {
                            firstInvalidInput = input;
                        }
                    } else {
                        input.classList.remove('border-red-500');
                    }
                }
                
                // Validar otros campos opcionales con formato específico si es necesario
                // (agregar más validaciones aquí si se necesitan)
            } else {
                // Si no tiene valor, quitar cualquier error previo
                input.classList.remove('border-red-500');
            }
        });
        
        // Validación especial para campos de contacto en PROFILE (paso 3 en PROFILE)
        if (isProfile && currentStep === 3) {
            const emailField = currentStepContent.querySelector('input[name="data[contact_info][email]"]');
            const phoneField = currentStepContent.querySelector('input[name="data[contact_info][phone]"]');
            
            if (emailField && phoneField) {
                const hasEmail = emailField.value.trim();
                const hasPhone = phoneField.value.trim();
                
                if (!hasEmail && !hasPhone) {
                    isValid = false;
                    emailField.classList.add('border-red-500');
                    phoneField.classList.add('border-red-500');
                    
                    if (!firstInvalidInput) {
                        firstInvalidInput = emailField;
                    }
                    
                    showFieldError(emailField, 'Debes proporcionar al menos un email o teléfono de contacto.');
                }
            }
        }
        
        // Validación especial para multimedia en paso 3 (solo para GIFT)
        if (!isProfile && currentStep === 3) {
            console.log('🎵 Validando multimedia en paso 3 para GIFT');
            
            // Validar Audio
            const audioTypeSelect = currentStepContent.querySelector('select[name="multimedia[audio][type]"]');
            console.log('🎵 Audio type select found:', audioTypeSelect);
            console.log('🎵 Audio type value:', audioTypeSelect ? audioTypeSelect.value : 'not found');
            
            if (audioTypeSelect && audioTypeSelect.value === 'file_upload') {
                console.log('🎵 Audio file_upload detectado, validando...');
                
                const audioFileUrl = currentStepContent.querySelector('input[name="multimedia[audio][file_url]"]');
                const audioUrlInput = currentStepContent.querySelector('input[name="multimedia[audio][url]"]');
                const audioPreview = currentStepContent.querySelector('#audio-preview');
                
                // Verificar si realmente hay un archivo subido (preview visible)
                const hasActualFileUploaded = audioPreview && !audioPreview.classList.contains('hidden');
                const hasUrlProvided = audioUrlInput && audioUrlInput.value.trim();
                
                console.log('🎵 File uploaded (preview visible):', hasActualFileUploaded);
                console.log('🎵 URL provided:', hasUrlProvided);
                console.log('🎵 File URL value:', audioFileUrl ? audioFileUrl.value : 'not found');
                console.log('🎵 URL value:', audioUrlInput ? audioUrlInput.value : 'not found');
                console.log('🎵 Preview element hidden:', audioPreview ? audioPreview.classList.contains('hidden') : 'preview not found');
                
                if (!hasActualFileUploaded && !hasUrlProvided) {
                    console.log('🎵 ERROR: No hay archivo ni URL REAL, bloqueando avance');
                    isValid = false;
                    const audioUploadArea = currentStepContent.querySelector('#audio-upload-area');
                    if (audioUploadArea) {
                        audioUploadArea.classList.add('border-red-500');
                        if (!firstInvalidInput) {
                            firstInvalidInput = audioUploadArea;
                        }
                        showFieldError(audioUploadArea, 'Debes subir un archivo de audio o proporcionar una URL.');
                    }
                } else {
                    console.log('🎵 ✅ Audio validation passed');
                }
            }
            
            // Validar Video
            const videoTypeSelect = currentStepContent.querySelector('select[name="multimedia[video][type]"]');
            console.log('🎬 Video type select found:', videoTypeSelect);
            console.log('🎬 Video type value:', videoTypeSelect ? videoTypeSelect.value : 'not found');
            
            if (videoTypeSelect && videoTypeSelect.value === 'file_upload') {
                console.log('🎬 Video file_upload detectado, validando...');
                
                const videoFileUrl = currentStepContent.querySelector('input[name="multimedia[video][file_url]"]');
                const videoUrlInput = currentStepContent.querySelector('input[name="multimedia[video][url]"]');
                const videoPreview = currentStepContent.querySelector('#video-preview');
                
                // Verificar si realmente hay un archivo subido (preview visible)
                const hasActualFileUploaded = videoPreview && !videoPreview.classList.contains('hidden');
                const hasUrlProvided = videoUrlInput && videoUrlInput.value.trim();
                
                console.log('🎬 File uploaded (preview visible):', hasActualFileUploaded);
                console.log('🎬 URL provided:', hasUrlProvided);
                console.log('🎬 File URL value:', videoFileUrl ? videoFileUrl.value : 'not found');
                console.log('🎬 URL value:', videoUrlInput ? videoUrlInput.value : 'not found');
                console.log('🎬 Preview element hidden:', videoPreview ? videoPreview.classList.contains('hidden') : 'preview not found');
                
                if (!hasActualFileUploaded && !hasUrlProvided) {
                    console.log('🎬 ERROR: No hay archivo ni URL REAL, bloqueando avance');
                    isValid = false;
                    const videoUploadArea = currentStepContent.querySelector('#video-upload-area');
                    if (videoUploadArea) {
                        videoUploadArea.classList.add('border-red-500');
                        if (!firstInvalidInput) {
                            firstInvalidInput = videoUploadArea;
                        }
                        showFieldError(videoUploadArea, 'Debes subir un archivo de video o proporcionar una URL.');
                    }
                } else {
                    console.log('🎬 ✅ Video validation passed');
                }
            }
        }
        
        // Validación especial para platos del menú en MENU (paso 3)
        const isMenu = '{{ $token->content_type }}' === 'MENU';
        if (isMenu && currentStep === 3) {
            console.log('🍽️ Validando platos del menú en step 3');
            console.log('🍽️ Estado isValid antes de validar menú:', isValid);
            
            // Verificar que hay exactamente 4 platos válidos
            const menuItems = currentStepContent.querySelectorAll('.menu-item');
            let validMenuItems = 0;
            let incompleteItems = [];
            
            menuItems.forEach((item, index) => {
                const nameInput = item.querySelector('input[name*="[name]"]');
                const priceInput = item.querySelector('input[name*="[price]"]');
                const descriptionInput = item.querySelector('textarea[name*="[description]"]');
                
                if (nameInput && priceInput && descriptionInput) {
                    const name = nameInput.value.trim();
                    const price = priceInput.value.trim();
                    const description = descriptionInput.value.trim();
                    
                    console.log(`🍽️ Plato ${index + 1}:`, { name, price, description });
                    
                    // Limpiar errores previos de este plato
                    [nameInput, priceInput, descriptionInput].forEach(input => {
                        input.classList.remove('border-red-500');
                    });
                    
                    let itemComplete = true;
                    
                    if (!name) {
                        console.log(`🍽️ Plato ${index + 1}: nombre vacío`);
                        nameInput.classList.add('border-red-500');
                        itemComplete = false;
                    }
                    if (!price) {
                        console.log(`🍽️ Plato ${index + 1}: precio vacío`);
                        priceInput.classList.add('border-red-500');
                        itemComplete = false;
                    } else if (isNaN(parseFloat(price)) || parseFloat(price) < 0) {
                        console.log(`🍽️ Plato ${index + 1}: precio inválido`);
                        priceInput.classList.add('border-red-500');
                        itemComplete = false;
                    }
                    if (!description) {
                        console.log(`🍽️ Plato ${index + 1}: descripción vacía`);
                        descriptionInput.classList.add('border-red-500');
                        itemComplete = false;
                    }
                    
                    if (itemComplete) {
                        validMenuItems++;
                        console.log(`🍽️ Plato ${index + 1}: COMPLETO`);
                    } else {
                        incompleteItems.push(index + 1);
                        console.log(`🍽️ Plato ${index + 1}: INCOMPLETO`);
                        if (!firstInvalidInput) {
                            firstInvalidInput = !name ? nameInput : (!price ? priceInput : descriptionInput);
                        }
                    }
                }
            });
            
            console.log('🍽️ Platos válidos encontrados:', validMenuItems, 'de', menuItems.length);
            console.log('🍽️ Platos incompletos:', incompleteItems);
            
            const menuContainer = currentStepContent.querySelector('#menuItemsContainer');
            
            if (menuItems.length < 4) {
                console.log('🍽️ ERROR: Faltan platos - solo hay', menuItems.length);
                isValid = false;
                
                if (menuContainer && !firstInvalidInput) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'wizard-error-message text-red-500 text-sm mt-2 font-medium bg-red-50 border border-red-200 rounded p-3';
                    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i>Necesitas exactamente 4 platos. Actualmente tienes ${menuItems.length}. Agrega ${4 - menuItems.length} plato${4 - menuItems.length !== 1 ? 's' : ''} más.`;
                    
                    // Remover error previo si existe
                    const existingError = menuContainer.querySelector('.wizard-error-message');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    menuContainer.insertBefore(errorDiv, menuContainer.firstChild);
                    firstInvalidInput = menuContainer;
                }
            } else if (validMenuItems < 4) {
                console.log('🍽️ ERROR: Platos incompletos - marcando isValid como false');
                isValid = false;
                
                if (menuContainer) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'wizard-error-message text-red-500 text-sm mt-2 font-medium bg-red-50 border border-red-200 rounded p-3';
                    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i>Debes completar todos los campos de los 4 platos. ${incompleteItems.length > 0 ? `Revisa los platos: ${incompleteItems.join(', ')}` : 'Algunos campos están vacíos o tienen valores inválidos.'}.`;
                    
                    // Remover error previo si existe
                    const existingError = menuContainer.querySelector('.wizard-error-message');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    menuContainer.insertBefore(errorDiv, menuContainer.firstChild);
                }
            } else {
                console.log('🍽️ SUCCESS: Todos los platos están completos');
                // Remover error previo si existe
                if (menuContainer) {
                    const existingError = menuContainer.querySelector('.wizard-error-message');
                    if (existingError) {
                        existingError.remove();
                    }
                }
                
                // Remover todos los bordes rojos si todo está válido
                menuItems.forEach(item => {
                    const inputs = item.querySelectorAll('input, textarea');
                    inputs.forEach(input => input.classList.remove('border-red-500'));
                });
            }
            
            console.log('🍽️ Estado isValid después de validar menú:', isValid);
        }
        
        if (firstInvalidInput) {
            firstInvalidInput.focus();
        }
        
        console.log('🏁 Validation complete. Result:', isValid);
        if (!isValid) {
            console.log('❌ Validation failed. First invalid input:', firstInvalidInput);
        }
        
        return isValid;
    }
    
    function getFieldLabel(input) {
        const label = input.closest('.form-group, div')?.querySelector('label');
        if (label) {
            return label.textContent.replace('*', '').replace(':', '').trim();
        }
        return input.name;
    }
    
    function showFieldError(input, message) {
        // Remover mensaje de error previo si existe
        const existingError = input.parentNode.querySelector('.wizard-error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Crear nuevo mensaje de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'wizard-error-message text-red-500 text-sm mt-1 font-medium';
        errorDiv.textContent = message;
        
        // Insertar después del input
        input.parentNode.insertBefore(errorDiv, input.nextSibling);
    }
    
    nextBtn.addEventListener('click', function(event) {
        console.log('🔄 Next button clicked, current step:', currentStep);
        console.log('🔄 Event object:', event);
        
        const isValid = validateCurrentStep();
        console.log('✅ Validation result:', isValid);
        console.log('📊 Current step:', currentStep, 'Total steps:', totalSteps);
        
        if (isValid && currentStep < totalSteps) {
            console.log('🎯 Advancing to next step');
            currentStep++;
            updateWizard();
            
            // Scroll hacia arriba del wizard después de cambiar de paso
            scrollToWizardTop();
        } else {
            console.log('❌ Cannot advance:', { isValid, currentStep, totalSteps });
            console.log('❌ STOPPING EVENT PROPAGATION');
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            return false;
        }
    });
    
    prevBtn.addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            updateWizard();
            
            // Scroll hacia arriba del wizard después de cambiar de paso
            scrollToWizardTop();
        }
    });
    
    // Allow clicking on step indicators to navigate (if step is accessible)
    document.querySelectorAll('.wizard-step').forEach(step => {
        step.addEventListener('click', function() {
            const targetStep = parseInt(this.getAttribute('data-step'));
            
            // Si intenta ir hacia atrás o al mismo paso, permitir sin validación
            if (targetStep <= currentStep) {
                currentStep = targetStep;
                updateWizard();
                scrollToWizardTop();
                return;
            }
            
            // Si intenta avanzar un paso, validar el paso actual primero
            if (targetStep === currentStep + 1) {
                console.log('🔄 Step indicator clicked, validating current step:', currentStep);
                const isValid = validateCurrentStep();
                console.log('✅ Step indicator validation result:', isValid);
                
                if (isValid) {
                    console.log('🎯 Step indicator: advancing to step', targetStep);
                    currentStep = targetStep;
                    updateWizard();
                    scrollToWizardTop();
                } else {
                    console.log('❌ Step indicator: cannot advance due to validation failure');
                }
            } else {
                console.log('❌ Step indicator: cannot skip steps. Current:', currentStep, 'Target:', targetStep);
            }
        });
    });
    
    // Initialize wizard
    updateWizard();
    
    // Debug: Check for multiple event listeners
    console.log('🔍 Checking next button event listeners');
    console.log('🔍 Next button element:', nextBtn);
    
    // Desactivar otros event listeners del botón SOLO para tipo MENU (que tiene conflictos con WizardManager)
    const isMenuType = '{{ $token->content_type }}' === 'MENU';
    
    if (nextBtn && isMenuType) {
        console.log('🍽️ MENU type detected - cleaning conflicting event listeners');
        
        // Clonar el botón para eliminar todos los event listeners
        const newNextBtn = nextBtn.cloneNode(true);
        nextBtn.parentNode.replaceChild(newNextBtn, nextBtn);
        
        // Re-asignar la variable al nuevo botón
        const updatedNextBtn = document.getElementById('next-step');
        
        // Re-agregar nuestro event listener al botón limpio
        updatedNextBtn.addEventListener('click', function(event) {
            console.log('🔄 Next button clicked (clean MENU), current step:', currentStep);
            console.log('🔄 Event object:', event);
            
            const isValid = validateCurrentStep();
            console.log('✅ Validation result:', isValid);
            console.log('📊 Current step:', currentStep, 'Total steps:', totalSteps);
            
            if (isValid && currentStep < totalSteps) {
                console.log('🎯 Advancing to next step');
                currentStep++;
                updateWizard();
                
                // Scroll hacia arriba del wizard después de cambiar de paso
                scrollToWizardTop();
            } else {
                console.log('❌ Cannot advance:', { isValid, currentStep, totalSteps });
                console.log('❌ STOPPING EVENT PROPAGATION');
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                return false;
            }
        });
        
        console.log('🔧 Cleaned MENU event listeners - other types unaffected');
    } else if (!isMenuType) {
        console.log('✅ Non-MENU type detected - keeping original event listeners');
    }
    
    // Guardar cambios (solo guarda, sin redirigir)
    const saveOnlyBtn = document.getElementById('save-only-btn');
    if (saveOnlyBtn) {
        saveOnlyBtn.addEventListener('click', function() {
            // Cambiar el texto del botón para indicar que está guardando
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> <span class="hidden sm:inline">Guardando...</span><span class="sm:hidden">Guardando...</span>';
            this.disabled = true;
            
            // Buscar el formulario padre
            const form = this.closest('form') || document.querySelector('form[method="POST"][enctype="multipart/form-data"]');
            
            if (form) {
                // Crear FormData para envio AJAX
                const formData = new FormData(form);
                
                // Enviar via AJAX
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mostrar mensaje de éxito
                        this.innerHTML = '<i class="fas fa-check mr-2"></i> <span class="hidden sm:inline">Guardado</span><span class="sm:hidden">Guardado</span>';
                        this.classList.remove('from-kraftdo-blue', 'to-kraftdo-green');
                        this.classList.add('from-kraftdo-green', 'to-kraftdo-green');
                        
                        // Restaurar el botón después de 2 segundos
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.classList.remove('from-kraftdo-green', 'to-kraftdo-green');
                            this.classList.add('from-kraftdo-blue', 'to-kraftdo-green');
                            this.disabled = false;
                        }, 2000);
                        
                        // Opcional: mostrar notificación
                        if (typeof showNotification === 'function') {
                            showNotification('Configuración guardada exitosamente', 'success');
                        }
                    } else {
                        throw new Error(data.message || 'Error al guardar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.innerHTML = '<i class="fas fa-times mr-2"></i> <span class="hidden sm:inline">Error</span><span class="sm:hidden">Error</span>';
                    this.classList.remove('from-kraftdo-blue', 'to-kraftdo-green');
                    this.classList.add('from-red-500', 'to-red-600');
                    
                    // Restaurar el botón después de 2 segundos
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.classList.remove('from-red-500', 'to-red-600');
                        this.classList.add('from-kraftdo-blue', 'to-kraftdo-green');
                        this.disabled = false;
                    }, 2000);
                    
                    alert('Error al guardar la configuración: ' + error.message);
                });
            } else {
                // Si no encuentra el formulario, restaurar el botón
                this.innerHTML = originalText;
                this.disabled = false;
                alert('Error: No se pudo encontrar el formulario de configuración.');
            }
        });
    }
    
    // Vista previa con guardado
    const previewWithSaveBtn = document.getElementById('preview-with-save-btn');
    if (previewWithSaveBtn) {
        previewWithSaveBtn.addEventListener('click', function() {
            // Cambiar el texto del botón para indicar que está guardando
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> <span class="hidden sm:inline">Guardando...</span><span class="sm:hidden">Guardando...</span>';
            this.disabled = true;
            
            // Buscar el formulario padre (debe estar en el mismo contexto)
            const form = this.closest('form') || document.querySelector('form[method="POST"][enctype="multipart/form-data"]');
            
            if (form) {
                // Crear un input hidden para indicar que queremos ir al preview después
                const previewInput = document.createElement('input');
                previewInput.type = 'hidden';
                previewInput.name = 'redirect_to_preview';
                previewInput.value = '1';
                form.appendChild(previewInput);
                
                // Enviar el formulario
                form.submit();
            } else {
                // Si no encuentra el formulario, restaurar el botón y mostrar error
                this.innerHTML = originalText;
                this.disabled = false;
                alert('Error: No se pudo encontrar el formulario de configuración.');
            }
        });
    }
    
});

// Función para publicar contenido
@if($content && $content->isDraft())
window.publishContent = function() {
    const confirmed = confirm('¿Estás seguro de que quieres publicar este contenido? Una vez publicado, solo podrás hacer modificaciones limitadas y no podrás volver a modo borrador.');
    
    if (confirmed) {
        // Crear formulario para publicar
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("content.publish", $content) }}';
        
        // Agregar token CSRF
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = getCsrfToken();
        form.appendChild(csrfInput);
        
        // Agregar al DOM y enviar
        document.body.appendChild(form);
        form.submit();
    }
};
@endif
</script>
@endpush 