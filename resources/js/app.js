import '../css/app.css';
import './bootstrap';
import { usePage } from '@inertiajs/vue3';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Función para manejar el tema oscuro
const setupDarkMode = () => {
    // Verificar el tema guardado o preferencia del sistema
    const isDarkMode = localStorage.getItem('theme') === 'dark' ||
        (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);

    // Aplicar el tema
    if (isDarkMode) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    // Exponer función global para cambiar el tema
    window.toggleDarkMode = () => {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
    };

    // Monitorear cambios en la preferencia del sistema
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (!localStorage.getItem('theme')) { // Solo cambiar si no hay preferencia guardada
            if (e.matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    });
};

// Sistema global de notificaciones toast
const setupToastNotifications = () => {
    // Función global para mostrar toast
    window.showToast = (message, type = 'info', duration = 5000) => {
        try {
            // Crear y disparar un evento personalizado
            const event = new CustomEvent('add-toast', {
                detail: { message, type, duration }
            });
            window.dispatchEvent(event);

            // También registramos en la consola para depuración
            console.log(`Toast (${type}): ${message}`);
        } catch (error) {
            console.error('Error al mostrar toast:', error);
        }
    };

    // Funciones de conveniencia para diferentes tipos de toast
    window.showSuccessToast = (message, duration = 5000) => window.showToast(message, 'success', duration);
    window.showErrorToast = (message, duration = 5000) => window.showToast(message, 'error', duration);
    window.showInfoToast = (message, duration = 5000) => window.showToast(message, 'info', duration);
    window.showWarningToast = (message, duration = 5000) => window.showToast(message, 'warning', duration);

    // También mostrar toast para mensajes flash existentes
    document.addEventListener('DOMContentLoaded', () => {
        try {
            const page = usePage();
            if (page.props.flash) {
                if (page.props.flash.success) {
                    window.showSuccessToast(page.props.flash.success);
                }
                if (page.props.flash.error) {
                    window.showErrorToast(page.props.flash.error);
                }
            }
        } catch (error) {
            console.error('Error al procesar mensajes flash:', error);
        }
    });
};

// Ejecutar la configuración del modo oscuro y notificaciones
setupDarkMode();
setupToastNotifications();

// Helper global para acceder a los mensajes flash de forma segura
window.flash = function(type) {
    try {
        const page = usePage();
        if (page.props && page.props.flash && page.props.flash[type]) {
            return page.props.flash[type];
        }
    } catch (error) {
        console.error('Error al acceder a mensaje flash:', error);
    }
    return null;
};

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
            try {
                const page = usePage();
                if (page.props && page.props.flash && page.props.flash[type]) {
                    return page.props.flash[type];
                }
            } catch (error) {
                console.error('Error al acceder a mensaje flash desde plugin:', error);
            }
            return null;
        };
    }
};
