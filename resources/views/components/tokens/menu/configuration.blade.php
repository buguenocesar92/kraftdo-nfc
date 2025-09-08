@props(['token', 'content'])

<!-- Configuración de platos para MENU -->
<div class="space-y-6">
    <!-- Gestión de Platos -->
    <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-kraftdo-green/20 shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-lg font-bold text-kraftdo-navy flex items-center">
                <div class="kraftdo-gradient w-10 h-10 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                    <i class="fas fa-list-alt text-white"></i>
                </div>
                Platos del Menú
                <span id="menu-items-count" class="ml-2 bg-kraftdo-blue/20 text-kraftdo-blue px-2 py-1 rounded-full text-sm font-medium">
                    0 platos
                </span>
            </h4>
            <div class="flex space-x-2">
                <button type="button" 
                        id="add-basic-item"
                        onclick="addMenuItem()"
                        class="kraftdo-gradient text-white px-4 py-2 rounded-xl hover:shadow-lg transition-all duration-200 transform hover:scale-105 font-semibold">
                    <i class="fas fa-plus mr-1"></i> Agregar Plato
                </button>
            </div>
        </div>
        
        
        <div id="menuItemsContainer" class="space-y-4">
            @if(isset($content->data['menu_items']) && count($content->data['menu_items']) > 0)
                @foreach($content->data['menu_items'] as $index => $item)
                    <div class="menu-item bg-white border border-gray-300 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <h6 class="font-medium text-gray-800">Plato #{{ $index + 1 }}</h6>
                            <button type="button" 
                                    onclick="removeMenuItem(this)"
                                    class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Nombre del plato:</label>
                                <input type="text" 
                                       name="data[menu_items][{{ $index }}][name]" 
                                       value="{{ $item['name'] }}"
                                       class="w-full border border-gray-300 rounded px-2 py-1"
                                       placeholder="Ej: Pasta Carbonara"
                                       data-required="true">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Precio:</label>
                                <input type="number" 
                                       name="data[menu_items][{{ $index }}][price]" 
                                       value="{{ $item['price'] }}"
                                       class="w-full border border-gray-300 rounded px-2 py-1"
                                       placeholder="15000"
                                       data-required="true"
                                       min="0">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm text-gray-600 mb-1">Descripción *:</label>
                                <textarea name="data[menu_items][{{ $index }}][description]" 
                                          rows="2"
                                          class="w-full border border-gray-300 rounded px-2 py-1"
                                          placeholder="Descripción del plato..."
                                          data-required="true">{{ $item['description'] ?? '' }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm text-gray-600 mb-1">Imagen del plato:</label>
                                <div class="flex items-center space-x-3">
                                    <button type="button" 
                                            onclick="document.getElementById('menu-item-image-{{ $index }}').click()"
                                            class="kraftdo-gradient text-white px-3 py-1 rounded-lg text-sm hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                        <i class="fas fa-camera mr-1"></i>Subir Imagen
                                    </button>
                                    <input type="url" 
                                           name="data[menu_items][{{ $index }}][image_url]" 
                                           value="{{ $item['image_url'] ?? '' }}"
                                           class="flex-1 border border-gray-300 rounded px-2 py-1 text-sm"
                                           placeholder="https://ejemplo.com/imagen-plato.jpg">
                                    <input type="file" 
                                           id="menu-item-image-{{ $index }}" 
                                           accept="image/*" 
                                           style="display: none;" 
                                           onchange="uploadMenuItemImage(this, {{ $index }})">
                                </div>
                                @if(!empty($item['image_url']))
                                    <div class="mt-2">
                                        <img src="{{ $item['image_url'] }}" 
                                             alt="Vista previa" 
                                             class="w-20 h-20 object-cover rounded border border-gray-200">
                                    </div>
                                @endif
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm text-gray-600 mb-1">Categoría:</label>
                                <select name="data[menu_items][{{ $index }}][category]" 
                                        class="w-full border border-gray-300 rounded px-2 py-1">
                                    <option value="Entradas" {{ ($item['category'] ?? '') === 'Entradas' ? 'selected' : '' }}>Entradas</option>
                                    <option value="Platos Principales" {{ ($item['category'] ?? '') === 'Platos Principales' ? 'selected' : '' }}>Platos Principales</option>
                                    <option value="Postres" {{ ($item['category'] ?? '') === 'Postres' ? 'selected' : '' }}>Postres</option>
                                    <option value="Bebidas" {{ ($item['category'] ?? '') === 'Bebidas' ? 'selected' : '' }}>Bebidas</option>
                                    <option value="Especiales" {{ ($item['category'] ?? '') === 'Especiales' ? 'selected' : '' }}>Especiales</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="data[menu_items][{{ $index }}][popular]" 
                                           value="1"
                                           {{ ($item['popular'] ?? false) ? 'checked' : '' }}
                                           class="mr-2">
                                    <span class="text-sm text-gray-600">Marcar como popular</span>
                                </label>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>


</div>

@push('scripts')
<script>
let menuItemIndex = {{ isset($content->data['menu_items']) ? count($content->data['menu_items']) : 0 }};
const MAX_DISHES = 4;

// Función para actualizar contador y controles
function updateMenuItemControls() {
    const currentCount = document.querySelectorAll('.menu-item').length;
    const countElement = document.getElementById('menu-items-count');
    const addBasicBtn = document.getElementById('add-basic-item');
    
    // Actualizar contador
    countElement.textContent = `${currentCount} plato${currentCount !== 1 ? 's' : ''}`;
    
    // Deshabilitar botón si se alcanzó el límite
    if (currentCount >= MAX_DISHES) {
        addBasicBtn.disabled = true;
        addBasicBtn.classList.add('opacity-50', 'cursor-not-allowed');
        addBasicBtn.innerHTML = '<i class="fas fa-ban mr-1"></i> Límite Alcanzado';
    } else {
        addBasicBtn.disabled = false;
        addBasicBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        addBasicBtn.innerHTML = '<i class="fas fa-plus mr-1"></i> Agregar Plato';
    }
}

function addMenuItem() {
    const container = document.getElementById('menuItemsContainer');
    const currentCount = container.querySelectorAll('.menu-item').length;
    
    // Verificar límite de platos
    if (currentCount >= MAX_DISHES) {
        alert(`Has alcanzado el límite máximo de ${MAX_DISHES} platos.`);
        return;
    }
    
    
    const newItem = document.createElement('div');
    newItem.className = 'menu-item bg-white border border-gray-300 rounded-lg p-4';
    
    // Usar un índice temporal que será corregido por updateMenuItemNumbers()
    const tempIndex = Date.now(); // Índice temporal único
    
    newItem.innerHTML = `
        <div class="flex justify-between items-start mb-3">
            <h6 class="font-medium text-gray-800">Plato #Nuevo</h6>
            <button type="button" 
                    onclick="removeMenuItem(this)"
                    class="text-red-500 hover:text-red-700">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Nombre del plato:</label>
                <input type="text" 
                       name="data[menu_items][${tempIndex}][name]" 
                       class="w-full border border-gray-300 rounded px-2 py-1"
                       placeholder="Ej: Pasta Carbonara"
                       data-required="true">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Precio:</label>
                <input type="number" 
                       name="data[menu_items][${tempIndex}][price]" 
                       class="w-full border border-gray-300 rounded px-2 py-1"
                       placeholder="15000"
                       data-required="true"
                       min="0">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-600 mb-1">Descripción *:</label>
                <textarea name="data[menu_items][${tempIndex}][description]" 
                          rows="2"
                          class="w-full border border-gray-300 rounded px-2 py-1"
                          placeholder="Descripción del plato..."
                          data-required="true"></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-600 mb-1">Imagen del plato:</label>
                <div class="flex items-center space-x-3">
                    <button type="button" 
                            onclick="document.getElementById('menu-item-image-${tempIndex}').click()"
                            class="kraftdo-gradient text-white px-3 py-1 rounded-lg text-sm hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-camera mr-1"></i>Subir Imagen
                    </button>
                    <input type="url" 
                           name="data[menu_items][${tempIndex}][image_url]" 
                           class="flex-1 border border-gray-300 rounded px-2 py-1 text-sm"
                           placeholder="https://ejemplo.com/imagen-plato.jpg">
                    <input type="file" 
                           id="menu-item-image-${tempIndex}" 
                           accept="image/*" 
                           style="display: none;" 
                           onchange="uploadMenuItemImage(this, ${tempIndex})">
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-600 mb-1">Categoría:</label>
                <select name="data[menu_items][${tempIndex}][category]" 
                        class="w-full border border-gray-300 rounded px-2 py-1">
                    <option value="Entradas">Entradas</option>
                    <option value="Platos Principales" selected>Platos Principales</option>
                    <option value="Postres">Postres</option>
                    <option value="Bebidas">Bebidas</option>
                    <option value="Especiales">Especiales</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="data[menu_items][${tempIndex}][popular]" 
                           value="1"
                           class="mr-2">
                    <span class="text-sm text-gray-600">Marcar como popular</span>
                </label>
            </div>
        </div>
    `;
    
    // Insertar el nuevo elemento al PRINCIPIO del container, no al final
    container.insertBefore(newItem, container.firstChild);
    
    // Actualizar INMEDIATAMENTE todos los números e índices
    updateMenuItemNumbers();
    updateMenuItemControls();
    
    // Hacer scroll suave hacia el nuevo elemento agregado
    newItem.scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    // Enfocar el primer input del nuevo plato
    const firstInput = newItem.querySelector('input[type="text"]');
    if (firstInput) {
        setTimeout(() => firstInput.focus(), 300);
    }
}

function removeMenuItem(button) {
    const menuItem = button.closest('.menu-item');
    menuItem.remove();
    
    // Actualizar números de platos y controles
    updateMenuItemNumbers();
    updateMenuItemControls();
}

function updateMenuItemNumbers() {
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach((item, index) => {
        const title = item.querySelector('h6');
        title.textContent = `Plato #${index + 1}`;
        
        // Actualizar TODOS los nombres de los campos para que tengan índices correctos
        const nameInput = item.querySelector('input[name*="[name]"]');
        const priceInput = item.querySelector('input[name*="[price]"]');
        const descriptionTextarea = item.querySelector('textarea[name*="[description]"]');
        const categorySelect = item.querySelector('select[name*="[category]"]');
        const popularCheckbox = item.querySelector('input[name*="[popular]"]');
        const imageUrlInput = item.querySelector('input[name*="[image_url]"]');
        const imageFileInput = item.querySelector('input[type="file"]');
        
        if (nameInput) nameInput.name = `data[menu_items][${index}][name]`;
        if (priceInput) priceInput.name = `data[menu_items][${index}][price]`;
        if (descriptionTextarea) descriptionTextarea.name = `data[menu_items][${index}][description]`;
        if (categorySelect) categorySelect.name = `data[menu_items][${index}][category]`;
        if (popularCheckbox) popularCheckbox.name = `data[menu_items][${index}][popular]`;
        if (imageUrlInput) imageUrlInput.name = `data[menu_items][${index}][image_url]`;
        if (imageFileInput) {
            imageFileInput.id = `menu-item-image-${index}`;
            // Actualizar el botón que hace referencia a este file input
            const uploadBtn = item.querySelector('button[onclick*="menu-item-image"]');
            if (uploadBtn) {
                uploadBtn.setAttribute('onclick', `document.getElementById('menu-item-image-${index}').click()`);
            }
            // Actualizar el onchange del file input
            imageFileInput.setAttribute('onchange', `uploadMenuItemImage(this, ${index})`);
        }
    });
}


// Función para upload de imagen de plato individual
function uploadMenuItemImage(input, itemIndex) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validar que es una imagen
        if (!file.type.startsWith('image/')) {
            alert('Por favor selecciona un archivo de imagen válido.');
            return;
        }
        
        // Validar tamaño (máximo 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('La imagen es demasiado grande. Máximo 5MB permitido.');
            return;
        }
        
        const formData = new FormData();
        formData.append('image', file);
        
        // Mostrar indicador de carga en el botón específico
        const uploadBtn = input.closest('.flex').querySelector('button');
        const originalText = uploadBtn.innerHTML;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Subiendo...';
        uploadBtn.disabled = true;
        
        fetch('{{ route("my-tokens.upload-image") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar el campo URL con la imagen subida
                const urlInput = input.closest('.flex').querySelector('input[type="url"]');
                urlInput.value = data.url;
                
                // Crear y mostrar preview de la imagen dinámicamente
                showMenuItemImagePreview(data.url, itemIndex);
            } else {
                alert('Error al subir la imagen: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al subir la imagen. Por favor intenta de nuevo.');
        })
        .finally(() => {
            // Restaurar botón
            uploadBtn.innerHTML = originalText;
            uploadBtn.disabled = false;
        });
    }
}

// Función para mostrar preview de imagen del plato dinámicamente
function showMenuItemImagePreview(imageUrl, itemIndex) {
    const menuItem = document.querySelector(`input[id="menu-item-image-${itemIndex}"]`).closest('.menu-item');
    let previewContainer = menuItem.querySelector('.menu-item-image-preview');
    
    // Si ya existe un preview, actualizarlo
    if (previewContainer) {
        previewContainer.querySelector('img').src = imageUrl;
    } else {
        // Crear nuevo preview
        const imageContainer = menuItem.querySelector('input[type="url"]').closest('.md\\:col-span-2');
        previewContainer = document.createElement('div');
        previewContainer.className = 'menu-item-image-preview mt-2';
        previewContainer.innerHTML = `
            <img src="${imageUrl}" 
                 alt="Vista previa del plato" 
                 class="w-20 h-20 object-cover rounded border border-gray-200">
        `;
        imageContainer.appendChild(previewContainer);
    }
}





// Inicializar controles al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    updateMenuItemControls();
});
</script>
@endpush 