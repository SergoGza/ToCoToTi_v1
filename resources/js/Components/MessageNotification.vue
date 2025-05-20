<template>
    <div class="relative inline-block">
        <Link :href="route('messages.index')" class="relative flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <span v-if="unreadCount > 0" class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-xs text-white">
                {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
        </Link>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const unreadCount = ref(0);
let polling = null;

// Función para obtener el número de mensajes no leídos
const getUnreadCount = async () => {
    try {
        const response = await axios.get(route('api.unreadMessages'), {
            headers: {
                'X-Silent-Request': 'true',
                'X-Inertia-Polling': 'true',
            }
        });
        unreadCount.value = response.data.count;
    } catch (error) {
        console.error('Error al obtener los mensajes no leídos:', error);
    }
};

onMounted(() => {
    // Obtener el conteo inicial
    getUnreadCount();

    // Configurar Echo para escuchar nuevos mensajes
    if (window.Echo) {
        window.Echo.private(`user.${window.userId}`)
            .listen('NewMessageEvent', () => {
                unreadCount.value++;
            });
    }

    // Configurar polling cada minuto para mantener actualizado el contador
    polling = setInterval(getUnreadCount, 60000); // Cada minuto
});

onUnmounted(() => {
    if (polling) {
        clearInterval(polling);
    }

    // Limpiar Echo
    if (window.Echo && window.userId) {
        window.Echo.leave(`private-user.${window.userId}`);
    }
});
</script>
