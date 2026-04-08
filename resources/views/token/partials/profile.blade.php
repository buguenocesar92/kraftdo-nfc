{{--
    Partial: Perfil NFC — réplica exacta del componente Next.js ProfilePreview
    Variables: $token, $dynamicContent, $contentProfile, $contentMultimedia
--}}
@php
    $palette   = $contentProfile?->color_palette ?? [];
    $primary   = $palette['primary']   ?? '#3B82F6';
    $secondary = $palette['secondary'] ?? '#8B5CF6';
    $accent    = $palette['accent']    ?? '#EC4899';

    $name       = $contentProfile?->name            ?? $dynamicContent?->title ?? 'Perfil';
    $bio        = $contentProfile?->bio             ?? '';
    $profession = $contentProfile?->profession      ?? '';
    $company    = $contentProfile?->company         ?? '';
    $location   = $contentProfile?->location        ?? '';
    $email      = $contentProfile?->contact_email   ?? '';
    $phone      = $contentProfile?->contact_phone   ?? '';
    $website    = $contentProfile?->contact_website ?? '';

    $socialLinks = $contentProfile?->socialLinks ?? collect();

    // Foto de perfil almacenada en multimedia settings
    $profileImg    = $contentMultimedia?->settings['profile_image'] ?? null;
    $profileImgUrl = $profileImg
        ? \Illuminate\Support\Facades\Storage::url($profileImg)
        : null;

    // Video
    $videoSrc = '';
    $isHtml5  = false;
    $isEmbed  = false;
    $embedSrc = '';
    if ($contentMultimedia) {
        if (in_array($contentMultimedia->video_type, ['file_upload', null]) && $contentMultimedia->video_file) {
            $videoSrc = asset('storage/' . $contentMultimedia->video_file);
            $isHtml5  = true;
        } elseif ($contentMultimedia->video_type === 'direct' && $contentMultimedia->video_url) {
            $videoSrc = $contentMultimedia->video_url;
            $isHtml5  = true;
        } elseif ($contentMultimedia->video_type === 'youtube' && $contentMultimedia->video_url) {
            preg_match('/(?:youtube\.com.*[?&]v=|youtu\.be\/)([^"&?\/ ]{11})/', $contentMultimedia->video_url, $m);
            if (!empty($m[1])) {
                $embedSrc = "https://www.youtube.com/embed/{$m[1]}?rel=0";
                $isEmbed  = true;
            }
        }
    }

    // Social link → icono Font Awesome
    $socialIcons = [
        'instagram' => 'fab fa-instagram',
        'linkedin'  => 'fab fa-linkedin',
        'twitter'   => 'fab fa-x-twitter',
        'facebook'  => 'fab fa-facebook',
        'github'    => 'fab fa-github',
        'youtube'   => 'fab fa-youtube',
        'tiktok'    => 'fab fa-tiktok',
        'snapchat'  => 'fab fa-snapchat',
        'whatsapp'  => 'fab fa-whatsapp',
        'telegram'  => 'fab fa-telegram',
    ];

    // Skills: el modelo ContentProfile puede tenerlas en color_palette['skills']
    // o en un campo data['skills']. Se muestran si existen.
    $skills = $contentProfile?->color_palette['skills'] ?? [];
@endphp

