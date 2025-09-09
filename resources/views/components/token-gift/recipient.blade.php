{{-- Token Gift Recipient Component --}}
@props([
    'contentGift' => null
])

@if($contentGift && $contentGift->recipient_name)
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">
            Para: {{ $contentGift->recipient_name }}
        </h2>
        @if($contentGift->sender_name)
            <p class="text-lg text-gray-600">
                De: {{ $contentGift->sender_name }}
            </p>
        @endif
    </div>
@endif