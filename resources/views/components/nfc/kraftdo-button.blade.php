@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, outline, danger
    'size' => 'md', // sm, md, lg, xl
    'loading' => false,
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left', // left, right
    'href' => null,
    'target' => null
])

@php
$baseClasses = 'font-semibold transition-all duration-300 transform focus:outline-none focus:ring-4 focus:ring-white/20 disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center';

$variants = [
    'primary' => 'bg-gradient-to-r from-blue-500 to-green-500 hover:from-blue-600 hover:to-green-600 text-white kraftdo-brand-shadow hover:shadow-2xl hover:scale-105 font-bold tracking-wide',
    'secondary' => 'bg-gradient-to-r from-navy-500 to-blue-500 hover:from-navy-600 hover:to-blue-600 text-white kraftdo-shadow hover:shadow-xl hover:scale-105',
    'outline' => 'bg-white/20 hover:bg-blue-500/20 border-2 border-blue-400/50 hover:border-blue-400/80 text-white backdrop-blur-sm hover:text-blue-100',
    'danger' => 'bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white kraftdo-shadow hover:shadow-xl hover:scale-105',
    'kraftdo' => 'bg-gradient-to-r from-blue-500 via-green-500 to-lime-500 hover:from-blue-600 hover:via-green-600 hover:to-lime-600 text-white kraftdo-brand-shadow hover:shadow-2xl hover:scale-110 font-black tracking-wider transform-gpu'
];

$sizes = [
    'sm' => 'px-4 py-2 text-sm rounded-lg',
    'md' => 'px-6 py-3 text-base rounded-xl',
    'lg' => 'px-8 py-4 text-lg rounded-xl',
    'xl' => 'px-10 py-5 text-xl rounded-2xl'
];

$classes = collect([$baseClasses, $variants[$variant], $sizes[$size]])->join(' ');
@endphp

@if($href)
    <a href="{{ $href }}" 
       @if($target) target="{{ $target }}" @endif
       class="{{ $classes }}"
       {{ $attributes }}>
        @if($icon && $iconPosition === 'left')
            <i class="{{ $icon }} mr-3"></i>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <i class="{{ $icon }} ml-3"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" 
            class="{{ $classes }}"
            @if($loading) disabled @endif
            @if($disabled) disabled @endif
            x-data="{ loading: {{ $loading ? 'true' : 'false' }} }"
            :disabled="loading"
            {{ $attributes }}>
        
        <!-- Loading State -->
        <template x-if="loading">
            <div class="flex items-center">
                <div class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin mr-3"></div>
                <span>Procesando...</span>
            </div>
        </template>
        
        <!-- Normal State -->
        <template x-if="!loading">
            <div class="flex items-center">
                @if($icon && $iconPosition === 'left')
                    <i class="{{ $icon }} mr-3 group-hover:animate-pulse"></i>
                @endif
                {{ $slot }}
                @if($icon && $iconPosition === 'right')
                    <i class="{{ $icon }} ml-3 group-hover:animate-pulse"></i>
                @endif
            </div>
        </template>
    </button>
@endif