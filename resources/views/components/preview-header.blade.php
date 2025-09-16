{{-- Preview Header Component --}}
@props([
    'content',
    'backRoute' => null,
    'showDraftBadge' => true
])

<header class="bg-white shadow-sm border-b">
    <div class="max-w-4xl mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                @if($backRoute)
                    <a href="{{ $backRoute }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                @endif
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-eye text-blue-600"></i>
                    {{ __('preview.header_title', ['title' => $content->title]) }}
                </h1>
            </div>
            
            @if($showDraftBadge && $content->isDraft())
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <i class="fas fa-edit mr-1"></i>
                    {{ __('preview.draft_badge') }}
                </span>
            @endif
        </div>
    </div>
</header>