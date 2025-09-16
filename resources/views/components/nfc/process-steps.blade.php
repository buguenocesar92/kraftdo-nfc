@props([
    'steps' => [],
    'animated' => true,
    'currentStep' => 1
])

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8" 
     x-data="kraftdoProcessSteps({{ $currentStep }})" 
     @if($animated) x-intersect="animateSteps()" @endif>
    
    @if(!empty($steps))
        @foreach($steps as $index => $step)
            @php $stepNumber = $index + 1; @endphp
            <div class="kraftdo-glass rounded-xl p-6 border border-white/20 text-center kraftdo-step-item relative"
                 @if($animated) style="opacity: 0; transform: scale(0.8);" @endif
                 data-step="{{ $stepNumber }}"
                 :class="currentStep >= {{ $stepNumber }} ? 'border-green-400/50 bg-green-500/10' : ''">
                
                <!-- Step Number -->
                <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3 transition-all duration-500"
                     :class="currentStep >= {{ $stepNumber }} ? 
                             'bg-gradient-to-br from-green-500 to-lime-500 kraftdo-animate-pulse-slow' : 
                             'bg-gradient-to-br {{ $step['gradient'] ?? 'from-navy-500 to-blue-500' }}'">
                    
                    <!-- Check icon for completed steps -->
                    <template x-if="currentStep > {{ $stepNumber }}">
                        <i class="fas fa-check text-white font-bold text-xl"></i>
                    </template>
                    
                    <!-- Step number for current/upcoming steps -->
                    <template x-if="currentStep <= {{ $stepNumber }}">
                        <span class="text-white font-bold text-xl">{{ $stepNumber }}</span>
                    </template>
                </div>
                
                <!-- Step Content -->
                <h4 class="font-bold text-white mb-2">{{ $step['title'] }}</h4>
                <p class="text-sm text-white/70">{{ $step['description'] }}</p>
                
                <!-- Active Step Indicator -->
                <template x-if="currentStep === {{ $stepNumber }}">
                    <div class="absolute top-2 right-2">
                        <div class="w-3 h-3 bg-yellow-400 rounded-full kraftdo-animate-pulse-slow"></div>
                    </div>
                </template>
                
                <!-- Connection Line (except for last step) -->
                @if($stepNumber < count($steps))
                    <div class="hidden sm:block absolute top-1/2 -right-2 w-4 h-0.5 bg-gradient-to-r from-white/30 to-transparent transform -translate-y-1/2 z-10"></div>
                @endif
            </div>
        @endforeach
    @else
        {{ $slot }}
    @endif
</div>

<script>
    function kraftdoProcessSteps(initialStep = 1) {
        return {
            currentStep: initialStep,
            
            animateSteps() {
                const items = this.$el.querySelectorAll('.kraftdo-step-item');
                items.forEach((item, index) => {
                    setTimeout(() => {
                        item.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                        item.style.opacity = '1';
                        item.style.transform = 'scale(1)';
                    }, index * 200);
                });
            },
            
            setCurrentStep(step) {
                this.currentStep = step;
            },
            
            nextStep() {
                const maxSteps = this.$el.querySelectorAll('.kraftdo-step-item').length;
                if (this.currentStep < maxSteps) {
                    this.currentStep++;
                }
            },
            
            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                }
            }
        }
    }
</script>