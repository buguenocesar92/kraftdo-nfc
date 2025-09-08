@props(['token', 'content'])

<!-- Configuración específica para TOURIST -->
<div class="space-y-6">
    <!-- Información Básica del Lugar -->
    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
        <h5 class="font-medium text-orange-800 mb-3">
            <i class="fas fa-map-marked-alt"></i> Información Básica del Lugar
        </h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Nombre del Lugar:</label>
                <input type="text" 
                       name="data[location_info][name]" 
                       value="{{ old('data.location_info.name', $content->data ? ($content->data['location_info']['name'] ?? '') : '') }}"
                       class="w-full border border-gray-300 rounded px-2 py-1"
                       placeholder="Valle del Elqui">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Región/Provincia:</label>
                <input type="text" 
                       name="data[location_info][region]" 
                       value="{{ old('data.location_info.region', $content->data ? ($content->data['location_info']['region'] ?? '') : '') }}"
                       class="w-full border border-gray-300 rounded px-2 py-1"
                       placeholder="Coquimbo">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-600 mb-1">Descripción General:</label>
                <textarea name="data[location_info][description]" 
                          rows="3"
                          class="w-full border border-gray-300 rounded px-2 py-1"
                          placeholder="Descripción detallada del lugar turístico...">{{ old('data.location_info.description', $content->data ? ($content->data['location_info']['description'] ?? '') : '') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Información de Contacto y Emergencias -->
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <h5 class="font-medium text-red-800 mb-3">
            <i class="fas fa-phone-alt"></i> Información de Contacto y Emergencias
        </h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Oficina de Turismo:</label>
                <input type="text" 
                       name="data[contact_info][tourism_office]" 
                       value="{{ old('data.contact_info.tourism_office', $content->data && isset($content->data['contact_info']['tourism_office']) ? $content->data['contact_info']['tourism_office'] : '') }}"
                       class="w-full border border-gray-300 rounded px-2 py-1"
                       placeholder="+56 51 234 5678">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Emergencias (133):</label>
                <input type="text" 
                       name="data[contact_info][emergency]" 
                       value="{{ old('data.contact_info.emergency', $content->data['contact_info']['emergency'] ?? '133') }}"
                       class="w-full border border-gray-300 rounded px-2 py-1"
                       placeholder="133">
            </div>
        </div>
    </div>

    <!-- Ubicación y Mapa -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h5 class="font-medium text-blue-800 mb-3">
            <i class="fas fa-map"></i> Ubicación y Mapa
        </h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Dirección Completa:</label>
                <input type="text" 
                       name="data[location_info][address]" 
                       value="{{ old('data.location_info.address', $content->data && isset($content->data['location_info']['address']) ? $content->data['location_info']['address'] : '') }}"
                       class="w-full border border-gray-300 rounded px-2 py-1"
                       placeholder="Camino Internacional 123, Paihuano">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-600 mb-1">Link de Google Maps:</label>
                <input type="url" 
                       name="data[location_info][google_maps_url]" 
                       value="{{ old('data.location_info.google_maps_url', $content->data['location_info']['google_maps_url'] ?? '') }}"
                       class="w-full border border-gray-300 rounded px-2 py-1"
                       placeholder="https://maps.google.com/?q=-30.123456,-70.123456">
                <p class="text-xs text-gray-500 mt-1">Pega aquí el link de Google Maps del lugar</p>
            </div>
        </div>
    </div>

    <!-- Actividades y Tours -->
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
        <h5 class="font-medium text-purple-800 mb-3">
            <i class="fas fa-hiking"></i> Actividades y Tours
        </h5>
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Agencia de Tours Principal:</label>
                    <input type="text" 
                           name="data[activities][main_agency]" 
                           value="{{ old('data.activities.main_agency', $content->data['activities']['main_agency'] ?? '') }}"
                           class="w-full border border-gray-300 rounded px-2 py-1"
                           placeholder="Tours Valle del Elqui">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Teléfono Agencia:</label>
                    <input type="text" 
                           name="data[activities][agency_phone]" 
                           value="{{ old('data.activities.agency_phone', $content->data['activities']['agency_phone'] ?? '') }}"
                           class="w-full border border-gray-300 rounded px-2 py-1"
                           placeholder="+56 51 234 5678">
                </div>
            </div>
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
        <h5 class="font-medium text-gray-800 mb-3">
            <i class="fas fa-info-circle"></i> Información Adicional
        </h5>
        <div class="space-y-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Horarios de Visita:</label>
                <input type="text" 
                       name="data[additional_info][visiting_hours]" 
                       value="{{ old('data.additional_info.visiting_hours', $content->data['additional_info']['visiting_hours'] ?? '') }}"
                       class="w-full border border-gray-300 rounded px-2 py-1"
                       placeholder="Lunes a Domingo 9:00 - 18:00">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Entrada (si aplica):</label>
                <input type="text" 
                       name="data[additional_info][entrance_fee]" 
                       value="{{ old('data.additional_info.entrance_fee', $content->data['additional_info']['entrance_fee'] ?? '') }}"
                       class="w-full border border-gray-300 rounded px-2 py-1"
                       placeholder="Gratis / $5.000">
            </div>
        </div>
    </div>
</div>

<div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
    <p class="text-blue-800 text-sm">
        <i class="fas fa-info-circle mr-1"></i>
        <strong>Nota:</strong> Esta es una versión simplificada del formulario TOURIST. 
        El formulario completo incluye más secciones como alojamiento, gastronomía, clima, etc.
    </p>
</div> 