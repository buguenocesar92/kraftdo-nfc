{{-- Profile Individual Social Links Component --}}
@props([
    'content',
    'accentColor' => '#0ea5e9',
    'primaryColor' => '#1e40af',
    'primaryGradient' => ''
])

@php
    $socialNetworks = [
        'linkedin' => ['icon' => 'fab fa-linkedin', 'label' => 'LinkedIn', 'gradient' => 'from-blue-600 to-blue-800'],
        'instagram' => ['icon' => 'fab fa-instagram', 'label' => 'Instagram', 'gradient' => 'from-purple-500 to-pink-500'],
        'twitter' => ['icon' => 'fab fa-x-twitter', 'label' => 'Twitter', 'gradient' => 'from-gray-800 to-black'],
        'facebook' => ['icon' => 'fab fa-facebook', 'label' => 'Facebook', 'gradient' => 'from-blue-500 to-blue-700'],
        'youtube' => ['icon' => 'fab fa-youtube', 'label' => 'YouTube', 'gradient' => 'from-red-500 to-red-700'],
        'tiktok' => ['icon' => 'fab fa-tiktok', 'label' => 'TikTok', 'gradient' => 'from-gray-900 to-black'],
        'telegram' => ['icon' => 'fab fa-telegram', 'label' => 'Telegram', 'gradient' => 'from-blue-400 to-blue-600'],
        'discord' => ['icon' => 'fab fa-discord', 'label' => 'Discord', 'gradient' => 'from-indigo-500 to-purple-600'],
        'snapchat' => ['icon' => 'fab fa-snapchat', 'label' => 'Snapchat', 'gradient' => 'from-yellow-400 to-yellow-600'],
        'threads' => ['icon' => 'fab fa-threads', 'label' => 'Threads', 'gradient' => 'from-gray-700 to-gray-900'],
        'github' => ['icon' => 'fab fa-github', 'label' => 'GitHub', 'gradient' => 'from-gray-800 to-gray-900'],
        'spotify' => ['icon' => 'fab fa-spotify', 'label' => 'Spotify', 'gradient' => 'from-green-400 to-green-600'],
    ];
@endphp

@foreach($socialNetworks as $key => $social)
    @php
        $url = $content->data['social_networks'][$key] ?? $content->data['social_links'][$key] ?? '';
    @endphp
    @if($url)
        <a href="{{ $url }}" 
           target="_blank"
           class="profile-social-button bg-gradient-to-r {{ $social['gradient'] }}">
            <i class="{{ $social['icon'] }} mr-3 text-xl group-hover:animate-bounce"></i> 
            <span class="font-semibold">{{ $social['label'] }}</span>
        </a>
    @endif
@endforeach

@php
    $websiteUrl = $content->data['social_networks']['website'] ?? $content->data['social_links']['website'] ?? '';
@endphp
@if($websiteUrl)
    <a href="{{ $websiteUrl }}" 
       target="_blank"
       class="profile-social-button" 
       style="background: linear-gradient(135deg, {{ $accentColor }}, {{ $primaryColor }});">
        <i class="fas fa-globe mr-3 text-xl group-hover:animate-bounce"></i>
        <span class="font-semibold">{{ __('profile.website') }}</span>
    </a>
@endif