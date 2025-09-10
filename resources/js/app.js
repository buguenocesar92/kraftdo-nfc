import './bootstrap';
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

// Register Alpine plugins
Alpine.plugin(intersect);

// Make Alpine available globally
window.Alpine = Alpine;

// Import NFC application components
import './nfc-app.js';

// Import token-gift components before starting Alpine
import './token-gift.js';

// Start Alpine
Alpine.start();
