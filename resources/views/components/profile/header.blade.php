{{-- Profile Header Component --}}
@props([
    'contentMultimedia' => null,
    'contentProfile' => null,
    'token' => null
])

<div class="relative h-32 bg-gradient-to-r from-blue-500 to-purple-600">
    {{-- Profile Image --}}
    <div class="absolute -bottom-12 left-1/2 transform -translate-x-1/2">
        @if($contentMultimedia && isset($contentMultimedia->settings['profile_image']))
            <img src="{{ Storage::url($contentMultimedia->settings['profile_image']) }}" 
                 alt="Foto de perfil" 
                 class="w-24 h-24 rounded-full border-4 border-white shadow-lg object-cover">
        @else
            <div class="w-24 h-24 rounded-full border-4 border-white shadow-lg bg-gray-200 flex items-center justify-center">
                <span class="text-3xl text-gray-400">👤</span>
            </div>
        @endif
    </div>
</div>