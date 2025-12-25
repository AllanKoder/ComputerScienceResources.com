import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import PrimeVue from "primevue/config";
import customTheme from "./../../theming.config";
import ToastService from 'primevue/toastservice';
import Toast from 'primevue/toast';
import Tooltip from 'primevue/tooltip';
import './axios';

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: {
                    preset: customTheme,
                    options: {
                        darkModeSelector: '.my-app-dark',
                    }
                },
            })
            .use(ToastService)
            .component('Toast', Toast)
            .directive('tooltip', Tooltip)
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
