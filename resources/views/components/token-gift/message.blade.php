{{-- Token Gift Message Component --}}
@props([
    'contentGift' => null
])

@if($contentGift && $contentGift->message)
    <div class="bg-gradient-to-r from-pink-50 to-purple-50 rounded-xl p-6 mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-3">💌 Mensaje Personal</h3>
        <p class="text-gray-700 leading-relaxed text-lg">{{ $contentGift->message }}</p>
    </div>
@endif