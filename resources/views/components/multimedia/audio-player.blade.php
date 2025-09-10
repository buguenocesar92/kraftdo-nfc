{{-- Generic Multimedia Audio Player Component --}}
@props([
    'audio' => null,
    'theme' => [
        'background' => 'from-blue-50 via-purple-50 to-pink-50',
        'primary' => 'blue-500',
        'secondary' => 'purple-600',
        'text' => 'text-gray-600',
        'controls' => 'bg-white bg-opacity-70'
    ],
    'visualization' => 'waveform', // waveform, bars, circle, none
    'showMetadata' => true,
    'size' => 'full' // full, contained
])

@if($audio)
    @php
        $audioId = '';
        $audioSrc = '';
        $audioType = '';
        
        // Determine audio source - accept different input formats
        if (is_object($audio)) {
            // ContentMultimedia object
            if (isset($audio->audio_type)) {
                $audioType = $audio->audio_type;
                if ($audioType === 'file_upload' && isset($audio->audio_file)) {
                    $audioId = 'local_' . md5($audio->audio_file);
                    $audioSrc = asset('storage/' . $audio->audio_file);
                } elseif ($audioType === 'direct' && isset($audio->audio_url)) {
                    $audioId = 'direct_' . md5($audio->audio_url);
                    $audioSrc = $audio->audio_url;
                } elseif (in_array($audioType, ['youtube_music', 'spotify', 'soundcloud']) && isset($audio->audio_url)) {
                    $audioSrc = $audio->audio_url;
                }
            }
        } elseif (is_string($audio)) {
            // Direct URL string
            $audioId = 'direct_' . md5($audio);
            $audioSrc = $audio;
            $audioType = 'direct';
        } elseif (is_array($audio)) {
            // Array with audio data
            $audioId = $audio['id'] ?? 'audio_' . uniqid();
            $audioSrc = $audio['src'] ?? '';
            $audioType = $audio['type'] ?? 'direct';
        }
    @endphp

    @if($audioSrc)
        @if(in_array($audioType, ['youtube_music', 'spotify', 'soundcloud']))
            <!-- External Audio Services -->
            <div class="bg-gradient-to-br {{ $theme['background'] }} rounded-xl p-6">
                <div class="{{ $theme['controls'] }} backdrop-blur-sm rounded-2xl p-6 text-center">
                    <div class="mb-4">
                        <svg class="w-16 h-16 mx-auto text-{{ $theme['primary'] }} mb-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        <h3 class="text-lg font-semibold {{ $theme['text'] }}">Audio Externo</h3>
                    </div>
                    <a href="{{ $audioSrc }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-{{ $theme['primary'] }} to-{{ $theme['secondary'] }} text-white font-semibold rounded-xl hover:shadow-lg transition-all transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        Escuchar en {{ ucfirst(str_replace('_', ' ', $audioType)) }}
                    </a>
                </div>
            </div>
        @else
            <!-- Local/Direct Audio Player -->
            <div class="bg-gradient-to-br {{ $theme['background'] }} rounded-xl p-6" 
                 x-data="multimediaAudioPlayer({
                    audioId: '{{ $audioId }}',
                    audioSrc: '{{ $audioSrc }}',
                    audioType: '{{ $audioType }}',
                    visualization: '{{ $visualization }}',
                    showMetadata: {{ $showMetadata ? 'true' : 'false' }}
                 })">

                <!-- Audio Element -->
                <audio x-ref="audioElement"
                       preload="metadata"
                       :loop="isLooping"
                       :src="audioSrc"
                       style="display: none;">
                    Tu navegador no soporta la reproducción de audio.
                </audio>

                <!-- Main Player Container -->
                <div class="{{ $theme['controls'] }} backdrop-blur-sm rounded-2xl p-6 shadow-lg relative"
                     :class="{
                         'w-screen left-1/2 -translate-x-1/2 sm:left-0 sm:translate-x-0 sm:w-full': '{{ $size }}' === 'full'
                     }">
                    
                    <!-- Error State -->
                    <div x-show="hasError" class="text-center py-8">
                        <div class="text-red-500 mb-4">
                            <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="font-medium">Error al cargar el audio</p>
                            <p class="text-sm text-gray-600">Verifica que el archivo sea válido</p>
                        </div>
                        <button x-on:click="retryAudio()"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Reintentar
                        </button>
                    </div>

                    <!-- Loading State -->
                    <div x-show="isLoading && !hasError" class="text-center py-8">
                        <div class="text-{{ $theme['primary'] }} mb-4">
                            <svg class="w-12 h-12 mx-auto animate-spin mb-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="font-medium">Cargando audio...</p>
                        </div>
                    </div>

                    <!-- Metadata Display -->
                    <div x-show="!isLoading && !hasError && showMetadata && (metadata.title || metadata.artist)" class="mb-4 text-center">
                        <h3 class="font-semibold {{ $theme['text'] }}" x-text="metadata.title"></h3>
                        <p class="text-sm text-gray-500" x-text="metadata.artist"></p>
                    </div>

                    <!-- Visualization -->
                    <div x-show="!isLoading && !hasError && visualization !== 'none'" class="mb-6">
                        <!-- Waveform Display -->
                        <div x-show="visualization === 'waveform'" class="relative h-24 bg-gray-100 rounded-lg overflow-hidden cursor-pointer"
                             x-on:click="seekToPosition($event)">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="flex items-end gap-1 h-16">
                                    <template x-for="(height, index) in waveformData" :key="index">
                                        <div class="bg-gray-300 rounded-full transition-all duration-75"
                                             :class="{ 'bg-{{ $theme['primary'] }}': (index / waveformData.length) <= (currentTime / duration) }"
                                             :style="`width: 3px; height: ${height * 60 + 10}px;`"></div>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Progress Indicator -->
                            <div class="absolute top-0 bottom-0 bg-{{ $theme['primary'] }} bg-opacity-30 transition-all duration-100"
                                 :style="`width: ${duration ? (currentTime / duration) * 100 : 0}%`"></div>
                        </div>

                        <!-- Bars Visualization -->
                        <div x-show="visualization === 'bars'" class="relative h-24 bg-gray-100 rounded-lg overflow-hidden flex items-end justify-center gap-1 p-2">
                            <template x-for="i in 20" :key="i">
                                <div class="bg-gradient-to-t from-{{ $theme['primary'] }} to-{{ $theme['secondary'] }} rounded-t transition-all duration-150"
                                     :class="{ 'animate-pulse': isPlaying }"
                                     :style="`width: 8px; height: ${isPlaying ? Math.random() * 70 + 10 : 20}px;`"></div>
                            </template>
                        </div>

                        <!-- Circle Visualization -->
                        <div x-show="visualization === 'circle'" class="relative h-24 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                            <div class="relative">
                                <div class="w-16 h-16 rounded-full border-4 border-gray-300"></div>
                                <div class="absolute inset-0 w-16 h-16 rounded-full border-4 border-{{ $theme['primary'] }} transition-all duration-1000"
                                     :style="`clip-path: polygon(50% 50%, 50% 0%, ${50 + 50 * Math.cos(2 * Math.PI * (currentTime / duration) - Math.PI/2)}% ${50 + 50 * Math.sin(2 * Math.PI * (currentTime / duration) - Math.PI/2)}%, 50% 50%)`"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <button x-on:click="togglePlay()"
                                            class="w-8 h-8 rounded-full bg-{{ $theme['primary'] }} text-white flex items-center justify-center hover:bg-{{ $theme['secondary'] }} transition-colors">
                                        <svg x-show="!isPlaying" class="w-4 h-4 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        <svg x-show="isPlaying" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div x-show="!isLoading && !hasError" class="mb-4">
                        <div class="relative bg-gray-200 rounded-full h-2 cursor-pointer"
                             x-on:click="seekToPosition($event)">
                            <!-- Buffer Bar -->
                            <div class="absolute top-0 left-0 h-full bg-gray-300 rounded-full transition-all"
                                 :style="`width: ${duration ? (buffered / duration) * 100 : 0}%`"></div>
                            <!-- Progress Bar -->
                            <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-{{ $theme['primary'] }} to-{{ $theme['secondary'] }} rounded-full transition-all"
                                 :style="`width: ${duration ? (currentTime / duration) * 100 : 0}%`"></div>
                            <!-- Progress Handle -->
                            <div class="absolute top-1/2 -translate-y-1/2 w-4 h-4 bg-white border-2 border-{{ $theme['primary'] }} rounded-full shadow-lg opacity-0 hover:opacity-100 transition-opacity"
                                 :style="`left: calc(${duration ? (currentTime / duration) * 100 : 0}% - 8px)`"></div>
                        </div>
                    </div>

                    <!-- Controls -->
                    <div x-show="!isLoading && !hasError" class="flex items-center justify-between">
                        <!-- Left Controls -->
                        <div class="flex items-center gap-3">
                            <!-- Skip Backward -->
                            <button x-on:click="skip(-10)"
                                    class="w-8 h-8 rounded flex items-center justify-center hover:bg-gray-100 transition-colors {{ $theme['text'] }}">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-1-13l-4 4h3v4h2v-4h3l-4-4z"/>
                                </svg>
                            </button>

                            <!-- Play/Pause -->
                            <button x-on:click="togglePlay()"
                                    class="w-12 h-12 rounded-full bg-gradient-to-r from-{{ $theme['primary'] }} to-{{ $theme['secondary'] }} text-white flex items-center justify-center hover:shadow-lg transition-all shadow-lg">
                                <svg x-show="!isPlaying" class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                <svg x-show="isPlaying" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                                </svg>
                            </button>

                            <!-- Skip Forward -->
                            <button x-on:click="skip(10)"
                                    class="w-8 h-8 rounded flex items-center justify-center hover:bg-gray-100 transition-colors {{ $theme['text'] }}">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm1-13v4H8l4 4h3v-4h-3l4-4z"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Center Time Display -->
                        <div class="flex items-center gap-2 text-sm font-mono {{ $theme['text'] }}">
                            <span x-text="formatTime(currentTime)">0:00</span>
                            <span>/</span>
                            <span x-text="formatTime(duration)">0:00</span>
                        </div>

                        <!-- Right Controls -->
                        <div class="flex items-center gap-2">
                            <!-- Loop -->
                            <button x-on:click="toggleLoop()"
                                    :class="{ 'text-{{ $theme['primary'] }}': isLooping, 'text-gray-400': !isLooping }"
                                    class="w-8 h-8 rounded flex items-center justify-center hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M7 7h10v3l4-4-4-4v3H5v6h2V7zm10 10H7v-3l-4 4 4 4v-3h12v-6h-2v4z"/>
                                </svg>
                            </button>

                            <!-- Volume -->
                            <div class="flex items-center gap-2 group">
                                <button x-on:click="toggleMute()"
                                        class="w-8 h-8 rounded flex items-center justify-center hover:bg-gray-100 transition-colors {{ $theme['text'] }}">
                                    <svg x-show="!isMuted && volume > 0.5" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02z"/>
                                    </svg>
                                    <svg x-show="!isMuted && volume <= 0.5 && volume > 0" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.5 12c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM5 9v6h4l5 5V4L9 9H5z"/>
                                    </svg>
                                    <svg x-show="isMuted || volume === 0" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
                                    </svg>
                                </button>
                                <input type="range" 
                                       min="0" 
                                       max="1" 
                                       step="0.1"
                                       :value="volume"
                                       x-on:input="setVolume($event.target.value)"
                                       class="w-16 opacity-60 group-hover:opacity-100 transition-opacity accent-{{ $theme['primary'] }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
@endif