import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Obtener el token CSRF de la meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

// Añadir un interceptor para detectar peticiones de polling
axios.interceptors.request.use(config => {
    // Si la URL contiene 'recent' o hay un parámetro 'polling=true', marcarla como silenciosa
    if (config.url.includes('/recent') || config.url.includes('polling=true')) {
        config.headers['X-Inertia-Polling'] = 'true';
        config.headers['X-Silent-Request'] = 'true';
    }
    return config;
});
