{{-- Profile Contact Component --}}
@props([
    'contentProfile' => null
])

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