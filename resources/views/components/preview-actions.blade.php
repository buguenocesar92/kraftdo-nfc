{{-- Preview Actions Component --}}
@props([
    'content',
    'canPublish' => false
])

<x-preview-container>
    <x-preview-card class="p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            
            <!-- Left side - Info -->
            <div>
                <h3 class="font-medium text-gray-800 mb-1">{{ __('preview.actions_title') }}</h3>
                <p class="text-sm text-gray-600">
                    @if($canPublish)
                        {{ __('preview.actions_can_publish') }}
                    @else
                        {{ __('preview.actions_published') }}
                    @endif
                </p>
            </div>
            
            <!-- Right side - Actions -->
            <div class="flex space-x-3">
                <!-- Edit Button -->
                <a href="{{ route('my-tokens.configure', $content->nfc_token_id) }}" 
                   class="preview-btn preview-btn-edit">
                    <i class="fas fa-edit mr-1"></i>
                    {{ __('preview.btn_edit') }}
                </a>
                
                @if($canPublish)
                    <!-- Publish Button -->
                    <form action="{{ route('content.publish', $content) }}" method="POST" class="inline" data-preview-confirm-publish>
                        @csrf
                        <button type="submit" class="preview-btn-publish">
                            <i class="fas fa-rocket mr-2"></i>
                            {{ __('preview.btn_publish') }}
                        </button>
                    </form>
                @else
                    <!-- Public Link -->
                    <a href="{{ url('/nfc?TYPE=' . $content->type . '&ID=' . $content->content_id) }}" 
                       target="_blank"
                       class="preview-btn preview-btn-view">
                        <i class="fas fa-external-link-alt mr-1"></i>
                        {{ __('preview.btn_view_public') }}
                    </a>
                @endif
            </div>
        </div>
    </x-preview-card>
</x-preview-container>