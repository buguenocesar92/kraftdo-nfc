/**
 * Multimedia Manager - Handles audio, video, and gallery components
 */
class MultimediaManager {
    constructor(eventBus) {
        this.eventBus = eventBus;
        this.components = new Map();
        this.initialized = false;
    }
    
    /**
     * Initialize multimedia components
     */
    init() {
        if (this.initialized) return;
        
        this.initAudioComponents();
        this.initVideoComponents();
        this.initGalleryComponents();
        
        this.initialized = true;
        this.eventBus.emit('multimedia:initialized');
    }
    
    /**
     * Initialize audio components
     */
    initAudioComponents() {
        document.querySelectorAll('select[name="multimedia[audio][type]"]').forEach(select => {
            const audioManager = new AudioComponentManager(select, this.eventBus);
            this.components.set(`audio-${select.id || Date.now()}`, audioManager);
        });
    }
    
    /**
     * Initialize video components
     */
    initVideoComponents() {
        document.querySelectorAll('select[name="multimedia[video][type]"]').forEach(select => {
            const videoManager = new VideoComponentManager(select, this.eventBus);
            this.components.set(`video-${select.id || Date.now()}`, videoManager);
        });
    }
    
    /**
     * Initialize gallery components
     */
    initGalleryComponents() {
        document.querySelectorAll('[id*="gallery"]').forEach(element => {
            if (element.querySelector('input[type="file"]')) {
                const galleryManager = new GalleryComponentManager(element, this.eventBus);
                this.components.set(`gallery-${element.id || Date.now()}`, galleryManager);
            }
        });
    }
    
    /**
     * Get component by ID
     */
    getComponent(id) {
        return this.components.get(id);
    }
    
    /**
     * Validate all multimedia components
     */
    validateAll() {
        let allValid = true;
        const errors = {};
        
        this.components.forEach((component, id) => {
            if (typeof component.validate === 'function') {
                const result = component.validate();
                if (!result.isValid) {
                    allValid = false;
                    errors[id] = result.errors;
                }
            }
        });
        
        return { isValid: allValid, errors };
    }
    
    /**
     * Destroy multimedia manager
     */
    destroy() {
        this.components.forEach(component => {
            if (typeof component.destroy === 'function') {
                component.destroy();
            }
        });
        
        this.components.clear();
        this.initialized = false;
        this.eventBus.emit('multimedia:destroyed');
    }
}

/**
 * Base Multimedia Component Manager
 */
class BaseMultimediaManager {
    constructor(element, eventBus) {
        this.element = element;
        this.eventBus = eventBus;
        this.type = this.getType();
        
        this.init();
    }
    
    /**
     * Initialize component
     */
    init() {
        this.setupEventListeners();
        this.initializeState();
    }
    
    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Override in subclasses
    }
    
    /**
     * Initialize component state
     */
    initializeState() {
        // Override in subclasses
    }
    
    /**
     * Get component type
     */
    getType() {
        return 'base';
    }
    
    /**
     * Validate component
     */
    validate() {
        return { isValid: true, errors: [] };
    }
    
    /**
     * Show/hide sections based on type
     */
    toggleSections(showUpload, showUrl) {
        const uploadSection = this.findSection('upload');
        const urlSection = this.findSection('url');
        
        if (uploadSection) {
            uploadSection.classList.toggle('hidden', !showUpload);
        }
        
        if (urlSection) {
            urlSection.classList.toggle('hidden', !showUrl);
        }
    }
    
    /**
     * Find related section
     */
    findSection(type) {
        const baseId = this.type;
        return document.getElementById(`${baseId}-${type}-section`);
    }
    
    /**
     * Show error message
     */
    showError(message) {
        this.eventBus.emit('notification:show', {
            type: 'error',
            message: message
        });
    }
    
    /**
     * Destroy component
     */
    destroy() {
        // Remove event listeners
    }
}

/**
 * Audio Component Manager
 */
class AudioComponentManager extends BaseMultimediaManager {
    getType() {
        return 'audio';
    }
    
    setupEventListeners() {
        // Type selector change
        this.element.addEventListener('change', (e) => {
            this.handleTypeChange(e.target.value);
        });
        
        // URL input validation
        const urlInput = document.querySelector('input[name="multimedia[audio][url]"]');
        if (urlInput) {
            urlInput.addEventListener('input', (e) => {
                this.validateUrl(e.target.value);
            });
            
            urlInput.addEventListener('change', (e) => {
                this.validateUrl(e.target.value);
            });
        }
        
        // Remove button
        const removeBtn = document.querySelector('[onclick="removeAudio()"]');
        if (removeBtn) {
            removeBtn.onclick = () => this.removeMedia();
        }
    }
    
    initializeState() {
        const currentValue = this.element.value;
        this.handleTypeChange(currentValue);
    }
    