{{-- ─────────────────────────────────────────────────────────── --}}
{{-- OUTER WRAPPER — fondo blanco, animación fade-in             --}}
{{-- ─────────────────────────────────────────────────────────── --}}
<div class="min-h-screen flex items-start lg:items-center justify-center bg-gray-100 p-0 lg:p-4">

    <div class="relative w-full lg:max-w-2xl xl:max-w-3xl mx-auto
                opacity-0 translate-y-4 transition-all duration-700 ease-out"
         x-data="{
             init() { this.$nextTick(() => { this.$el.classList.remove('opacity-0','translate-y-4') }) },
             name:       @js($name),
             profession: @js($profession),
             company:    @js($company),
             location:   @js($location),
             email:      @js($email),
             phone:      @js($phone),
             website:    @js($website),
             showAndroidInstructions: false,
             blobUrl: null,
             generateVCard() {
                 const lines = ['BEGIN:VCARD','VERSION:3.0',`FN:${this.name}`];
                 const parts = this.name.split(' ').reverse();
                 lines.push(`N:${parts.join(';')};;;`);
                 if (this.company)    lines.push(`ORG:${this.company}`);
                 if (this.profession) lines.push(`TITLE:${this.profession}`);
                 if (this.phone)      lines.push(`TEL;TYPE=CELL:${this.phone}`);
                 if (this.email)      lines.push(`EMAIL:${this.email}`);
                 if (this.website)    lines.push(`URL:${this.website}`);
                 if (this.location)   lines.push(`ADR;TYPE=WORK:;;${this.location};;;;`);
                 lines.push('END:VCARD');
                 const blob = new Blob([lines.join('\n')], {type:'text/vcard;charset=utf-8'});
                 const url  = window.URL.createObjectURL(blob);
                 const fname = this.name.replace(/\s+/g,'_') + '.vcf';
                 const isIOS     = /iPhone|iPad|iPod/i.test(navigator.userAgent);
                 const isAndroid = /Android/i.test(navigator.userAgent);
                 const a = document.createElement('a');
                 a.href = url; a.download = fname;
                 if (isIOS) {
                     window.open(url,'_blank');
                 } else {
                     document.body.appendChild(a); a.click(); document.body.removeChild(a);
                     if (isAndroid) { this.blobUrl = url; this.showAndroidInstructions = true; return; }
                 }
                 window.URL.revokeObjectURL(url);
             },
             openDownloadedFile() {
                 if (this.blobUrl) window.open(this.blobUrl,'_blank');
             },
             dismissInstructions() {
                 this.showAndroidInstructions = false;
                 if (this.blobUrl) { window.URL.revokeObjectURL(this.blobUrl); this.blobUrl = null; }
             }
         }">

        {{-- Carta blanca --}}
        <div class="bg-white rounded-none lg:rounded-3xl overflow-hidden shadow-2xl flex flex-col min-h-screen lg:min-h-0">

            {{-- ── HEADER CON GRADIENTE ── --}}
            <div class="relative h-40 sm:h-48 flex-shrink-0"
                 style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }}, {{ $accent }});">
                {{-- Círculos decorativos (igual que Next.js) --}}
                <div class="absolute top-4 right-4 w-3 h-3 bg-white/30 rounded-full"></div>
                <div class="absolute top-8 right-8 w-2 h-2 bg-white/20 rounded-full"></div>
                <div class="absolute top-6 left-6 w-4 h-4 bg-white/20 rounded-full"></div>
                <div class="absolute top-12 left-4 w-2 h-2 bg-white/25 rounded-full"></div>
            </div>

            {{-- ── FOTO SUPERPUESTA AL HEADER ── --}}
            <div class="relative -mt-16 sm:-mt-20 flex justify-center mb-4 sm:mb-6 z-10">
                <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full bg-white p-2 sm:p-3 shadow-2xl
                            transition-transform duration-300 hover:scale-105">
                    @if($profileImgUrl)
                        <img src="{{ $profileImgUrl }}"
                             alt="{{ $name }}"
                             class="w-full h-full object-cover rounded-full border-4 border-white/50">
                    @else
                        <div class="w-full h-full rounded-full bg-gradient-to-br from-gray-100 to-gray-200
                                    flex items-center justify-center border-4 border-white/50">
                            <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── CONTENIDO ── --}}
            <div class="flex-1 overflow-y-auto px-4 sm:px-6 md:px-8 pb-6 sm:pb-8 space-y-4 sm:space-y-6">

                {{-- Nombre + Bio --}}
                <div class="text-center">
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2 sm:mb-3">
                        {{ $name }}
                    </h1>
                    @if($bio)
                        <p class="text-base sm:text-lg text-gray-600 leading-relaxed break-words hyphens-auto">
                            {{ $bio }}
                        </p>
                    @endif
                </div>

                {{-- ── INFORMACIÓN PROFESIONAL ── --}}
                @if($profession || $company || $location)
                    <div class="space-y-2 sm:space-y-3">
                        {{-- Section header --}}
                        <h4 class="flex items-center text-base sm:text-lg font-medium text-gray-900">
                            <span class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-orange-100 text-orange-600
                                         flex items-center justify-center mr-2 sm:mr-3 transition-transform duration-300 hover:rotate-6">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 6h-2.18c.07-.44.18-.88.18-1.36C18 2.51 16.49 1 14.64 1c-1.07 0-1.72.5-2.64 1.56C11.08 1.5 10.43 1 9.36 1 7.51 1 6 2.51 6 4.64c0 .48.11.92.18 1.36H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zM9.36 3c.71 0 1.28.57 1.28 1.28v.08c0 .71-.57 1.28-1.28 1.28s-1.28-.57-1.28-1.28S8.65 3 9.36 3zm5.28 0c.71 0 1.28.57 1.28 1.28s-.57 1.28-1.28 1.28-1.28-.57-1.28-1.28V4.28C13.36 3.57 13.93 3 14.64 3zM20 19H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
                                </svg>
                            </span>
                            Información Profesional
                        </h4>

                        @if($profession)
                            <div class="flex items-center p-3 sm:p-4 rounded-xl bg-gray-50 hover:shadow-md
                                        transition-all duration-300 hover:scale-105">
                                <span class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-lg bg-blue-100 text-blue-600
                                             flex items-center justify-center mr-3 sm:mr-4 shrink-0
                                             transition-transform duration-300 hover:rotate-6">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20 6h-2.18c.07-.44.18-.88.18-1.36C18 2.51 16.49 1 14.64 1c-1.07 0-1.72.5-2.64 1.56C11.08 1.5 10.43 1 9.36 1 7.51 1 6 2.51 6 4.64c0 .48.11.92.18 1.36H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                                    </svg>
                                </span>
                                <div class="flex-1">
                                    <div class="text-base sm:text-lg font-medium text-gray-900 break-words">{{ $profession }}</div>
                                </div>
                            </div>
                        @endif

                        @if($company)
                            <div class="flex items-center p-3 sm:p-4 rounded-xl bg-gray-50 hover:shadow-md
                                        transition-all duration-300 hover:scale-105">
                                <span class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-lg bg-purple-100 text-purple-600
                                             flex items-center justify-center mr-3 sm:mr-4 shrink-0
                                             transition-transform duration-300 hover:rotate-6">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 7V3H2v18h20V7H12zm-2 12H4v-2h6v2zm0-4H4v-2h6v2zm0-4H4V9h6v2zm0-4H4V5h6v2zm10 12h-8V9h8v10zm-2-8h-4v2h4v-2zm0 4h-4v2h4v-2z"/>
                                    </svg>
                                </span>
                                <div class="flex-1">
                                    <div class="text-base sm:text-lg font-medium text-gray-900 break-words">{{ $company }}</div>
                                </div>
                            </div>
                        @endif

                        @if($location)
                            <div class="flex items-center p-3 sm:p-4 rounded-xl bg-gray-50 hover:shadow-md
                                        transition-all duration-300 hover:scale-105">
                                <span class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-lg bg-green-100 text-green-600
                                             flex items-center justify-center mr-3 sm:mr-4 shrink-0
                                             transition-transform duration-300 hover:rotate-6">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                    </svg>
                                </span>
                                <div class="flex-1">
                                    <div class="text-base sm:text-lg font-medium text-gray-900 break-words">{{ $location }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ── INFORMACIÓN DE CONTACTO ── --}}
                @if($email || $phone || $website)
                    <div class="space-y-2 sm:space-y-3">
                        <h4 class="flex items-center text-base sm:text-lg font-medium text-gray-900">
                            <span class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-blue-100 text-blue-600
                                         flex items-center justify-center mr-2 sm:mr-3 transition-transform duration-300 hover:rotate-6">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </span>
                            Información de Contacto
                        </h4>

                        @if($email)
                            <a href="mailto:{{ $email }}"
                               class="flex items-center p-3 sm:p-4 rounded-xl bg-gray-50 hover:shadow-md
                                      transition-all duration-300 hover:scale-105">
                                <span class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-lg bg-purple-100 text-purple-600
                                             flex items-center justify-center mr-3 sm:mr-4 shrink-0
                                             transition-transform duration-300 hover:rotate-6">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                    </svg>
                                </span>
                                <div class="flex-1">
                                    <span class="text-base sm:text-lg font-medium text-blue-600 hover:underline break-all">{{ $email }}</span>
                                </div>
                            </a>
                        @endif

                        @if($phone)
                            <a href="tel:{{ $phone }}"
                               class="flex items-center p-3 sm:p-4 rounded-xl bg-gray-50 hover:shadow-md
                                      transition-all duration-300 hover:scale-105">
                                <span class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-lg bg-green-100 text-green-600
                                             flex items-center justify-center mr-3 sm:mr-4 shrink-0
                                             transition-transform duration-300 hover:rotate-6">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                    </svg>
                                </span>
                                <div class="flex-1">
                                    <span class="text-base sm:text-lg font-medium text-blue-600 hover:underline">{{ $phone }}</span>
                                </div>
                            </a>
                        @endif

                        @if($website)
                            <a href="{{ $website }}" target="_blank" rel="noopener"
                               class="flex items-center p-3 sm:p-4 rounded-xl bg-gray-50 hover:shadow-md
                                      transition-all duration-300 hover:scale-105">
                                <span class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-lg bg-indigo-100 text-indigo-600
                                             flex items-center justify-center mr-3 sm:mr-4 shrink-0
                                             transition-transform duration-300 hover:rotate-6">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                    </svg>
                                </span>
                                <div class="flex-1">
                                    <span class="text-base sm:text-lg font-medium text-blue-600 hover:underline break-all">{{ $website }}</span>
                                </div>
                            </a>
                        @endif
                    </div>
                @endif

                {{-- ── HABILIDADES ── --}}
                @if(!empty($skills))
                    <div class="space-y-2 sm:space-y-3">
                        <h4 class="flex items-center text-base sm:text-lg font-medium text-gray-900">
                            <span class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-purple-100 text-purple-600
                                         flex items-center justify-center mr-2 sm:mr-3">
                                <span class="text-xs sm:text-sm">🎯</span>
                            </span>
                            Habilidades
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($skills as $skill)
                                @php
                                    $skillName  = is_array($skill) ? ($skill['name'] ?? '') : $skill;
                                    $skillLevel = is_array($skill) ? ($skill['level'] ?? null) : null;
                                @endphp
                                <div class="p-3 sm:p-4 rounded-xl bg-gray-50 hover:shadow-md transition-all duration-300 hover:scale-105">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-900 break-words">{{ $skillName }}</span>
                                        @if($skillLevel)
                                            <span class="text-xs text-gray-500">{{ $skillLevel }}/10</span>
                                        @endif
                                    </div>
                                    @if($skillLevel)
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 transition-all duration-500"
                                                 style="width: {{ ($skillLevel / 10) * 100 }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ── REDES SOCIALES ── --}}
                @if($socialLinks->isNotEmpty())
                    <div class="space-y-2 sm:space-y-3">
                        <h4 class="flex items-center text-base sm:text-lg font-medium text-gray-900">
                            <span class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-pink-100 text-pink-600
                                         flex items-center justify-center mr-2 sm:mr-3">
                                <span class="text-xs sm:text-sm">🌐</span>
                            </span>
                            Redes Sociales
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($socialLinks as $link)
                                @php
                                    $info = \App\Models\ContentSocialLink::PLATFORMS[$link->platform]
                                          ?? ['name' => ucfirst($link->platform), 'icon' => 'fas fa-globe'];
                                    $faIcon = $socialIcons[$link->platform] ?? 'fas fa-globe';
                                @endphp
                                <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                                   class="flex items-center p-3 sm:p-4 rounded-xl bg-gray-50 hover:shadow-md
                                          transition-all duration-300 hover:scale-105">
                                    <span class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 rounded-lg bg-pink-100 text-pink-600
                                                 flex items-center justify-center mr-3 sm:mr-4 shrink-0
                                                 transition-transform duration-300 hover:rotate-6">
                                        <i class="{{ $faIcon }} text-sm sm:text-base md:text-lg"></i>
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm sm:text-base font-medium text-gray-900 capitalize">{{ $link->platform }}</div>
                                        @if($link->username)
                                            <div class="text-xs sm:text-sm text-gray-500 truncate">@{{ $link->username }}</div>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ── VIDEO ── --}}
                @if($isHtml5 || $isEmbed)
                    <div class="space-y-2 sm:space-y-3">
                        <h4 class="flex items-center text-base sm:text-lg font-medium text-gray-900">
                            <span class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-green-100 text-green-600
                                         flex items-center justify-center mr-2 sm:mr-3 transition-transform duration-300 hover:rotate-6">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </span>
                            Video de Presentación
                        </h4>
                        <div class="bg-gray-900 rounded-xl overflow-hidden">
                            @if($isHtml5)
                                <div class="relative aspect-video">
                                    <video src="{{ $videoSrc }}"
                                           controls playsinline preload="metadata"
                                           class="w-full h-full object-contain">
                                        Tu navegador no soporta video HTML5.
                                    </video>
                                </div>
                            @else
                                <div class="aspect-video">
                                    <iframe src="{{ $embedSrc }}" class="w-full h-full"
                                            frameborder="0" allowfullscreen
                                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"></iframe>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- ── GUARDAR CONTACTO ── --}}
                <div class="space-y-2">
                    {{-- Instrucciones Android (se muestran después de descargar) --}}
                    <div x-show="showAndroidInstructions"
                         x-transition
                         class="p-4 rounded-xl bg-blue-50 border border-blue-200 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 space-y-1">
                                <p class="text-sm font-medium text-gray-900 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                    </svg>
                                    Contacto descargado
                                </p>
                                <p class="text-xs text-gray-600 leading-relaxed">
                                    Haz clic en el botón para intentar abrir el contacto, o ábrelo desde tus notificaciones.
                                </p>
                            </div>
                            <button @click="dismissInstructions()"
                                    class="text-gray-400 hover:text-gray-600 text-xs transition-colors">✕</button>
                        </div>
                        <button @click="openDownloadedFile()"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4
                                       rounded-lg transition-all duration-300 hover:scale-105
                                       flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                            </svg>
                            Abrir Contacto
                        </button>
                    </div>

                    {{-- Botón principal --}}
                    <div x-show="!showAndroidInstructions">
                        <button @click="generateVCard()"
                                class="w-full bg-gradient-to-r from-blue-600 to-purple-600
                                       hover:from-blue-700 hover:to-purple-700
                                       text-white font-medium py-3 px-4 rounded-lg
                                       transition-all duration-300 hover:scale-105 transform
                                       flex items-center justify-center gap-2 shadow-lg">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                            </svg>
                            Guardar Contacto
                        </button>
                        <p class="text-xs text-center text-gray-500 mt-2 leading-relaxed">
                            Descarga la información de contacto a tu teléfono
                        </p>
                    </div>
                </div>

            </div>{{-- fin content --}}

            {{-- ── FOOTER ── --}}
            <div class="px-4 py-4 sm:py-6 text-center border-t border-gray-100">
                <p class="text-sm text-gray-600 font-medium">
                    Un perfil hecho con ❤️ especialmente para ti
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    Creado con tecnología NFC by KraftDo
                </p>
            </div>

        </div>{{-- fin carta blanca --}}
    </div>{{-- fin wrapper animado --}}
</div>{{-- fin outer --}}
