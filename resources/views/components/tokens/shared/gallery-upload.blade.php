@props(['token', 'content'])

<!-- Panel de Galería (Solo para PROFILE) -->
<div id="multimedia-panel" class="mt-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
    <div class="space-y-6">
        <div>
            <h4 class="text-lg font-medium text-gray-800 mb-4">📸 Galería de Fotos (6 máximo)</h4>
            <p class="text-sm text-gray-600 mb-6">Sube hasta 6 fotos para crear una hermosa galería de recuerdos.</p>
        </div>

        <!-- Grid de 6 fotos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @for($i = 0; $i < 6; $i++)
                @php
                    $photoData = null;
                    if (isset($content->data['multimedia']['gallery'][$i])) {
                        $photoData = $content->data['multimedia']['gallery'][$i];
                        $photoUrl = is_array($photoData) ? ($photoData['url'] ?? '') : $photoData;
                    } else {
                        $photoUrl = '';
                    }
                @endphp
                
                <div class="gallery-photo-slot" data-index="{{ $i }}">
                    <div class="bg-white border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-blue-400 transition-colors">
                        <div class="text-center">
                            <div class="mb-3">
                                <span class="text-sm font-medium text-gray-700">Foto {{ $i + 1 }}</span>
                            </div>
                            
                            <!-- Upload Area -->
                            <div class="gallery-upload-area cursor-pointer" onclick="document.getElementById('gallery-file-{{ $i }}').click()">
                                <div class="gallery-placeholder {{ $photoUrl ? 'hidden' : '' }}">
                                    <i class="fas fa-camera text-3xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-500">Haz clic para subir</p>
                                    <p class="text-xs text-gray-400 mt-1">JPG, PNG (máx 5MB)</p>
                                </div>
                                
                                <!-- Preview Image -->
                                <div class="gallery-preview {{ $photoUrl ? '' : 'hidden' }}">
                                    <img src="{{ $photoUrl }}" alt="Preview" class="w-full h-32 object-cover rounded-lg">
                                    <div class="mt-2 flex justify-center space-x-2">
                                        <button type="button" class="text-kraftdo-blue hover:text-kraftdo-green text-xs font-medium transition-colors" onclick="document.getElementById('gallery-file-{{ $i }}').click(); event.stopPropagation();">
                                            <i class="fas fa-edit"></i> Cambiar
                                        </button>
                                        <button type="button" class="text-red-600 hover:text-red-800 text-xs font-medium transition-colors" onclick="clearGallerySlot({{ $i }}); event.stopPropagation();">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden file input -->
                            <input type="file" id="gallery-file-{{ $i }}" accept="image/*" class="hidden" onchange="handleGalleryPhotoUpload({{ $i }}, this)">
                            
                            <!-- URL input (hidden, stores the actual value) -->
                            <input type="hidden" name="multimedia[gallery][{{ $i }}]" id="gallery-url-{{ $i }}" value="{{ $photoUrl }}">
                            
                            <!-- Upload progress -->
                            <div class="gallery-upload-progress hidden mt-2">
                                <div class="bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">Subiendo...</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>

@push('scripts')
<script>
// New Gallery System - 6 Fixed Photo Slots
window.handleGalleryPhotoUpload = function(slotIndex, fileInput) {
    const file = fileInput.files[0];
    if (!file) return;

    console.log(`Gallery upload started for slot ${slotIndex}, file:`, file.name);
    
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        alert('El archivo es muy grande. Máximo 5MB permitidos.');
        fileInput.value = ''; // Clear the input
        return;
    }

    // Validate file type
    if (!file.type.startsWith('image/')) {
        alert('Solo se permiten archivos de imagen.');
        fileInput.value = ''; // Clear the input
        return;
    }

    const slot = document.querySelector(`[data-index="${slotIndex}"]`);
    const progressDiv = slot.querySelector('.gallery-upload-progress');
    const progressBar = progressDiv.querySelector('div > div');
    const placeholderDiv = slot.querySelector('.gallery-placeholder');
    const previewDiv = slot.querySelector('.gallery-preview');
    const urlInput = document.getElementById(`gallery-url-${slotIndex}`);

    // Show upload progress
    placeholderDiv.classList.add('hidden');
    previewDiv.classList.add('hidden');
    progressDiv.classList.remove('hidden');

    // Create form data
    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', getCsrfToken());

    // Upload to server
    fetch('{{ route("my-tokens.upload-image") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log(`Gallery upload response for slot ${slotIndex}:`, data);
        
        // Hide progress
        progressDiv.classList.add('hidden');
        
        if (data.success) {
            // Update URL input (this is what gets sent to server)
            urlInput.value = data.url;
            
            // Update preview
            const previewImg = previewDiv.querySelector('img');
            previewImg.src = data.url;
            previewDiv.classList.remove('hidden');
            
            console.log(`Slot ${slotIndex} updated with URL:`, data.url);
        } else {
            console.error(`Gallery upload failed for slot ${slotIndex}:`, data.message);
            alert('Error subiendo imagen: ' + (data.message || 'Error desconocido'));
            placeholderDiv.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error(`Gallery upload error for slot ${slotIndex}:`, error);
        alert('Error subiendo imagen. Por favor intenta de nuevo.');
        progressDiv.classList.add('hidden');
        placeholderDiv.classList.remove('hidden');
    });
};

window.clearGallerySlot = function(slotIndex) {
    const urlInput = document.getElementById(`gallery-url-${slotIndex}`);
    const fileInput = document.getElementById(`gallery-file-${slotIndex}`);
    const slot = document.querySelector(`[data-index="${slotIndex}"]`);
    const placeholderDiv = slot.querySelector('.gallery-placeholder');
    const previewDiv = slot.querySelector('.gallery-preview');

    // Clear values
    urlInput.value = '';
    fileInput.value = '';

    // Update UI
    previewDiv.classList.add('hidden');
    placeholderDiv.classList.remove('hidden');

    console.log(`Gallery slot ${slotIndex} cleared`);
};

// Debug form submission for new gallery system
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('=== FORM SUBMISSION DEBUG ===');
            for (let i = 0; i < 6; i++) {
                const input = document.getElementById(`gallery-url-${i}`);
                console.log(`Gallery slot ${i}:`, input ? input.value : 'Input not found');
            }
        });
    }
});
</script>
@endpush 