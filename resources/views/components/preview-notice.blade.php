{{-- Preview Notice Component --}}
@props([
    'content',
    'isDraft' => false
])

<x-preview-container class="py-4">
    <div class="preview-notice">
        <div class="flex">
            <i class="fas fa-info-circle text-blue-600 mr-3 mt-1"></i>
            <div>
                <h3 class="font-medium text-blue-800 mb-1">{{ __('preview.notice_title') }}</h3>
                <p class="text-sm text-blue-700">
                    {{ __('preview.notice_description') }}
                    @if($isDraft)
                        {{ __('preview.notice_draft_warning') }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</x-preview-container>