@props(['content'])

<!-- Imagen -->
<div class="bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200/50">
    <label class="block text-sm font-semibold text-kraftdo-navy mb-3">Imagen (Opcional)</label>
    
    <!-- Tabs for URL vs Upload -->
    <div class="mb-4">
        <div class="flex space-x-2 bg-gradient-to-r from-gray-100 to-gray-200 rounded-xl p-1 shadow-sm">
            <button type="button" id="url-tab" class="flex-1 py-3 px-4 rounded-lg text-sm font-semibold transition-all duration-200 bg-white text-kraftdo-navy shadow-md hover:shadow-lg">
                🔗 URL
            </button>
            <button type="button" id="upload-tab" class="flex-1 py-3 px-4 rounded-lg text-sm font-semibold transition-all duration-200 text-kraftdo-navy/70 hover:text-kraftdo-navy hover:bg-white/50">
                📁 Subir archivo
            </button>
        </div>
    </div>

    <!-- URL Input -->
    <div id="url-section">
        <div class="space-y-3">
            <input type="url" 
                   id="image_url_input"
                   name="image_url" 
                   value="{{ old('image_url', $content->image_url ?? '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green"
                   placeholder="https://ejemplo.com/imagen.jpg">
            <button type="button" id="load-url-btn" class="w-full kraftdo-gradient text-white px-4 sm:px-8 py-3 rounded-xl hover:shadow-lg transition-all duration-200 transform hover:scale-105 font-semibold text-center text-sm sm:text-base">
                Cargar Imagen
            </button>
        </div>
        @error('image_url')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Upload Section -->
    <div id="upload-section" class="hidden">
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
            <input type="file" 
                   id="image_upload" 
                   accept="image/*"
                   name="image_file"
                   class="hidden">
            <div id="upload-area">
                <div class="mb-4">
                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
                </div>
                <p class="text-gray-600 mb-2">Arrastra una imagen aquí o</p>
                <button type="button" 
                        id="select-file-btn"
                        class="kraftdo-gradient text-white px-4 py-2 rounded-lg hover:scale-105 transition-transform duration-200 focus:ring-2 focus:ring-kraftdo-green">
                    Seleccionar archivo
                </button>
                <p class="text-xs text-gray-500 mt-2">Todos los tipos de imagen hasta 5MB</p>
            </div>
            <div id="upload-progress" class="hidden">
                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                    <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p class="text-sm text-gray-600">Subiendo imagen...</p>
            </div>
        </div>
        @error('image_file')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Image Preview -->
    <div id="image-preview" class="mt-4 {{ ($content->image_url ?? old('image_url')) ? '' : 'hidden' }}">
        <label class="block text-sm font-medium text-kraftdo-navy mb-2">Vista previa:</label>
        <div class="relative inline-block">
            <img id="preview-img" 
                 src="{{ old('image_url', $content->image_url ?? '') }}" 
                 alt="Vista previa" 
                 class="max-w-xs max-h-48 rounded-lg border border-gray-300 shadow-sm">
            <button type="button" 
                    id="remove-image"
                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 focus:ring-2 focus:ring-red-500 transition-transform hover:scale-105">
                ✕
            </button>
        </div>
        <p class="text-xs text-gray-500 mt-1">Haz clic en la ✕ para eliminar la imagen</p>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const urlTab = document.getElementById('url-tab');
    const uploadTab = document.getElementById('upload-tab');
    const urlSection = document.getElementById('url-section');
    const uploadSection = document.getElementById('upload-section');
    const imageUrlInput = document.getElementById('image_url_input');
    const loadUrlBtn = document.getElementById('load-url-btn');
    const imageUpload = document.getElementById('image_upload');
    const selectFileBtn = document.getElementById('select-file-btn');
    const uploadArea = document.getElementById('upload-area');
    const uploadProgress = document.getElementById('upload-progress');
    const progressBar = document.getElementById('progress-bar');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const removeImageBtn = document.getElementById('remove-image');

    // Tab switching
    urlTab.addEventListener('click', function() {
        urlTab.classList.add('bg-white', 'text-kraftdo-navy', 'shadow-sm');
        urlTab.classList.remove('text-kraftdo-navy/70', 'hover:text-kraftdo-navy');
        uploadTab.classList.remove('bg-white', 'text-kraftdo-navy', 'shadow-sm');
        uploadTab.classList.add('text-kraftdo-navy/70', 'hover:text-kraftdo-navy');
        
        urlSection.classList.remove('hidden');
        uploadSection.classList.add('hidden');
    });

    uploadTab.addEventListener('click', function() {
        uploadTab.classList.add('bg-white', 'text-kraftdo-navy', 'shadow-sm');
        uploadTab.classList.remove('text-kraftdo-navy/70', 'hover:text-kraftdo-navy');
        urlTab.classList.remove('bg-white', 'text-kraftdo-navy', 'shadow-sm');
        urlTab.classList.add('text-kraftdo-navy/70', 'hover:text-kraftdo-navy');
        
        uploadSection.classList.remove('hidden');
        urlSection.classList.add('hidden');
    });

    // Load image from URL
    loadUrlBtn.addEventListener('click', function() {
        const url = imageUrlInput.value.trim();
        if (url) {
            loadImagePreview(url);
        }
    });

    imageUrlInput.addEventListener('blur', function() {
        const url = this.value.trim();
        if (url) {
            loadImagePreview(url);
        }
    });

    // File selection
    selectFileBtn.addEventListener('click', function() {
        imageUpload.click();
    });

    // File upload handling
    imageUpload.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            handleFileUpload(file);
        }
    });

    // Drag and drop
    const uploadDropArea = uploadSection.querySelector('.border-dashed');
    uploadDropArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-blue-400', 'bg-blue-50');
    });

    uploadDropArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-400', 'bg-blue-50');
    });

    uploadDropArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-400', 'bg-blue-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            if (file.type.startsWith('image/')) {
                handleFileUpload(file);
            } else {
                alert('Por favor, selecciona solo archivos de imagen.');
            }
        }
    });

    // Remove image
    removeImageBtn.addEventListener('click', function() {
        removeImage();
    });

    // Functions
    function loadImagePreview(url) {
        const img = new Image();
        img.onload = function() {
            previewImg.src = url;
            imagePreview.classList.remove('hidden');
            imageUpload.value = '';
        };
        img.onerror = function() {
            alert('No se pudo cargar la imagen desde la URL proporcionada.');
        };
        img.src = url;
    }

    function handleFileUpload(file) {
        if (file.size > 5 * 1024 * 1024) {
            alert('El archivo es demasiado grande. El tamaño máximo es 5MB.');
            return;
        }

        if (!file.type.startsWith('image/')) {
            alert('Por favor, selecciona solo archivos de imagen.');
            return;
        }

        uploadArea.classList.add('hidden');
        uploadProgress.classList.remove('hidden');

        const formData = new FormData();
        formData.append('image', file);
        
        const csrfToken = getCsrfToken();
        if (!csrfToken) {
            alert('Error: No se pudo obtener el token CSRF. Recarga la página e intenta de nuevo.');
            return;
        }
        formData.append('_token', csrfToken);
        
        console.log('CSRF Token:', csrfToken); // Debug

        fetch('{{ route("my-tokens.upload-image") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status); // Debug
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            progressBar.style.width = '100%';
            setTimeout(() => {
                if (data.success) {
                    previewImg.src = data.url;
                    imagePreview.classList.remove('hidden');
                    imageUrlInput.value = data.url;
                } else {
                    alert('Error al subir la imagen: ' + (data.message || 'Error desconocido'));
                }
            }, 500);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al subir la imagen.');
        })
        .finally(() => {
            uploadProgress.classList.add('hidden');
            uploadArea.classList.remove('hidden');
            progressBar.style.width = '0%';
        });
    }

    function removeImage() {
        imagePreview.classList.add('hidden');
        previewImg.src = '';
        imageUrlInput.value = '';
        imageUpload.value = '';
    }
});
</script>
@endpush 