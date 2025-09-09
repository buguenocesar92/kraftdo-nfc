import './bootstrap';
import Alpine from 'alpinejs';

// Make Alpine available globally
window.Alpine = Alpine;

// Import NFC application components
import './nfc-app.js';

// Import token-gift components before starting Alpine
import './token-gift.js';

// Start Alpine
Alpine.start();
