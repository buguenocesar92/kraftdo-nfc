{{-- Enhanced Profile Action Buttons Component with Smart Contact Saving --}}
@props([
    'contentProfile' => null,
    'token' => null,
    'colors' => ['primary' => '#3B82F6', 'secondary' => '#8B5CF6', 'accent' => '#EC4899']
])

{{-- Assets are loaded in the main view --}}

<div class="space-y-3 sm:space-y-4 animate-fade-in-up" style="animation-delay: 0.6s">
    {{-- Primary Action Button - Smart Contact Save --}}
    <button x-data="contactComponent({
                name: '{{ $contentProfile->name ?? $token->name ?? 'Contacto' }}',
                @if($contentProfile)
                    @if($contentProfile->contact_email)
                        email: '{{ $contentProfile->contact_email }}',
                    @endif
                    @if($contentProfile->contact_phone)
                        phone: '{{ $contentProfile->contact_phone }}',
                    @endif
                    @if($contentProfile->contact_website)
                        website: '{{ $contentProfile->contact_website }}',
                    @endif
                    @if($contentProfile->job_title)
                        title: '{{ $contentProfile->job_title }}',
                    @endif
                    @if($contentProfile->bio)
                        note: '{{ str_replace(["\n", "\r", "'"], ["\\n", "", "\\'"], $contentProfile->bio) }}',
                    @endif
                @endif
            })" 
            @click="saveContact()"
            id="saveContactBtn"
            class="w-full text-white px-6 py-3 sm:py-4 rounded-xl font-semibold hover:shadow-lg transform hover:scale-[1.02] transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 group relative overflow-hidden"
            style="background: linear-gradient(135deg, {{ $colors['primary'] }}, {{ $colors['secondary'] }}, {{ $colors['accent'] }}); focus-ring-color: {{ $colors['primary'] }};">
        <span class="relative z-10 flex items-center justify-center gap-3">
            <div class="w-5 h-5 transition-transform duration-200 group-hover:scale-110">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 616 0z"/>
                </svg>
            </div>
            <span class="text-sm sm:text-base">Guardar Contacto</span>
        </span>
        <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-600"></div>
    </button>
</div>