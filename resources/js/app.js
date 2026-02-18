import './bootstrap';

// Alpine (used for small UI interactions like dropdowns)
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();
