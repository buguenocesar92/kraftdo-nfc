@props([
    'title' => 'Kraftdo NFC',
    'subtitle' => '',
    'emoji' => '🎯',
    'icon' => 'fas fa-bolt',
    'animated' => true
])

<div class="text-center mb-12 kraftdo-animate-fade-in" x-data="kraftdoHero()" x-init="init('{{ $emoji }}')">
    <div class="mb-6">
        <div class="inline-flex items-center justify-center w-28 h-28 sm:w-36 sm:h-36 kraftdo-glass rounded-full kraftdo-shadow backdrop-blur-sm {{ $animated ? 'kraftdo-animate-float' : '' }}">
            <i class="{{ $icon }} text-4xl sm:text-6xl text-white"></i>
        </div>
    </div>
    
    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4 font-black tracking-tight">
        {{ $title }}
    </h1>
    
    <div class="text-6xl mb-4 kraftdo-animate-float" x-text="heroEmoji"></div>
    
    @if($subtitle)
        <p class="text-lg sm:text-xl text-white/90 font-medium max-w-2xl mx-auto">
            {{ $subtitle }}
        </p>
    @endif
    
    {{ $slot }}
</div>

<script>
    function kraftdoHero() {
        return {
            heroEmoji: '🎯',
            
            init(emoji) {
                this.heroEmoji = emoji;
                this.$dispatch('hero-loaded', { emoji });
            }
        }
    }
</script>