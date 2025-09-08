{{-- Gift Scripts Data Component --}}
@props([
    'jsConfig' => []
])

<!-- Hidden inputs to pass PHP data to JavaScript -->
@if(isset($jsConfig) && (($jsConfig['audio']['type'] ?? '') || ($jsConfig['video']['type'] ?? '')))
    <input type="hidden" id="audio-type" value="{{ $jsConfig['audio']['type'] ?? '' }}">
    <input type="hidden" id="audio-url" value="{{ $jsConfig['audio']['url'] ?? '' }}">
    <input type="hidden" id="audio-autoplay" value="{{ ($jsConfig['audio']['autoplay'] ?? false) ? '1' : '0' }}">
    <input type="hidden" id="video-type" value="{{ $jsConfig['video']['type'] ?? '' }}">
    <input type="hidden" id="video-url" value="{{ $jsConfig['video']['url'] ?? '' }}">
    <input type="hidden" id="theme-primary-gradient" value="{{ $jsConfig['theme']['primary_gradient'] ?? 'from-pink-600 to-purple-600' }}">
    <input type="hidden" id="theme-accent-color" value="{{ $jsConfig['theme']['accent_color'] ?? 'text-pink-600' }}">
@endif

<!-- Gallery data for JavaScript -->
@if(isset($jsConfig) && !empty($jsConfig['gallery']))
    <input type="hidden" id="gallery-images" value="{{ json_encode($jsConfig['gallery']) }}">
@endif