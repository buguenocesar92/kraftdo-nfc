{{-- Message Card Component --}}
@php
$content = $content ?? null;
$theme = $theme ?? [];
$fontFamily = $fontFamily ?? 'font-sans';
@endphp

<div class="{{ $theme['card_style'] ?? 'bg-white/90 backdrop-blur-md' }} rounded-3xl shadow-2xl p-6 sm:p-8 lg:p-10 border-2 {{ str_replace(['gradient-to-r', 'from-', 'to-'], ['', 'border-', ''], $theme['primary_gradient'] ?? 'border-pink-400') }} relative overflow-hidden">
    
    <!-- Elementos decorativos del mensaje -->
    <div class="absolute top-4 left-4 text-4xl sm:text-6xl opacity-10 {{ $theme['accent_color'] ?? 'text-pink-600' }}">💌</div>
    <div class="absolute bottom-4 right-4 text-3xl sm:text-4xl opacity-10">✨</div>
    
    <div class="relative z-10 text-center">
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold bg-gradient-to-r {{ $theme['primary_gradient'] ?? 'from-pink-600 to-purple-600' }} bg-clip-text text-transparent mb-4 sm:mb-6">
            💌 Mensaje Especial
        </h2>
        
        @if($content && isset($content->data['love_message']) && !empty($content->data['love_message']))
            <div class="relative max-w-3xl mx-auto">
                <div class="absolute -top-2 -left-2 sm:-top-4 sm:-left-4 text-2xl sm:text-4xl opacity-20 {{ $theme['accent_color'] ?? 'text-pink-600' }} font-serif">"</div>
                <blockquote class="{{ $theme['message_style'] ?? 'italic text-pink-800' }} text-base sm:text-lg lg:text-xl leading-relaxed {{ $fontFamily }} relative z-10 px-4 sm:px-6">
                    {{ $content->data['love_message'] }}
                </blockquote>
                <div class="absolute -bottom-2 -right-2 sm:-bottom-4 sm:-right-4 text-2xl sm:text-4xl opacity-20 {{ $theme['accent_color'] ?? 'text-pink-600' }} font-serif">"</div>
            </div>
        @else
            <div class="relative max-w-2xl mx-auto">
                <div class="absolute -top-2 -left-2 sm:-top-4 sm:-left-4 text-2xl sm:text-4xl opacity-20 {{ $theme['accent_color'] ?? 'text-pink-600' }} font-serif">"</div>
                <blockquote class="{{ $theme['message_style'] ?? 'italic text-pink-800' }} text-base sm:text-lg lg:text-xl leading-relaxed {{ $fontFamily }} relative z-10 px-4 sm:px-6">
                    Este regalo fue pensado especialmente para ti, con todo mi cariño y los mejores deseos. Espero que disfrutes este momento único y que te haga sonreír tanto como tú me haces sonreír a mí.
                </blockquote>
                <div class="absolute -bottom-2 -right-2 sm:-bottom-4 sm:-right-4 text-2xl sm:text-4xl opacity-20 {{ $theme['accent_color'] ?? 'text-pink-600' }} font-serif">"</div>
            </div>
        @endif
    </div>
</div>