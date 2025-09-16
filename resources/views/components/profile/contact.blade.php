{{-- Enhanced Profile Contact Component --}}
@props([
    'contentProfile' => null
])

@if($contentProfile && $contentProfile->hasContactInfo())
    <div class="space-y-2 sm:space-y-3 mb-6 sm:mb-8 animate-fade-in-up" style="animation-delay: 0.4s">
        @if($contentProfile->contact_email)
            <a href="mailto:{{ $contentProfile->contact_email }}" 
               class="flex items-center gap-3 p-3 sm:p-4 bg-gray-50 rounded-xl hover:bg-blue-50 hover:border-blue-200 border border-transparent transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 group"
               aria-label="Enviar email a {{ $contentProfile->contact_email }}">
                <div class="w-5 h-5 text-blue-600 group-hover:text-blue-700 transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-gray-700 text-sm sm:text-base flex-1 truncate">{{ $contentProfile->contact_email }}</span>
                <div class="w-4 h-4 text-gray-400 group-hover:text-blue-600 transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </div>
            </a>
        @endif
        
        @if($contentProfile->contact_phone)
            <a href="tel:{{ $contentProfile->contact_phone }}" 
               class="flex items-center gap-3 p-3 sm:p-4 bg-gray-50 rounded-xl hover:bg-green-50 hover:border-green-200 border border-transparent transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 group"
               aria-label="Llamar a {{ $contentProfile->contact_phone }}">
                <div class="w-5 h-5 text-green-600 group-hover:text-green-700 transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <span class="text-gray-700 text-sm sm:text-base flex-1">{{ $contentProfile->contact_phone }}</span>
                <div class="w-4 h-4 text-gray-400 group-hover:text-green-600 transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </div>
            </a>
        @endif
        
        @if($contentProfile->contact_website)
            <a href="{{ $contentProfile->contact_website }}" target="_blank" rel="noopener noreferrer"
               class="flex items-center gap-3 p-3 sm:p-4 bg-gray-50 rounded-xl hover:bg-purple-50 hover:border-purple-200 border border-transparent transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 group"
               aria-label="Visitar sitio web (se abre en nueva pestaña)">
                <div class="w-5 h-5 text-purple-600 group-hover:text-purple-700 transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0 9c-1.657 0-3-4.03-3-9s1.343-9 3-9m0 18c1.657 0 3-4.03 3-9s-1.343-9-3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                </div>
                <span class="text-gray-700 text-sm sm:text-base flex-1">Sitio Web</span>
                <div class="w-4 h-4 text-gray-400 group-hover:text-purple-600 transition-colors">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </div>
            </a>
        @endif
    </div>
@endif