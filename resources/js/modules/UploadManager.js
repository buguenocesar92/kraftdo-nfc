/**
 * Upload Manager - Factory Pattern for different upload types
 */
class UploadManager {
    constructor(eventBus, ajaxService) {
        this.eventBus = eventBus;
        this.ajaxService = ajaxService;
        this.uploaders = new Map();
        this.activeUploads = new Map();
        
        this.initUploaders();
    }
    
    /**
     * Initialize upload types
     */
    initUploaders() {
        this.uploaders.set('image', new ImageUploader(this.eventBus, this.ajaxService));
        this.uploaders.set('audio', new AudioUploader(this.eventBus, this.ajaxService));
        this.uploaders.set('video', new VideoUploader(this.eventBus, this.ajaxService));
        this.uploaders.set('gallery', new GalleryUploader(this.eventBus, this.ajaxService));
    }
    
    /**
     * Initialize upload areas
     */
    init() {
        console.log('🔍 UploadManager initializing, looking for upload areas...');
        
        // Find upload areas and initialize them
        const uploadAreas = document.querySelectorAll('[id$="-upload-area"]');
        console.log('📦 Found upload areas:', Array.from(uploadAreas).map(area => area.id));
        
        uploadAreas.forEach(area => {
            const uploadType = this.detectUploadType(area);
            console.log(`🎯 Area ${area.id} detected as type:`, uploadType);
            if (uploadType) {
                this.initUploadArea(area, uploadType);
            }
        });
        
        this.eventBus.emit('uploads:initialized');
    }
    
    /**
     * Detect upload type from element
     */
    detectUploadType(element) {
        const id = element.id;
        
        if (id.includes('image')) return 'image';
        if (id.includes('audio')) return 'audio';
        if (id.includes('video')) return 'video';
        if (id.includes('gallery')) return 'gallery';
        
        return null;
    }
    
    /**
     * Initialize upload area
     */
    initUploadArea(area, type) {
        const uploader = this.uploaders.get(type);
        if (!uploader) {
            console.warn(`Unknown upload type: ${type}`);
            return;
        }
        
        uploader.init(area);
        
        // Store reference
        this.activeUploads.set(area.id, {
            area,
            type,
            uploader
        });
    }
    
    /**
     * Get uploader for area
     */
    getUploader(areaId) {
        const upload = this.activeUploads.get(areaId);
        return upload ? upload.uploader : null;
    }
    
    /**
     * Cancel all uploads
     */
    cancelAllUploads() {
        this.activeUploads.forEach(upload => {
            if (upload.uploader.cancel) {
                upload.uploader.cancel();
            }
        });
    }
    
    /**
     * Destroy upload manager
     */
    destroy() {
        this.activeUploads.forEach(upload => {
            if (upload.uploader.destroy) {
                upload.uploader.destroy();
            }
        });
        
        this.activeUploads.clear();
        this.eventBus.emit('uploads:destroyed');
    }
}

/**
 * Base uploader class
 */
class BaseUploader {
    constructor(eventBus, ajaxService, options = {}) {
        this.eventBus = eventBus;
        this.ajaxService = ajaxService;
        this.options = {
            maxSize: 10 * 1024 * 1024, // 10MB
            allowedTypes: [],
            ...options
        };
        this.currentUpload = null;
    }
    
    /**
     * Initialize upload area
     */
    init(area) {
        this.area = area;
        this.setupEventListeners();
        this.eventBus.emit('uploader:initialized', { type: this.constructor.name, area });
    }
    
    /**
     * Setup event listeners
     */
    setupEventListeners() {
        const input = this.area.querySelector('input[type="file"]');
        
        if (input) {
            input.addEventListener('change', (e) => this.handleFileSelect(e));
        }
        
        // Drag and drop
        this.area.addEventListener('dragover', this.handleDragOver.bind(this));
        this.area.addEventListener('dragleave', this.handleDragLeave.bind(this));
        this.area.addEventListener('drop', this.handleDrop.bind(this));
        this.area.addEventListener('click', () => input?.click());
    }
    
    /**
     * Handle file selection
     */
    async handleFileSelect(event) {
        const files = event.target.files;
        if (files.length > 0) {
            await this.processFile(files[0]);
        }
    }
    
    /**
     * Handle drag over
     */
    handleDragOver(event) {
        event.preventDefault();
        this.area.classList.add('border-blue-500', 'bg-blue-50');
    }
    
    /**
     * Handle drag leave
     */
    handleDragLeave(event) {
        event.preventDefault();
        this.area.classList.remove('border-blue-500', 'bg-blue-50');
    }
    
    /**
     * Handle file drop
     */
    async handleDrop(event) {
        event.preventDefault();
        this.area.classList.remove('border-blue-500', 'bg-blue-50');
        
        const files = event.dataTransfer.files;
        if (files.length > 0) {
            await this.processFile(files[0]);
        }
    }
    
    /**
     * Process uploaded file
     */
    async processFile(file) {
        // Validate file
        const validation = this.validateFile(file);
        if (!validation.isValid) {
            this.showError(validation.message);
            return;
        }
        
        // Show upload progress
        this.showProgress();
        
        try {
            const result = await this.uploadFile(file);
            this.showPreview(file, result);
            this.eventBus.emit('file:uploaded', { file, result, type: this.constructor.name });
        } catch (error) {
            this.showError(error.message);
            this.eventBus.emit('file:upload-error', { file, error, type: this.constructor.name });
        } finally {
            this.hideProgress();
        }
    }
    
    /**
     * Validate file
     */
    validateFile(file) {
        // Check file size
        if (file.size > this.options.maxSize) {
            return {
                isValid: false,
                message: `Archivo demasiado grande. Máximo ${this.formatFileSize(this.options.maxSize)}.`
            };
        }
        
        // Check file type
        if (this.options.allowedTypes.length > 0) {
            const isValidType = this.options.allowedTypes.some(type => {
                return file.type.includes(type) || file.name.toLowerCase().endsWith(`.${type}`);
            });
            
            if (!isValidType) {
                return {
                    isValid: false,
                    message: `Tipo de archivo no permitido. Permitidos: ${this.options.allowedTypes.join(', ')}`
                };
            }
        }
        
        // Additional validation for specific file types
        const additionalValidation = this.validateFileAdditional(file);
        if (!additionalValidation.isValid) {
            return additionalValidation;
        }
        
        return { isValid: true };
    }
    
