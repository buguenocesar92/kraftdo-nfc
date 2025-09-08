{{-- Preview Content Body Component --}}
@props([
    'content',
    'multimedia' => [],
    'jsConfig' => [],
    'theme' => []
])

@php
use App\Services\ContentTypeConfigService;
$typeConfig = ContentTypeConfigService::getConfigForType($content->type);
@endphp

<div class="p-6">
    @if($typeConfig)
        @if($typeConfig['mergeVars'])
            @include($typeConfig['view'], array_merge(get_defined_vars(), [
                'hideFooter' => $typeConfig['hideFooter'],
                'multimedia' => $multimedia,
                'jsConfig' => $jsConfig,
                'theme' => $theme
            ]))
        @else
            @include($typeConfig['view'], [
                'content' => $content, 
                'hideFooter' => $typeConfig['hideFooter'],
                'multimedia' => $multimedia,
                'jsConfig' => $jsConfig,
                'theme' => $theme
            ])
        @endif
    @else
        <x-preview-unknown-content :type="$content->type" />
    @endif
</div>