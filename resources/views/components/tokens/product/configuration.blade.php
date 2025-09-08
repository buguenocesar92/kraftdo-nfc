@props(['token', 'content'])

<!-- Configuración específica para PRODUCT -->
<div class="space-y-6">
    <!-- Información del Producto -->
    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
        <h5 class="font-medium text-indigo-800 mb-3">
            <i class="fas fa-shopping-cart"></i> Información del Producto
        </h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Nombre del Producto:</label>
                <input type="text" 
                       name="data[product_info][name]" 
                       value="{{ old('data.product_info.name', $content->data['product_info']['name'] ?? '') }}"
                       class="w-full border border-gray-300 rounded px-2 py-1"
                       placeholder="Mi Producto">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Precio:</label>
                <input type="number" 
                       name="data[product_info][price]" 
                       value="{{ old('data.product_info.price', $content->data['product_info']['price'] ?? '') }}"
                       class="w-full border border-gray-300 rounded px-2 py-1"
                       placeholder="50000">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-600 mb-1">Descripción:</label>
                <textarea name="data[product_info][description]" 
                          rows="3"
                          class="w-full border border-gray-300 rounded px-2 py-1"
                          placeholder="Descripción detallada del producto...">{{ old('data.product_info.description', $content->data['product_info']['description'] ?? '') }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-600 mb-1">Características (una por línea):</label>
                <textarea name="data[product_info][features]" 
                          rows="3"
                          class="w-full border border-gray-300 rounded px-2 py-1"
                          placeholder="Calidad premium&#10;Diseño único&#10;Garantía">{{ old('data.product_info.features', isset($content->data['product_info']['features']) && is_array($content->data['product_info']['features']) ? implode("\n", $content->data['product_info']['features']) : '') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Escribe cada característica en una línea separada</p>
            </div>
        </div>
    </div>
</div> 