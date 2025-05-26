import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const setupCsrfToken = () => {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        window.csrfToken = token.content;
        return token.content;
    } else {
        console.error('CSRF token not found');
        return null;
    }
};

// Configurar el token inicialmente
setupCsrfToken();

// Configurar Axios para incluir credenciales
window.axios.defaults.withCredentials = true;

window.updateCsrfToken = setupCsrfToken;

// Interceptor para manejar errores CSRF
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 419) {
            console.error('CSRF token mismatch. Token actual:', window.csrfToken);
            console.error('Recargando página para obtener nuevo token...');

            // Mostrar mensaje de error si está disponible
            if (window.showErrorToast) {
                window.showErrorToast('Error de seguridad. Recargando página...');
            }

            setTimeout(() => {
                window.location.reload();
            }, 1500);
        }
        return Promise.reject(error);
    }
);

// Añadir un interceptor para detectar peticiones de polling
axios.interceptors.request.use(config => {
    // Asegurar que todas las peticiones tengan el token CSRF más reciente
    const currentToken = document.head.querySelector('meta[name="csrf-token"]')?.content;
    if (currentToken && currentToken !== window.csrfToken) {
        window.csrfToken = currentToken;
        config.headers['X-CSRF-TOKEN'] = currentToken;
    }

    // Si la URL contiene 'recent' o hay un parámetro 'polling=true', marcarla como silenciosa
    if (config.url.includes('/recent') || config.url.includes('polling=true')) {
        config.headers['X-Inertia-Polling'] = 'true';
        config.headers['X-Silent-Request'] = 'true';
    }

    return config;
});
