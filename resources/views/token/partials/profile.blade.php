{{--
    Partial: Perfil NFC — mobile-first, Tailwind
    Variables: $token, $dynamicContent, $contentProfile, $contentMultimedia
--}}
@php
    $palette   = $contentProfile?->color_palette ?? [];
    $primary   = $palette['primary']   ?? '#1e40af';
    $secondary = $palette['secondary'] ?? '#64748b';
    $bg        = $palette['background'] ?? '#f8fafc';

    $name       = $contentProfile?->name       ?? $dynamicContent?->title ?? 'Perfil';
    $bio        = $contentProfile?->bio         ?? '';
    $profession = $contentProfile?->profession  ?? '';
    $company    = $contentProfile?->company     ?? '';
    $location   = $contentProfile?->location    ?? '';
    $email      = $contentProfile?->contact_email   ?? '';
    $phone      = $contentProfile?->contact_phone   ?? '';
    $website    = $contentProfile?->contact_website ?? '';

    $socialLinks = $contentProfile?->socialLinks ?? collect();

    // Foto de perfil: settings['profile_image'] en multimedia
    $profileImg = $contentMultimedia?->settings['profile_image'] ?? null;
    $profileImgUrl = $profileImg ? \Illuminate\Support\Facades\Storage::url($profileImg) : null;

    $gradients = [
        'instagram' => 'from-purple-500 to-pink-500',
        'linkedin'  => 'from-blue-600 to-blue-800',
        'twitter'   => 'from-gray-800 to-black',
        'facebook'  => 'from-blue-500 to-blue-700',
        'tiktok'    => 'from-gray-900 to-black',
        'youtube'   => 'from-red-500 to-red-700',
        'github'    => 'from-gray-700 to-gray-900',
        'whatsapp'  => 'from-green-500 to-green-700',
        'website'   => 'from-blue-400 to-blue-600',
    ];
@endphp

<div class="min-h-screen pb-8" style="background-color: {{ $bg }};">
    <div class="max-w-md mx-auto px-4 pt-6 space-y-4">

        {{-- ── HEADER ── --}}
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden">
            <div class="h-2" style="background: linear-gradient(90deg, {{ $primary }}, {{ $secondary }});"></div>
            <div class="p-6 text-center">

                {{-- Foto --}}
                <div class="mb-4">
                    @if($profileImgUrl)
                        <img src="{{ $profileImgUrl }}"
                             alt="{{ $name }}"
                             class="w-28 h-28 sm:w-32 sm:h-32 rounded-full mx-auto object-cover ring-4 ring-white shadow-xl">
                    @else
                        <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-full mx-auto flex items-center justify-center shadow-xl"
                             style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">
                            <i class="fas fa-user text-4xl text-white"></i>
                        </div>
                    @endif
                </div>

                {{-- Nombre --}}
                <h1 class="text-2xl font-bold text-gray-900">{{ $name }}</h1>

                {{-- Badges --}}
                @if($profession || $company || $location)
                    <div class="flex flex-wrap justify-center gap-2 mt-3">
                        @if($profession)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold text-white"
                                  style="background: {{ $primary }};">
                                <i class="fas fa-briefcase"></i> {{ $profession }}
                            </span>
                        @endif
                        @if($company)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold text-white"
                                  style="background: {{ $secondary }};">
                                <i class="fas fa-building"></i> {{ $company }}
                            </span>
                        @endif
                        @if($location)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold text-white"
                                  style="background: {{ $secondary }};">
                                <i class="fas fa-map-marker-alt"></i> {{ $location }}
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Bio --}}
                @if($bio)
                    <p class="mt-3 text-gray-600 text-sm leading-relaxed">{{ $bio }}</p>
                @endif
            </div>
        </div>

        {{-- ── CONTACTO ── --}}
        @if($email || $phone || $website)
            <div class="bg-white rounded-3xl shadow-lg p-5 space-y-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Contacto</p>

                @if($email)
                    <a href="mailto:{{ $email }}"
                       class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 active:bg-gray-100 transition-colors">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs shrink-0"
                              style="background: {{ $primary }};"><i class="fas fa-envelope"></i></span>
                        <span class="text-gray-700 text-sm truncate">{{ $email }}</span>
                    </a>
                @endif

                @if($phone)
                    <a href="tel:{{ $phone }}"
                       class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 active:bg-gray-100 transition-colors">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs shrink-0"
                              style="background: {{ $primary }};"><i class="fas fa-phone"></i></span>
                        <span class="text-gray-700 text-sm">{{ $phone }}</span>
                    </a>
                @endif

                @if($website)
                    <a href="{{ $website }}" target="_blank" rel="noopener"
                       class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 active:bg-gray-100 transition-colors">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs shrink-0"
                              style="background: {{ $secondary }};"><i class="fas fa-globe"></i></span>
                        <span class="text-gray-700 text-sm truncate">{{ $website }}</span>
                    </a>
                @endif
            </div>
        @endif

        {{-- ── REDES SOCIALES ── --}}
        @if($socialLinks->isNotEmpty())
            <div class="bg-white rounded-3xl shadow-lg p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Redes Sociales</p>
                <div class="space-y-2">
                    @foreach($socialLinks as $link)
                        @php
                            $info = \App\Models\ContentSocialLink::PLATFORMS[$link->platform]
                                  ?? ['name' => ucfirst($link->platform), 'icon' => 'fas fa-link'];
                            $grad = $gradients[$link->platform] ?? 'from-gray-500 to-gray-700';
                        @endphp
                        <a href="{{ $link->url }}" target="_blank" rel="noopener"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-white font-medium text-sm active:scale-95 transition-transform bg-gradient-to-r {{ $grad }}">
                            <i class="{{ $info['icon'] }} text-base w-5 text-center"></i>
                            <span>{{ $info['name'] }}</span>
                            @if($link->username)
                                <span class="ml-auto text-white/70 text-xs truncate max-w-[120px]">{{ $link->username }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ── VIDEO ── --}}
        @if($contentMultimedia && ($contentMultimedia->video_url || $contentMultimedia->video_file))
            @php
                $videoSrc = '';
                $isHtml5  = false;
                $isEmbed  = false;
                $embedSrc = '';

                if ($contentMultimedia->video_type === 'file_upload' && $contentMultimedia->video_file) {
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
            @endphp

            <div class="bg-white rounded-3xl shadow-lg overflow-hidden">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 pt-4 pb-2">Video</p>
                @if($isHtml5)
                    <video src="{{ $videoSrc }}"
                           controls playsinline preload="metadata"
                           class="w-full rounded-b-3xl max-h-72 object-contain bg-black">
                        Tu navegador no soporta video HTML5.
                    </video>
                @elseif($isEmbed)
                    <div class="aspect-video">
                        <iframe src="{{ $embedSrc }}" class="w-full h-full rounded-b-3xl"
                                frameborder="0" allowfullscreen
                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"></iframe>
                    </div>
                @endif
            </div>
        @endif

        {{-- ── HABILIDADES ── --}}
        @if(!empty($contentProfile?->data['skills']))
            <div class="bg-white rounded-3xl shadow-lg p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Habilidades</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($contentProfile->data['skills'] as $skill)
                        <span class="px-3 py-1 rounded-full text-xs font-medium text-white"
                              style="background: linear-gradient(90deg, {{ $primary }}, {{ $secondary }});">
                            {{ $skill }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Footer --}}
        <p class="text-center text-xs text-gray-400 py-2">Powered by <span class="font-semibold">KraftDo</span></p>

    </div>
</div>
