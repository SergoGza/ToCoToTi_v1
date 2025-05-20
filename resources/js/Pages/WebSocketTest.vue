<template>
    <Head title="WebSockets Test" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">WebSockets Test</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-white">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold mb-2">Estado de conexión WebSockets</h3>
                            <div class="flex items-center">
                                <span class="w-3 h-3 rounded-full mr-2" :class="{
                                    'bg-green-500': isConnected,
                                    'bg-red-500': !isConnected
                                }"></span>
                                <span>{{ isConnected ? 'Conectado' : 'Desconectado' }}</span>
                            </div>
                            <div v-if="lastError" class="mt-2 text-red-500 text-sm">
                                Error: {{ lastError }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <button
                                @click="reconnect"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Reconectar
                            </button>
                        </div>

                        <div class="mt-6">
                            <h3 class="text-lg font-semibold mb-2">Enviar mensaje de prueba</h3>
                            <div class="mb-3">
                                <label class="block mb-1">Mensaje:</label>
                                <input
                                    v-model="testMessage"
                                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Escribe un mensaje de prueba"
                                />
                            </div>
                            <button
                                @click="sendTestEvent"
                                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                                :disabled="!testMessage"
                            >
                                Enviar evento de prueba
                            </button>
                        </div>

                        <div class="mt-6">
                            <h3 class="text-lg font-semibold mb-2">Eventos recibidos:</h3>
                            <div class="bg-gray-100 p-4 rounded max-h-60 overflow-y-auto dark:bg-gray-700">
                                <div v-if="events.length === 0" class="text-gray-500 dark:text-gray-400">
                                    No hay eventos recibidos aún
                                </div>
                                <div v-for="(event, index) in events" :key="index" class="mb-2 p-2 border-b border-gray-200 dark:border-gray-600">
                                    <div class="flex justify-between">
                                        <span class="font-medium">{{ event.type }}</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ formatTime(event.time) }}</span>
                                    </div>
                                    <pre class="text-sm mt-1 break-words whitespace-pre-wrap">{{ JSON.stringify(event.data, null, 2) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

// Estado
const isConnected = ref(false);
const lastError = ref(null);
const testMessage = ref('');
const events = ref([]);

// Método para reconectar
const reconnect = () => {
    if (window.Echo) {
        try {
            window.Echo.disconnect();
            setTimeout(() => {
                window.initializeEcho();
                setupListeners();
            }, 1000);
        } catch (error) {
            console.error('Error al reconectar:', error);
            lastError.value = error.message;
        }
    }
};

// Enviar evento de prueba
const sendTestEvent = async () => {
    if (!testMessage.value) return;

    try {
        const response = await axios.post('/broadcast-test', {
            message: testMessage.value
        });

        addEvent('Enviado', {
            message: testMessage.value,
            response: response.data
        });

        testMessage.value = '';
    } catch (error) {
        console.error('Error al enviar evento:', error);
        lastError.value = error.message;
    }
};

// Añadir evento al registro
const addEvent = (type, data) => {
    events.value.unshift({
        type,
        data,
        time: new Date()
    });

    // Limitar el número de eventos mostrados
    if (events.value.length > 20) {
        events.value = events.value.slice(0, 20);
    }
};

// Formatear hora
const formatTime = (date) => {
    return date.toLocaleTimeString();
};

// Configurar listeners
const setupListeners = () => {
    if (window.Echo) {
        // Escuchar eventos en el canal público
        window.Echo.channel('public-channel')
            .listen('TestEvent', (e) => {
                console.log('Evento de prueba recibido:', e);
                addEvent('TestEvent recibido', e);
            });

        // Actualizar estado de conexión
        updateConnectionStatus();
    }
};

// Actualizar estado de conexión
const updateConnectionStatus = () => {
    if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
        isConnected.value = window.Echo.connector.pusher.connection.state === 'connected';
        console.log('Estado de conexión:', window.Echo.connector.pusher.connection.state);
    } else {
        isConnected.value = false;
    }
};

// Handlers para eventos
const handleConnect = () => {
    isConnected.value = true;
    lastError.value = null;
    addEvent('Conectado', { time: new Date() });
};

const handleDisconnect = () => {
    isConnected.value = false;
    addEvent('Desconectado', { time: new Date() });
};

const handleError = (err) => {
    isConnected.value = false;
    lastError.value = err.message || 'Error desconocido';
    addEvent('Error', { error: err });
};

// Ciclo de vida del componente
onMounted(() => {
    // Configurar listeners para eventos globales de WebSocket
    window.addEventListener('websocket-connected', handleConnect);
    window.addEventListener('websocket-disconnected', handleDisconnect);
    window.addEventListener('websocket-error', (e) => handleError(e.detail));

    // Inicializar conexión
    if (window.wsConnected) {
        isConnected.value = true;
    } else if (window.wsLastError) {
        lastError.value = window.wsLastError.message || 'Error desconocido';
    }

    // Configurar listeners para canales
    setupListeners();

    // Verificar estado de conexión
    setInterval(updateConnectionStatus, 5000);
});

onBeforeUnmount(() => {
    // Limpiar listeners
    window.removeEventListener('websocket-connected', handleConnect);
    window.removeEventListener('websocket-disconnected', handleDisconnect);
    window.removeEventListener('websocket-error', handleError);
});
</script>
