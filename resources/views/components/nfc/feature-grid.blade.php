@props([
    'features' => [],
    'columns' => '3', // 1, 2, 3, 4
    'animated' => true
])

@php
$gridClasses = match($columns) {
    '1' => 'grid-cols-1',
    '2' => 'grid-cols-1 sm:grid-cols-2',
    '3' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    '4' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
    default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'
};
@endphp

<div class="grid {{ $gridClasses }} gap-4 mb-8" 
     x-data="kraftdoFeatureGrid()" 
     x-init="@if($animated) setTimeout(() => animateFeatures(), 300) @endif">
    
    @if(!empty($features))
        @foreach($features as $index => $feature)
            <div class="kraftdo-glass rounded-xl p-4 sm:p-6 border border-white/20 kraftdo-feature-item"
                 @if($animated) style="opacity: 0; transform: translateY(20px);" @endif
                 data-index="{{ $index }}">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br {{ $feature['gradient'] ?? 'from-blue-500 to-green-500' }} rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 kraftdo-animate-pulse-slow">
                    <i class="{{ $feature['icon'] }} text-white text-xl sm:text-2xl"></i>
                </div>
                <h4 class="font-bold text-white mb-2 text-center">{{ $feature['title'] }}</h4>
                <p class="text-sm text-white/70 text-center">{{ $feature['description'] }}</p>
            </div>
        @endforeach
    @else
        {{ $slot }}
    @endif
</div>

<script>
    function kraftdoFeatureGrid() {
        return {
            animateFeatures() {
                const items = this.$el.querySelectorAll('.kraftdo-feature-item');
                items.forEach((item, index) => {
                    setTimeout(() => {
                        item.style.transition = 'all 0.6s ease-out';
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                    }, index * 150);
                });
            }
        }
    }
</script>