    /**
     * Additional file validation (to be overridden by subclasses)
     */
    validateFileAdditional(file) {
        return { isValid: true };
    }
    
    /**
     * Upload file
     */
    async uploadFile(file) {
        const formData = new FormData();
        formData.append(this.getFieldName(), file);
        formData.append('type', this.getUploadType());
        
        return this.ajaxService.upload(this.getUploadEndpoint(), formData, (progress) => {
            this.updateProgress(progress);
        });
    }
    
    /**
     * Get field name for upload
     */
    getFieldName() {
        return 'file';
    }
    
    /**
     * Get upload endpoint based on type
     */
    getUploadEndpoint() {
        return '/api/upload';
    }
    
    /**
     * Show upload progress
     */
    showProgress() {
        const progressElement = this.getProgressElement();
        if (progressElement) {
            progressElement.classList.remove('hidden');
        }
    }
    
    /**
     * Update progress
     */
    updateProgress(percentage) {
        const progressBar = this.getProgressBar();
        const progressText = this.getProgressText();
        
        if (progressBar) {
            progressBar.style.width = `${percentage}%`;
        }
        
        if (progressText) {
            progressText.textContent = `${Math.round(percentage)}%`;
        }
    }
    
    /**
     * Hide upload progress
     */
    hideProgress() {
        const progressElement = this.getProgressElement();
        if (progressElement) {
            progressElement.classList.add('hidden');
        }
    }
    
    /**
     * Show error message
     */
    showError(message) {
        this.eventBus.emit('notification:show', {
            type: 'error',
            message: message
        });
        
        // Also show in upload area
        this.area.classList.add('border-red-500');
        setTimeout(() => {
            this.area.classList.remove('border-red-500');
        }, 3000);
    }
    
    /**
     * Show file preview (to be implemented by subclasses)
     */
    showPreview(file, result) {
        // Override in subclasses
    }
    
    /**
     * Get upload type (to be implemented by subclasses)
     */
    getUploadType() {
        return 'file';
    }
    
    /**
     * Get progress elements
     */
    getProgressElement() {
        return this.area.parentElement?.querySelector(`#${this.area.id.replace('-area', '-progress')}`);
    }
    
    getProgressBar() {
        return this.area.parentElement?.querySelector(`#${this.area.id.replace('-area', '-progress-bar')}`);
    }
    
    getProgressText() {
        return this.area.parentElement?.querySelector(`#${this.area.id.replace('-area', '-progress-text')}`);
    }
    
    /**
     * Format file size
     */
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    /**
     * Cancel current upload
     */
    cancel() {
        if (this.currentUpload) {
            this.currentUpload.abort();
            this.currentUpload = null;
        }
    }
    
    /**
     * Destroy uploader
     */
    destroy() {
        this.cancel();
        // Remove event listeners would go here
    }
}

/**
 * Image Uploader
 */
class ImageUploader extends BaseUploader {
    constructor(eventBus, ajaxService) {
        super(eventBus, ajaxService, {
            maxSize: 5 * 1024 * 1024, // 5MB
            allowedTypes: ['jpeg', 'jpg', 'png', 'gif', 'webp']
        });
    }
    
    getUploadType() {
        return 'image';
    }
    
    getUploadEndpoint() {
        return '/my-tokens/upload-image';
    }
    
    showPreview(file, result) {
        const previewElement = this.area.parentElement?.querySelector(`#${this.area.id.replace('-area', '-preview')}`);
        
        if (previewElement) {
            const img = previewElement.querySelector('img') || document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'w-full h-32 object-cover rounded-lg';
            
            if (!previewElement.contains(img)) {
                previewElement.appendChild(img);
            }
            
            previewElement.classList.remove('hidden');
        }
        
        // Update hidden input with upload URL
        const hiddenInput = document.querySelector(`#${this.area.id.replace('-area', '-url')}`);
        if (hiddenInput) {
            hiddenInput.value = result.url || result.path || '';
        }
    }
}

/**
 * Audio Uploader
 */
class AudioUploader extends BaseUploader {
    constructor(eventBus, ajaxService) {
        super(eventBus, ajaxService, {
            maxSize: 10 * 1024 * 1024, // 10MB
            allowedTypes: ['mp3', 'wav', 'm4a', 'ogg']
        });
    }
    
    getUploadType() {
        return 'audio';
    }
    
    getUploadEndpoint() {
        return '/my-tokens/upload-audio';
    }
    
    getFieldName() {
        return 'audio';
    }
    
    showPreview(file, result) {
        console.log('🎵 AudioUploader showPreview called:', { file: file.name, result });
        
        // Hide upload area
        if (this.area) {
            this.area.classList.add('hidden');
        }
        
        // Show audio preview using the existing function
        if (window.showAudioPreview && result.url) {
            console.log('🎵 Calling showAudioPreview function');
            window.showAudioPreview(result.url, result.filename || file.name);
        } else if (AudioUploadHelper.showAudioPreview && result.url) {
            console.log('🎵 Calling AudioUploadHelper.showAudioPreview function');
            AudioUploadHelper.showAudioPreview(result.url, result.filename || file.name);
        } else {
            console.error('❌ showAudioPreview function not found or no URL');
        }
        
        // Update hidden input - use specific ID for audio
        const hiddenInput = document.getElementById('audio-file-url');
        if (hiddenInput) {
            hiddenInput.value = result.url || result.path || '';
            console.log('✅ AudioUploader updated hidden input:', hiddenInput.value);
        } else {
            console.error('❌ Hidden input not found: #audio-file-url');
        }
    }
    
    /**
     * Get progress elements specific to audio upload
     */
    getProgressElement() {
        return document.getElementById('audio-upload-progress');
    }
    
    getProgressBar() {
        return document.getElementById('audio-progress-bar');
    }
    
    getProgressText() {
        return document.getElementById('audio-progress-text');
    }
}

/**
 * Video Uploader
 */
class VideoUploader extends BaseUploader {
    constructor(eventBus, ajaxService) {
        super(eventBus, ajaxService, {
            maxSize: 50 * 1024 * 1024, // 50MB
            allowedTypes: ['mp4', 'webm', 'avi', 'mov']
        });
    }
    
    /**
     * Enhanced video file validation
     */
    validateFileAdditional(file) {
        // Validate video-specific requirements
        const validation = this.validateVideoFile(file);
        return validation;
    }
    
