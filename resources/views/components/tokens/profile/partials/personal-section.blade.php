@props(['content'])

<!-- Información Personal -->
<div class="bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200/50">
    <div class="mb-6">
        <p class="text-sm font-semibold text-kraftdo-navy">
            <i class="fas fa-user text-kraftdo-blue mr-2"></i>
            Completa tu información profesional para crear un perfil atractivo
        </p>
    </div>
    
    <div class="space-y-4">
        <div class="mb-4 p-3 bg-kraftdo-lime/10 border border-kraftdo-green/30 rounded-lg">
            <p class="text-kraftdo-navy text-sm">
                <i class="fas fa-info-circle text-kraftdo-blue mr-2"></i>
                <strong>Información requerida:</strong> Profesión, empresa y biografía son obligatorias para crear un perfil profesional completo.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    Profesión/Cargo <span class="text-red-500">*</span>:
                </label>
                <input type="text" 
                       name="data[personal_info][profession]" 
                       value="{{ old('data.personal_info.profession', $content->data['personal_info']['profession'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="Desarrollador Web"
                       data-required="true">
                @error('data.personal_info.profession')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    Empresa/Organización <span class="text-red-500">*</span>:
                </label>
                <input type="text" 
                       name="data[personal_info][company]" 
                       value="{{ old('data.personal_info.company', $content->data['personal_info']['company'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="Mi Empresa S.A."
                       data-required="true">
                @error('data.personal_info.company')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-kraftdo-navy mb-2">Ubicación:</label>
            <input type="text" 
                   name="data[personal_info][location]" 
                   value="{{ old('data.personal_info.location', $content->data['personal_info']['location'] ?? '') }}"
                   class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                   placeholder="Santiago, Chile">
            @error('data.personal_info.location')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                Acerca de mí <span class="text-red-500">*</span>:
            </label>
            <textarea name="data[personal_info][bio]" 
                      rows="4"
                      class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm resize-none"
                      placeholder="Cuéntanos sobre ti, tu experiencia y lo que haces..."
                      data-required="true"
                      data-minlength="20">{{ old('data.personal_info.bio', $content->data['personal_info']['bio'] ?? '') }}</textarea>
            @error('data.personal_info.bio')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            <p class="text-kraftdo-navy/70 text-xs mt-1">Mínimo 20 caracteres para una biografía completa</p>
        </div>
    </div>
    
    <div class="mt-4 p-3 bg-purple-100 border border-purple-300 rounded-lg">
        <p class="text-purple-800 text-sm">
            <i class="fas fa-info-circle mr-1"></i>
            Esta información ayudará a las personas a conocerte mejor profesional y personalmente.
        </p>
    </div>
</div>