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

// Import multimedia components before starting Alpine
import './multimedia-components.js';

// Import tourist map components before starting Alpine
import './tourist-map.js';

// Start Alpine
Alpine.start();
