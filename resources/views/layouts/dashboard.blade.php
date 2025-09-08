<x-app-layout>
    <x-slot name="header">
        @yield('header')
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @yield('messages')
            
            @yield('welcome-section')
            
            @yield('stats-section')
            
            @yield('token-management')
            
            @yield('global-stats')
            
            @yield('type-distribution')
            
            @yield('empty-state')

        </div>
    </div>
</x-app-layout>