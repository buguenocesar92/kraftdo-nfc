{{-- 
    Professional Token Profile View
    
    Modern profile card implementation with multimedia content and social links
    Enhanced with accessibility, SEO, performance optimizations and analytics
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#3B82F6">
    <meta name="description" content="{{ $contentProfile?->bio ?? 'Perfil digital profesional NFC de ' . ($contentProfile?->name ?? $token->name ?? 'usuario') }}">
    <meta name="keywords" content="perfil digital, NFC, contacto, {{ $contentProfile?->name ?? $token->name }}">
    <meta name="author" content="{{ $contentProfile?->name ?? $token->name ?? 'Usuario' }}">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ request()->url() }}">
    
    {{-- Page Title --}}
    <title>{{ $contentProfile?->name ?? $token->name ?? 'Perfil Digital' }} - Perfil NFC Profesional</title>
    
    {{-- Enhanced Open Graph Meta Tags --}}
    <meta property="og:title" content="{{ $contentProfile?->name ?? $token->name ?? 'Perfil Digital' }}">
    <meta property="og:description" content="{{ $contentProfile?->bio ?? 'Perfil digital profesional' }}">
    <meta property="og:type" content="profile">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:site_name" content="Perfil NFC">
    @if($contentMultimedia && isset($contentMultimedia->settings['profile_image']))
    <meta property="og:image" content="{{ asset('storage/' . $contentMultimedia->settings['profile_image']) }}">
    <meta property="og:image:width" content="400">
    <meta property="og:image:height" content="400">
    <meta property="og:image:alt" content="Foto de perfil de {{ $contentProfile?->name ?? $token->name }}">
    @endif
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $contentProfile?->name ?? $token->name ?? 'Perfil Digital' }}">
    <meta name="twitter:description" content="{{ $contentProfile?->bio ?? 'Perfil digital profesional' }}">
    @if($contentMultimedia && isset($contentMultimedia->settings['profile_image']))
    <meta name="twitter:image" content="{{ asset('storage/' . $contentMultimedia->settings['profile_image']) }}">
    @endif
    
    {{-- Schema.org JSON-LD --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Person",
        "name": "{{ $contentProfile?->name ?? $token->name ?? 'Perfil Digital' }}"
        @if($contentProfile?->bio)
        ,"description": "{{ addslashes($contentProfile->bio) }}"
        @endif
        @if($contentProfile?->contact_email)
        ,"email": "{{ $contentProfile->contact_email }}"
        @endif
        @if($contentProfile?->contact_phone)
        ,"telephone": "{{ $contentProfile->contact_phone }}"
        @endif
        @if($contentProfile?->contact_website)
        ,"url": "{{ $contentProfile->contact_website }}"
        @endif
        @if($contentMultimedia && isset($contentMultimedia->settings['profile_image']))
        ,"image": "{{ asset('storage/' . $contentMultimedia->settings['profile_image']) }}"
        @endif
        ,"sameAs": [
            @if($socialLinks && count($socialLinks) > 0)
            @foreach($socialLinks as $link)
            "{{ $link->url ?: ($link->platform_info['base_url'] . $link->username) }}"{{ !$loop->last ? ',' : '' }}
            @endforeach
            @endif
        ]
    }
    </script>
    
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

<body class="h-full bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 font-sans antialiased" role="document">
    {{-- Skip to main content for accessibility --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-white text-blue-600 px-4 py-2 rounded-lg z-50 transition-all">
        Saltar al contenido principal
    </a>
    
    <div class="min-h-screen flex items-center justify-center py-4 sm:py-8 md:py-12 px-3 sm:px-4 md:px-6 lg:px-8">
        <div class="w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl xl:max-w-2xl space-y-6">
            
            {{-- Profile Card --}}
            <main id="main-content" class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl overflow-hidden transform transition-all duration-700 animate-fade-in-up" role="main">
                
                {{-- Profile Header --}}
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
                
                {{-- Profile Content --}}
                <div class="pt-12 sm:pt-16 md:pt-20 pb-6 px-4 sm:px-6">
                    
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
                    
                    {{-- Save Contact Button --}}
                    <div class="text-center">
                        <button onclick="downloadVCard()" 
                                class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                            💾 Guardar Contacto
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function downloadVCard() {
            // Generate vCard content
            let vcard = "BEGIN:VCARD\nVERSION:3.0\n";
            vcard += "FN:{{ $contentProfile?->name ?? $token->name ?? 'Contacto' }}\n";
            
            @if($contentProfile)
                @if($contentProfile->contact_email)
                    vcard += "EMAIL:{{ $contentProfile->contact_email }}\n";
                @endif
                @if($contentProfile->contact_phone)
                    vcard += "TEL:{{ $contentProfile->contact_phone }}\n";
                @endif
                @if($contentProfile->contact_website)
                    vcard += "URL:{{ $contentProfile->contact_website }}\n";
                @endif
                @if($contentProfile->bio)
                    vcard += "NOTE:{{ str_replace(["\n", "\r"], ["\\n", ""], $contentProfile->bio) }}\n";
                @endif
            @endif
            
            vcard += "END:VCARD";
            
            // Create download link
            const element = document.createElement('a');
            const file = new Blob([vcard], {type: 'text/vcard'});
            element.href = URL.createObjectURL(file);
            element.download = "{{ Str::slug($contentProfile?->name ?? $token->name ?? 'contacto') }}.vcf";
            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);
        }
    </script>

    {{-- Critical CSS for animations --}}
    <style>
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes float-delayed {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }
        
        .animate-fade-in-up {
            animation: fade-in-up 0.6s ease-out forwards;
            opacity: 0;
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        .animate-float-delayed {
            animation: float-delayed 4s ease-in-out infinite 1s;
        }
        
        .animate-float-slow {
            animation: float-slow 5s ease-in-out infinite 2s;
        }
        
        /* Accessibility improvements */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        
        .focus\:not-sr-only:focus {
            position: static;
            width: auto;
            height: auto;
            padding: initial;
            margin: initial;
            overflow: visible;
            clip: auto;
            white-space: normal;
        }
        
        /* Performance optimizations */
        img {
            content-visibility: auto;
        }
        
        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            .animate-fade-in-up,
            .animate-float,
            .animate-float-delayed,
            .animate-float-slow {
                animation: none;
                opacity: 1;
                transform: none;
            }
            
            .transition-all,
            .transition-transform,
            .transition-colors,
            .transition-opacity {
                transition: none;
            }
        }
    </style>
</body>
</html>