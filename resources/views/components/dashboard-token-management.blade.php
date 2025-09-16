{{-- Dashboard Token Management Component --}}
@props(['tokenStats', 'hasTokens'])

@if($hasTokens)
<div class="bg-gradient-to-r from-kraftdo-blue/20 to-kraftdo-green/20 backdrop-blur-md rounded-3xl shadow-xl border-2 border-kraftdo-green/30 overflow-hidden animate-fade-in-up mb-8 hover:border-kraftdo-green/50 transition-all duration-300" style="animation-delay: 0.5s;">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="kraftdo-gradient p-4 rounded-2xl shadow-lg">
                    <i class="fas fa-cogs text-white text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold bg-gradient-to-r from-kraftdo-blue to-kraftdo-green bg-clip-text text-transparent">
                        Gestión de Tokens NFC
                    </h3>
                    <p class="text-gray-300 text-sm mt-1">Administra, configura y personaliza todos tus tokens</p>
                </div>
            </div>
            <div class="hidden sm:block">
                <div class="bg-kraftdo-navy/30 px-4 py-2 rounded-xl border border-kraftdo-green/20">
                    <span class="text-kraftdo-green font-bold text-lg">{{ $tokenStats['total'] }}</span>
                    <span class="text-gray-300 text-sm ml-1">{{ $tokenStats['total'] == 1 ? 'token' : 'tokens' }}</span>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex flex-col sm:flex-row gap-4">
            <a href="{{ route('my-tokens.index') }}" 
               class="flex-1 kraftdo-gradient text-white px-6 py-4 rounded-2xl hover:kraftdo-gradient-reverse hover:shadow-2xl hover:shadow-kraftdo-green/25 transition-all duration-300 transform hover:scale-[1.02] text-center font-semibold">
                <div class="flex items-center justify-center space-x-3">
                    <i class="fas fa-microchip text-xl"></i>
                    <span>Ver Todos Mis Tokens</span>
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            
            <div class="flex gap-3">
                <div class="bg-kraftdo-navy/50 px-4 py-2 rounded-xl text-center border border-kraftdo-blue/20">
                    <div class="text-kraftdo-blue font-bold">{{ $tokenStats['configured'] }}</div>
                    <div class="text-gray-400 text-xs">Configurados</div>
                </div>
                <div class="bg-kraftdo-navy/50 px-4 py-2 rounded-xl text-center border border-kraftdo-green/20">
                    <div class="text-kraftdo-green font-bold">{{ $tokenStats['active'] }}</div>
                    <div class="text-gray-400 text-xs">Activos</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif