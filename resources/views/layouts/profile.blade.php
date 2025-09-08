<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Perfil Profesional')</title>
    <meta name="description" content="@yield('description', 'Perfil profesional digital')">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite('resources/css/profile-config.css')
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="@yield('body-class', 'bg-gradient-to-br from-gray-50 via-white to-gray-100') min-h-screen font-sans" style="@yield('body-style')">
    
    @yield('background-decorations')
    
    <div class="relative z-10 max-w-4xl mx-auto px-3 py-4 sm:px-6 lg:px-8 min-h-screen">
        
        @yield('header')
        
        @yield('bio')
        
        @yield('social-networks')
        
        @yield('contact-info')
        
    </div>

    @yield('footer')

    @yield('scripts')
</body>
</html>