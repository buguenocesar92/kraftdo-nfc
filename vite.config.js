import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Main application files
                'resources/css/app.css', 
                'resources/js/app.js',
                
                // CSS files
                'resources/css/animations.css',
                'resources/css/audio-overlay.css',
                'resources/css/content-preview.css',
                'resources/css/gift-tailwind-config.css',
                'resources/css/kraftdo-theme.css',
                'resources/css/multimedia-components.css',
                'resources/css/profile.css',
                'resources/css/profile-config.css',
                'resources/css/profile-enhancements.css',
                'resources/css/qr-code-print.css',
                'resources/css/restaurant-menu.css',
                'resources/css/video-container.css',
                'resources/css/video-styles.css',
                
                // JavaScript files
                'resources/js/app-initializer.js',
                'resources/js/audio-overlay-system.js',
                'resources/js/content-preview.js',
                'resources/js/csrf-helpers.js',
                'resources/js/design-preview.js',
                'resources/js/gallery-modal-advanced.js',
                'resources/js/multimedia-components.js',
                'resources/js/multimedia-system.js',
                'resources/js/parallax-effects.js',
                'resources/js/qr-code-generator.js',
                'resources/js/streaming-controls.js',
                'resources/js/video-orientation.js',
                'resources/js/video-orientation-system.js',
            ],
            refresh: true,
        }),
    ],
});
