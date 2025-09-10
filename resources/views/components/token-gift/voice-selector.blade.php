{{-- Enhanced Voice Selector Component --}}
<div class="text-center mb-6 animate-fade-in" 
     x-data="voiceSelector()" 
     x-init="initVoiceSelector()">
    
    <div class="flex flex-col items-center gap-4">
        <!-- Main Play Button - Enhanced with Accessibility -->
        <button x-on:click="playMessage()" 
                class="group btn-animated inline-flex items-center gap-3 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white font-semibold py-4 px-8 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 relative overflow-hidden pulse-slow focus:outline-none focus:ring-4 focus:ring-purple-300"
                aria-label="Reproducir mensaje personal con texto a voz"
                role="button"
                tabindex="0">
            <svg class="w-6 h-6 icon-bounce group-hover:animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z"/>
            </svg>
            <span class="text-lg">🔊 Reproducir Mensaje</span>
            
            <!-- Shimmer effect -->
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
        </button>
        
        <!-- Voice Selector Toggle - Enhanced with Accessibility -->
        <button x-on:click="showVoiceSelector = !showVoiceSelector"
                class="group inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 underline hover:no-underline bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
                :aria-label="showVoiceSelector ? 'Ocultar opciones de voz' : 'Mostrar opciones de voz'"
                :aria-expanded="showVoiceSelector"
                role="button"
                tabindex="0">
            <svg class="w-4 h-4 icon-rotate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
            <span x-text="showVoiceSelector ? 'Ocultar opciones de voz' : 'Cambiar voz'"></span>
        </button>
        
        <!-- Voice Selector Dropdown - Enhanced Accessibility -->
        <div x-show="showVoiceSelector" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="w-full max-w-md"
             role="region"
             aria-label="Selector de voz para texto a voz"
             id="voice-selector-dropdown">
            
            <div class="bg-white rounded-lg shadow-lg border p-4">
                
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