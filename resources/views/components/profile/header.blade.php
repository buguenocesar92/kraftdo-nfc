{{-- Enhanced Profile Header Component --}}
@props([
    'contentMultimedia' => null,
    'contentProfile' => null,
    'token' => null
])

<div class="relative h-24 sm:h-32 md:h-40 bg-gradient-to-r from-blue-500 via-purple-600 to-pink-500 overflow-hidden rounded-t-3xl">
    {{-- Animated background pattern --}}
    <div class="absolute inset-0 opacity-20">
        <div class="absolute -top-4 -left-4 w-8 h-8 bg-white rounded-full animate-float"></div>
        <div class="absolute top-8 right-8 w-4 h-4 bg-white rounded-full animate-float-delayed"></div>
        <div class="absolute bottom-4 left-8 w-6 h-6 bg-white rounded-full animate-float-slow"></div>
    </div>
</div>