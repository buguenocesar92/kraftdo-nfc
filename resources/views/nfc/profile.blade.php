@extends('layouts.profile')

@section('title', $content->title . ' - ' . __('profile.professional_profile'))
@section('description', $content->description)

@php
    // Theme configuration
    $isDarkTheme = (bool) ($content->data['design']['dark_theme'] ?? false);
    $customColors = $content->data['design']['custom_colors'] ?? [];
    
    $primaryColor = $customColors['primary'] ?? '#1e40af';
    $secondaryColor = $customColors['secondary'] ?? '#64748b';
    $accentColor = $customColors['accent'] ?? '#0ea5e9';
    
    $cardStyle = $isDarkTheme ? 'bg-gray-800/90 backdrop-blur-md text-white' : 'bg-white/90 backdrop-blur-md';
    $bgClass = $isDarkTheme ? 'bg-gradient-to-br from-gray-900 via-gray-800 to-black' : 'bg-gradient-to-br from-gray-50 via-white to-gray-100';
    
    $primaryGradient = "linear-gradient(135deg, {$primaryColor}, {$secondaryColor})";
    $secondaryGradient = "linear-gradient(135deg, {$secondaryColor}, {$accentColor})";
@endphp

@section('body-class', $bgClass)

@section('header')
    <x-profile-header 
        :content="$content"
        :is-dark-theme="$isDarkTheme"
        :primary-color="$primaryColor"
        :secondary-color="$secondaryColor"
        :primary-gradient="$primaryGradient"
        :secondary-gradient="$secondaryGradient"
        :card-style="$cardStyle"
    />
@endsection

@section('bio')
    <x-profile-bio 
        :content="$content"
        :is-dark-theme="$isDarkTheme"
        :primary-color="$primaryColor"
        :primary-gradient="$primaryGradient"
        :card-style="$cardStyle"
    />
@endsection

@section('social-networks')
    <x-profile-social-networks 
        :content="$content"
        :is-dark-theme="$isDarkTheme"
        :secondary-color="$secondaryColor"
        :primary-gradient="$primaryGradient"
        :accent-color="$accentColor"
        :primary-color="$primaryColor"
        :card-style="$cardStyle"
    />
@endsection

@section('contact-info')
    <x-profile-contact-info 
        :content="$content"
        :is-dark-theme="$isDarkTheme"
        :accent-color="$accentColor"
        :primary-gradient="$primaryGradient"
        :card-style="$cardStyle"
    />
@endsection

@section('footer')
    @if(!isset($hideFooter) || !$hideFooter)
        <x-shared.footer 
            :content="$content" 
            theme="profile" 
            :show-admin-info="request()->has('admin')" 
        />
    @endif
@endsection