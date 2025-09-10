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
        'resources/css/token-gift.css'
    ])
    
    {{-- JavaScript --}}
    @vite([
        'resources/js/app.js',
        'resources/js/token-gift.js'
    ])
</head>

<body class="h-full bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500" x-data="tokenProfile()">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-6">
            
            {{-- Profile Card --}}
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden animate-fade-in">
                
                {{-- Profile Header --}}
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
                
                {{-- Profile Content --}}
                <div class="pt-16 pb-6 px-6">
                    
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
                    
                    {{-- Contact Information --}}
                    @if($contentProfile && $contentProfile->hasContactInfo())
                        <div class="space-y-3 mb-6">
                            @if($contentProfile->contact_email)
                                <a href="mailto:{{ $contentProfile->contact_email }}" 
                                   class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                    <span class="text-xl">📧</span>
                                    <span class="text-gray-700 text-sm">{{ $contentProfile->contact_email }}</span>
                                </a>
                            @endif
                            
                            @if($contentProfile->contact_phone)
                                <a href="tel:{{ $contentProfile->contact_phone }}" 
                                   class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                    <span class="text-xl">📱</span>
                                    <span class="text-gray-700 text-sm">{{ $contentProfile->contact_phone }}</span>
                                </a>
                            @endif
                            
                            @if($contentProfile->contact_website)
                                <a href="{{ $contentProfile->contact_website }}" target="_blank"
                                   class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                    <span class="text-xl">🌐</span>
                                    <span class="text-gray-700 text-sm">Sitio Web</span>
                                </a>
                            @endif
                        </div>
                    @endif
                    
                    {{-- Social Links --}}
                    @if($socialLinks && count($socialLinks) > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 text-center">
                                Sígueme en
                            </h3>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($socialLinks as $link)
                                    @php
                                        $platform = $link->platform_info;
                                        $url = $link->url ?: ($platform['base_url'] . $link->username);
                                    @endphp
                                    <a href="{{ $url }}" target="_blank" rel="noopener"
                                       class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors text-sm">
                                        <span class="text-lg">
                                            @switch($link->platform)
                                                @case('instagram') 📷 @break
                                                @case('linkedin') 💼 @break
                                                @case('twitter') 🐦 @break
                                                @case('facebook') 📘 @break
                                                @case('tiktok') 🎵 @break
                                                @case('youtube') 📹 @break
                                                @case('github') 💻 @break
                                                @default 🌐 @break
                                            @endswitch
                                        </span>
                                        <span class="text-gray-700 truncate">
                                            {{ $link->username ?: $platform['name'] }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    {{-- Video Presentation --}}
                    @if($contentMultimedia && ($contentMultimedia->video_url || $contentMultimedia->video_file))
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 text-center">
                                Video de Presentación
                            </h3>
                            <div class="rounded-xl overflow-hidden">
                                <x-token-gift.video-player :content-multimedia="$contentMultimedia" />
                            </div>
                        </div>
                    @endif
                    
                    {{-- Gallery --}}
                    @if($galleryImages && count($galleryImages) > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 text-center">
                                Galería
                            </h3>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($galleryImages->take(6) as $image)
                                    <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden cursor-pointer hover:opacity-90 transition-opacity"
                                         onclick="openImageModal('{{ Storage::url($image->image_path) }}')">
                                        <img src="{{ Storage::url($image->image_path) }}" 
                                             alt="{{ $image->alt_text }}"
                                             class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                            @if(count($galleryImages) > 6)
                                <p class="text-center text-sm text-gray-500 mt-2">
                                    +{{ count($galleryImages) - 6 }} más
                                </p>
                            @endif
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
            </div>
        </div>
    </div>

    {{-- Image Modal --}}
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50" onclick="closeImageModal()">
        <div class="max-w-4xl max-h-4xl p-4">
            <img id="modalImage" src="" alt="" class="max-w-full max-h-full rounded-lg">
        </div>
    </div>

    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
            document.getElementById('imageModal').classList.add('flex');
        }
        
        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.getElementById('imageModal').classList.remove('flex');
        }
        
        function downloadVCard() {
            // Generate vCard content
            let vcard = "BEGIN:VCARD\nVERSION:3.0\n";
            vcard += "FN:{{ $contentProfile->name ?? $token->name ?? 'Contacto' }}\n";
            
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
                    vcard += "NOTE:{{ str_replace("\n", "\\n", $contentProfile->bio) }}\n";
                @endif
            @endif
            
            vcard += "END:VCARD";
            
            // Create download link
            const element = document.createElement('a');
            const file = new Blob([vcard], {type: 'text/vcard'});
            element.href = URL.createObjectURL(file);
            element.download = "{{ Str::slug($contentProfile->name ?? $token->name ?? 'contacto') }}.vcf";
            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);
        }
        
        // Alpine.js component for profile
        function tokenProfile() {
            return {
                init() {
                    console.log('Profile loaded');
                }
            }
        }
    </script>
</body>
</html>