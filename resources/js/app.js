import '../css/app.css';
import './bootstrap';

import { createInertiaApp, usePage } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Importar Echo y Pusher después de las otras importaciones
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

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
    window.showToast = (message, type = 'info', duration = 5000) => {
        try {
            // Crear y disparar un evento personalizado
            const event = new CustomEvent('add-toast', {
                detail: { message, type, duration }
            });
            window.dispatchEvent(event);

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
};

// Configuración de Echo - Colocada después de las funciones de setup
window.Pusher = Pusher;

// Configuración segura de Echo
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('meta[name="user-id"]')) {
        window.userId = document.querySelector('meta[name="user-id"]').getAttribute('content');

        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: window.location.hostname,
            wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
            forceTLS: false,
            disableStats: true,
            debug: true,  // Importante para más logs
            enabledTransports: ['ws', 'wss'],
            cluster: 'mt1',
            encrypted: false,

            authorizer: (channel, options) => ({
                authorize: (socketId, callback) => {
                    console.log('Intentando autorización de canal:', channel.name);

                    axios.post('/broadcasting/auth', {
                        socket_id: socketId,
                        channel_name: channel.name
                    })
                        .then(response => {
                            console.log('Autorización exitosa:', response.data);
                            callback(false, response.data);
                        })
                        .catch(error => {
                            console.error('Error de autorización:', error);
                            callback(true, error);
                        });
                }
            }),
        });

        console.log('Echo inicializado para el usuario ID:', window.userId);
    }
});

const updateCsrfToken = () => {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        window.csrfToken = token.content;

        // Debug: confirmar que el token está configurado
        console.log('Token CSRF actualizado:', token.content.substring(0, 10) + '...');
        return true;
    } else {
        console.error('Meta tag csrf-token no encontrada');
        return false;
    }
};

window.getCsrfToken = () => {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    return token ? token.content : null;
};

window.verifyCsrfToken = () => {
    const token = window.getCsrfToken();
    const hasToken = !!token;
    console.log('Verificación CSRF:', hasToken ? 'OK' : 'FALTA');
    if (hasToken) {
        console.log('Token:', token.substring(0, 10) + '...');
    }
    return hasToken;
};

// Ejecutar la configuración del modo oscuro
setupDarkMode();
setupToastNotifications();

// Asegurarse de que el token CSRF esté actualizado
document.addEventListener('DOMContentLoaded', () => {
    updateCsrfToken();

    setTimeout(() => {
        if (!window.verifyCsrfToken()) {
            console.warn('Token CSRF no disponible después del DOM ready');
        }
    }, 100);
});

document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        updateCsrfToken();
    }
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

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(flashPlugin);

        // Montar la aplicación
        app.mount(el);

        // Solo procesar mensajes flash después de que la aplicación esté montada
        setTimeout(() => {
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
        }, 0);

        setTimeout(() => {
            window.verifyCsrfToken();
        }, 500);
    },
    progress: {
        color: '#4B5563',
    },
});
