{{-- Preview Scripts Data Component --}}
@props([
    'galleryConfig' => [],
    'audioUrl' => null,
    'videoUrl' => null
])

<!-- JavaScript Data Inputs -->
@if(!empty($galleryConfig))
    <input type="hidden" id="preview-gallery-images" value="{{ json_encode($galleryConfig) }}">
@endif

@if($audioUrl)
    <input type="hidden" id="preview-audio-type" value="url">
    <input type="hidden" id="preview-audio-url" value="{{ $audioUrl }}">
    <input type="hidden" id="preview-audio-autoplay" value="0">
@endif

@if($videoUrl)
    <input type="hidden" id="preview-video-type" value="url">
    <input type="hidden" id="preview-video-url" value="{{ $videoUrl }}">
@endif

<!-- Confirmation message for publish button -->
<input type="hidden" id="preview-publish-confirm-message" value="{{ __('preview.publish_confirm') }}">