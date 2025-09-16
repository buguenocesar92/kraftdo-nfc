{{-- Preview Container Component --}}
@props([
    'class' => 'pb-6'
])

<div class="max-w-4xl mx-auto px-4 {{ $class }}">
    {{ $slot }}
</div>