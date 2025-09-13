import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Main application files
                'resources/css/app.css', 
                'resources/js/app.js',
                
                // No Filament theme - using defaults
                
                // Core CSS files only
                'resources/css/kraftdo-theme.css',
                'resources/css/animations.css',
                
                // Core JS files only
                'resources/js/app-initializer.js',
                'resources/js/csrf-helpers.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            onwarn(warning, warn) {
                // Ignore CSS warnings during build
                if (warning.code === 'CSS_RESOLUTION_ERROR') {
                    return;
                }
                warn(warning);
            }
        }
    }
});