    /**
     * Comprehensive video file validation
     */
    validateVideoFile(file) {
        console.log('🎬 Validating video file:', {
            name: file.name,
            type: file.type,
            size: file.size
        });
        
        // Check MIME type more strictly
        const validVideoMimes = [
            'video/mp4',
            'video/webm',
            'video/quicktime', // MOV
            'video/x-msvideo', // AVI
            'video/avi'
        ];
        
        if (!validVideoMimes.includes(file.type)) {
            console.warn('🎬 Invalid MIME type:', file.type);
            return {
                isValid: false,
                message: `Formato de video no compatible. Se detectó: ${file.type || 'desconocido'}. Sube archivos MP4, WebM, MOV o AVI.`
            };
        }
        
        // Check file extension
        const fileName = file.name.toLowerCase();
        const validExtensions = ['.mp4', '.webm', '.mov', '.avi'];
        const hasValidExtension = validExtensions.some(ext => fileName.endsWith(ext));
        
        if (!hasValidExtension) {
            return {
                isValid: false,
                message: 'Extensión de archivo no válida. Usa: .mp4, .webm, .mov, .avi'
            };
        }
        
        // Check for suspicious file names that might cause encoding issues
        if (fileName.includes(' ') && fileName.includes('%')) {
            return {
                isValid: false,
                message: 'El nombre del archivo contiene caracteres que pueden causar problemas. Renómbralo sin espacios especiales.'
            };
        }
        
        // Validate file size more granularly
        const maxSize = this.options.maxSize;
        if (file.size > maxSize) {
            const sizeInMB = Math.round(file.size / (1024 * 1024));
            const maxSizeInMB = Math.round(maxSize / (1024 * 1024));
            return {
                isValid: false,
                message: `Video demasiado grande (${sizeInMB}MB). El máximo permitido es ${maxSizeInMB}MB. Comprime el video antes de subirlo.`
            };
        }
        
        // Warn about very small files (might be corrupted)
        if (file.size < 1024) { // Less than 1KB
            return {
                isValid: false,
                message: 'El archivo parece estar dañado o vacío. Verifica que el video se reproduzca correctamente.'
            };
        }
        
        // Additional checks for optimal video formats
        if (file.type === 'video/mp4') {
            console.log('✅ MP4 format detected - optimal for web');
        } else if (file.type === 'video/webm') {
            console.log('✅ WebM format detected - good for modern browsers');
        } else {
            console.warn('⚠️ Video format may need conversion for optimal web playback');
        }
        
        // Validate aspect ratio expectations (accept all orientations)
        console.log('📐 Video will support all orientations: landscape, portrait, and square');
        
        console.log('✅ Video file validation passed');
        return { isValid: true };
    }
    
    /**
     * Enhanced file processing with orientation detection
     */
    async processFile(file) {
        console.log('🎬 Processing video file with orientation support:', {
            name: file.name,
            type: file.type,
            size: file.size
        });
        
        // First, validate the file
        const validation = this.validateFile(file);
        if (!validation.isValid) {
            this.showError(validation.message);
            return;
        }
        
        // Show upload progress
        this.showProgress();
        
        try {
            // Upload file
            const result = await this.uploadFile(file);
            
            // Show preview with orientation detection
            this.showPreview(file, result);
            
            console.log('✅ Video uploaded and preview shown with orientation support');
            this.eventBus.emit('file:uploaded', { file, result, type: this.constructor.name });
            
        } catch (error) {
            this.showError(error.message);
            this.eventBus.emit('file:upload-error', { file, error, type: this.constructor.name });
        } finally {
            this.hideProgress();
        }
    }
    
    getUploadType() {
        return 'video';
    }
    
    getUploadEndpoint() {
        return '/my-tokens/upload-video';
    }
    
    getFieldName() {
        return 'video';
    }
    
    showPreview(file, result) {
        console.log('🎬 VideoUploader showPreview called:', { file: file.name, result });
        
        // Hide upload area
        if (this.area) {
            this.area.classList.add('hidden');
        }
        
        // Show video preview using the existing function
        if (window.showVideoPreview && result.url) {
            console.log('📺 Calling showVideoPreview function');
            window.showVideoPreview(result.url, result.filename || file.name);
        } else {
            console.error('❌ showVideoPreview function not found or no URL');
        }
        
        // Update hidden input - use specific ID for video
        const hiddenInput = document.getElementById('video-file-url');
        if (hiddenInput) {
            hiddenInput.value = result.url || result.path || '';
            console.log('✅ VideoUploader updated hidden input:', hiddenInput.value);
        } else {
            console.error('❌ Hidden input not found: #video-file-url');
        }
    }
    
    /**
     * Get progress elements specific to video upload
     */
    getProgressElement() {
        return document.getElementById('video-upload-progress');
    }
    
    getProgressBar() {
        return document.getElementById('video-progress-bar');
    }
    
    getProgressText() {
        return document.getElementById('video-progress-text');
    }
}

/**
 * Gallery Uploader (multiple images)
 */
class GalleryUploader extends ImageUploader {
    constructor(eventBus, ajaxService) {
        super(eventBus, ajaxService);
        this.uploadedFiles = [];
    }
    
    getUploadType() {
        return 'gallery';
    }
    
    async processFile(file) {
        // Allow multiple files
        await super.processFile(file);
        this.uploadedFiles.push(file);
    }
    
    showPreview(file, result) {
        const previewContainer = this.area.parentElement?.querySelector(`#${this.area.id.replace('-area', '-preview')}`);
        
        if (previewContainer) {
            const imageContainer = document.createElement('div');
            imageContainer.className = 'relative inline-block m-1';
            
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'w-20 h-20 object-cover rounded-lg';
            
            const removeBtn = document.createElement('button');
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.className = 'absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600';
            removeBtn.onclick = () => this.removeImage(imageContainer, result);
            
            imageContainer.appendChild(img);
            imageContainer.appendChild(removeBtn);
            previewContainer.appendChild(imageContainer);
            previewContainer.classList.remove('hidden');
        }
    }
    
    removeImage(container, result) {
        container.remove();
        // Remove from uploaded files array
        this.uploadedFiles = this.uploadedFiles.filter(f => f !== result);
        
        // Hide preview container if empty
        const previewContainer = this.area.parentElement?.querySelector(`#${this.area.id.replace('-area', '-preview')}`);
        if (previewContainer && previewContainer.children.length === 0) {
            previewContainer.classList.add('hidden');
        }
    }
}

