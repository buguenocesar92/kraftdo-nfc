@props(['token', 'content'])

<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Tipo de Audio</label>
        <select name="multimedia[audio][type]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" onchange="toggleAudioInputType(this)">
            <option value="">Sin audio</option>
            <option value="youtube_music" {{ (($content->data['multimedia']['audio']['type'] ?? '') === 'youtube_music') ? 'selected' : '' }}>YouTube Music</option>
            <option value="file_upload" {{ (($content->data['multimedia']['audio']['type'] ?? '') === 'file_upload') ? 'selected' : '' }}>Subir Archivo</option>
        </select>
    </div>
    
    <!-- URL Input (for Spotify/YouTube Music) -->
    <div id="audio-url-section">
        <label class="block text-sm font-medium text-gray-700">URL del Audio</label>
        <input type="url" name="multimedia[audio][url]" 
               id="audio-url-input"
               value="{{ old('multimedia.audio.url', $content->data['multimedia']['audio']['url'] ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
               placeholder="https://music.youtube.com/watch?v=... o archivos de audio directos"
               onchange="validateAudioUrl(this)"
               oninput="validateAudioUrl(this)">
        
        <!-- Mensaje de error (JavaScript) -->
        <div id="audio-url-error" class="mt-2 text-sm text-red-600 hidden"></div>
        
        <!-- Mensaje de error (Laravel) -->
        @error('multimedia.audio.url')
            <div class="mt-2 text-sm text-red-600 bg-red-50 border border-red-200 rounded p-2">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- File Upload Section (for file uploads) -->
    <div id="audio-upload-section" class="hidden">
        <label class="block text-sm font-medium text-gray-700 mb-2">Subir Archivo de Audio</label>
        
        <!-- Upload Area -->
        <div id="audio-upload-area" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors cursor-pointer">
            <div class="space-y-3">
                <div class="flex justify-center">
                    <i class="fas fa-music text-4xl text-gray-400"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">
                        <span class="font-medium text-blue-600 hover:text-blue-500 cursor-pointer">Haz clic para subir</span>
                        o arrastra y suelta
                    </p>
                    <p class="text-xs text-gray-500 mt-1">MP3, WAV, M4A hasta 10MB</p>
                </div>
            </div>
            <input type="file" id="audio-file-input" name="audio_file" accept=".mp3,.wav,.m4a,audio/*" class="hidden">
        </div>
        
        <!-- Upload Progress -->
        <div id="audio-upload-progress" class="mt-4 hidden">
            <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                <span>Subiendo audio...</span>
                <span id="audio-progress-text">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="audio-progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>

        <!-- Audio Preview -->
        <div id="audio-preview" class="mt-4 hidden">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-music text-blue-600"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900" id="audio-filename">audio.mp3</p>
                            <p class="text-xs text-gray-500">Audio subido exitosamente</p>
                        </div>
                    </div>
                    <button type="button" onclick="removeAudio()" class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
                <audio controls class="w-full mt-3" id="audio-player">
                    Tu navegador no soporta el elemento de audio.
                </audio>
            </div>
        </div>

        <!-- Hidden input to store uploaded file URL -->
        <input type="hidden" name="multimedia[audio][file_url]" id="audio-file-url" value="{{ old('multimedia.audio.file_url', $content->data['multimedia']['audio']['file_url'] ?? $content->data['multimedia']['audio']['url'] ?? '') }}">
    </div>
</div>

