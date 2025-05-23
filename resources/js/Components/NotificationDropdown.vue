<template>
    <div class="relative">
        <!-- Icono y contador de notificaciones -->
        <button
            @click="toggleDropdown"
            class="flex items-center justify-center h-8 w-8 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 relative dark:hover:bg-gray-700"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>

            <!-- Contador de notificaciones -->
            <span
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 h-5 w-5 flex items-center justify-center rounded-full bg-red-500 text-white text-xs"
            >
                {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown de notificaciones -->
        <div
            v-if="isOpen"
            class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg overflow-hidden z-50 dark:bg-gray-800 dark:border dark:border-gray-700"
        >
            <div class="py-2 px-4 bg-gray-100 dark:bg-gray-700 flex justify-between items-center">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Notificaciones</h3>
                <button
                    v-if="unreadCount > 0"
                    @click="markAllAsRead"
                    class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                >
                    Marcar todas como leídas
                </button>
            </div>

            <div class="max-h-80 overflow-y-auto">
                <div v-if="loading" class="p-4 text-center">
                    <p class="text-gray-500 dark:text-gray-400">Cargando notificaciones...</p>
                </div>

                <div v-else-if="notifications.length === 0" class="p-4 text-center">
                    <p class="text-gray-500 dark:text-gray-400">No tienes notificaciones nuevas</p>
                </div>

                <div v-else>
                    <div v-for="notification in notifications" :key="notification.id"
                         class="p-3 border-b hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700"
                         :class="{ 'bg-blue-50 dark:bg-blue-900/20': !notification.read }">
                        <div class="flex items-start">
                            <!-- Icono según tipo -->
                            <div class="mr-3">
                                <div
                                    class="h-8 w-8 rounded-full flex items-center justify-center"
                                    :class="{
                                        'bg-yellow-100 dark:bg-yellow-900': notification.type === 'interest_received',
                                        'bg-green-100 dark:bg-green-900': notification.type === 'interest_accepted',
                                        'bg-red-100 dark:bg-red-900': notification.type === 'interest_rejected',
                                        'bg-blue-100 dark:bg-blue-900': notification.type.includes('item_')
                                    }"
                                >
                                    <svg
                                        v-if="notification.type === 'interest_received'"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-yellow-500 dark:text-yellow-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>

                                    <svg
                                        v-else-if="notification.type === 'interest_accepted'"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-green-500 dark:text-green-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>

                                    <svg
                                        v-else-if="notification.type === 'interest_rejected'"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-red-500 dark:text-red-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>

                                    <svg
                                        v-else
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-blue-500 dark:text-blue-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Contenido -->
                            <div class="flex-1">
                                <p class="text-sm mb-1 dark:text-gray-200">{{ notification.message }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(notification.created_at) }}</p>
                            </div>

                            <!-- Marcar como leído -->
                            <button
                                @click="markAsRead(notification)"
                                class="ml-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
                                title="Marcar como leída"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-2 px-4 border-t border-gray-100 dark:border-gray-700">
                <Link :href="route('notifications.index')" class="block text-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    Ver todas las notificaciones
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';

const isOpen = ref(false);
const notifications = ref([]);
const unreadCount = ref(0);
const loading = ref(false);

// Toggle el dropdown
const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        fetchNotifications();
    }
};

// Cerrar dropdown si se hace clic fuera
const closeOnOutsideClick = (e) => {
    if (isOpen.value && !e.target.closest('.relative')) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', closeOnOutsideClick);
    fetchNotifications();

    // Obtener notificaciones cada 30 segundos (polling como respaldo)
    const interval = setInterval(fetchNotifications, 30000);

    // Configurar Echo para escuchar nuevas notificaciones
    if (window.Echo && window.userId) {
        window.Echo.private(`user.${window.userId}`)
            .listen('NewNotificationEvent', (event) => {
                // Añadir la nueva notificación al principio
                if (event.notification) {
                    notifications.value.unshift(event.notification);
                    unreadCount.value++;

                    // Mostrar toast de notificación si tenemos la función disponible
                    if (typeof window.showToast === 'function') {
                        window.showToast(
                            event.notification.message,
                            'info',
                            5000
                        );
                    }
                }
            });
    }

    onBeforeUnmount(() => {
        clearInterval(interval);
        document.removeEventListener('click', closeOnOutsideClick);
        // Desuscribir del canal
        if (window.Echo && window.userId) {
            window.Echo.leave(`private-user.${window.userId}`);
        }
    });
});

// Obtener notificaciones no leídas
const fetchNotifications = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('api.unreadNotifications'));
        notifications.value = response.data.notifications;
        unreadCount.value = response.data.count;
    } catch (error) {
        console.error('Error al cargar notificaciones:', error);
    } finally {
        loading.value = false;
    }
};

// Marcar una notificación como leída
// Marcar una notificación como leída
const markAsRead = (notification) => {
    const form = useForm({});

    form.patch(route('notifications.markAsRead', notification.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // Eliminar la notificación del array local
            notifications.value = notifications.value.filter(n => n.id !== notification.id);
            unreadCount.value = Math.max(0, unreadCount.value - 1);

            // Si la notificación tiene un enlace, redirigir
            if (notification.link) {
                router.visit(notification.link);
                isOpen.value = false;
            }
        },
        onError: (errors) => {
            console.error('Error al marcar como leída:', errors);
        }
    });
};

// Marcar todas las notificaciones como leídas
const markAllAsRead = () => {
    const form = useForm({});

    form.post(route('notifications.markAllAsRead'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            notifications.value = [];
            unreadCount.value = 0;
        },
        onError: (errors) => {
            console.error('Error al marcar todas como leídas:', errors);
        }
    });
};

// Formatear fecha
const formatDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffMinutes = Math.floor(diffTime / (1000 * 60));
    const diffHours = Math.floor(diffTime / (1000 * 60 * 60));
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

    if (diffMinutes < 60) {
        return `Hace ${diffMinutes} ${diffMinutes === 1 ? 'minuto' : 'minutos'}`;
    } else if (diffHours < 24) {
        return `Hace ${diffHours} ${diffHours === 1 ? 'hora' : 'horas'}`;
    } else if (diffDays < 7) {
        return `Hace ${diffDays} ${diffDays === 1 ? 'día' : 'días'}`;
    } else {
        return date.toLocaleDateString();
    }
};
</script>
