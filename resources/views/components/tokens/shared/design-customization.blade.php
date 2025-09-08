@props(['token', 'content'])

<div class="space-y-6">
    <div>
        <label class="block text-sm font-semibold text-kraftdo-navy mb-3">Fondo de la Página</label>
        <div class="space-y-4">
            <!-- Tipo de fondo -->
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">Tipo de Fondo</label>
                <select name="multimedia[design][background_type]" id="background-type-select" class="mt-1 block w-full rounded-xl border border-kraftdo-green/30 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80">
                    <option value="default" {{ (($content->data['multimedia']['design']['background_type'] ?? 'default') === 'default') ? 'selected' : '' }}>Por defecto (subtipo)</option>
                    <option value="color" {{ (($content->data['multimedia']['design']['background_type'] ?? '') === 'color') ? 'selected' : '' }}>Color sólido</option>
                    <option value="gradient" {{ (($content->data['multimedia']['design']['background_type'] ?? '') === 'gradient') ? 'selected' : '' }}>Gradiente</option>
                    <option value="transparent" {{ (($content->data['multimedia']['design']['background_type'] ?? '') === 'transparent') ? 'selected' : '' }}>Transparente/Blanco</option>
                </select>
            </div>

            <!-- Color sólido -->
            <div id="solid-color-section" class="bg-kraftdo-lime/10 p-4 rounded-xl border border-kraftdo-green/20" style="display: none;">
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">Color de Fondo</label>
                <div class="flex items-center space-x-2">
                    <input type="color" name="multimedia[design][background_color]" 
                           value="{{ old('multimedia.design.background_color', $content->data['multimedia']['design']['background_color'] ?? '#ffffff') }}"
                           class="w-16 h-10 rounded-lg border border-kraftdo-green/30">
                    <span class="text-sm text-kraftdo-navy/70">Selecciona el color de fondo</span>
                </div>
            </div>

            <!-- Gradiente -->
            <div id="gradient-section" class="bg-kraftdo-lime/10 p-4 rounded-xl border border-kraftdo-green/20" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-3">Gradiente</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Color inicial:</label>
                        <input type="color" name="multimedia[design][gradient_start]" 
                               value="{{ old('multimedia.design.gradient_start', $content->data['multimedia']['design']['gradient_start'] ?? '#ff6b6b') }}"
                               class="w-full h-10 rounded border border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Color final:</label>
                        <input type="color" name="multimedia[design][gradient_end]" 
                               value="{{ old('multimedia.design.gradient_end', $content->data['multimedia']['design']['gradient_end'] ?? '#4ecdc4') }}"
                               class="w-full h-10 rounded border border-gray-300">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-sm text-gray-600 mb-2">Dirección:</label>
                    <select name="multimedia[design][gradient_direction]" class="w-full rounded border border-gray-300">
                        <option value="to-br" {{ (($content->data['multimedia']['design']['gradient_direction'] ?? 'to-br') === 'to-br') ? 'selected' : '' }}>Diagonal (arriba izq. a abajo der.)</option>
                        <option value="to-r" {{ (($content->data['multimedia']['design']['gradient_direction'] ?? '') === 'to-r') ? 'selected' : '' }}>Horizontal (izquierda a derecha)</option>
                        <option value="to-b" {{ (($content->data['multimedia']['design']['gradient_direction'] ?? '') === 'to-b') ? 'selected' : '' }}>Vertical (arriba a abajo)</option>
                        <option value="to-bl" {{ (($content->data['multimedia']['design']['gradient_direction'] ?? '') === 'to-bl') ? 'selected' : '' }}>Diagonal (arriba der. a abajo izq.)</option>
                    </select>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
// Manejo del fondo personalizado
const backgroundTypeSelect = document.getElementById('background-type-select');
const solidColorSection = document.getElementById('solid-color-section');
const gradientSection = document.getElementById('gradient-section');

function updateBackgroundSections() {
    const selectedType = backgroundTypeSelect?.value;
    
    // Ocultar todas las secciones
    if (solidColorSection) solidColorSection.style.display = 'none';
    if (gradientSection) gradientSection.style.display = 'none';
    
    // Mostrar sección correspondiente
    switch(selectedType) {
        case 'color':
            if (solidColorSection) solidColorSection.style.display = 'block';
            break;
        case 'gradient':
            if (gradientSection) gradientSection.style.display = 'block';
            break;
    }
}

// Event listeners para el fondo
if (backgroundTypeSelect) {
    backgroundTypeSelect.addEventListener('change', updateBackgroundSections);
    updateBackgroundSections(); // Inicializar
}
</script>
@endpush 