{{-- Enhanced Profile Info Component --}}
@props([
    'contentProfile' => null,
    'token' => null
])

{{-- Name --}}
<div class="text-center mb-4 sm:mb-6 animate-fade-in-up" style="animation-delay: 0.2s">
    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 mb-1 leading-tight">
        {{ $contentProfile?->name ?? $token->name ?? 'Mi Perfil' }}
    </h1>
    @if($contentProfile?->job_title)
        <p class="text-sm sm:text-base text-gray-600 font-medium">
            {{ $contentProfile->job_title }}
        </p>
    @endif
</div>

{{-- Bio --}}
@if($contentProfile && $contentProfile->bio)
    <div class="text-center mb-6 sm:mb-8 animate-fade-in-up" style="animation-delay: 0.3s">
        <p class="text-sm sm:text-base text-gray-600 leading-relaxed max-w-md mx-auto">
            {{ $contentProfile->bio }}
        </p>
    </div>
@endif