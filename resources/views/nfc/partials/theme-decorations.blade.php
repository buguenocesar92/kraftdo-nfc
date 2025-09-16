{{-- Theme Decorations Component --}}
@php
$theme = $theme ?? [];
@endphp

<div class="fixed inset-0 overflow-hidden pointer-events-none">
    @if(isset($theme['decorative_elements']) && !empty($theme['decorative_elements']))
        {{-- Custom theme decorations --}}
        @foreach($theme['decorative_elements'] as $index => $element)
            <div class="absolute {{ $element['position'] ?? 'top-10 left-10' }} {{ $element['size'] ?? 'w-20 h-20' }} opacity-30 {{ $element['animation'] ?? 'animate-float' }}" 
                 style="animation-delay: {{ $index * 0.5 }}s;">
                {{ $element['emoji'] ?? '💝' }}
            </div>
        @endforeach
    @else
        {{-- Default decorations --}}
        <div class="absolute top-10 left-10 w-20 h-20 bg-pink-200 rounded-full opacity-20 animate-float"></div>
        <div class="absolute top-32 right-16 w-16 h-16 bg-purple-200 rounded-full opacity-20 animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-20 left-20 w-24 h-24 bg-indigo-200 rounded-full opacity-20 animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-32 right-10 w-12 h-12 bg-pink-300 rounded-full opacity-20 animate-float" style="animation-delay: 0.5s;"></div>
    @endif
</div>