{{-- Profile Biography Component --}}
@props([
    'content',
    'isDarkTheme' => false,
    'primaryColor' => '#1e40af',
    'primaryGradient' => '',
    'cardStyle' => ''
])

@if(isset($content->data['personal_info']['bio']) && $content->data['personal_info']['bio'])
    <!-- Biografía -->
    <div class="mb-10">
        <div class="{{ $cardStyle }} rounded-3xl shadow-xl p-8 sm:p-10 border relative overflow-hidden" style="border-color: {{ $primaryColor }};">
            
            <!-- Decoraciones de citas -->
            <div class="absolute top-4 left-4 text-4xl sm:text-6xl opacity-10 {{ $isDarkTheme ? 'text-gray-400' : '' }}" style="{{ $isDarkTheme ? '' : 'color: ' . $primaryColor . ';' }}">
                <i class="fas fa-quote-left"></i>
            </div>
            <div class="absolute bottom-4 right-4 text-3xl sm:text-4xl opacity-10 {{ $isDarkTheme ? 'text-gray-400' : '' }}" style="{{ $isDarkTheme ? '' : 'color: ' . $primaryColor . ';' }}">
                <i class="fas fa-quote-right"></i>
            </div>
            
            <div class="relative z-10 text-center">
                <h2 class="text-2xl sm:text-3xl font-bold mb-6 {{ $isDarkTheme ? 'text-white' : 'text-transparent' }}" style="{{ $isDarkTheme ? '' : 'background: ' . $primaryGradient . '; background-clip: text; -webkit-background-clip: text;' }}">
                    💼 {{ __('profile.about_me') }}
                </h2>
                
                <div class="max-w-3xl mx-auto">
                    <blockquote class="{{ $isDarkTheme ? 'text-gray-300' : 'text-gray-600' }} text-lg sm:text-xl leading-relaxed font-medium italic relative z-10 px-4 sm:px-6">
                        {{ $content->data['personal_info']['bio'] }}
                    </blockquote>
                </div>
            </div>
        </div>
    </div>
@endif