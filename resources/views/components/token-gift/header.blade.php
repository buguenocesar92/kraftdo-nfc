{{-- Token Gift Header Component - Enhanced Mobile-First --}}
@props([
    'title' => '🎁 Regalo Especial',
    'subtitle' => '¡Tienes un regalo personalizado!',
    'theme' => []
])

<div class="text-center relative overflow-hidden">
    <!-- Background decoration -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-1/4 w-32 h-32 bg-white rounded-full blur-2xl animate-pulse"></div>
        <div class="absolute bottom-0 right-1/4 w-24 h-24 bg-purple-300 rounded-full blur-xl animate-bounce" style="animation-delay: 0.5s;"></div>
    </div>
    
    <!-- Main content -->
    <div class="relative z-10 py-6 sm:py-8">
        <!-- Spacer for better visual balance -->
        <div class="mb-4">
        </div>
        
        <!-- Title with staggered animation -->
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-3 leading-tight animate-fade-in flex items-center justify-center gap-3 flex-wrap">
            <!-- Gift icon with dynamic theme emoji -->
            <div class="text-4xl sm:text-5xl lg:text-6xl animate-bounce inline-block relative" style="animation-duration: 3s;">
                <span class="gift-emoji absolute inset-0 flex items-center justify-center" style="filter: brightness(1.5) contrast(1.3) saturate(1.4);">{{ $theme['header_icon'] ?? '🎁' }}</span>
            </div>
            <span class="bg-gradient-to-r from-white via-purple-100 to-pink-100 bg-clip-text text-transparent drop-shadow-lg">
                {{ $theme['name'] ?? 'Regalo' }} Especial
            </span>
        </h1>
        
        <!-- Subtitle with delay -->
        <p class="text-lg sm:text-xl text-purple-100 max-w-md mx-auto animate-fade-in px-4" style="animation-delay: 0.3s;">
            {{ $subtitle }}
        </p>
        
        <!-- Decorative element -->
        <div class="mt-4 flex justify-center space-x-2 animate-fade-in" style="animation-delay: 0.6s;">
            <div class="w-2 h-2 bg-white rounded-full opacity-60 animate-pulse"></div>
            <div class="w-2 h-2 bg-purple-200 rounded-full opacity-60 animate-pulse" style="animation-delay: 0.2s;"></div>
            <div class="w-2 h-2 bg-pink-200 rounded-full opacity-60 animate-pulse" style="animation-delay: 0.4s;"></div>
        </div>
    </div>
</div>