@props([
    'padding' => 'p-8 sm:p-10 lg:p-12',
    'rounded' => 'rounded-3xl',
    'shadow' => true,
    'animated' => true,
    'delay' => '0.3s'
])

<div class="kraftdo-glass backdrop-blur-md {{ $rounded }} border border-white/20 {{ $padding }} relative overflow-hidden {{ $shadow ? 'kraftdo-shadow' : '' }}"
     @if($animated) 
     style="animation-delay: {{ $delay }};" 
     x-intersect="$el.classList.add('kraftdo-animate-fade-in')"
     @endif>
    
    <!-- Gradient overlay -->
    <div class="absolute inset-0 bg-gradient-to-r from-white/5 to-transparent pointer-events-none"></div>
    
    <!-- Top accent line -->
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-cyan-500"></div>
    
    <!-- Content -->
    <div class="relative z-10">
        {{ $slot }}
    </div>
</div>