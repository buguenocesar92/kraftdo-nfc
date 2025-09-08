@props([
    'token',
    'content'
])

<x-nfc.configuration-form :token="$token" :content="$content">
    <!-- Step 2: Tipo de Regalo (solo para GIFT) -->
    <div class="wizard-step-content hidden" data-step="2">
        <h3 class="text-xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent mb-6">🎁 Tipo de Regalo</h3>
        
        <x-tokens.gift.partials.gift-type-section :token="$token" :content="$content" />
        
        <div class="flex justify-between mt-8">
            <x-ui.button type="button" variant="secondary" onclick="previousStep()" icon="fa-arrow-left">
                Anterior
            </x-ui.button>
            <x-ui.button type="button" onclick="nextStep()" icon-right="fa-arrow-right">
                Siguiente
            </x-ui.button>
        </div>
    </div>

    <!-- Step 3: Multimedia -->
    <div class="wizard-step-content hidden" data-step="3">
        <h3 class="text-xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent mb-6">🎵 Multimedia</h3>
        
        <x-tokens.gift.partials.multimedia-section :token="$token" :content="$content" />
        
        <div class="flex justify-between mt-8">
            <x-ui.button type="button" variant="secondary" onclick="previousStep()" icon="fa-arrow-left">
                Anterior
            </x-ui.button>
            <x-ui.button type="button" onclick="nextStep()" icon-right="fa-arrow-right">
                Siguiente
            </x-ui.button>
        </div>
    </div>

    <!-- Step 4: Diseño -->
    <div class="wizard-step-content hidden" data-step="4">
        <h3 class="text-xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent mb-6">🎨 Diseño</h3>
        
        <x-tokens.gift.partials.design-section :token="$token" :content="$content" />
        
        <div class="flex justify-between mt-8">
            <x-ui.button type="button" variant="secondary" onclick="previousStep()" icon="fa-arrow-left">
                Anterior
            </x-ui.button>
            <x-ui.button type="button" onclick="nextStep()" icon-right="fa-arrow-right">
                Siguiente
            </x-ui.button>
        </div>
    </div>

    <!-- Step 5: Revisión Final -->
    <div class="wizard-step-content hidden" data-step="5">
        <h3 class="text-xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent mb-6">✅ Revisión Final</h3>
        
        <x-tokens.gift.partials.review-section :token="$token" :content="$content" />
        
        <div class="flex justify-between mt-8">
            <x-ui.button type="button" variant="secondary" onclick="previousStep()" icon="fa-arrow-left">
                Anterior
            </x-ui.button>
            <x-ui.button type="submit" variant="success" icon="fa-save" class="bg-gradient-to-r from-pink-500 to-purple-500">
                Guardar Configuración
            </x-ui.button>
        </div>
    </div>
</x-nfc.configuration-form>