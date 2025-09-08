{{-- Gift Sender Info Component --}}
@props([
    'content',
    'theme' => []
])

@if((isset($content->data['from']) && $content->data['from']) || (isset($content->data['to']) && $content->data['to']))
    <div class="mb-8">
        <div class="max-w-2xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if(isset($content->data['from']) && $content->data['from'])
                    <div class="gift-sender-card {{ $theme['card_style'] ?? 'bg-white/80 backdrop-blur-sm' }} {{ str_replace(['gradient-to-r', 'from-', 'to-'], ['', 'border-', ''], $theme['secondary_gradient'] ?? 'border-pink-300') }}">
                        <p class="gift-sender-label {{ $theme['accent_color'] ?? 'text-pink-600' }}">💝 {{ __('gift.from') }}</p>
                        <p class="gift-sender-name {{ $theme['accent_color'] ?? 'text-pink-800' }}">{{ $content->data['from'] }}</p>
                    </div>
                @endif
                
                @if(isset($content->data['to']) && $content->data['to'])
                    <div class="gift-sender-card {{ $theme['card_style'] ?? 'bg-white/80 backdrop-blur-sm' }} {{ str_replace(['gradient-to-r', 'from-', 'to-'], ['', 'border-', ''], $theme['secondary_gradient'] ?? 'border-pink-300') }}">
                        <p class="gift-sender-label {{ $theme['accent_color'] ?? 'text-pink-600' }}">🎁 {{ __('gift.to') }}</p>
                        <p class="gift-sender-name {{ $theme['accent_color'] ?? 'text-pink-800' }}">{{ $content->data['to'] }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif