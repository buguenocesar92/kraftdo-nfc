@props(['token', 'content'])

@vite(['resources/css/video-container.css'])
@vite(['resources/js/video-orientation-system.js'])

<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Tipo de Video</label>
        <select name="multimedia[video][type]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" onchange="toggleVideoInputType(this)">
            <option value="">Sin video</option>
            <option value="youtube" {{ (($content->data['multimedia']['video']['type'] ?? '') === 'youtube') ? 'selected' : '' }}>YouTube</option>
            <option value="file_upload" {{ (($content->data['multimedia']['video']['type'] ?? '') === 'file_upload') ? 'selected' : '' }}>Subir Video</option>
        </select>
    </div>
    
    <!-- URL Input (for YouTube) -->
    <div id="video-url-section">
        <label class="block text-sm font-medium text-gray-700">URL del Video</label>
        <input type="url" name="multimedia[video][url]" 
               id="video-url-input"
               value="{{ old('multimedia.video.url', $content->data['multimedia']['video']['url'] ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
               placeholder="https://youtube.com/watch?v=...">
        
        <!-- Mensaje de validación de YouTube -->
        <div id="youtube-validation-message" class="mt-2 hidden">
            <div class="flex items-center p-3 text-sm rounded-lg bg-red-50 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-red-800">
                            <span class="font-medium">❌ URLs de YouTube normal no están permitidas.</span>
                            <br>
                            <span class="text-red-700">Debes usar una URL válida de YouTube (youtube.com, youtu.be, m.youtube.com)</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mensaje de éxito -->
        <div id="youtube-success-message" class="mt-2 hidden">
            <div class="flex items-center p-3 text-sm rounded-lg bg-green-50 border border-green-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-green-800">
                            <span class="font-medium">✅ URL de YouTube válida</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- File Upload Section (for video uploads) -->
    <div id="video-upload-section" class="hidden">
        <label class="block text-sm font-medium text-gray-700 mb-2">Subir Archivo de Video</label>
        
        <!-- Upload Area -->
        <div id="video-upload-area" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors cursor-pointer">
            <div class="space-y-3">
                <div class="flex justify-center">
                    <i class="fas fa-video text-4xl text-gray-400"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">
                        <span class="font-medium text-blue-600 hover:text-blue-500 cursor-pointer">Haz clic para subir</span>
                        o arrastra y suelta
                    </p>
                    <p class="text-xs text-gray-500 mt-1">MP4, MOV, AVI hasta 50MB</p>
                </div>
            </div>
            <input type="file" id="video-file-input" name="video_file" accept=".mp4,.mov,.avi,video/*" class="hidden">
        </div>
        
        <!-- Upload Progress -->
        <div id="video-upload-progress" class="mt-4 hidden">
            <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                <span>Subiendo video...</span>
                <span id="video-progress-text">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="video-progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>

        <!-- Video Preview -->
        <div id="video-preview" class="mt-4 hidden">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-video text-blue-600"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900" id="video-filename">video.mp4</p>
                            <p class="text-xs text-gray-500">Video subido exitosamente</p>
                        </div>
                    </div>
                    <button type="button" onclick="removeVideo()" class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
                <div id="video-container" class="video-container">
                    <video controls preload="metadata" playsinline class="video-player" id="video-player" data-video-enhanced="true">
                        <source src="" type="video/mp4">
                        <source src="" type="video/webm">
                        Tu navegador no soporta el elemento de video.
                    </video>
                </div>
                
                <!-- External video container styles loaded via vite -->
                
                <!-- Video Status Indicator -->
                <div id="video-status" class="mt-2 text-sm text-gray-600 hidden">
                    <div class="flex items-center space-x-2">
                        <div id="video-loading" class="hidden">
                            <i class="fas fa-spinner animate-spin"></i>
                            <span>Cargando video...</span>
                        </div>
                        <div id="video-buffering" class="hidden">
                            <i class="fas fa-hourglass-half animate-pulse"></i>
                            <span>Buffering...</span>
                        </div>
                        <div id="video-error" class="hidden text-red-600">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Error de reproducción</span>
                        </div>
                        <div id="video-ready" class="hidden text-green-600">
                            <i class="fas fa-check-circle"></i>
                            <span>Listo para reproducir</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden input to store uploaded file URL -->
        <input type="hidden" name="multimedia[video][file_url]" id="video-file-url" value="{{ old('multimedia.video.file_url', $content->data['multimedia']['video']['file_url'] ?? $content->data['multimedia']['video']['url'] ?? '') }}">
    </div>
</div>