/**
 * Video Upload Component Helper Functions
 */
class VideoUploadHelper {
    static init() {
        console.log('🚀 VideoUploadHelper.init() called');
        
        const initializeHelper = function() {
            console.log('📄 Initializing VideoUploadHelper');
            VideoUploadHelper.initVideoTypeToggle();
            VideoUploadHelper.initYouTubeValidation();
        };
        
        if (document.readyState === 'loading') {
            console.log('⏳ Document still loading, waiting for DOMContentLoaded');
            document.addEventListener('DOMContentLoaded', initializeHelper);
        } else {
            console.log('✅ Document already loaded, initializing immediately');
            initializeHelper();
        }
    }
    
    static initVideoTypeToggle() {
        const videoTypeSelect = document.querySelector('select[name="multimedia[video][type]"]');
        console.log('🎬 Initializing video type toggle, current value:', videoTypeSelect?.value);
        
        if (videoTypeSelect && videoTypeSelect.value === 'file_upload') {
            console.log('📂 Video type is file_upload, toggling sections');
            VideoUploadHelper.toggleVideoInputType(videoTypeSelect);
            
            const videoFileUrl = document.getElementById('video-file-url');
            const existingVideoFileUrl = videoFileUrl ? videoFileUrl.value : '';
            console.log('🔍 Checking existing video URL:', existingVideoFileUrl);
            console.log('🔍 Hidden input element:', videoFileUrl);
            console.log('🔍 Hidden input outerHTML:', videoFileUrl?.outerHTML);
            
            if (existingVideoFileUrl) {
                const filename = existingVideoFileUrl.split('/').pop();
                console.log('📹 Showing existing video preview:', { url: existingVideoFileUrl, filename });
                VideoUploadHelper.showVideoPreview(existingVideoFileUrl, filename);
            } else {
                console.log('ℹ️ No existing video URL found');
            }
        } else {
            console.log('ℹ️ Video type is not file_upload or select not found');
        }
    }
    
    static initYouTubeValidation() {
        const videoUrlInput = document.getElementById('video-url-input');
        if (videoUrlInput) {
            videoUrlInput.addEventListener('input', function() {
                const url = this.value.trim();
                const validation = VideoUploadHelper.validateYouTubeURL(url);
                VideoUploadHelper.showYouTubeValidation(validation.isValid, validation.message);
            });
            
            videoUrlInput.addEventListener('blur', function() {
                const url = this.value.trim();
                if (url) {
                    const validation = VideoUploadHelper.validateYouTubeURL(url);
                    VideoUploadHelper.showYouTubeValidation(validation.isValid, validation.message);
                } else {
                    VideoUploadHelper.showYouTubeValidation(true, '');
                }
            });
            
            const initialValue = videoUrlInput.value.trim();
            if (initialValue) {
                const validation = VideoUploadHelper.validateYouTubeURL(initialValue);
                VideoUploadHelper.showYouTubeValidation(validation.isValid, validation.message);
            }
        }
    }
    
    static toggleVideoInputType(select) {
        const videoUrlSection = document.getElementById('video-url-section');
        const videoUploadSection = document.getElementById('video-upload-section');
        const videoFileUrl = document.getElementById('video-file-url');
        const videoUrlInput = document.getElementById('video-url-input');
        
        if (select.value === 'file_upload') {
            videoUrlSection.classList.add('hidden');
            videoUploadSection.classList.remove('hidden');
            if (videoUrlInput) videoUrlInput.value = '';
        } else {
            videoUrlSection.classList.remove('hidden');
            videoUploadSection.classList.add('hidden');
            if (videoFileUrl) videoFileUrl.value = '';
            VideoUploadHelper.removeVideo();
        }
        
        if (select.value === '') {
            if (videoUrlInput) videoUrlInput.value = '';
            if (videoFileUrl) videoFileUrl.value = '';
            VideoUploadHelper.removeVideo();
        }
    }
    
    static removeVideo() {
        const videoPreview = document.getElementById('video-preview');
        const videoPlayer = document.getElementById('video-player');
        const videoFileUrl = document.getElementById('video-file-url');
        const videoFileInput = document.getElementById('video-file-input');
        const videoUploadArea = document.getElementById('video-upload-area');
        
        if (videoPreview) videoPreview.classList.add('hidden');
        if (videoPlayer) videoPlayer.src = '';
        if (videoFileUrl) videoFileUrl.value = '';
        if (videoFileInput) videoFileInput.value = '';
        if (videoUploadArea) videoUploadArea.classList.remove('hidden');
    }
    
    static validateYouTubeURL(url) {
        if (!url || url.trim() === '') {
            return { isValid: true, message: '' };
        }
        
        const youtubePatterns = [
            /^https?:\/\/(www\.)?youtube\.com\/watch\?v=[\w-]+/,
            /^https?:\/\/(www\.)?youtube\.com\/embed\/[\w-]+/,
            /^https?:\/\/(www\.)?youtube\.com\/v\/[\w-]+/,
            /^https?:\/\/youtu\.be\/[\w-]+/,
            /^https?:\/\/m\.youtube\.com\/watch\?v=[\w-]+/,
            /^https?:\/\/(www\.)?youtube\.com\/shorts\/[\w-]+/
        ];
        
        const isValidYoutube = youtubePatterns.some(pattern => pattern.test(url));
        
        if (!isValidYoutube) {
            return {
                isValid: false,
                message: 'Debes usar una URL válida de YouTube (youtube.com, youtu.be, m.youtube.com)'
            };
        }
        
        return { isValid: true, message: 'URL de YouTube válida' };
    }
    
    static showYouTubeValidation(isValid, message) {
        const validationMessage = document.getElementById('youtube-validation-message');
        const successMessage = document.getElementById('youtube-success-message');
        const urlInput = document.getElementById('video-url-input');
        
        if (validationMessage) validationMessage.classList.add('hidden');
        if (successMessage) successMessage.classList.add('hidden');
        
        if (urlInput) {
            urlInput.classList.remove('border-red-500', 'border-green-500');
            urlInput.classList.add('border-gray-300');
        }
        
        if (message) {
            if (isValid) {
                if (successMessage) successMessage.classList.remove('hidden');
                if (urlInput) {
                    urlInput.classList.remove('border-gray-300');
                    urlInput.classList.add('border-green-500');
                }
            } else {
                if (validationMessage) validationMessage.classList.remove('hidden');
                if (urlInput) {
                    urlInput.classList.remove('border-gray-300');
                    urlInput.classList.add('border-red-500');
                }
            }
        }
    }
    
