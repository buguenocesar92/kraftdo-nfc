{{-- Custom Audio Controls for File Upload --}}
<div class="flex items-center justify-center space-x-4 mb-4">
    <div class="bg-gradient-to-r from-pink-500 to-rose-600 rounded-full p-4 shadow-lg">
        <button id="main-audio-toggle" class="text-white hover:text-pink-100 transition-colors">
            <i id="main-audio-icon" class="fas fa-play text-2xl"></i>
        </button>
    </div>
    
    <div id="audio-equalizer" class="hidden">
        <div class="flex items-center space-x-1">
            <div class="w-2 bg-pink-500 rounded-full animate-bounce" style="height: 24px;"></div>
            <div class="w-2 bg-rose-500 rounded-full animate-bounce" style="height: 18px; animation-delay: 0.1s;"></div>
            <div class="w-2 bg-pink-500 rounded-full animate-bounce" style="height: 30px; animation-delay: 0.2s;"></div>
            <div class="w-2 bg-rose-500 rounded-full animate-bounce" style="height: 22px; animation-delay: 0.3s;"></div>
            <div class="w-2 bg-pink-500 rounded-full animate-bounce" style="height: 26px; animation-delay: 0.4s;"></div>
        </div>
    </div>
</div>

<div class="text-center">
    <p class="text-sm text-pink-700 font-medium" id="audio-status">Haz clic para reproducir</p>
</div>

<!-- Hidden Audio Element -->
<audio id="background-audio" preload="auto" loop src="{{ $audio['url'] ?? '' }}" class="hidden"></audio>