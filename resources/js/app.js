import './bootstrap';

import Alpine from 'alpinejs';
import htmx from "htmx.org";
import focus from '@alpinejs/focus'
 
window.Alpine = Alpine;
window.htmx = htmx;

Alpine.plugin(focus)
Alpine.start();
