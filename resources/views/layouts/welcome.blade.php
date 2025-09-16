<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'KRAFTDO - Soluciones Digitales Innovadoras')</title>
    <meta name="description" content="@yield('description', 'Tecnología NFC Inteligente para el Futuro')">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite('resources/css/welcome.css')
    @vite(['resources/js/parallax-effects.js'])

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Welcome Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'kraftdo-navy': '#3B4A6B',
                        'kraftdo-dark': '#2A3441',
                        'kraftdo-blue': '#4A90E2',
                        'kraftdo-green': '#00FF7F',
                        'kraftdo-lime': '#32FF32'
                    },
                    fontFamily: {
                        'kraftdo': ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>

<body class="font-kraftdo bg-kraftdo-dark text-white overflow-x-hidden">
    <div class="min-h-screen bg-gradient-to-br from-kraftdo-dark via-kraftdo-navy to-kraftdo-dark relative overflow-hidden">
        
        @yield('background-effects')
        
        @yield('header')
        
        @yield('main-content')
        
    </div>
    
    @yield('footer')
    
    @yield('scripts')
</body>
</html>