{{-- Enhanced Token Gift Recipient Component --}}
@props([
    'contentGift' => null,
    'theme' => []
])

@if($contentGift && $contentGift->recipient_name)
    <div class="text-center mb-8 animate-slide-up">
        <!-- Decorative hearts with theme emoji -->
        <div class="flex justify-center gap-2 mb-4 animate-fade-in animate-delay-200">
            <span class="text-2xl animate-pulse gift-emoji">{{ $theme['header_icon'] ?? '💝' }}</span>
            <span class="text-xl animate-pulse gift-emoji" style="animation-delay: 0.3s;">{{ $theme['emoji'] ?? '❤️' }}</span>
            <span class="text-2xl animate-pulse gift-emoji" style="animation-delay: 0.6s;">{{ $theme['header_icon'] ?? '💝' }}</span>
        </div>
        
        <!-- Recipient name with enhanced styling -->
        <div class="relative inline-block mb-4">
            <h2 class="text-3xl sm:text-4xl font-bold text-transparent bg-gradient-to-r {{ $theme['colors']['section_text'] ?? 'from-purple-600 via-pink-600 to-indigo-600' }} bg-clip-text mb-2 animate-scale-in animate-delay-300">
                Para: {{ $contentGift->recipient_name }}
            </h2>
            <!-- Underline decoration -->
            <div class="absolute -bottom-1 left-0 right-0 h-1 bg-gradient-to-r {{ $theme['colors']['secondary'] ?? 'from-purple-300 via-pink-300 to-indigo-300' }} rounded-full animate-fade-in animate-delay-600"></div>
        </div>
        
        @if($contentGift->sender_name)
            <div class="relative animate-fade-in animate-delay-800">
                <p class="text-lg text-gray-600 font-medium">
                    <span class="text-gray-500">De:</span> 
                    <span class="{{ $theme['colors']['accent'] ?? 'text-indigo-600' }} font-semibold">{{ $contentGift->sender_name }}</span>
                </p>
                <!-- Small decoration -->
                <span class="absolute -top-2 -right-8 text-sm gift-emoji">{{ $theme['emoji'] ?? '💕' }}</span>
            </div>
        @endif
    </div>
@endif