{{-- Token Gift Header Component --}}
@props([
    'title' => '🎁 Regalo Especial',
    'subtitle' => '¡Tienes un regalo personalizado!'
])

<div class="text-center animate-fade-in">
    <h1 class="text-4xl font-bold text-white mb-2">{{ $title }}</h1>
    <p class="text-xl text-indigo-100">{{ $subtitle }}</p>
</div>