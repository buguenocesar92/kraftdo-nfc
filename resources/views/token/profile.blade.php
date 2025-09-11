{{-- 
    Professional Token Profile View
    
    Modern profile card implementation with multimedia content and social links
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $contentProfile?->bio ?? 'Perfil digital profesional de ' . ($contentProfile?->name ?? $token->name ?? 'usuario') . '. Conecta conmigo a través de mi tarjeta digital NFC.' }}">
    <meta name="keywords" content="perfil digital, NFC, tarjeta digital, contacto profesional, {{ $contentProfile?->name ?? $token->name ?? '' }}">
    <meta name="author" content="{{ $contentProfile?->name ?? $token->name ?? 'Usuario' }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#3b82f6">
    
    {{-- Page Title --}}
    <title>{{ $contentProfile?->name ?? $token->name ?? 'Perfil Digital' }} | Tarjeta Digital NFC</title>
    
    {{-- Open Graph Meta Tags --}}
    <meta property="og:title" content="{{ $contentProfile?->name ?? $token->name ?? 'Perfil Digital' }}">
    <meta property="og:description" content="{{ $contentProfile?->bio ?? 'Conecta conmigo a través de mi perfil digital profesional' }}">
    <meta property="og:type" content="profile">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:site_name" content="Kraftdo NFC">
    @if($contentMultimedia && isset($contentMultimedia->settings['profile_image']))
        <meta property="og:image" content="{{ asset(Storage::url($contentMultimedia->settings['profile_image'])) }}">
        <meta property="og:image:width" content="400">
        <meta property="og:image:height" content="400">
        <meta property="og:image:type" content="image/jpeg">
    @endif
    
    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $contentProfile?->name ?? $token->name ?? 'Perfil Digital' }}">
    <meta name="twitter:description" content="{{ $contentProfile?->bio ?? 'Conecta conmigo a través de mi perfil digital profesional' }}">
    @if($contentMultimedia && isset($contentMultimedia->settings['profile_image']))
        <meta name="twitter:image" content="{{ asset(Storage::url($contentMultimedia->settings['profile_image'])) }}">
    @endif
    
    {{-- Additional Meta Tags --}}
    <meta name="format-detection" content="telephone=yes">
    <meta name="format-detection" content="email=yes">
    <link rel="canonical" href="{{ request()->url() }}">
    
    {{-- CSS --}}
    @vite([
        'resources/css/app.css',
        'resources/css/multimedia-components.css',
        'resources/css/profile-enhancements.css'
    ])
    
    {{-- JavaScript --}}
    @vite([
        'resources/js/app.js',
        'resources/js/multimedia-components.js',
        'resources/js/profile-enhancements.js'
    ])
</head>

@php
    $defaultColors = ['#3B82F6', '#8B5CF6', '#EC4899']; // Azul, Morado, Rosa
    $customColors = $contentProfile?->color_palette ?? [];
    $primaryColor = $customColors['primary'] ?? $defaultColors[0];
    $secondaryColor = $customColors['secondary'] ?? $defaultColors[1];
    $accentColor = $customColors['accent'] ?? $defaultColors[2];
@endphp

{{-- Debug: mostrar colores en el HTML (solo para verificar) --}}
<!-- Colors: {{ $primaryColor }}, {{ $secondaryColor }}, {{ $accentColor }} -->

