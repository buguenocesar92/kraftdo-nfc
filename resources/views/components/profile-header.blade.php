{{-- Profile Header Component --}}
@props([
    'content',
    'isDarkTheme' => false,
    'primaryColor' => '#1e40af',
    'secondaryColor' => '#64748b', 
    'primaryGradient' => '',
    'secondaryGradient' => '',
    'cardStyle' => ''
])

<!-- Header del Perfil Profesional -->
<div class="text-center mb-10">
    <div class="{{ $cardStyle }} rounded-3xl shadow-2xl p-8 sm:p-12 border-2 relative overflow-hidden" style="border-color: {{ $secondaryColor }};">
        
        <!-- Efectos de fondo en la tarjeta -->
        <div class="absolute top-0 left-0 w-full h-2" style="background: {{ $primaryGradient }};"></div>
        <div class="absolute top-4 right-4 text-6xl opacity-5 {{ $isDarkTheme ? 'text-gray-400' : '' }}" style="{{ $isDarkTheme ? '' : 'color: ' . $primaryColor . ';' }}">
            <i class="fas fa-user-tie"></i>
        </div>
        
        <div class="relative z-10">
            <!-- Avatar/Imagen profesional -->
            <div class="mb-8">
                @if($content->image_url)
                    <div class="relative inline-block">
                        <img src="{{ $content->image_url }}" alt="{{ $content->title }}" 
                            class="w-40 h-40 sm:w-48 sm:h-48 rounded-full mx-auto object-cover shadow-2xl ring-4 ring-white/50 hover:scale-105 transition-transform duration-500">
                        <!-- Indicador de estado online -->
                        <div class="absolute bottom-4 right-4 w-6 h-6 bg-emerald-500 rounded-full border-4 border-white shadow-lg animate-pulse-slow"></div>
                    </div>
                @else
                    <div class="inline-flex items-center justify-center w-40 h-40 sm:w-48 sm:h-48 rounded-full shadow-2xl" style="background: {{ $primaryGradient }};">
                        <i class="fas fa-user text-6xl sm:text-8xl text-white"></i>
                    </div>
                @endif
            </div>
            
            <!-- Información principal -->
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-4 font-serif {{ $isDarkTheme ? 'text-white' : 'text-transparent' }}" style="{{ $isDarkTheme ? '' : 'background: ' . $primaryGradient . '; background-clip: text; -webkit-background-clip: text;' }}">
                {{ $content->title }}
            </h1>
            
            @if($content->description)
                <p class="{{ $isDarkTheme ? 'text-gray-300' : 'text-gray-600' }} text-lg sm:text-xl leading-relaxed max-w-3xl mx-auto mb-6">
                    {{ $content->description }}
                </p>
            @endif
            
            <!-- Badges profesionales -->
            <div class="flex flex-wrap justify-center gap-3 mt-6">
                @if(isset($content->data['personal_info']['profession']) && $content->data['personal_info']['profession'])
                    <span class="inline-flex items-center px-4 py-2 text-white rounded-full text-sm font-semibold shadow-lg" style="background: {{ $secondaryGradient }};">
                        <i class="fas fa-briefcase mr-2"></i>
                        {{ $content->data['personal_info']['profession'] }}
                    </span>
                @endif
                
                @if(isset($content->data['personal_info']['company']) && $content->data['personal_info']['company'])
                    <span class="inline-flex items-center px-4 py-2 text-white rounded-full text-sm font-semibold shadow-lg" style="background: {{ $primaryGradient }};">
                        <i class="fas fa-building mr-2"></i>
                        {{ $content->data['personal_info']['company'] }}
                    </span>
                @endif
                
                @if(isset($content->data['personal_info']['location']) && $content->data['personal_info']['location'])
                    <span class="inline-flex items-center px-4 py-2 text-white rounded-full text-sm font-semibold shadow-lg" style="background: {{ $secondaryGradient }};">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        {{ $content->data['personal_info']['location'] }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>