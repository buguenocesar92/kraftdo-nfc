{{-- 
    Professional Token Profile View
    
    Modern profile card implementation with multimedia content and social links
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Perfil Digital NFC - {{ $contentProfile?->contact_name ?? 'Perfil profesional' }}">
    
    {{-- Page Title --}}
    <title>{{ $token->name ? $token->name . ' - Perfil NFC' : 'Perfil NFC' }}</title>
    
    {{-- Open Graph Meta Tags --}}
    <meta property="og:title" content="{{ $token->name ?? 'Perfil Digital' }}">
    <meta property="og:description" content="{{ $contentProfile?->bio ?? 'Perfil digital profesional' }}">
    <meta property="og:type" content="profile">
    
    {{-- CSS --}}
    @vite([
        'resources/css/app.css',
        'resources/css/multimedia-components.css'
    ])
    
    {{-- JavaScript --}}
    @vite([
        'resources/js/app.js',
        'resources/js/multimedia-components.js'
    ])
</head>

<body class="h-full bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-6">
            
            {{-- Profile Card --}}
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden animate-fade-in">
                
                {{-- Profile Header --}}
                <x-profile.header 
                    :content-multimedia="$contentMultimedia" 
                    :content-profile="$contentProfile" 
                    :token="$token" />
                
                {{-- Profile Content --}}
                <div class="pt-16 pb-6 px-6">
                    
                    {{-- Profile Info (Name & Bio) --}}
                    <x-profile.info 
                        :content-profile="$contentProfile" 
                        :token="$token" />
                    
                    {{-- Contact Information --}}
                    <x-profile.contact :content-profile="$contentProfile" />
                    
                    {{-- Social Links --}}
                    <x-profile.social-links :social-links="$socialLinks" />
                    
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
                        :token="$token" />
                </div>
            </div>
        </div>
    </div>


</body>
</html>