<body class="h-full" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }}, {{ $accentColor }})">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-6">
            
            {{-- Profile Card --}}
            <div class="bg-white rounded-3xl shadow-2xl animate-fade-in relative">
                
                {{-- Profile Header --}}
                <x-profile.header 
                    :content-multimedia="$contentMultimedia" 
                    :content-profile="$contentProfile" 
                    :token="$token"
                    :colors="['primary' => $primaryColor, 'secondary' => $secondaryColor, 'accent' => $accentColor]" />
                
                {{-- Profile Image (moved outside header) --}}
                <div class="profile-image-container transition-all duration-500 hover:scale-105" style="top: calc(6rem - 2rem); /* h-24 - half image height */">
                    @if($contentMultimedia && isset($contentMultimedia->settings['profile_image']))
                        <div class="relative group">
                            <img data-src="{{ Storage::url($contentMultimedia->settings['profile_image']) }}" 
                                 src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PGNpcmNsZSBjeD0iMTAwIiBjeT0iNzUiIHI9IjMwIiBmaWxsPSIjZDFkNWRiIi8+PHBhdGggZD0ibTEwMCAxMDBjLTE2LjU2OSAwLTMwIDEzLjQzMS0zMCAzMGg2MGMwLTE2LjU2OS0xMy40MzEtMzAtMzAtMzB6IiBmaWxsPSIjZDFkNWRiIi8+PC9zdmc+"
                                 alt="Foto de perfil de {{ $contentProfile?->name ?? $token->name ?? 'usuario' }}" 
                                 class="w-24 h-24 sm:w-32 sm:h-32 md:w-36 md:h-36 rounded-full border-3 sm:border-4 border-white shadow-lg object-cover transition-all duration-300 group-hover:shadow-xl lazy-load"
                                 loading="lazy"
                                 decoding="async">
                            <div class="absolute inset-0 rounded-full bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                    @else
                        <div class="w-24 h-24 sm:w-32 sm:h-32 md:w-36 md:h-36 rounded-full border-3 sm:border-4 border-white shadow-lg bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center transition-all duration-300 hover:shadow-xl">
                            <svg class="w-8 h-8 sm:w-12 sm:h-12 md:w-14 md:h-14 text-gray-400" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                    @endif
                </div>
                
                {{-- Profile Content --}}
                <div class="pt-16 pb-6 px-6">
                    
                    {{-- Profile Info (Name & Bio) --}}
                    <x-profile.info 
                        :content-profile="$contentProfile" 
                        :token="$token"
                        :colors="['primary' => $primaryColor, 'secondary' => $secondaryColor, 'accent' => $accentColor]" />
                    
                    {{-- Contact Information --}}
                    <x-profile.contact 
                        :content-profile="$contentProfile"
                        :colors="['primary' => $primaryColor, 'secondary' => $secondaryColor, 'accent' => $accentColor]" />
                    
                    {{-- Social Links --}}
                    <x-profile.social-links 
                        :social-links="$socialLinks"
                        :colors="['primary' => $primaryColor, 'secondary' => $secondaryColor, 'accent' => $accentColor]" />
                    
                    {{-- Video Presentation --}}
                    @if($contentMultimedia && ($contentMultimedia->video_url || $contentMultimedia->video_file))
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 text-center">
                                Video de Presentación
                            </h3>
                            <div class="rounded-xl overflow-hidden">
                                <x-multimedia.video-player 
                                    :video="$contentMultimedia"
                                    :theme="[
                                        'background' => 'from-blue-50 via-purple-50 to-pink-50',
                                        'primary' => 'blue-500',
                                        'secondary' => 'purple-600'
                                    ]"
                                    size="contained" />
                            </div>
                        </div>
                    @endif
                    
                    {{-- Gallery --}}
                    @if($galleryImages && count($galleryImages) > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 text-center">
                                Galería
                            </h3>
                            <x-multimedia.gallery 
                                :images="$galleryImages"
                                :theme="[
                                    'background' => 'from-blue-50 via-purple-50 to-pink-50',
                                    'text' => 'text-gray-600'
                                ]"
                                layout="masonry"
                                :show-stats="true" />
                        </div>
                    @endif
                    
                    {{-- Action Buttons --}}
                    <x-profile.action-buttons 
                        :content-profile="$contentProfile" 
                        :token="$token"
                        :colors="['primary' => $primaryColor, 'secondary' => $secondaryColor, 'accent' => $accentColor]" />
                </div>
            </div>
        </div>
    </div>

</body>
</html>