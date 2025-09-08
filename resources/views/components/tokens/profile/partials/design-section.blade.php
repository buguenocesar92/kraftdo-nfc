@props(['content'])

@push('scripts')
    @vite(['resources/js/design-preview.js'])
@endpush

@php
    $customColors = $content->data['design']['custom_colors'] ?? [];
    $isDarkTheme = (bool) ($content->data['design']['dark_theme'] ?? false);
@endphp

<div class="space-y-6">
    <!-- Tema Claro/Oscuro -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-adjust text-purple-600 mr-2"></i>
            Apariencia del Perfil
        </h4>
        
        <div class="space-y-4">
            <div class="flex items-center space-x-3">
                <!-- Campo hidden para enviar 0 cuando el checkbox no está marcado -->
                <input type="hidden" name="data[design][dark_theme]" value="0">
                <input type="checkbox" 
                       name="data[design][dark_theme]" 
                       id="dark-theme-checkbox"
                       value="1"
                       {{ $isDarkTheme ? 'checked' : '' }}
                       class="w-5 h-5 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500">
                <label for="dark-theme-checkbox" class="text-sm font-medium text-gray-700">
                    Usar tema oscuro
                </label>
            </div>
            
            <p class="text-sm text-gray-600">
                <i class="fas fa-sun text-yellow-500 mr-1"></i>
                <strong>Tema claro:</strong> Fondo blanco con texto negro
                <br>
                <i class="fas fa-moon text-indigo-500 mr-1"></i>
                <strong>Tema oscuro:</strong> Fondo negro con texto blanco
                <br>
                <small class="text-xs text-gray-400">
                    Estado actual: {{ $isDarkTheme ? 'Oscuro' : 'Claro' }} 
                    (Valor: {{ json_encode($content->data['design']['dark_theme'] ?? 'null') }})
                </small>
            </p>
        </div>
    </div>
    
    <!-- Branding Personalizado -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-paint-brush text-blue-600 mr-2"></i>
            Branding de tu Empresa
        </h4>
        
        <p class="text-sm text-gray-600 mb-4">
            Personaliza los colores para que coincidan con la identidad visual de tu empresa o marca personal.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Color Primario -->
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700">
                    Color Primario
                    <span class="text-xs text-gray-500 block">Color principal de tu marca</span>
                </label>
                <div class="flex items-center space-x-3">
                    <input type="color" 
                           name="data[design][custom_colors][primary]" 
                           id="primary-color"
                           value="{{ $customColors['primary'] ?? '#1e40af' }}"
                           class="w-12 h-12 border border-gray-300 rounded-lg cursor-pointer">
                    <input type="text" 
                           name="data[design][custom_colors][primary_hex]"
                           id="primary-hex"
                           value="{{ $customColors['primary_hex'] ?? '#1e40af' }}"
                           placeholder="#1e40af"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono">
                </div>
            </div>
            
            <!-- Color Secundario -->
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700">
                    Color Secundario
                    <span class="text-xs text-gray-500 block">Color complementario</span>
                </label>
                <div class="flex items-center space-x-3">
                    <input type="color" 
                           name="data[design][custom_colors][secondary]" 
                           id="secondary-color"
                           value="{{ $customColors['secondary'] ?? '#64748b' }}"
                           class="w-12 h-12 border border-gray-300 rounded-lg cursor-pointer">
                    <input type="text" 
                           name="data[design][custom_colors][secondary_hex]"
                           id="secondary-hex"
                           value="{{ $customColors['secondary_hex'] ?? '#64748b' }}"
                           placeholder="#64748b"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono">
                </div>
            </div>
            
            <!-- Color de Acento -->
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700">
                    Color de Acento
                    <span class="text-xs text-gray-500 block">Para botones y enlaces</span>
                </label>
                <div class="flex items-center space-x-3">
                    <input type="color" 
                           name="data[design][custom_colors][accent]" 
                           id="accent-color"
                           value="{{ $customColors['accent'] ?? '#0ea5e9' }}"
                           class="w-12 h-12 border border-gray-300 rounded-lg cursor-pointer">
                    <input type="text" 
                           name="data[design][custom_colors][accent_hex]"
                           id="accent-hex"
                           value="{{ $customColors['accent_hex'] ?? '#0ea5e9' }}"
                           placeholder="#0ea5e9"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono">
                </div>
            </div>
        </div>
    </div>
    
    
    <!-- Vista Previa -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-eye text-green-600 mr-2"></i>
            Vista Previa
        </h4>
        
        <div id="design-preview" class="border border-gray-200 rounded-lg p-6">
            <div id="preview-card" class="max-w-sm mx-auto rounded-xl shadow-lg overflow-hidden {{ $isDarkTheme ? 'bg-gray-800' : 'bg-white' }}">
                <div id="preview-header" class="h-24" style="background: linear-gradient(135deg, {{ $customColors['primary'] ?? '#1e40af' }}, {{ $customColors['secondary'] ?? '#64748b' }})"></div>
                <div id="preview-content" class="p-4">
                    <div class="flex items-center space-x-3 mb-3">
                        <div id="preview-avatar" class="w-12 h-12 {{ $isDarkTheme ? 'bg-gray-600' : 'bg-gray-200' }} rounded-full"></div>
                        <div>
                            <h3 id="preview-name" class="font-semibold {{ $isDarkTheme ? 'text-white' : 'text-black' }}">
                                Tu Nombre
                            </h3>
                            <p id="preview-profession" class="text-sm {{ $isDarkTheme ? 'text-gray-300' : 'text-gray-600' }}">
                               Tu Profesión
                            </p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div id="preview-button" class="text-center py-2 px-4 rounded-lg text-white text-sm font-medium" style="background-color: {{ $customColors['accent'] ?? '#0ea5e9' }}">
                            Botón de Contacto
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

