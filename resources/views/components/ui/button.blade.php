@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, success, danger
    'icon' => null,
    'iconRight' => null,
])

@php
    $base = 'inline-flex items-center gap-2 px-4 sm:px-8 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-lg text-center text-sm sm:text-base';
    $variants = [
        'primary' => 'kraftdo-gradient text-white focus:ring-kraftdo-green',
        'secondary' => 'bg-gray-200 text-kraftdo-navy hover:bg-gray-300 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 focus:ring-gray-400',
        'success' => 'kraftdo-gradient text-white focus:ring-kraftdo-green',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    ];
    $classes = $base.' '.($variants[$variant] ?? $variants['primary']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <i class="fa {{ $icon }}"></i>
    @endif
    {{ $slot }}
    @if($iconRight)
        <i class="fa {{ $iconRight }}"></i>
    @endif
</button> 