{{-- Profile Professional Badges Component --}}
@props([
    'content',
    'primaryGradient',
    'secondaryGradient'
])

<div class="flex flex-wrap justify-center gap-3 mt-6">
    @if(isset($content->data['personal_info']['profession']) && $content->data['personal_info']['profession'])
        <span class="profile-badge" style="background: {{ $secondaryGradient }};">
            <i class="fas fa-briefcase mr-2"></i>
            {{ $content->data['personal_info']['profession'] }}
        </span>
    @endif
    
    @if(isset($content->data['personal_info']['company']) && $content->data['personal_info']['company'])
        <span class="profile-badge" style="background: {{ $primaryGradient }};">
            <i class="fas fa-building mr-2"></i>
            {{ $content->data['personal_info']['company'] }}
        </span>
    @endif
    
    @if(isset($content->data['personal_info']['location']) && $content->data['personal_info']['location'])
        <span class="profile-badge" style="background: {{ $secondaryGradient }};">
            <i class="fas fa-map-marker-alt mr-2"></i>
            {{ $content->data['personal_info']['location'] }}
        </span>
    @endif
</div>