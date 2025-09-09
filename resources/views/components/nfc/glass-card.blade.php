@props([
    'padding' => 'p-8 sm:p-10 lg:p-12',
    'rounded' => 'rounded-3xl',
    'shadow' => true,
    'animated' => true,
    'delay' => '0.3s'
])

<div class="kraftdo-glass {{ $rounded }} {{ $padding }} relative overflow-hidden {{ $animated ? 'kraftdo-animate-fade-in' : '' }}"
     @if($animated) 
     style="animation-delay: {{ $delay }};" 
     @endif>
    
    <!-- Gradient overlay -->
    <div class="absolute inset-0 bg-gradient-to-r from-white/5 to-transparent pointer-events-none"></div>
    
    <!-- Top accent line -->
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-green-500 to-lime-500"></div>
    
    <!-- Content -->
    <div class="relative z-10">
        {{ $slot }}
    </div>
</div>