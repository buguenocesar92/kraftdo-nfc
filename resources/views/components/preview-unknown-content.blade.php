{{-- Preview Unknown Content Component --}}
@props([
    'type'
])

<div class="text-center py-8">
    <i class="fas fa-question-circle text-4xl text-gray-300 mb-4"></i>
    <h3 class="text-lg font-medium text-gray-600 mb-2">{{ __('preview.unknown_content_title') }}</h3>
    <p class="text-gray-500">{{ __('preview.unknown_content_type', ['type' => $type]) }}</p>
    
    @if(app()->environment('local'))
        <div class="mt-4 text-xs text-gray-400 bg-gray-50 rounded p-2 inline-block">
            <strong>Debug:</strong> Content type "{{ $type }}" not found in ContentTypeConfigService
        </div>
    @endif
</div>