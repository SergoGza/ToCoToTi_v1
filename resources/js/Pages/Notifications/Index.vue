<template>
    <Head title="Mis notificaciones" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Mis notificaciones</h2>
                <button
                    v-if="hasUnreadNotifications"
                    @click="markAllAsRead"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600"
                >
                    Marcar todas como leídas
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-200">
                        <div v-if="notifications.data.length" class="space-y-4">
                            <div v-for="notification in notifications.data" :key="notification.id"
                                 class="p-4 border rounded-lg overflow-hidden"
                                 :class="{ 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800': !notification.read }"
                            >
                                <div class="flex">
                                    <!-- Icono según tipo -->
                                    <div class="mr-4">
                                        <div
                                            class="h-10 w-10 rounded-full flex items-center justify-center"
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
                                                class="h-6 w-6 text-yellow-500 dark:text-yellow-400"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>

                                            <svg
                                                v-else-if="notification.type === 'interest_accepted'"
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-6 w-6 text-green-500 dark:text-green-400"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>

                                            <svg
                                                v-else-if="notification.type === 'interest_rejected'"
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-6 w-6 text-red-500 dark:text-red-400"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>

                                            <svg
                                                v-else
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-6 w-6 text-blue-500 dark:text-blue-400"
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
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-base font-medium mb-1">{{ notification.message }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(notification.created_at) }}</p>
                                            </div>

                                            <div class="flex space-x-2">
                                                <!-- Marcar como leída -->
                                                <button
                                                    v-if="!notification.read"
                                                    @click="markAsRead(notification)"
                                                    class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                                                >
                                                    Marcar como leída
                                                </button>

                                                <!-- Enlace a la página relacionada -->
                                                <Link
                                                    v-if="notification.link"
                                                    :href="notification.link"
                                                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm dark:bg-blue-700 dark:hover:bg-blue-600"
                                                >
                                                    Ver detalles
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">No tienes notificaciones todavía.</p>
                        </div>

                        <!-- Paginación -->
                        <div class="mt-6">
                            <Pagination :links="notifications.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';

// Props
const props = defineProps({
    notifications: Object
});

// Comprobar si hay notificaciones no leídas
const hasUnreadNotifications = computed(() => {
    return props.notifications.data.some(notification => !notification.read);
});

// Marcar una notificación como leída
const markAsRead = (notification) => {
    useForm({}).patch(route('notifications.markAsRead', notification.id), {
        preserveScroll: true
    });
};

// Marcar todas las notificaciones como leídas
const markAllAsRead = () => {
    useForm({}).post(route('notifications.markAllAsRead'), {
        preserveScroll: true
    });
};

// Formatear fecha
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>
