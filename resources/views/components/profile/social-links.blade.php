{{-- Enhanced Profile Social Links Component --}}
@props([
    'socialLinks' => []
])

@if($socialLinks && count($socialLinks) > 0)
    <div class="mb-6 sm:mb-8 animate-fade-in-up" style="animation-delay: 0.5s">
        <h3 class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 sm:mb-4 text-center">
            Sígueme en
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
            @foreach($socialLinks as $link)
                @php
                    $platform = $link->platform_info;
                    $url = $link->url ?: ($platform['base_url'] . $link->username);
                    $platformColors = [
                        'instagram' => ['bg' => 'hover:bg-pink-50', 'border' => 'hover:border-pink-200', 'text' => 'text-pink-600', 'focus' => 'focus:ring-pink-500'],
                        'linkedin' => ['bg' => 'hover:bg-blue-50', 'border' => 'hover:border-blue-200', 'text' => 'text-blue-600', 'focus' => 'focus:ring-blue-500'],
                        'twitter' => ['bg' => 'hover:bg-sky-50', 'border' => 'hover:border-sky-200', 'text' => 'text-sky-600', 'focus' => 'focus:ring-sky-500'],
                        'facebook' => ['bg' => 'hover:bg-indigo-50', 'border' => 'hover:border-indigo-200', 'text' => 'text-indigo-600', 'focus' => 'focus:ring-indigo-500'],
                        'tiktok' => ['bg' => 'hover:bg-gray-50', 'border' => 'hover:border-gray-200', 'text' => 'text-gray-800', 'focus' => 'focus:ring-gray-500'],
                        'youtube' => ['bg' => 'hover:bg-red-50', 'border' => 'hover:border-red-200', 'text' => 'text-red-600', 'focus' => 'focus:ring-red-500'],
                        'github' => ['bg' => 'hover:bg-gray-50', 'border' => 'hover:border-gray-200', 'text' => 'text-gray-800', 'focus' => 'focus:ring-gray-500']
                    ];
                    $colors = $platformColors[$link->platform] ?? ['bg' => 'hover:bg-gray-50', 'border' => 'hover:border-gray-200', 'text' => 'text-gray-600', 'focus' => 'focus:ring-gray-500'];
                @endphp
                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-3 p-3 sm:p-4 bg-gray-50 rounded-xl {{ $colors['bg'] }} {{ $colors['border'] }} border border-transparent transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-2 {{ $colors['focus'] }} focus:ring-offset-2 group text-sm sm:text-base"
                   aria-label="Seguir en {{ ucfirst($link->platform) }} (se abre en nueva pestaña)">
                    <div class="w-5 h-5 {{ $colors['text'] }} group-hover:scale-110 transition-transform duration-200">
                        @switch($link->platform)
                            @case('instagram')
                                <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            @break
                            @case('linkedin')
                                <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            @break
                            @case('twitter')
                                <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            @break
                            @case('facebook')
                                <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            @break
                            @case('tiktok')
                                <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-.88-.05A6.33 6.33 0 005 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.1z"/>
                                </svg>
                            @break
                            @case('youtube')
                                <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            @break
                            @case('github')
                                <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                </svg>
                            @break
                            @default
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0 9c-1.657 0-3-4.03-3-9s1.343-9 3-9m0 18c1.657 0 3-4.03 3-9s-1.343-9-3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                            @break
                        @endswitch
                    </div>
                    <span class="text-gray-700 truncate flex-1">
                        {{ $link->username ?: $platform['name'] }}
                    </span>
                    <div class="w-4 h-4 text-gray-400 {{ $colors['text'] }} opacity-0 group-hover:opacity-100 transition-all duration-200">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif