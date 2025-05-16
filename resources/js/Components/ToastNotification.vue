<!-- resources/js/Components/ToastNotification.vue -->
<template>
    <div class="fixed bottom-4 right-4 z-50 flex flex-col space-y-2">
        <transition-group
            name="toast"
            tag="div"
            class="space-y-2"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="p-4 max-w-md rounded-lg shadow-lg transform transition-all duration-300"
                :class="{
                    'bg-green-100 border border-green-400 text-green-700': toast.type === 'success',
                    'bg-red-100 border border-red-400 text-red-700': toast.type === 'error',
                    'bg-blue-100 border border-blue-400 text-blue-700': toast.type === 'info',
                    'bg-yellow-100 border border-yellow-400 text-yellow-700': toast.type === 'warning'
                }"
            >
                <div class="flex justify-between items-start">
                    <div class="flex items-center">
                        <!-- Icono basado en el tipo -->
                        <svg v-if="toast.type === 'success'" class="h-6 w-6 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg v-else-if="toast.type === 'error'" class="h-6 w-6 mr-2 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg v-else-if="toast.type === 'warning'" class="h-6 w-6 mr-2 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <svg v-else class="h-6 w-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>

                        <span>{{ toast.message }}</span>
                    </div>
                    <button @click="removeToast(toast.id)" class="text-gray-500 hover:text-gray-800">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </transition-group>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const toasts = ref([]);
let toastIdCounter = 0;

// Añadir una nueva notificación toast
const addToast = (message, type = 'info', duration = 5000) => {
    const id = toastIdCounter++;

    toasts.value.push({
        id,
        message,
        type,
    });

    // Eliminar automáticamente después de la duración especificada
    setTimeout(() => {
        removeToast(id);
    }, duration);
};

// Eliminar una notificación específica
const removeToast = (id) => {
    const index = toasts.value.findIndex(toast => toast.id === id);
    if (index !== -1) {
        toasts.value.splice(index, 1);
    }
};

// Limpiar todas las notificaciones
const clearToasts = () => {
    toasts.value = [];
};

// Métodos específicos por tipo
const success = (message, duration = 5000) => addToast(message, 'success', duration);
const error = (message, duration = 5000) => addToast(message, 'error', duration);
const info = (message, duration = 5000) => addToast(message, 'info', duration);
const warning = (message, duration = 5000) => addToast(message, 'warning', duration);

// Exponer los métodos para usarlos desde fuera
defineExpose({
    addToast,
    removeToast,
    clearToasts,
    success,
    error,
    info,
    warning
});

// Eventos globales para mostrar toasts
const handleAddToast = (event) => {
    const { message, type, duration } = event.detail;
    addToast(message, type, duration);
};

// Escuchar eventos globales
onMounted(() => {
    window.addEventListener('add-toast', handleAddToast);
});

onUnmounted(() => {
    window.removeEventListener('add-toast', handleAddToast);
});
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from {
    opacity: 0;
