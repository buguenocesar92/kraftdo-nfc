{{-- Profile Info Component --}}
@props([
    'contentProfile' => null,
    'token' => null
])

{{-- Name --}}
<div class="text-center mb-4">
    <h1 class="text-2xl font-bold text-gray-900 mb-1">
        {{ $contentProfile->name ?? $token->name ?? 'Mi Perfil' }}
    </h1>
</div>

{{-- Bio --}}
@if($contentProfile && $contentProfile->bio)
    <div class="text-center mb-6">
        <p class="text-gray-600 leading-relaxed">
            {{ $contentProfile->bio }}
        </p>
    </div>
@endif