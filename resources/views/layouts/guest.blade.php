<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>KRAFTDO - {{ config('app.name', 'Laravel') }}</title>

        <script src="https://cdn.tailwindcss.com"></script>
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

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-kraftdo bg-kraftdo-dark text-white overflow-x-hidden">
        <div class="min-h-screen bg-gradient-to-br from-kraftdo-dark via-kraftdo-navy to-kraftdo-dark flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
            <!-- Animated Background -->
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-r from-kraftdo-blue to-kraftdo-green rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-r from-kraftdo-green to-kraftdo-lime rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
            
            <!-- Logo -->
            <div class="mb-8">
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-12 h-12 bg-gradient-to-r from-kraftdo-blue to-kraftdo-green rounded-full animate-pulse"></div>
                    <span class="text-2xl font-bold text-white">KRAFTDO</span>
                </a>
            </div>

            <div class="w-full sm:max-w-lg relative z-10">
                {{ $slot }}
            </div>
            
            <!-- Footer igual que en welcome -->
            <div class="mt-12 w-full">
                <x-shared.footer 
                    :content="null" 
                    theme="default" 
                    :showAdminInfo="false" 
                />
            </div>
        </div>
    </body>
</html>
