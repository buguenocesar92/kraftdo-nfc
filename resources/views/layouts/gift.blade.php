<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description', 'Regalo personalizado NFC')">
    <meta property="og:title" content="@yield('title', 'Regalo Especial')">
    <meta property="og:description" content="@yield('description', 'Un regalo personalizado y especial')">
    
    <title>@yield('title', 'Regalo Especial')</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/animations.css', 'resources/css/audio-overlay.css', 'resources/css/video-styles.css', 'resources/css/gift-tailwind-config.css'])
    @vite(['resources/js/app-initializer.js', 'resources/js/audio-overlay-system.js', 'resources/js/streaming-controls.js', 'resources/js/gallery-modal-advanced.js', 'resources/js/video-orientation-system.js'])
    
    <!-- External Dependencies -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Dancing+Script:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('head')
</head>

<body class="@yield('body-class', 'bg-gradient-to-br from-pink-50 via-purple-50 to-indigo-100') min-h-screen @yield('font-class', 'font-sans')" style="@yield('body-style')">
    
    @yield('background-decorations')
    
    <div class="relative z-10 w-full max-w-4xl mx-auto px-3 py-4 sm:px-6 lg:px-8 min-h-screen">
        
        @yield('header')
        
        @yield('message')
        
        @yield('sender-info')
        
        @yield('main-image')
        
        <main>
            @yield('multimedia')
        </main>
        
    </div>
    
    @yield('footer')
    
    @yield('modals')
    
    @yield('scripts')

</body>
</html>