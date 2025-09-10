{{-- Enhanced Profile Header Component --}}
@props([
    'contentMultimedia' => null,
    'contentProfile' => null,
    'token' => null
])

<div class="relative h-24 sm:h-32 md:h-40 bg-gradient-to-r from-blue-500 via-purple-600 to-pink-500 overflow-hidden">
    {{-- Animated background pattern --}}
    <div class="absolute inset-0 opacity-20">
        <div class="absolute -top-4 -left-4 w-8 h-8 bg-white rounded-full animate-float"></div>
        <div class="absolute top-8 right-8 w-4 h-4 bg-white rounded-full animate-float-delayed"></div>
        <div class="absolute bottom-4 left-8 w-6 h-6 bg-white rounded-full animate-float-slow"></div>
    </div>
    
    {{-- Profile Image --}}
    <div class="absolute -bottom-8 sm:-bottom-12 left-1/2 transform -translate-x-1/2 transition-all duration-500 hover:scale-105">
        @if($contentMultimedia && isset($contentMultimedia->settings['profile_image']))
            <div class="relative group">
                <img src="{{ Storage::url($contentMultimedia->settings['profile_image']) }}" 
                     alt="Foto de perfil de {{ $contentProfile?->name ?? $token->name ?? 'usuario' }}" 
                     class="w-16 h-16 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded-full border-3 sm:border-4 border-white shadow-lg object-cover transition-all duration-300 group-hover:shadow-xl"
                     loading="eager"
                     decoding="async">
                <div class="absolute inset-0 rounded-full bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>
        @else
            <div class="w-16 h-16 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded-full border-3 sm:border-4 border-white shadow-lg bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center transition-all duration-300 hover:shadow-xl">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 text-gray-400" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
        @endif
    </div>
</div>