    static showVideoPreview(url, filename) {
        console.log('📺 showVideoPreview called with:', { url, filename });
        
        const videoFilename = document.getElementById('video-filename');
        const videoPlayer = document.getElementById('video-player');
        const videoContainer = document.getElementById('video-container');
        const videoPreview = document.getElementById('video-preview');
        const videoUploadArea = document.getElementById('video-upload-area');
        
        console.log('🎬 Found elements:', {
            videoFilename: !!videoFilename,
            videoPlayer: !!videoPlayer,
            videoContainer: !!videoContainer,
            videoPreview: !!videoPreview,
            videoUploadArea: !!videoUploadArea
        });
        
        if (videoFilename) {
            videoFilename.textContent = filename;
            console.log('✅ Updated filename:', filename);
        }
        
        if (videoPlayer) {
            // Setup video with proper sources and enhanced monitoring
            VideoUploadHelper.setupVideoPlayer(videoPlayer, url);
            
            // Detect and apply orientation once video loads
            VideoUploadHelper.detectVideoOrientation(videoPlayer, videoContainer);
            
            console.log('✅ Setup enhanced video player with URL:', url);
        }
        
        if (videoPreview) {
            videoPreview.classList.remove('hidden');
            console.log('✅ Showed video preview');
        }
        
        if (videoUploadArea) {
            videoUploadArea.classList.add('hidden');
            videoUploadArea.classList.remove('border-red-500');
            const errorMessage = videoUploadArea.parentNode.querySelector('.wizard-error-message');
            if (errorMessage) {
                errorMessage.remove();
            }
            console.log('✅ Hid upload area and cleaned errors');
        }
    }
    
    /**
     * Enhanced Video Player Setup with Monitoring and Recovery
     */
    static setupVideoPlayer(videoElement, url) {
        console.log('🎬 Setting up enhanced video player for:', url);
        
        // Clear any existing event listeners
        const newVideoElement = VideoUploadHelper.clearVideoEventListeners(videoElement);
        
        // Set video sources
        const sources = newVideoElement.querySelectorAll('source');
        if (sources.length > 0) {
            sources.forEach(source => source.src = url);
        }
        newVideoElement.src = url;
        
        // Setup video monitoring
        VideoUploadHelper.setupVideoMonitoring(newVideoElement);
        
        // Setup recovery mechanism
        VideoUploadHelper.setupVideoRecovery(newVideoElement);
    }
    
    /**
     * Clear existing event listeners
     */
    static clearVideoEventListeners(videoElement) {
        // Clone the element to remove all event listeners
        const newVideo = videoElement.cloneNode(true);
        videoElement.parentNode.replaceChild(newVideo, videoElement);
        return newVideo;
    }
    
    /**
     * Setup comprehensive video monitoring
     */
    static setupVideoMonitoring(videoElement) {
        const statusContainer = document.getElementById('video-status');
        const loadingIndicator = document.getElementById('video-loading');
        const bufferingIndicator = document.getElementById('video-buffering');
        const errorIndicator = document.getElementById('video-error');
        const readyIndicator = document.getElementById('video-ready');
        
        // Store references for timeout management
        videoElement._videoTimeouts = {};
        videoElement._lastCurrentTime = 0;
        videoElement._stallCount = 0;
        
        const showStatus = (type) => {
            [loadingIndicator, bufferingIndicator, errorIndicator, readyIndicator].forEach(el => {
                if (el) el.classList.add('hidden');
            });
            
            const indicator = document.getElementById(`video-${type}`);
            if (indicator && statusContainer) {
                statusContainer.classList.remove('hidden');
                indicator.classList.remove('hidden');
            }
        };
        
        const hideStatus = () => {
            if (statusContainer) {
                statusContainer.classList.add('hidden');
            }
        };
        
        // Loading events
        videoElement.addEventListener('loadstart', () => {
            console.log('📼 Video: Load started');
            showStatus('loading');
        });
        
        videoElement.addEventListener('loadedmetadata', () => {
            console.log('📼 Video: Metadata loaded');
            videoElement._stallCount = 0;
        });
        
        videoElement.addEventListener('loadeddata', () => {
            console.log('📼 Video: Data loaded');
        });
        
        videoElement.addEventListener('canplay', () => {
            console.log('📼 Video: Can start playing');
            showStatus('ready');
            setTimeout(hideStatus, 3000);
        });
        
        videoElement.addEventListener('canplaythrough', () => {
            console.log('📼 Video: Can play through');
            hideStatus();
        });
        
        // Playback events
        videoElement.addEventListener('play', () => {
            console.log('📼 Video: Play started');
            hideStatus();
            VideoUploadHelper.startStallMonitoring(videoElement);
        });
        
        videoElement.addEventListener('pause', () => {
            console.log('📼 Video: Paused');
            VideoUploadHelper.stopStallMonitoring(videoElement);
        });
        
        videoElement.addEventListener('waiting', () => {
            console.log('📼 Video: Waiting for data (buffering)');
            showStatus('buffering');
            VideoUploadHelper.handleVideoStall(videoElement);
        });
        
        videoElement.addEventListener('playing', () => {
            console.log('📼 Video: Playing resumed');
            hideStatus();
            videoElement._stallCount = 0;
        });
        
        videoElement.addEventListener('seeked', () => {
            console.log('📼 Video: Seek completed');
            hideStatus();
        });
        
        // Error handling
        videoElement.addEventListener('error', (e) => {
            console.error('📼 Video error:', e);
            showStatus('error');
            VideoUploadHelper.handleVideoError(videoElement, e);
        });
        
        videoElement.addEventListener('stalled', () => {
            console.warn('📼 Video: Network stalled');
            VideoUploadHelper.handleVideoStall(videoElement);
        });
        
        videoElement.addEventListener('suspend', () => {
            console.log('📼 Video: Loading suspended');
        });
        
        videoElement.addEventListener('abort', () => {
            console.log('📼 Video: Loading aborted');
        });
        
        // Progress monitoring
        videoElement.addEventListener('progress', () => {
            const buffered = videoElement.buffered;
            if (buffered.length > 0) {
                const bufferedEnd = buffered.end(buffered.length - 1);
                const duration = videoElement.duration;
                if (duration > 0) {
                    const bufferedPercent = (bufferedEnd / duration) * 100;
                    console.log(`📼 Video buffered: ${bufferedPercent.toFixed(1)}%`);
                }
            }
        });
    }
    
