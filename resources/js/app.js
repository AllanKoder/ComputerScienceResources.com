import './bootstrap';

import Alpine from 'alpinejs';
import htmx from "htmx.org";
import focus from '@alpinejs/focus'
import { createInertiaApp } from '@inertiajs/svelte'
 
window.Alpine = Alpine;
window.htmx = htmx;

Alpine.plugin(focus)
Alpine.start();

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.svelte', { eager: true })
    return pages[`./Pages/${name}.svelte`]
  },
  setup({ el, App, props }) {
    new App({ target: el, props })
  },
})