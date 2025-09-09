{{-- 
    Professional Token Gift View
    
    Modular implementation with external CSS/JS and Alpine.js components
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Regalo personalizado NFC - {{ $contentGift?->recipient_name ?? 'Regalo especial' }}">
    
    {{-- Page Title --}}
    <title>{{ $contentGift?->recipient_name ? $contentGift->recipient_name . ' - Regalo NFC' : 'Regalo NFC' }}</title>
    
    {{-- External Assets --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    {{-- Vite Assets --}}
    @vite([
        'resources/css/app.css', 
        'resources/css/token-gift.css',
        'resources/js/app.js',
        'resources/js/token-gift.js'
    ])
    
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="h-full gradient-bg" x-data="tokenGift()">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            
            {{-- Header Component --}}
            <x-token-gift.header />

            {{-- Main Card --}}
            <main class="bg-white rounded-2xl card-shadow p-8 animate-fade-in" style="animation-delay: 0.2s;" role="main">
                
                {{-- Recipient Information --}}
                <x-token-gift.recipient :content-gift="$contentGift" />

                {{-- Personal Message --}}
                <x-token-gift.message :content-gift="$contentGift" />

                {{-- Multimedia Content --}}
                @if($contentMultimedia)
                    <div class="space-y-6">
                        {{-- Video Player --}}
                        <x-token-gift.video-player :content-multimedia="$contentMultimedia" />

                        {{-- Audio Player --}}
                        <x-token-gift.audio-player :content-multimedia="$contentMultimedia" />
                    </div>
                @endif

                {{-- Gallery --}}
                <x-token-gift.gallery :gallery-images="$galleryImages ?? []" />

                {{-- Footer --}}
                <x-token-gift.footer />
                
            </main>
        </div>
    </div>

    {{-- Modal Component --}}
    <x-token-gift.modal />
</body>
</html>