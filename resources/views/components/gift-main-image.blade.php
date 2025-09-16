{{-- Gift Main Image Component --}}
@props([
    'imageUrl',
    'title'
])

@if($imageUrl)
    <div class="gift-section">
        <div class="max-w-2xl mx-auto text-center">
            <img src="{{ $imageUrl }}" 
                 alt="{{ $title }}" 
                 class="gift-main-image">
        </div>
    </div>
@endif