    /**
     * Setup video recovery mechanisms
     */
    static setupVideoRecovery(videoElement) {
        // Recovery attempt counter
        videoElement._recoveryAttempts = 0;
        videoElement._maxRecoveryAttempts = 3;
    }
    
    /**
     * Start monitoring for video stalls during playback
     */
    static startStallMonitoring(videoElement) {
        VideoUploadHelper.stopStallMonitoring(videoElement);
        
        videoElement._stallMonitor = setInterval(() => {
            if (!videoElement.paused && !videoElement.ended) {
                const currentTime = videoElement.currentTime;
                
                if (currentTime === videoElement._lastCurrentTime) {
                    videoElement._stallCount++;
                    console.warn(`📼 Video stall detected (count: ${videoElement._stallCount})`);
                    
                    if (videoElement._stallCount >= 3) {
                        console.error('📼 Video appears to be stuck');
                        VideoUploadHelper.attemptVideoRecovery(videoElement);
                    }
                } else {
                    videoElement._stallCount = 0;
                }
                
                videoElement._lastCurrentTime = currentTime;
            }
        }, 2000); // Check every 2 seconds
    }
    
    /**
     * Stop stall monitoring
     */
    static stopStallMonitoring(videoElement) {
        if (videoElement._stallMonitor) {
            clearInterval(videoElement._stallMonitor);
            videoElement._stallMonitor = null;
        }
    }
    
    /**
     * Handle video stalls
     */
    static handleVideoStall(videoElement) {
        console.log('📼 Handling video stall...');
        
        // Clear any existing timeout
        if (videoElement._videoTimeouts.stallTimeout) {
            clearTimeout(videoElement._videoTimeouts.stallTimeout);
        }
        
        // Set recovery timeout
        videoElement._videoTimeouts.stallTimeout = setTimeout(() => {
            if (videoElement.readyState < 3) { // Not HAVE_FUTURE_DATA
                console.warn('📼 Video still stalling after 10 seconds, attempting recovery');
                VideoUploadHelper.attemptVideoRecovery(videoElement);
            }
        }, 10000); // 10 second timeout
    }
    
    /**
     * Handle video errors
     */
    static handleVideoError(videoElement, error) {
        console.error('📼 Video error details:', {
            error: error,
            networkState: videoElement.networkState,
            readyState: videoElement.readyState,
            src: videoElement.src
        });
        
        const errorCode = videoElement.error?.code;
        let errorMessage = 'Error desconocido';
        
        switch (errorCode) {
            case 1:
                errorMessage = 'Reproducción abortada';
                break;
            case 2:
                errorMessage = 'Error de red';
                break;
            case 3:
                errorMessage = 'Error de decodificación';
                break;
            case 4:
                errorMessage = 'Formato no soportado';
                break;
        }
        
        console.error(`📼 Video error: ${errorMessage}`);
        
        // Attempt recovery for network errors
        if (errorCode === 2) {
            setTimeout(() => {
                VideoUploadHelper.attemptVideoRecovery(videoElement);
            }, 2000);
        }
    }
    
    /**
     * Attempt video recovery
     */
    static attemptVideoRecovery(videoElement) {
        if (videoElement._recoveryAttempts >= videoElement._maxRecoveryAttempts) {
            console.error('📼 Max recovery attempts reached, giving up');
            return;
        }
        
        videoElement._recoveryAttempts++;
        console.log(`📼 Attempting video recovery (attempt ${videoElement._recoveryAttempts})`);
        
        const currentTime = videoElement.currentTime;
        const wasPlaying = !videoElement.paused;
        
        // Method 1: Reload video source
        const originalSrc = videoElement.src;
        videoElement.src = '';
        videoElement.load();
        
        setTimeout(() => {
            videoElement.src = originalSrc;
            videoElement.load();
            
            videoElement.addEventListener('canplay', function onCanPlay() {
                videoElement.removeEventListener('canplay', onCanPlay);
                
                // Restore position
                if (currentTime > 0) {
                    videoElement.currentTime = currentTime;
                }
                
                // Resume playing if it was playing
                if (wasPlaying) {
                    videoElement.play().catch(e => {
                        console.error('📼 Failed to resume playback after recovery:', e);
                    });
                }
            });
        }, 1000);
        
        // Reset stall count
        videoElement._stallCount = 0;
    }
    
    /**
     * Detect video orientation and apply appropriate styling
     */
    static detectVideoOrientation(videoElement, containerElement) {
        console.log('🎬 Setting up video orientation detection');
        
        if (!containerElement) {
            console.warn('🎬 No container element provided for orientation detection');
            return;
        }
        
        // Function to apply orientation styles
        const applyOrientation = () => {
            const videoWidth = videoElement.videoWidth;
            const videoHeight = videoElement.videoHeight;
            
            console.log('🎬 Video dimensions:', { width: videoWidth, height: videoHeight });
            
            if (videoWidth && videoHeight) {
                const aspectRatio = videoWidth / videoHeight;
                
                // Remove existing orientation classes
                containerElement.classList.remove('landscape', 'portrait', 'square', 'loading');
                
                if (aspectRatio > 1.3) {
                    // Horizontal/Landscape video
                    containerElement.classList.add('landscape');
                    console.log('🎬 Applied landscape orientation');
                } else if (aspectRatio < 0.8) {
                    // Vertical/Portrait video
                    containerElement.classList.add('portrait');
                    console.log('🎬 Applied portrait orientation');
                } else {
                    // Square or near-square video
                    containerElement.classList.add('square');
                    console.log('🎬 Applied square orientation');
                }
                
                // Add data attributes for CSS targeting
                containerElement.dataset.aspectRatio = aspectRatio.toFixed(2);
                containerElement.dataset.orientation = aspectRatio > 1.3 ? 'landscape' : 
                                                    aspectRatio < 0.8 ? 'portrait' : 'square';
            }
        };
        
        // Apply orientation when metadata is loaded
        if (videoElement.videoWidth && videoElement.videoHeight) {
            // Video dimensions already available
            applyOrientation();
        } else {
            // Wait for metadata to load
            containerElement.classList.add('loading');
            
            const onLoadedMetadata = () => {
                console.log('🎬 Video metadata loaded, applying orientation');
                applyOrientation();
                videoElement.removeEventListener('loadedmetadata', onLoadedMetadata);
            };
            
            videoElement.addEventListener('loadedmetadata', onLoadedMetadata);
            
            // Fallback timeout
            setTimeout(() => {
                if (!videoElement.videoWidth || !videoElement.videoHeight) {
                    console.warn('🎬 Video dimensions not available after timeout, using default');
                    containerElement.classList.remove('loading');
                    containerElement.classList.add('landscape'); // Default fallback
                }
            }, 5000);
        }
    }
    
