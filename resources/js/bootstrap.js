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

// Importar Echo y Pusher
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Habilitar logging de Pusher para ver exactamente qué está pasando
Pusher.logToConsole = true;

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// Agregar un escucha de prueba para el evento TestEvent
window.Echo.channel('public-channel')
    .listen('TestEvent', (e) => {
        console.log('Test event received:', e);
        alert('¡Evento de prueba recibido! El broadcasting funciona.');
    });

// Registrar conexión exitosa
window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('✅ Conectado a Pusher correctamente');
});

// Registrar errores de conexión
window.Echo.connector.pusher.connection.bind('error', (err) => {
    console.error('❌ Error de conexión a Pusher:', err);
});
