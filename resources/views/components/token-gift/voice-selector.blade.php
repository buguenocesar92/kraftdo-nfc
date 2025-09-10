{{-- Voice Selector Component --}}
<div class="text-center mb-6" 
     x-data="voiceSelector()" 
     x-init="initVoiceSelector()">
    
    <div class="flex flex-col items-center gap-3">
        <!-- Main Play Button -->
        <button x-on:click="playMessage()" 
                class="inline-flex items-center gap-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z"/>
            </svg>
            🔊 Reproducir Mensaje
        </button>
        
        <!-- Voice Selector Toggle -->
        <button x-on:click="showVoiceSelector = !showVoiceSelector"
                class="text-sm text-blue-600 hover:text-blue-800 underline">
            <span x-text="showVoiceSelector ? 'Ocultar opciones de voz' : 'Cambiar voz'"></span>
        </button>
        
        <!-- Voice Selector Dropdown -->
        <div x-show="showVoiceSelector" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="w-full max-w-md">
            
            <div class="bg-white rounded-lg shadow-lg border p-4">
                <h3 class="font-semibold text-gray-700 mb-3">Seleccionar Voz:</h3>
                
                <div class="max-h-32 overflow-y-auto space-y-2">
                    <template x-for="voice in availableVoices" :key="voice.name">
                        <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="radio" 
                                   :name="'voice-selector'"
                                   :value="voice.name"
                                   x-on:change="selectVoice(voice)"
                                   :checked="selectedVoice && selectedVoice.name === voice.name"
                                   class="mr-3 text-blue-600">
                            <div class="flex-1">
                                <div class="font-medium text-sm" x-text="voice.name"></div>
                                <div class="text-xs text-gray-500" x-text="voice.lang + (voice.localService ? ' (Local)' : ' (Remote)')"></div>
                            </div>
                        </label>
                    </template>
                </div>
                
                <!-- Test Voice Button -->
                <button x-on:click="testVoice()"
                        :disabled="!selectedVoice"
                        class="w-full mt-3 py-2 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-sm">
                    🎵 Probar Voz
                </button>
            </div>
        </div>
    </div>
</div>