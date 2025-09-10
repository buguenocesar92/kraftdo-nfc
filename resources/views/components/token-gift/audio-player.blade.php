{{-- Advanced Token Gift Audio Player Component --}}
@props([
    'contentMultimedia' => null
])

@if($contentMultimedia && ($contentMultimedia->audio_url || $contentMultimedia->audio_file))
    @php
        $audioId = '';
        $audioSrc = '';
        $audioType = $contentMultimedia->audio_type ?? 'file_upload';
        
        if ($audioType === 'file_upload' && $contentMultimedia->audio_file) {
            $audioId = 'local_' . md5($contentMultimedia->audio_file);
            $audioSrc = asset('storage/' . $contentMultimedia->audio_file);
        } elseif ($audioType === 'direct' && $contentMultimedia->audio_url) {
            $audioId = 'direct_' . md5($contentMultimedia->audio_url);
            $audioSrc = $contentMultimedia->audio_url;
        }
    @endphp

    @if($audioSrc)
        <div class="bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 rounded-xl p-6" 
             x-data="{
                audioId: '{{ $audioId }}',
                audioSrc: '{{ $audioSrc }}',
                audioType: '{{ $audioType }}',
                isPlaying: false,
                isLoading: true,
                hasError: false,
                currentTime: 0,
                duration: 0,
                buffered: 0,
                volume: 1,
                isMuted: false,
                playbackRate: 1,
                isLooping: false,
                waveformData: [],
                metadata: {
                    title: '',
                    artist: '',
                    album: '',
                    artwork: null
                },
                visualMode: 'waveform', // 'waveform', 'bars', 'circle'
                
                // Methods
                togglePlay() {
                    const audio = this.$refs.audioElement;
                    console.log('togglePlay called', { audio, isPlaying: this.isPlaying, src: audio?.src });
                    
                    if (!audio) {
                        console.error('Audio element not found');
                        return;
                    }
                    
                    if (this.isPlaying) {
                        audio.pause();
                    } else {
                        // Ensure audio source is set
                        if (!audio.src) {
                            console.log('Setting audio src:', this.audioSrc);
                            audio.src = this.audioSrc;
                        }
                        
                        this.isLoading = true;
                        audio.play()
                            .then(() => {
                                console.log('Audio started playing successfully');
                                this.isLoading = false;
                            })
                            .catch(e => {
                                console.error('Play failed:', e);
                                this.isLoading = false;
                                this.hasError = true;
                            });
                    }
                },
                
                skip(seconds) {
                    const audio = this.$refs.audioElement;
                    audio.currentTime = Math.max(0, Math.min(this.duration, audio.currentTime + seconds));
                },
                
                seekToPosition(event) {
                    const audio = this.$refs.audioElement;
                    const rect = event.currentTarget.getBoundingClientRect();
                    const clickX = event.clientX - rect.left;
                    const progress = clickX / rect.width;
                    audio.currentTime = progress * this.duration;
                },
                
                setVolume(value) {
                    const audio = this.$refs.audioElement;
                    this.volume = parseFloat(value);
                    audio.volume = this.volume;
                    if (this.volume > 0) {
                        audio.muted = false;
                        this.isMuted = false;
                    }
                },
                
                toggleMute() {
                    const audio = this.$refs.audioElement;
                    audio.muted = !audio.muted;
                    this.isMuted = audio.muted;
                },
                
                setPlaybackRate(rate) {
                    const audio = this.$refs.audioElement;
                    audio.playbackRate = rate;
                    this.playbackRate = rate;
                },
                
                formatTime(seconds) {
                    if (isNaN(seconds)) return '0:00';
                    const minutes = Math.floor(seconds / 60);
                    const secs = Math.floor(seconds % 60);
                    return minutes + ':' + secs.toString().padStart(2, '0');
                },
                
                generateWaveform() {
                    // Generate mock waveform data
                    this.waveformData = Array.from({length: 60}, () => Math.random());
                },
                
                updateWaveformProgress() {
                    // Update waveform visualization based on current time
                },
                
                loadAudioMetadata() {
                    // Try to load audio metadata if available
                    if (this.audioSrc.includes('.mp3') || this.audioSrc.includes('.wav')) {
                        // Could integrate with a metadata extraction library
                    }
                }
             }"
             x-init="
                console.log('Audio component initializing', { audioSrc: audioSrc });
                $nextTick(() => {
                    const audio = $refs.audioElement;
                    console.log('Audio element found:', audio);
                    if (audio) {
                        // Load audio metadata
                        audio.addEventListener('loadedmetadata', () => {
                            duration = audio.duration;
                            isLoading = false;
                            
                            // Try to extract metadata
                            if (audio.title) metadata.title = audio.title;
                            this.loadAudioMetadata();
                        });
                        
                        audio.addEventListener('timeupdate', () => {
                            currentTime = audio.currentTime;
                            this.updateWaveformProgress();
                        });
                        
                        audio.addEventListener('play', () => {
                            isPlaying = true;
                        });
                        
                        audio.addEventListener('pause', () => {
                            isPlaying = false;
                        });
                        
                        audio.addEventListener('ended', () => {
                            isPlaying = false;
                            if (!isLooping) {
                                currentTime = 0;
                            }
                        });
                        
                        audio.addEventListener('volumechange', () => {
                            volume = audio.volume;
                            isMuted = audio.muted;
                        });
                        
                        audio.addEventListener('progress', () => {
                            if (audio.buffered.length > 0) {
                                buffered = audio.buffered.end(audio.buffered.length - 1);
                            }
                        });
                        
                        audio.addEventListener('error', () => {
                            hasError = true;
                            isLoading = false;
                        });
                        
                        audio.addEventListener('loadstart', () => {
                            isLoading = true;
                            hasError = false;
                        });
                        
                        // Generate waveform visualization
                        this.generateWaveform();
                    }
                });
             ">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                            <span>Audio Player</span>
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full capitalize">{{ $audioType }}</span>
                        </h3>
                        <div class="text-sm text-gray-600" x-show="metadata.title">
                            <span x-text="metadata.title"></span>
                            <span x-show="metadata.artist" x-text="' - ' + metadata.artist"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Visualization Mode Toggle -->
                <div class="flex items-center gap-2">
                    <button x-on:click="visualMode = visualMode === 'waveform' ? 'bars' : visualMode === 'bars' ? 'circle' : 'waveform'"
                            class="p-2 rounded-lg bg-white bg-opacity-60 hover:bg-opacity-80 transition-all"
                            :title="'Modo: ' + visualMode">
                        <svg x-show="visualMode === 'waveform'" class="w-5 h-5 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 9v6h4l5 5V4L7 9H3z"/>
                        </svg>
                        <svg x-show="visualMode === 'bars'" class="w-5 h-5 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6 2h2v20H6V2zm4 4h2v16h-2V6zm4-2h2v18h-2V4zm4 6h2v8h-2v-8z"/>
                        </svg>
                        <svg x-show="visualMode === 'circle'" class="w-5 h-5 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Audio Element -->
            <audio x-ref="audioElement"
                   preload="metadata"
                   :loop="isLooping"
                   :src="audioSrc"
                   style="display: none;">
                Tu navegador no soporta la reproducción de audio.
            </audio>

            <!-- Main Player Container -->
            <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 shadow-lg">
                
                <!-- Error State -->
                <div x-show="hasError" class="text-center py-8">
                    <div class="text-red-500 mb-4">
                        <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="font-medium">Error al cargar el audio</p>
                        <p class="text-sm text-gray-600">Verifica que el archivo sea válido</p>
                    </div>
                    <button x-on:click="$refs.audioElement.load(); hasError = false; isLoading = true;"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Reintentar
                    </button>
                </div>

                <!-- Loading State -->
                <div x-show="isLoading && !hasError" class="text-center py-8">
                    <div class="text-blue-500 mb-4">
                        <svg class="w-12 h-12 mx-auto animate-spin mb-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="font-medium">Cargando audio...</p>
                    </div>
                </div>

                <!-- Waveform Visualization -->
                <div x-show="!isLoading && !hasError" class="mb-6">
                    <!-- Waveform Display -->
                    <div x-show="visualMode === 'waveform'" class="relative h-24 bg-gray-100 rounded-lg overflow-hidden cursor-pointer"
                         x-on:click="seekToPosition($event)">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="flex items-end gap-1 h-16" x-ref="waveformContainer">
                                <!-- Waveform bars will be generated here -->
                                <template x-for="i in 60" :key="i">
                                    <div class="bg-gray-300 rounded-full transition-all duration-75"
                                         :class="{ 'bg-blue-500': (i / 60) <= (currentTime / duration) }"
                                         :style="`width: 3px; height: ${Math.random() * 60 + 10}px;`"></div>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Progress Indicator -->
                        <div class="absolute top-0 bottom-0 bg-blue-500 bg-opacity-30 transition-all duration-100"
                             :style="`width: ${duration ? (currentTime / duration) * 100 : 0}%`"></div>
                    </div>

                    <!-- Bars Visualization -->
                    <div x-show="visualMode === 'bars'" class="relative h-24 bg-gray-100 rounded-lg overflow-hidden flex items-end justify-center gap-1 p-2">
                        <template x-for="i in 20" :key="i">
                            <div class="bg-gradient-to-t from-blue-500 to-purple-500 rounded-t transition-all duration-150"
                                 :class="{ 'animate-pulse': isPlaying }"
                                 :style="`width: 8px; height: ${isPlaying ? Math.random() * 70 + 10 : 20}px;`"></div>
                        </template>
                    </div>

                    <!-- Circle Visualization -->
                    <div x-show="visualMode === 'circle'" class="relative h-24 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                        <div class="relative">
                            <div class="w-16 h-16 rounded-full border-4 border-gray-300"></div>
                            <div class="absolute inset-0 w-16 h-16 rounded-full border-4 border-blue-500 transition-all duration-1000"
                                 :style="`clip-path: polygon(50% 50%, 50% 0%, ${50 + 50 * Math.cos(2 * Math.PI * (currentTime / duration) - Math.PI/2)}% ${50 + 50 * Math.sin(2 * Math.PI * (currentTime / duration) - Math.PI/2)}%, 50% 50%)`"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <button x-on:click="togglePlay()"
                                        class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center hover:bg-blue-600 transition-colors">
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
                        <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-blue-500 to-purple-600 rounded-full transition-all"
                             :style="`width: ${duration ? (currentTime / duration) * 100 : 0}%`"></div>
                        <!-- Progress Handle -->
                        <div class="absolute top-1/2 -translate-y-1/2 w-4 h-4 bg-white border-2 border-blue-500 rounded-full shadow-lg opacity-0 hover:opacity-100 transition-opacity"
                             :style="`left: calc(${duration ? (currentTime / duration) * 100 : 0}% - 8px)`"></div>
                    </div>
                </div>

                <!-- Controls -->
                <div x-show="!isLoading && !hasError" class="flex items-center justify-between">
                    <!-- Left Controls -->
                    <div class="flex items-center gap-3">
                        <!-- Play/Pause -->
                        <button x-on:click="togglePlay()"
                                class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 text-white flex items-center justify-center hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg">
                            <svg x-show="!isPlaying" class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                            <svg x-show="isPlaying" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                            </svg>
                        </button>

                        <!-- Skip Back -->
                        <button x-on:click="skip(-10)"
                                class="w-10 h-10 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11 18V6l-8.5 6 8.5 6zm.5-6l8.5 6V6l-8.5 6z"/>
                            </svg>
                        </button>

                        <!-- Skip Forward -->
                        <button x-on:click="skip(10)"
                                class="w-10 h-10 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 18l8.5-6L4 6v12zm9-12v12l8.5-6L13 6z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Center Time Display -->
                    <div class="flex items-center gap-2 text-sm font-mono text-gray-600">
                        <span x-text="formatTime(currentTime)">0:00</span>
                        <span>/</span>
                        <span x-text="formatTime(duration)">0:00</span>
                    </div>

                    <!-- Right Controls -->
                    <div class="flex items-center gap-2">
                        <!-- Loop -->
                        <button x-on:click="isLooping = !isLooping; $refs.audioElement.loop = isLooping"
                                :class="{ 'text-blue-500': isLooping, 'text-gray-400': !isLooping }"
                                class="w-8 h-8 rounded flex items-center justify-center hover:bg-gray-100 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7 7h10v3l4-4-4-4v3H5v6h2V7zm10 10H7v-3l-4 4 4 4v-3h12v-6h-2v4z"/>
                            </svg>
                        </button>

                        <!-- Volume -->
                        <div class="flex items-center gap-2 group">
                            <button x-on:click="toggleMute()"
                                    class="w-8 h-8 rounded flex items-center justify-center hover:bg-gray-100 transition-colors">
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
                                   class="w-16 opacity-0 group-hover:opacity-100 transition-opacity accent-blue-500">
                        </div>

                        <!-- Speed -->
                        <div class="relative group">
                            <button class="px-2 py-1 text-xs rounded hover:bg-gray-100 transition-colors"
                                    x-text="playbackRate === 1 ? '1x' : playbackRate + 'x'">1x</button>
                            <div class="absolute bottom-full mb-2 right-0 bg-black bg-opacity-90 rounded-lg p-2 opacity-0 group-hover:opacity-100 transition-opacity min-w-max">
                                <div class="space-y-1">
                                    <template x-for="rate in [0.5, 0.75, 1, 1.25, 1.5, 2]">
                                        <button x-on:click="setPlaybackRate(rate)"
                                                class="block w-full text-left px-3 py-1 text-sm text-white hover:bg-white hover:bg-opacity-20 rounded"
                                                :class="{ 'bg-blue-600': playbackRate === rate }"
                                                x-text="rate + 'x'">
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif(in_array($audioType, ['youtube_music', 'spotify', 'soundcloud']) && $contentMultimedia->audio_url)
        <!-- External Audio Services -->
        <div class="bg-gradient-to-br from-green-50 via-blue-50 to-purple-50 rounded-xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-800">🎵 Audio Externo</h3>
                    <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $audioType)) }}</p>
                </div>
            </div>
            
            <div class="bg-white bg-opacity-70 backdrop-blur-sm rounded-2xl p-6 text-center">
                <a href="{{ $contentMultimedia->audio_url }}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-500 to-blue-500 text-white font-semibold rounded-xl hover:from-green-600 hover:to-blue-600 transition-all transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    Escuchar en {{ ucfirst(str_replace('_', ' ', $audioType)) }}
                </a>
            </div>
        </div>
    @endif
@endif