    /**
     * Get video orientation info
     */
    static getVideoOrientation(videoElement) {
        if (!videoElement.videoWidth || !videoElement.videoHeight) {
            return null;
        }
        
        const aspectRatio = videoElement.videoWidth / videoElement.videoHeight;
        
        return {
            width: videoElement.videoWidth,
            height: videoElement.videoHeight,
            aspectRatio: aspectRatio,
            orientation: aspectRatio > 1.3 ? 'landscape' : 
                        aspectRatio < 0.8 ? 'portrait' : 'square',
            isVertical: aspectRatio < 0.8,
            isHorizontal: aspectRatio > 1.3,
            isSquare: aspectRatio >= 0.8 && aspectRatio <= 1.3
        };
    }
}

/**
 * Audio Upload Component Helper Functions
 */
class AudioUploadHelper {
    static init() {
        console.log('🚀 AudioUploadHelper.init() called');
        
        const initializeHelper = function() {
            console.log('📄 Initializing AudioUploadHelper');
            AudioUploadHelper.initAudioTypeToggle();
            AudioUploadHelper.initAudioValidation();
        };
        
        if (document.readyState === 'loading') {
            console.log('⏳ Document still loading, waiting for DOMContentLoaded');
            document.addEventListener('DOMContentLoaded', initializeHelper);
        } else {
            console.log('✅ Document already loaded, initializing immediately');
            initializeHelper();
        }
    }
    
    static initAudioTypeToggle() {
        const audioTypeSelect = document.querySelector('select[name="multimedia[audio][type]"]');
        console.log('🎵 Initializing audio type toggle, current value:', audioTypeSelect?.value);
        
        if (audioTypeSelect && audioTypeSelect.value === 'file_upload') {
            console.log('📂 Audio type is file_upload, toggling sections');
            AudioUploadHelper.toggleAudioInputType(audioTypeSelect);
            
            const audioFileUrl = document.getElementById('audio-file-url');
            const existingAudioFileUrl = audioFileUrl ? audioFileUrl.value : '';
            console.log('🔍 Checking existing audio URL:', existingAudioFileUrl);
            
            if (existingAudioFileUrl) {
                const filename = existingAudioFileUrl.split('/').pop();
                console.log('🎵 Showing existing audio preview:', { url: existingAudioFileUrl, filename });
                AudioUploadHelper.showAudioPreview(existingAudioFileUrl, filename);
            } else {
                console.log('ℹ️ No existing audio URL found');
            }
        } else {
            console.log('ℹ️ Audio type is not file_upload or select not found');
        }
    }
    
    static initAudioValidation() {
        const audioUrlInput = document.getElementById('audio-url-input');
        if (audioUrlInput) {
            audioUrlInput.addEventListener('input', function() {
                const url = this.value.trim();
                AudioUploadHelper.validateAudioUrl(this);
            });
            
            audioUrlInput.addEventListener('blur', function() {
                const url = this.value.trim();
                if (url) {
                    AudioUploadHelper.validateAudioUrl(this);
                }
            });
            
            const initialValue = audioUrlInput.value.trim();
            if (initialValue) {
                AudioUploadHelper.validateAudioUrl(audioUrlInput);
            }
        }
    }
    
    static toggleAudioInputType(select) {
        const audioUrlSection = document.getElementById('audio-url-section');
        const audioUploadSection = document.getElementById('audio-upload-section');
        const audioFileUrl = document.getElementById('audio-file-url');
        const audioUrlInput = document.getElementById('audio-url-input');
        
        if (select.value === 'file_upload') {
            audioUrlSection.classList.add('hidden');
            audioUploadSection.classList.remove('hidden');
            if (audioUrlInput) audioUrlInput.value = '';
        } else {
            audioUrlSection.classList.remove('hidden');
            audioUploadSection.classList.add('hidden');
            if (audioFileUrl) audioFileUrl.value = '';
            AudioUploadHelper.removeAudio();
        }
        
        if (select.value === '') {
            if (audioUrlInput) audioUrlInput.value = '';
            if (audioFileUrl) audioFileUrl.value = '';
            AudioUploadHelper.removeAudio();
        }
    }
    
    static removeAudio() {
        const audioPreview = document.getElementById('audio-preview');
        const audioPlayer = document.getElementById('audio-player');
        const audioFileUrl = document.getElementById('audio-file-url');
        const audioFileInput = document.getElementById('audio-file-input');
        const audioUploadArea = document.getElementById('audio-upload-area');
        
        if (audioPreview) audioPreview.classList.add('hidden');
        if (audioPlayer) audioPlayer.src = '';
        if (audioFileUrl) audioFileUrl.value = '';
        if (audioFileInput) audioFileInput.value = '';
        if (audioUploadArea) audioUploadArea.classList.remove('hidden');
    }
    