    handleTypeChange(value) {
        switch (value) {
            case '':
                this.toggleSections(false, false);
                break;
            case 'youtube_music':
                this.toggleSections(false, true);
                break;
            case 'file_upload':
                this.toggleSections(true, false);
                break;
        }
        
        this.eventBus.emit('audio:type-changed', { type: value });
    }
    
    validateUrl(url) {
        const errorDiv = document.getElementById('audio-url-error');
        if (!errorDiv || !url) return;
        
        const isYouTubeMusic = url.includes('music.youtube.com') || url.includes('youtu.be');
        const isDirectAudio = this.isDirectAudioUrl(url);
        
        if (!isYouTubeMusic && !isDirectAudio) {
            errorDiv.textContent = 'URL no válida. Usa YouTube Music o archivos de audio directos (.mp3, .wav, etc.)';
            errorDiv.classList.remove('hidden');
        } else {
            errorDiv.classList.add('hidden');
        }
        
        this.eventBus.emit('audio:url-validated', { url, isValid: isYouTubeMusic || isDirectAudio });
    }
    
    isDirectAudioUrl(url) {
        const audioExtensions = ['.mp3', '.wav', '.m4a', '.ogg', '.flac'];
        return audioExtensions.some(ext => url.toLowerCase().includes(ext));
    }
    
    removeMedia() {
        // Clear file input
        const fileInput = document.getElementById('audio-file-input');
        if (fileInput) {
            fileInput.value = '';
        }
        
        // Hide preview
        const preview = document.getElementById('audio-preview');
        if (preview) {
            preview.classList.add('hidden');
        }
        
        // Clear hidden URL input
        const hiddenInput = document.getElementById('audio-file-url');
        if (hiddenInput) {
            hiddenInput.value = '';
        }
        
        this.eventBus.emit('audio:removed');
    }
    
    validate() {
        const type = this.element.value;
        
        if (type === 'file_upload') {
            const preview = document.getElementById('audio-preview');
            const urlInput = document.querySelector('input[name="multimedia[audio][url]"]');
            
            const hasFile = preview && !preview.classList.contains('hidden');
            const hasUrl = urlInput && urlInput.value.trim();
            
            if (!hasFile && !hasUrl) {
                return {
                    isValid: false,
                    errors: ['Debes subir un archivo de audio o proporcionar una URL.']
                };
            }
        }
        
        return { isValid: true, errors: [] };
    }
}

/**
 * Video Component Manager
 */
class VideoComponentManager extends BaseMultimediaManager {
    getType() {
        return 'video';
    }
    
    setupEventListeners() {
        // Type selector change
        this.element.addEventListener('change', (e) => {
            this.handleTypeChange(e.target.value);
        });
        
        // URL input validation
        const urlInput = document.querySelector('input[name="multimedia[video][url]"]');
        if (urlInput) {
            urlInput.addEventListener('input', (e) => {
                this.validateUrl(e.target.value);
            });
        }
        
        // Remove button
        const removeBtn = document.querySelector('[onclick="removeVideo()"]');
        if (removeBtn) {
            removeBtn.onclick = () => this.removeMedia();
        }
    }
    
    initializeState() {
        const currentValue = this.element.value;
        this.handleTypeChange(currentValue);
    }
    
    handleTypeChange(value) {
        switch (value) {
            case '':
                this.toggleSections(false, false);
                break;
            case 'youtube':
                this.toggleSections(false, true);
                break;
            case 'file_upload':
                this.toggleSections(true, false);
                break;
        }
        
        this.eventBus.emit('video:type-changed', { type: value });
    }
    
    validateUrl(url) {
        const errorDiv = document.getElementById('video-url-error');
        if (!errorDiv || !url) return;
        
        const isYouTube = url.includes('youtube.com') || url.includes('youtu.be');
        const isDirectVideo = this.isDirectVideoUrl(url);
        
        if (!isYouTube && !isDirectVideo) {
            errorDiv.textContent = 'URL no válida. Usa YouTube o archivos de video directos (.mp4, .webm, etc.)';
            errorDiv.classList.remove('hidden');
        } else {
            errorDiv.classList.add('hidden');
        }
        
        this.eventBus.emit('video:url-validated', { url, isValid: isYouTube || isDirectVideo });
    }
    
    isDirectVideoUrl(url) {
        const videoExtensions = ['.mp4', '.webm', '.avi', '.mov', '.wmv'];
        return videoExtensions.some(ext => url.toLowerCase().includes(ext));
    }
    
    removeMedia() {
        // Clear file input
        const fileInput = document.getElementById('video-file-input');
        if (fileInput) {
            fileInput.value = '';
        }
        
        // Hide preview
        const preview = document.getElementById('video-preview');
        if (preview) {
            preview.classList.add('hidden');
        }
        
        // Clear hidden URL input
        const hiddenInput = document.getElementById('video-file-url');
        if (hiddenInput) {
            hiddenInput.value = '';
        }
        
        this.eventBus.emit('video:removed');
    }
    
