{{-- Enhanced Profile Header Component --}}
@props([
    'contentMultimedia' => null,
    'contentProfile' => null,
    'token' => null,
    'colors' => ['primary' => '#3B82F6', 'secondary' => '#8B5CF6', 'accent' => '#EC4899']
])

<div class="relative h-24 sm:h-32 md:h-40 overflow-hidden rounded-t-3xl" 
     style="background: linear-gradient(135deg, {{ $colors['primary'] }}, {{ $colors['secondary'] }}, {{ $colors['accent'] }})">
    {{-- Animated background pattern --}}
    <div class="absolute inset-0 opacity-20">
        <div class="absolute -top-4 -left-4 w-8 h-8 bg-white rounded-full animate-float"></div>
        <div class="absolute top-8 right-8 w-4 h-4 bg-white rounded-full animate-float-delayed"></div>
        <div class="absolute bottom-4 left-8 w-6 h-6 bg-white rounded-full animate-float-slow"></div>
    </div>
</div>