@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-kraftdo-green text-start text-base font-medium text-kraftdo-green bg-kraftdo-navy/20 focus:outline-none focus:text-kraftdo-lime focus:bg-kraftdo-navy/30 focus:border-kraftdo-lime transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-white hover:text-kraftdo-green hover:bg-kraftdo-navy/20 hover:border-kraftdo-green focus:outline-none focus:text-kraftdo-green focus:bg-kraftdo-navy/20 focus:border-kraftdo-green transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
