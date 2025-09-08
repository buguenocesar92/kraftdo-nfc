@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-kraftdo-green text-sm font-medium leading-5 text-kraftdo-green focus:outline-none focus:border-kraftdo-lime transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-kraftdo-green hover:border-kraftdo-green focus:outline-none focus:text-kraftdo-green focus:border-kraftdo-green transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
