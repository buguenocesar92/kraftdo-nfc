@props(['token', 'content'])

<!-- Información del Restaurante para MENU Step 2 -->
<div class="space-y-6">
    <!-- Información del Restaurante -->
    <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-kraftdo-green/20 shadow-lg">
        <h4 class="text-lg font-bold text-kraftdo-navy mb-6 flex items-center">
            <div class="kraftdo-gradient w-10 h-10 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                <i class="fas fa-utensils text-white"></i>
            </div>
            Información del Restaurante
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Dirección -->
            <div class="group">
                <label class="flex items-center text-sm font-semibold text-kraftdo-navy mb-3">
                    <div class="kraftdo-gradient w-6 h-6 rounded-lg flex items-center justify-center mr-2 shadow-sm">
                        <i class="fas fa-map-marker-alt text-white text-xs"></i>
                    </div>
                    Dirección *
                </label>
                <input type="text" 
                       name="data[restaurant_info][address]" 
                       id="restaurant_address"
                       value="{{ old('data.restaurant_info.address', $content->data['restaurant_info']['address'] ?? '') }}"
                       class="w-full border-2 border-kraftdo-navy/30 bg-white/70 text-kraftdo-navy rounded-xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-kraftdo-green/20 focus:border-kraftdo-green transition-all duration-300 hover:border-kraftdo-blue/50 shadow-sm"
                       placeholder="Av. Principal 123, Santiago"
                       maxlength="255"
                       data-required="true">
                @error('data.restaurant_info.address')
                    <p class="text-red-500 text-sm mt-2 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Teléfono -->
            <div class="group">
                <label class="flex items-center text-sm font-semibold text-kraftdo-navy mb-3">
                    <div class="kraftdo-gradient w-6 h-6 rounded-lg flex items-center justify-center mr-2 shadow-sm">
                        <i class="fas fa-phone text-white text-xs"></i>
                    </div>
                    Teléfono *
                </label>
                <input type="text" 
                       name="data[restaurant_info][phone]" 
                       id="restaurant_phone"
                       value="{{ old('data.restaurant_info.phone', $content->data['restaurant_info']['phone'] ?? '') }}"
                       class="w-full border-2 border-kraftdo-navy/30 bg-white/70 text-kraftdo-navy rounded-xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-kraftdo-green/20 focus:border-kraftdo-green transition-all duration-300 hover:border-kraftdo-blue/50 shadow-sm"
                       placeholder="+56 9 1234 5678"
                       maxlength="25"
                       data-required="true">
                @error('data.restaurant_info.phone')
                    <p class="text-red-500 text-sm mt-2 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Horarios -->
            <div class="group">
                <label class="flex items-center text-sm font-semibold text-kraftdo-navy mb-3">
                    <div class="kraftdo-gradient w-6 h-6 rounded-lg flex items-center justify-center mr-2 shadow-sm">
                        <i class="fas fa-clock text-white text-xs"></i>
                    </div>
                    Horarios de Atención *
                </label>
                <input type="text" 
                       name="data[restaurant_info][hours]" 
                       id="restaurant_hours"
                       value="{{ old('data.restaurant_info.hours', $content->data['restaurant_info']['hours'] ?? '') }}"
                       class="w-full border-2 border-kraftdo-navy/30 bg-white/70 text-kraftdo-navy rounded-xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-kraftdo-green/20 focus:border-kraftdo-green transition-all duration-300 hover:border-kraftdo-blue/50 shadow-sm"
                       placeholder="Lun-Dom 12:00-22:00"
                       maxlength="100"
                       data-required="true">
                @error('data.restaurant_info.hours')
                    <p class="text-red-500 text-sm mt-2 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- WhatsApp -->
            <div class="group">
                <label class="flex items-center text-sm font-semibold text-kraftdo-navy mb-3">
                    <div class="kraftdo-gradient w-6 h-6 rounded-lg flex items-center justify-center mr-2 shadow-sm">
                        <i class="fab fa-whatsapp text-white text-xs"></i>
                    </div>
                    WhatsApp
                </label>
                <input type="text" 
                       name="data[restaurant_info][whatsapp]" 
                       id="restaurant_whatsapp"
                       value="{{ old('data.restaurant_info.whatsapp', $content->data['restaurant_info']['whatsapp'] ?? '') }}"
                       class="w-full border-2 border-kraftdo-navy/30 bg-white/70 text-kraftdo-navy rounded-xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-kraftdo-green/20 focus:border-kraftdo-green transition-all duration-300 hover:border-kraftdo-blue/50 shadow-sm"
                       placeholder="+56 9 1234 5678"
                       maxlength="25">
                @error('data.restaurant_info.whatsapp')
                    <p class="text-red-500 text-sm mt-2 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <!-- Información adicional -->
        <div class="mt-8 bg-kraftdo-lime/10 border border-kraftdo-green/30 rounded-xl p-4">
            <div class="flex items-start">
                <div class="kraftdo-gradient rounded-full p-2 mr-3 flex-shrink-0">
                    <i class="fas fa-info-circle text-white text-sm"></i>
                </div>
                <div>
                    <h5 class="font-semibold text-kraftdo-navy mb-2">Información obligatoria del restaurante</h5>
                    <ul class="text-sm text-kraftdo-navy/80 space-y-1">
                        <li class="flex items-start">
                            <i class="fas fa-asterisk text-red-500 mr-2 mt-0.5 text-xs"></i>
                            <span><strong>Dirección:</strong> Obligatoria para que los clientes puedan encontrarte fácilmente</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-asterisk text-red-500 mr-2 mt-0.5 text-xs"></i>
                            <span><strong>Teléfono:</strong> Obligatorio, será un enlace directo para que puedan llamarte</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-asterisk text-red-500 mr-2 mt-0.5 text-xs"></i>
                            <span><strong>Horarios:</strong> Obligatorios para que los clientes sepan cuándo estás abierto</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-kraftdo-green mr-2 mt-0.5 text-xs"></i>
                            <span><strong>WhatsApp:</strong> Opcional, será un botón directo para contacto rápido</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

