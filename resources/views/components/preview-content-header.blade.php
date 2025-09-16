{{-- Preview Content Header Component --}}
@props([
    'title',
    'description' => null
])

<div class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 p-4 border-b">
    <h2 class="text-xl font-semibold mb-1">{{ $title }}</h2>
    @if($description)
        <p class="text-gray-600">{{ $description }}</p>
    @endif
</div>