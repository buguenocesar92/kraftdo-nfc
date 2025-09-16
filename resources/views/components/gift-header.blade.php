{{-- Gift Header Component --}}
@props([
    'content',
    'theme' => [],
    'currentSubtype' => [],
    'config' => []
])

<div class="gift-header">
    <div class="mb-4">
        <div class="gift-icon-wrapper bg-gradient-to-br {{ $theme['primary_gradient'] ?? 'from-pink-400 to-purple-500' }}">
            @if(isset($currentSubtype['icon']))
                <i class="{{ $currentSubtype['icon'] }} gift-icon"></i>
            @else
                <span class="gift-icon">{{ $config['icon'] ?? '💝' }}</span>
            @endif
        </div>
    </div>
    <h1 class="gift-title bg-gradient-to-r {{ $theme['primary_gradient'] ?? 'from-pink-600 to-purple-600' }}">
        {{ $content->title }}
    </h1>
    <p class="gift-subtitle {{ $theme['accent_color'] ?? 'text-pink-600' }}">
        {{ $currentSubtype['name'] ?? __('gift.special_gift') }}
    </p>
</div>