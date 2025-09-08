@props(['token', 'currentStep' => 1])

@php
    $isProfile = $token->content_type === 'PROFILE';
    $isMenu = $token->content_type === 'MENU';
    
    if ($isProfile) {
        $totalSteps = 5;
        $steps = [
            1 => 'Información',
            2 => 'Personal',
            3 => 'Contacto',
            4 => 'Redes',
            5 => 'Diseño'
        ];
    } elseif ($isMenu) {
        $totalSteps = 4;
        $steps = [
            1 => 'Información',
            2 => 'Restaurante',
            3 => 'Menú',
            4 => 'Diseño'
        ];
    } else {
        $totalSteps = 4;
        $steps = [
            1 => 'Información',
            2 => 'Regalo',
            3 => 'Multimedia',
            4 => 'Diseño'
        ];
    }
@endphp

<!-- Wizard Steps Navigation -->
<div class="mb-8 bg-white/50 backdrop-blur-sm rounded-2xl p-4 sm:p-6 border border-gray-200/50">
    <!-- Desktop Navigation -->
    <div class="hidden lg:flex items-center justify-center">
        <div class="flex items-center space-x-1 xl:space-x-2">
            @for($i = 1; $i <= $totalSteps; $i++)
                <div class="flex items-center flex-shrink-0">
                    <div class="w-8 h-8 xl:w-10 xl:h-10 {{ $i <= 1 ? 'kraftdo-gradient text-white' : 'bg-gray-200 text-gray-500' }} rounded-full flex items-center justify-center text-xs xl:text-sm font-bold wizard-step shadow-lg {{ $i === 1 ? 'active' : '' }} cursor-pointer" data-step="{{ $i }}" id="desktop-step-{{ $i }}">
                        {{ $i }}
                    </div>
                    <span class="ml-2 text-xs font-semibold {{ $i <= 1 ? 'text-kraftdo-blue' : 'text-gray-500' }} whitespace-nowrap" id="desktop-label-{{ $i }}">
                        {{ $steps[$i] ?? '' }}
                    </span>
                </div>
                @if($i < $totalSteps)
                    <div class="w-4 h-1 {{ $i < 1 ? 'bg-gradient-to-r from-kraftdo-blue/50 to-kraftdo-green/50' : 'bg-gray-200' }} wizard-connector rounded-full flex-shrink-0" data-step="{{ $i }}" id="desktop-connector-{{ $i }}"></div>
                @endif
            @endfor
        </div>
    </div>

    <!-- Mobile Navigation -->
    <div class="lg:hidden">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-kraftdo-navy" id="mobile-step-title">Paso 1 de {{ $totalSteps }}</h3>
            <span class="text-sm text-kraftdo-navy/70" id="mobile-step-percentage">20%</span>
        </div>
        
        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
            <div id="mobile-progress-bar" class="kraftdo-gradient h-2 rounded-full transition-all duration-300" style="width: 20%"></div>
        </div>
        
        <!-- Current Step -->
        <div class="text-center">
            <div id="mobile-current-step" class="w-12 h-12 kraftdo-gradient text-white rounded-full flex items-center justify-center text-lg font-bold mx-auto mb-2">
                1
            </div>
            <h4 id="mobile-step-name" class="text-base font-medium text-kraftdo-blue">{{ $steps[1] ?? '' }}</h4>
        </div>
        
        <!-- Step Indicators -->
        <div class="flex justify-center mt-4 space-x-2" id="mobile-step-indicators">
            @for($i = 1; $i <= $totalSteps; $i++)
                <div class="w-3 h-3 {{ $i <= 1 ? 'kraftdo-gradient' : 'bg-gray-200' }} rounded-full wizard-step cursor-pointer" data-step="{{ $i }}" id="mobile-indicator-{{ $i }}"></div>
            @endfor
        </div>
    </div>
</div> 