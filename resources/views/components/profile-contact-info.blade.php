{{-- Profile Contact Information Component --}}
@props([
    'content',
    'isDarkTheme' => false,
    'accentColor' => '#0ea5e9',
    'primaryGradient' => '',
    'cardStyle' => ''
])

@if(isset($content->data['contact']) || isset($content->data['contact_info']))
    <!-- Información de Contacto Profesional -->
    <div class="mb-10">
        <div class="{{ $cardStyle }} rounded-3xl shadow-xl p-8 sm:p-10 border relative overflow-hidden" style="border-color: {{ $accentColor }};">
            
            <!-- Decoración -->
            <div class="absolute top-4 right-4 text-4xl opacity-10 {{ $isDarkTheme ? 'text-gray-400' : '' }}" style="{{ $isDarkTheme ? '' : 'color: ' . $accentColor . ';' }}">
                <i class="fas fa-address-card"></i>
            </div>
            
            <div class="relative z-10">
                <h2 class="text-2xl sm:text-3xl font-bold mb-8 text-center {{ $isDarkTheme ? 'text-white' : 'text-transparent' }}" style="{{ $isDarkTheme ? '' : 'background: ' . $primaryGradient . '; background-clip: text; -webkit-background-clip: text;' }}">
                    📞 {{ __('profile.contact_info') }}
                </h2>
                
                @php
                    // Obtener número de teléfono
                    $phone = $content->data['contact']['phone'] ?? $content->data['contact_info']['phone'] ?? '';
                    
                    // Buscar número de WhatsApp en diferentes ubicaciones, o usar el teléfono principal
                    $whatsapp = $content->data['contact']['whatsapp'] ?? 
                               $content->data['contact_info']['whatsapp'] ?? 
                               $content->data['social_networks']['whatsapp'] ?? 
                               $content->data['social_links']['whatsapp'] ?? 
                               $phone; // Usar teléfono principal como fallback
                    
                    // Limpiar el número de WhatsApp: remover espacios, guiones, paréntesis y el signo +
                    $cleanWhatsapp = '';
                    if ($whatsapp) {
                        $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsapp);
                    }
                @endphp
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($phone)
                        <div class="space-y-3">
                            <!-- Botón para llamar -->
                            <a href="tel:{{ $phone }}" 
                               class="group bg-gradient-to-r from-blue-500 to-blue-700 text-white py-4 px-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center text-center">
                                <div class="text-center w-full">
                                    <i class="fas fa-phone text-2xl mb-2 block group-hover:animate-bounce mx-auto"></i>
                                    <div class="font-semibold">Llamar</div>
                                    <div class="text-xs opacity-90">{{ $phone }}</div>
                                </div>
                            </a>
                            
                            @if($cleanWhatsapp)
                                <!-- Botón para WhatsApp usando el mismo número -->
                                <a href="https://wa.me/{{ $cleanWhatsapp }}" 
                                   target="_blank"
                                   class="group bg-gradient-to-r from-green-500 to-green-700 text-white py-4 px-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center text-center">
                                    <div class="text-center w-full">
                                        <i class="fab fa-whatsapp text-2xl mb-2 block group-hover:animate-bounce mx-auto"></i>
                                        <div class="font-semibold">WhatsApp</div>
                                        <div class="text-xs opacity-90">Chatear</div>
                                    </div>
                                </a>
                            @endif
                        </div>
                    @endif
                    
                    @if(isset($content->data['contact']['email']) || isset($content->data['contact_info']['email']))
                        @php
                            $email = $content->data['contact']['email'] ?? $content->data['contact_info']['email'] ?? '';
                        @endphp
                        <a href="mailto:{{ $email }}" 
                           class="group bg-gradient-to-r from-purple-500 to-purple-700 text-white py-6 px-8 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center text-center">
                            <div class="text-center w-full">
                                <i class="fas fa-envelope text-3xl mb-3 block group-hover:animate-bounce mx-auto"></i>
                                <div class="font-semibold text-lg">Email</div>
                                <div class="text-sm opacity-90">{{ $email }}</div>
                            </div>
                        </a>
                    @endif
                    
                    @if(isset($content->data['contact']['website']) || isset($content->data['contact_info']['website']))
                        @php
                            $website = $content->data['contact']['website'] ?? $content->data['contact_info']['website'] ?? '';
                        @endphp
                        <a href="{{ $website }}" 
                           target="_blank"
                           class="group text-white py-6 px-8 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center text-center" style="background: {{ $primaryGradient }};">
                            <div class="text-center w-full">
                                <i class="fas fa-globe text-3xl mb-3 block group-hover:animate-bounce mx-auto"></i>
                                <div class="font-semibold text-lg">Sitio Web</div>
                                <div class="text-sm opacity-90">Visitar</div>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif