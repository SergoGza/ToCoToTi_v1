import '../css/app.css';
import './bootstrap';
import { usePage } from '@inertiajs/vue3';


import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - Laravel`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(flashPlugin) // Registrar nuestro plugin flash
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// Definir la función flash global como un plugin de Vue
const flashPlugin = {
    install: (app) => {
        app.config.globalProperties.flash = function(type) {
            const page = usePage();
            if (page.props.flash && page.props.flash[type]) {
                return page.props.flash[type];
            }
            return null;
        };
    }
};

// Helper global para acceder a los mensajes flash de forma segura
window.flash = function(type) {
    const page = usePage();
    if (page.props.flash && page.props.flash[type]) {
        return page.props.flash[type];
    }
    return null;
};