    validate() {
        const type = this.element.value;
        
        if (type === 'file_upload') {
            const preview = document.getElementById('video-preview');
            const urlInput = document.querySelector('input[name="multimedia[video][url]"]');
            
            const hasFile = preview && !preview.classList.contains('hidden');
            const hasUrl = urlInput && urlInput.value.trim();
            
            if (!hasFile && !hasUrl) {
                return {
                    isValid: false,
                    errors: ['Debes subir un archivo de video o proporcionar una URL.']
                };
            }
        }
        
        return { isValid: true, errors: [] };
    }
}

/**
 * Gallery Component Manager
 */
class GalleryComponentManager extends BaseMultimediaManager {
    constructor(element, eventBus) {
        super(element, eventBus);
        this.uploadedImages = [];
    }
    
    getType() {
        return 'gallery';
    }
    
    setupEventListeners() {
        const fileInput = this.element.querySelector('input[type="file"]');
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                this.handleFileSelection(e);
            });
        }
        
        // Setup drag and drop
        this.element.addEventListener('dragover', this.handleDragOver.bind(this));
        this.element.addEventListener('drop', this.handleDrop.bind(this));
    }
    
    handleFileSelection(event) {
        const files = Array.from(event.target.files);
        files.forEach(file => this.addImage(file));
    }
    
    handleDragOver(event) {
        event.preventDefault();
        this.element.classList.add('border-blue-500');
    }
    
    handleDrop(event) {
        event.preventDefault();
        this.element.classList.remove('border-blue-500');
        
        const files = Array.from(event.dataTransfer.files);
        files.forEach(file => this.addImage(file));
    }
    
    addImage(file) {
        if (!this.validateImageFile(file)) {
            return;
        }
        
        const imagePreview = this.createImagePreview(file);
        const previewContainer = document.getElementById('gallery-preview');
        
        if (previewContainer) {
            previewContainer.appendChild(imagePreview);
            previewContainer.classList.remove('hidden');
        }
        
        this.uploadedImages.push(file);
        this.eventBus.emit('gallery:image-added', { file });
    }
    
    validateImageFile(file) {
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!allowedTypes.includes(file.type)) {
            this.showError('Tipo de archivo no permitido. Solo se permiten imágenes (JPG, PNG, GIF).');
            return false;
        }
        
        if (file.size > maxSize) {
            this.showError('Archivo demasiado grande. Máximo 5MB por imagen.');
            return false;
        }
        
        return true;
    }
    
    createImagePreview(file) {
        const container = document.createElement('div');
        container.className = 'relative inline-block m-2';
        
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.className = 'w-24 h-24 object-cover rounded-lg border-2 border-gray-200';
        
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.onclick = () => this.removeImage(container, file);
        
        container.appendChild(img);
        container.appendChild(removeBtn);
        
        return container;
    }
    
    removeImage(container, file) {
        container.remove();
        
        // Remove from uploaded images
        this.uploadedImages = this.uploadedImages.filter(f => f !== file);
        
        // Hide preview container if empty
        const previewContainer = document.getElementById('gallery-preview');
        if (previewContainer && previewContainer.children.length === 0) {
            previewContainer.classList.add('hidden');
        }
        
        this.eventBus.emit('gallery:image-removed', { file });
    }
    
    validate() {
        // Gallery is optional, so always valid
        return { isValid: true, errors: [] };
    }
}

// Make multimedia functions available globally for backward compatibility
window.toggleAudioInputType = function(select) {
    const manager = window.app?.getModule('multimedia');
    const audioManager = manager?.getComponent(`audio-${select.id || 'default'}`);
    if (audioManager) {
        audioManager.handleTypeChange(select.value);
    }
};

window.validateAudioUrl = function(input) {
    const manager = window.app?.getModule('multimedia');
    const audioManager = manager?.getComponent('audio-default');
    if (audioManager) {
        audioManager.validateUrl(input.value);
    }
};

window.removeAudio = function() {
    const manager = window.app?.getModule('multimedia');
    const audioManager = manager?.getComponent('audio-default');
    if (audioManager) {
        audioManager.removeMedia();
    }
};

window.removeVideo = function() {
    const manager = window.app?.getModule('multimedia');
    const videoManager = manager?.getComponent('video-default');
    if (videoManager) {
        videoManager.removeMedia();
    }
};

window.MultimediaManager = MultimediaManager;
window.BaseMultimediaManager = BaseMultimediaManager;
window.AudioComponentManager = AudioComponentManager;
window.VideoComponentManager = VideoComponentManager;
window.GalleryComponentManager = GalleryComponentManager;