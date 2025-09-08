{{-- Profile Avatar Component --}}
@props([
    'imageUrl',
    'title',
    'primaryGradient'
])

<div class="mb-8">
    @if($imageUrl)
        <div class="relative inline-block">
            <img src="{{ $imageUrl }}" alt="{{ $title }}" class="profile-avatar">
            <!-- Online Status Indicator -->
            <div class="profile-status-indicator"></div>
        </div>
    @else
        <div class="profile-avatar-placeholder" style="background: {{ $primaryGradient }};">
            <i class="fas fa-user text-6xl sm:text-8xl text-white"></i>
        </div>
    @endif
</div>