<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-kraftdo antialiased bg-kraftdo-dark text-white">
        <div class="min-h-screen kraftdo-bg-pattern">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-kraftdo-dark/95 backdrop-blur-sm border-b border-kraftdo-navy/20 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-white">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-kraftdo-green/20 border border-kraftdo-green/50 text-kraftdo-green px-4 py-3 rounded relative mx-auto max-w-7xl mt-4" role="alert">
                    <strong class="font-bold">¡Éxito!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                        <svg class="fill-current h-6 w-6 text-kraftdo-green" role="button" viewBox="0 0 20 20">
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-500/20 border border-red-500/50 text-red-400 px-4 py-3 rounded relative mx-auto max-w-7xl mt-4" role="alert">
                    <strong class="font-bold">¡Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" viewBox="0 0 20 20">
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </span>
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-kraftdo-blue/20 border border-kraftdo-blue/50 text-kraftdo-blue px-4 py-3 rounded relative mx-auto max-w-7xl mt-4" role="alert">
                    <strong class="font-bold">¡Atención!</strong>
                    <span class="block sm:inline">{{ session('warning') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                        <svg class="fill-current h-6 w-6 text-kraftdo-blue" role="button" viewBox="0 0 20 20">
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </span>
                </div>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
            
            <!-- Footer para todas las vistas del dashboard -->
            <x-shared.footer 
                :content="null" 
                theme="default" 
                :showAdminInfo="false" 
            />
        </div>
    </body>
</html>
