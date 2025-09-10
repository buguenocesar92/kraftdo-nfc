{{-- Enhanced Token Gift Message Component --}}
@props([
    'contentGift' => null
])

@if($contentGift && $contentGift->message)
    <div class="relative bg-gradient-to-br from-pink-50 via-purple-50 to-indigo-50 rounded-2xl p-6 sm:p-8 mb-8 card-shadow hover:card-float animate-fade-in animate-delay-400 overflow-hidden">
        <!-- Background decoration -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-pink-200/30 to-transparent rounded-full blur-xl"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-purple-200/30 to-transparent rounded-full blur-lg"></div>
        
        <!-- Content -->
        <div class="relative z-10">
            <!-- Enhanced title -->
            <div class="flex items-center gap-3 mb-4">
                <span class="text-3xl icon-bounce">💌</span>
                <h3 class="text-xl sm:text-2xl font-bold text-transparent bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text">
                    Mensaje Personal
                </h3>
            </div>
            
            <!-- Message content with better typography -->
            <div class="relative">
                <p class="text-gray-700 leading-relaxed text-lg sm:text-xl font-medium italic">
                    "{{ $contentGift->message }}"
                </p>
                <!-- Quotation marks decoration -->
                <div class="absolute -top-4 -left-2 text-6xl text-pink-200 font-serif">"</div>
                <div class="absolute -bottom-8 -right-2 text-6xl text-purple-200 font-serif rotate-180">"</div>
            </div>
        </div>
        
        <!-- Border glow effect -->
        <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-pink-400 via-purple-400 to-indigo-400 opacity-20 blur-sm -z-10"></div>
    </div>
@endif