    static validateAudioUrl(input) {
        const url = input.value.trim();
        const errorDiv = document.getElementById('audio-url-error');
        
        if (!url) {
            AudioUploadHelper.hideError(errorDiv);
            return true;
        }
        
        const allowedPatterns = [
            /^https:\/\/music\.youtube\.com\/watch\?v=.*$/i,
            /^https:\/\/music\.youtube\.com\/channel\/.*$/i,
            /^https:\/\/music\.youtube\.com\/browse\/.*$/i,
            /^https:\/\/music\.youtube\.com\/playlist\?list=.*$/i,
            /^https:\/\/soundcloud\.com\/.*$/i,
            /^https?:\/\/.*\.(mp3|wav|ogg|m4a)$/i
        ];
        
        const blockedPatterns = [
            /^https:\/\/(www\.)?youtube\.com\/watch\?v=.*$/i,
            /^https:\/\/youtu\.be\/.*$/i
        ];
        
        const isBlocked = blockedPatterns.some(pattern => pattern.test(url));
        if (isBlocked) {
            AudioUploadHelper.showError(errorDiv, '❌ URLs de YouTube normal no están permitidas. Usa YouTube Music (music.youtube.com) para mejor autoplay.');
            return false;
        }
        
        const isAllowed = allowedPatterns.some(pattern => pattern.test(url));
        if (!isAllowed) {
            AudioUploadHelper.showError(errorDiv, '❌ URL no válida. Solo se permiten: YouTube Music o archivos de audio directos.');
            return false;
        }
        
        AudioUploadHelper.hideError(errorDiv);
        return true;
    }
    
    static showError(errorDiv, message) {
        errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle mr-1"></i>${message}`;
        errorDiv.classList.remove('hidden');
        errorDiv.classList.add('bg-red-50', 'border', 'border-red-200', 'rounded', 'p-2');
    }
    
    static hideError(errorDiv) {
        errorDiv.classList.add('hidden');
        errorDiv.classList.remove('bg-red-50', 'border', 'border-red-200', 'rounded', 'p-2');
    }
    
    static showAudioPreview(url, filename) {
        console.log('🎵 showAudioPreview called with:', { url, filename });
        
        const audioFilename = document.getElementById('audio-filename');
        const audioPlayer = document.getElementById('audio-player');
        const audioPreview = document.getElementById('audio-preview');
        const audioUploadArea = document.getElementById('audio-upload-area');
        
        console.log('🎵 Found elements:', {
            audioFilename: !!audioFilename,
            audioPlayer: !!audioPlayer, 
            audioPreview: !!audioPreview,
            audioUploadArea: !!audioUploadArea
        });
        
        if (audioFilename) {
            audioFilename.textContent = filename;
            console.log('✅ Updated filename:', filename);
        }
        
        if (audioPlayer) {
            audioPlayer.src = url;
            console.log('✅ Updated audio player src:', url);
        }
        
        if (audioPreview) {
            audioPreview.classList.remove('hidden');
            console.log('✅ Showed audio preview');
        }
        
        if (audioUploadArea) {
            audioUploadArea.classList.add('hidden');
            audioUploadArea.classList.remove('border-red-500');
            const errorMessage = audioUploadArea.parentNode.querySelector('.wizard-error-message');
            if (errorMessage) {
                errorMessage.remove();
            }
            console.log('✅ Hid upload area and cleaned errors');
        }
    }
}

// Initialize helpers
VideoUploadHelper.init();
AudioUploadHelper.init();

// Add event listeners for UploadManager integration
document.addEventListener('DOMContentLoaded', function() {
    // Wait for app to be initialized
    const initEventListener = function() {
        if (window.app && window.app.eventBus) {
            console.log('🎥 Video upload event listeners initialized');
            
            window.app.eventBus.on('file:uploaded', function(data) {
                console.log('📤 File uploaded event received:', data);
                if (data.type === 'VideoUploader') {
                    console.log('🎬 Video upload successful:', data.result);
                    const videoFileUrl = document.getElementById('video-file-url');
                    if (videoFileUrl && data.result.url) {
                        videoFileUrl.value = data.result.url;
                        console.log('✅ Hidden input updated with URL:', data.result.url);
                        VideoUploadHelper.showVideoPreview(data.result.url, data.result.filename || data.file.name);
                    } else {
                        console.error('❌ Hidden input not found or no URL in result');
                    }
                } else if (data.type === 'AudioUploader') {
                    console.log('🎵 Audio upload successful:', data.result);
                    const audioFileUrl = document.getElementById('audio-file-url');
                    if (audioFileUrl && data.result.url) {
                        audioFileUrl.value = data.result.url;
                        console.log('✅ Audio hidden input updated with URL:', data.result.url);
                        AudioUploadHelper.showAudioPreview(data.result.url, data.result.filename || data.file.name);
                    } else {
                        console.error('❌ Audio hidden input not found or no URL in result');
                    }
                }
            });
            
            window.app.eventBus.on('file:upload-error', function(data) {
                if (data.type === 'VideoUploader') {
                    console.error('❌ Video upload error:', data.error);
                    const videoUploadArea = document.getElementById('video-upload-area');
                    if (videoUploadArea) {
                        videoUploadArea.classList.remove('hidden');
                    }
                } else if (data.type === 'AudioUploader') {
                    console.error('❌ Audio upload error:', data.error);
                    const audioUploadArea = document.getElementById('audio-upload-area');
                    if (audioUploadArea) {
                        audioUploadArea.classList.remove('hidden');
                    }
                }
            });
            
            // Listen to wizard step changes to reinitialize video component
            window.app.eventBus.on('wizard:stepChanged', function(data) {
                console.log('🧙 Wizard step changed:', data);
                if (data.to === 2) {
                    console.log('🎬 Entering video step, reinitializing video component');
                    setTimeout(() => {
                        VideoUploadHelper.initVideoTypeToggle();
                    }, 100); // Small delay to ensure DOM is updated
                }
            });
        } else {
            console.log('⏳ Waiting for app initialization...');
            setTimeout(initEventListener, 100);
        }
    };
    
    initEventListener();
});

// Export classes and functions to window
window.UploadManager = UploadManager;
window.BaseUploader = BaseUploader;
window.ImageUploader = ImageUploader;
window.AudioUploader = AudioUploader;
window.VideoUploader = VideoUploader;
window.GalleryUploader = GalleryUploader;
window.VideoUploadHelper = VideoUploadHelper;
window.AudioUploadHelper = AudioUploadHelper;

// Export global functions for backwards compatibility
window.toggleVideoInputType = VideoUploadHelper.toggleVideoInputType;
window.removeVideo = VideoUploadHelper.removeVideo;
window.showVideoPreview = VideoUploadHelper.showVideoPreview;

window.toggleAudioInputType = AudioUploadHelper.toggleAudioInputType;
window.removeAudio = AudioUploadHelper.removeAudio;
window.showAudioPreview = AudioUploadHelper.showAudioPreview;
window.validateAudioUrl = AudioUploadHelper.validateAudioUrl;