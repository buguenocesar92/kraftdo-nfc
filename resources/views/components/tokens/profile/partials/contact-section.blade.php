@props(['content'])

<!-- Información de Contacto -->
<div class="bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200/50">
    <div class="mb-6">
        <p class="text-sm font-semibold text-kraftdo-navy">
            <i class="fas fa-address-card text-kraftdo-blue mr-2"></i>
            Proporciona tu información de contacto para que la gente pueda comunicarse contigo
        </p>
    </div>
    
    <div class="space-y-4">
        <div class="mb-4 p-3 bg-kraftdo-lime/10 border border-kraftdo-green/30 rounded-lg">
            <p class="text-kraftdo-navy text-sm">
                <i class="fas fa-info-circle text-kraftdo-blue mr-2"></i>
                <strong>Información requerida:</strong> Teléfono y WhatsApp son obligatorios. Email es opcional pero recomendado.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    Email <span class="text-gray-500">(recomendado)</span>:
                </label>
                <input type="email" 
                       name="data[contact_info][email]" 
                       value="{{ old('data.contact_info.email', $content->data['contact_info']['email'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="tu@email.com">
                @error('data.contact_info.email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    Teléfono <span class="text-red-500">*</span>:
                </label>
                <input type="text" 
                       name="data[contact_info][phone]" 
                       value="{{ old('data.contact_info.phone', $content->data['contact_info']['phone'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="+56 9 1234 5678"
                       data-required="true">
                @error('data.contact_info.phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    <i class="fab fa-whatsapp text-kraftdo-green mr-1"></i> WhatsApp <span class="text-red-500">*</span>:
                </label>
                <input type="text" 
                       name="data[contact_info][whatsapp]" 
                       value="{{ old('data.contact_info.whatsapp', $content->data['contact_info']['whatsapp'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="+56 9 1234 5678"
                       data-required="true">
                @error('data.contact_info.whatsapp')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    <i class="fas fa-globe text-kraftdo-blue mr-1"></i> Sitio Web:
                </label>
                <input type="url" 
                       name="data[contact_info][website]" 
                       value="{{ old('data.contact_info.website', $content->data['contact_info']['website'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="https://tusitio.com">
                @error('data.contact_info.website')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
    
    <div class="mt-4 p-3 bg-kraftdo-lime/10 border border-kraftdo-green/30 rounded-lg">
        <p class="text-kraftdo-navy text-sm">
            <i class="fas fa-info-circle text-kraftdo-blue mr-2"></i>
            Esta información aparecerá en tu perfil NFC. Usa formato internacional para teléfonos y WhatsApp (+56912345678).
        </p>
    </div>
</div>