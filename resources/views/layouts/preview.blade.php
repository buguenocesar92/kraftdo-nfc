<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Vista previa de contenido NFC">
    
    <title>@yield('title', __('preview.default_title'))</title>
    
    <!-- External CDN Resources -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/css/content-preview.css', 'resources/css/animations.css', 'resources/css/audio-overlay.css', 'resources/css/video-styles.css'])
    @vite(['resources/js/content-preview.js', 'resources/js/app-initializer.js', 'resources/js/audio-overlay-system.js', 'resources/js/streaming-controls.js', 'resources/js/gallery-modal-advanced.js', 'resources/js/video-orientation-system.js'])
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('head')
</head>
<body class="bg-gray-50 text-gray-900 overflow-x-hidden">
    
    @yield('header')
    
    @yield('notice')
    
    <main>
        @yield('content')
    </main>
    
    @yield('actions')
    
    @yield('multimedia')
    
    @yield('scripts')
    
    @yield('footer')

</body>
</html>