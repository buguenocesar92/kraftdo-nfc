@props(['content'])

<!-- Selector de subtipo de regalo -->
<div class="bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200/50">
    <label class="block text-sm font-semibold text-kraftdo-navy mb-4">
        <i class="fas fa-palette mr-2 text-kraftdo-blue"></i> Selecciona el Tipo de Regalo
    </label>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach(\App\Models\DynamicContent::getGiftSubtypes() as $key => $subtype)
            <div class="relative">
                <input type="radio" 
                       id="gift_subtype_{{ $key }}" 
                       name="gift_subtype" 
                       value="{{ $key }}"
                       {{ ($content->gift_subtype ?? 'LOVE') === $key ? 'checked' : '' }}
                       class="sr-only">
                <label for="gift_subtype_{{ $key }}" 
                       class="block p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 hover:bg-white/80 hover:shadow-md transform hover:scale-105
                              {{ ($content->gift_subtype ?? 'LOVE') === $key ? 'border-kraftdo-green bg-kraftdo-lime/10' : 'border-gray-300' }} 
                              hover:border-kraftdo-blue gift-subtype-label">
                    <div class="text-center">
                        <div class="text-2xl mb-2 text-kraftdo-blue"><i class="{{ $subtype['icon'] }}"></i></div>
                        <div class="font-semibold text-sm text-kraftdo-navy">{{ $subtype['name'] }}</div>
                        <div class="text-xs text-kraftdo-navy/70 mt-1">{{ $subtype['description'] }}</div>
                    </div>
                </label>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Script para subtipos de regalo
    const subtypeInputs = document.querySelectorAll('input[name="gift_subtype"]');
    const messageContext = document.getElementById('message_context');
    const messageTextarea = document.getElementById('love_message');
    
    if (subtypeInputs.length > 0 && messageContext && messageTextarea) {
        const contexts = {
            'LOVE': { text: 'de amor', placeholder: 'Escribe tu mensaje de amor...' },
            'FRIEND': { text: 'de amistad', placeholder: 'Escribe tu mensaje de amistad...' },
            'PROFESSIONAL': { text: 'profesional', placeholder: 'Escribe tu mensaje profesional...' },
            'FAMILY': { text: 'familiar', placeholder: 'Escribe tu mensaje familiar...' },
            'CELEBRATION': { text: 'de celebración', placeholder: 'Escribe tu mensaje de celebración...' }
        };
        
        subtypeInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.checked) {
                    const context = contexts[this.value];
                    if (context) {
                        messageContext.textContent = context.text;
                        messageTextarea.placeholder = context.placeholder;
                        
                        // Actualizar estilo del label seleccionado
                        document.querySelectorAll('.gift-subtype-label').forEach(label => {
                            label.classList.remove('border-kraftdo-green', 'bg-kraftdo-lime/10');
                            label.classList.add('border-gray-300');
                        });
                        
                        const selectedLabel = document.querySelector(`label[for="gift_subtype_${this.value}"]`);
                        if (selectedLabel) {
                            selectedLabel.classList.remove('border-gray-300');
                            selectedLabel.classList.add('border-kraftdo-green', 'bg-kraftdo-lime/10');
                        }
                    }
                }
            });
        });
        
        // Función para inicializar la selección
        function initializeSubtypeSelection() {
            // Limpiar todos los estilos primero
            document.querySelectorAll('.gift-subtype-label').forEach(label => {
                label.classList.remove('border-kraftdo-green', 'bg-kraftdo-lime/10');
                label.classList.add('border-gray-300');
            });
            
            // Aplicar estilo al subtipo seleccionado
            const checkedInput = document.querySelector('input[name="gift_subtype"]:checked');
            if (checkedInput) {
                const selectedLabel = document.querySelector(`label[for="gift_subtype_${checkedInput.value}"]`);
                if (selectedLabel) {
                    selectedLabel.classList.remove('border-gray-300');
                    selectedLabel.classList.add('border-kraftdo-green', 'bg-kraftdo-lime/10');
                }
                
                // Actualizar contexto del mensaje si está disponible
                const context = contexts[checkedInput.value];
                if (context && messageContext && messageTextarea) {
                    messageContext.textContent = context.text;
                    messageTextarea.placeholder = context.placeholder;
                }
            }
        }
        
        // Inicializar selección al cargar
        initializeSubtypeSelection();
        
        // También inicializar cuando se muestre este paso del wizard
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const target = mutation.target;
                    if (target.classList.contains('wizard-step-content') && 
                        target.getAttribute('data-step') === '2' && 
                        !target.classList.contains('hidden')) {
                        // Este step se acaba de mostrar, reinicializar
                        setTimeout(() => {
                            initializeSubtypeSelection();
                        }, 100);
                    }
                }
            });
        });
        
        // Observar cambios en los pasos del wizard
        const wizardSteps = document.querySelectorAll('.wizard-step-content[data-step="2"]');
        wizardSteps.forEach(step => {
            observer.observe(step, { attributes: true, attributeFilter: ['class'] });
        });
    }
});
</script>
@endpush 