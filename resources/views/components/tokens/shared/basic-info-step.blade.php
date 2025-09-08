@props(['token', 'content'])

<!-- Step 1: Información Básica -->
<div class="wizard-step-content" data-step="1">
    <h3 class="text-xl font-bold bg-gradient-to-r from-kraftdo-blue to-kraftdo-green bg-clip-text text-transparent mb-6">📝 Información Básica</h3>
    <div class="space-y-6">
        @if($token->content_type === 'PROFILE')
            <!-- Nombre para PROFILE -->
            <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-kraftdo-green/20">
                <label class="block text-sm font-semibold text-kraftdo-navy mb-3">Tu Nombre *</label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title', $content->title ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="Ej: Juan Pérez"
                       data-required="true">
                @error('title')
                    <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Breve Descripción para PROFILE -->
            <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-kraftdo-green/20">
                <label class="block text-sm font-semibold text-kraftdo-navy mb-3">Descripción Personal *</label>
                <textarea name="description" 
                          rows="3"
                          class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm resize-none"
                          placeholder="Una breve descripción sobre ti..."
                          data-required="true">{{ old('description', $content->description ?? '') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                @enderror
            </div>
            
        @elseif($token->content_type === 'GIFT')
            <!-- Título del Regalo para GIFT -->
            <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-kraftdo-green/20">
                <label class="block text-sm font-semibold text-kraftdo-navy mb-3">Título del Regalo *</label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title', $content->title ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="Ej: Un regalo especial para ti"
                       data-required="true">
                @error('title')
                    <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Descripción del Regalo para GIFT -->
            <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-kraftdo-green/20">
                <label class="block text-sm font-semibold text-kraftdo-navy mb-3">Descripción del Regalo *</label>
                <textarea name="description" 
                          rows="3"
                          class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm resize-none"
                          placeholder="Describe brevemente este regalo especial..."
                          data-required="true">{{ old('description', $content->description ?? '') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                @enderror
            </div>
            
        @else
            <!-- Título general para otros tipos -->
            <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-kraftdo-green/20">
                <label class="block text-sm font-semibold text-kraftdo-navy mb-3">Título *</label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title', $content->title ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="Ej: Mi {{ $token->content_type }}"
                       data-required="true">
                @error('title')
                    <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Descripción general para otros tipos -->
            <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-kraftdo-green/20">
                <label class="block text-sm font-semibold text-kraftdo-navy mb-3">Descripción *</label>
                <textarea name="description" 
                          rows="3"
                          class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm resize-none"
                          placeholder="Describe brevemente el contenido..."
                          data-required="true">{{ old('description', $content->description ?? '') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <!-- Imagen -->
        <x-tokens.shared.image-upload :content="$content" />

        @if($token->content_type === 'GIFT')
            <!-- Mensaje personalizado para GIFT -->
            <div class="mb-6">
                <div class="bg-kraftdo-lime/10 border border-kraftdo-green/30 rounded-lg p-4">
                    <h5 class="font-medium text-kraftdo-navy mb-3">
                        <i class="fas fa-heart text-kraftdo-green"></i> Mensaje Especial
                    </h5>
                    <p class="text-kraftdo-navy/80 text-sm mb-3">
                        Personaliza tu mensaje para crear un momento único. Este mensaje aparecerá en el contenido del regalo.
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm text-kraftdo-navy mb-1">De (tu nombre) *:</label>
                            <input type="text" 
                                   name="data[from]" 
                                   value="{{ old('data.from', $content->data['from'] ?? auth()->user()->name) }}"
                                   class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green"
                                   data-required="true">
                            @error('data.from')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm text-kraftdo-navy mb-1">Para *:</label>
                            <input type="text" 
                                   name="data[to]" 
                                   value="{{ old('data.to', $content->data['to'] ?? '') }}"
                                   class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green"
                                   data-required="true">
                            @error('data.to')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm text-kraftdo-navy mb-1">
                            Mensaje *
                            <span id="message_context" class="text-kraftdo-blue font-medium">
                                @switch($content->gift_subtype ?? 'LOVE')
                                    @case('LOVE') de amor @break
                                    @case('FRIEND') de amistad @break
                                    @case('PROFESSIONAL') profesional @break
                                    @case('FAMILY') familiar @break
                                    @case('CELEBRATION') de celebración @break
                                    @default de amor @break
                                @endswitch
                            </span>
                        </label>
                        <textarea name="data[love_message]" 
                                  id="love_message"
                                  rows="3"
                                  class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green"
                                  placeholder="Escribe tu mensaje especial..."
                                  data-required="true">{{ old('data.love_message', $content->data['love_message'] ?? '') }}</textarea>
                        @error('data.love_message')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
