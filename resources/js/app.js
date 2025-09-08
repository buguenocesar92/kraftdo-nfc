import './bootstrap';
import Alpine from 'alpinejs';

// Make Alpine available globally
window.Alpine = Alpine;

// Import NFC application components
import './nfc-app.js';

// Start Alpine
Alpine.start();
