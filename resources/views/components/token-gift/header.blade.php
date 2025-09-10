{{-- Token Gift Header Component - Enhanced Mobile-First --}}
@props([
    'title' => '🎁 Regalo Especial',
    'subtitle' => '¡Tienes un regalo personalizado!'
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
            <!-- Gift icon with emoji and SVG fallback -->
            <div class="text-4xl sm:text-5xl lg:text-6xl animate-bounce inline-block relative" style="animation-duration: 3s;">
                <span class="gift-emoji absolute inset-0 flex items-center justify-center" style="filter: brightness(1.5) contrast(1.3) saturate(1.4);">🎁</span>
                <!-- SVG fallback if emoji doesn't render properly -->
                <svg class="w-12 h-12 sm:w-16 sm:h-16 lg:w-20 lg:h-20 text-pink-400 opacity-0 hover:opacity-100 transition-opacity duration-300" fill="currentColor" viewBox="0 0 24 24" style="filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3));">
                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 6V20C21 21.1 20.1 22 19 22H5C3.9 22 3 21.1 3 20V6C3 4.9 3.9 4 5 4H8.5C8.8 4 9 4.2 9 4.5S8.8 5 8.5 5H5V20H19V5H15.5C15.2 5 15 4.8 15 4.5S15.2 4 15.5 4H19C20.1 4 21 4.9 21 6ZM12 8L10 10H14L12 8ZM8 12H16V14H8V12ZM8 16H16V18H8V16Z"/>
                </svg>
            </div>
            <span class="bg-gradient-to-r from-white via-purple-100 to-pink-100 bg-clip-text text-transparent drop-shadow-lg">
                Regalo Especial
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