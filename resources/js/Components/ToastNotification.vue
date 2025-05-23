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
                class="p-4 max-w-md rounded-lg shadow-lg transform transition-all duration-300 dark:border dark:border-opacity-10"
                :class="{
                    'bg-primary-light/20 border border-primary text-primary-dark dark:bg-primary-dark/30 dark:text-primary-light': toast.type === 'success',
                    'bg-red-100 border border-red-400 text-red-700 dark:bg-red-900 dark:text-red-100': toast.type === 'error',
                    'bg-blue-100 border border-blue-400 text-blue-700 dark:bg-blue-900 dark:text-blue-100': toast.type === 'info',
                    'bg-yellow-100 border border-yellow-400 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-100': toast.type === 'warning'
                }"
            >
                <div class="flex justify-between items-start">
                    <div class="flex items-center">
                        <!-- Icono basado en el tipo -->
                        <svg v-if="toast.type === 'success'" class="h-6 w-6 mr-2 text-primary dark:text-primary-light" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg v-else-if="toast.type === 'error'" class="h-6 w-6 mr-2 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg v-else-if="toast.type === 'warning'" class="h-6 w-6 mr-2 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <svg v-else class="h-6 w-6 mr-2 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>

                        <span class="text-sm md:text-base">{{ toast.message }}</span>
                    </div>
                    <button @click="removeToast(toast.id)" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 ml-2 focus:outline-none">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Barra de progreso -->
                <div
                    v-if="toast.showProgress"
                    class="mt-2 w-full bg-gray-200 rounded-full h-1 dark:bg-gray-700"
                >
                    <div
                        class="h-1 rounded-full transition-all duration-100"
                        :class="{
                            'bg-primary dark:bg-primary-light': toast.type === 'success',
                            'bg-red-500 dark:bg-red-400': toast.type === 'error',
                            'bg-blue-500 dark:bg-blue-400': toast.type === 'info',
                            'bg-yellow-500 dark:bg-yellow-400': toast.type === 'warning'
                        }"
                        :style="`width: ${toast.progress}%`"
                    ></div>
                </div>
            </div>
        </transition-group>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const toasts = ref([]);
let toastIdCounter = 0;
const progressIntervals = {};

// Añadir una nueva notificación toast
const addToast = (message, type = 'info', duration = 5000, showProgress = true) => {
    try {
        const id = toastIdCounter++;

        // Añadir el nuevo toast
        const newToast = {
            id,
            message,
            type,
            showProgress,
            progress: 100
        };

        toasts.value.push(newToast);

        // Configurar la barra de progreso si está activada
        if (showProgress && duration > 0) {
            const startTime = Date.now();
            const interval = setInterval(() => {
                const elapsedTime = Date.now() - startTime;
                const remainingPercentage = Math.max(0, 100 - (elapsedTime / duration * 100));

                // Actualizar el progreso
                const toastIndex = toasts.value.findIndex(t => t.id === id);
                if (toastIndex !== -1) {
                    toasts.value[toastIndex].progress = remainingPercentage;
                }

                // Si el tiempo ha acabado, limpiar
                if (elapsedTime >= duration) {
                    clearInterval(progressIntervals[id]);
                    delete progressIntervals[id];
                }
            }, 50);

            progressIntervals[id] = interval;
        }

        // Eliminar automáticamente después de la duración especificada
        if (duration > 0) {
            setTimeout(() => {
                removeToast(id);
            }, duration);
        }

        return id;
    } catch (error) {
        console.error('Error al añadir toast:', error);
        return -1;
    }
};

// Eliminar una notificación específica
const removeToast = (id) => {
    try {
        const index = toasts.value.findIndex(toast => toast.id === id);
        if (index !== -1) {
            toasts.value.splice(index, 1);

            // Limpiar el intervalo de progreso si existe
            if (progressIntervals[id]) {
                clearInterval(progressIntervals[id]);
                delete progressIntervals[id];
            }
        }
    } catch (error) {
        console.error('Error al eliminar toast:', error);
    }
};

// Limpiar todas las notificaciones
const clearToasts = () => {
    try {
        // Limpiar todos los intervalos de progreso
        Object.values(progressIntervals).forEach(interval => clearInterval(interval));
        toasts.value = [];
    } catch (error) {
        console.error('Error al limpiar toasts:', error);
    }
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
    try {
        const { message, type, duration } = event.detail;
        addToast(message, type, duration);
    } catch (error) {
        console.error('Error al manejar evento de toast:', error);
    }
};

// Escuchar eventos globales
onMounted(() => {
    window.addEventListener('add-toast', handleAddToast);

    // Intentar mostrar mensajes flash como toast al montar
    try {
        if (window.flash) {
            const successMsg = window.flash('success');
            const errorMsg = window.flash('error');

            if (successMsg) {
                success(successMsg);
            }

            if (errorMsg) {
                error(errorMsg);
            }
        }
    } catch (e) {
        console.error('Error al procesar mensajes flash en montaje:', e);
    }
});

onUnmounted(() => {
    window.removeEventListener('add-toast', handleAddToast);

    // Limpiar todos los intervalos de progreso
    Object.values(progressIntervals).forEach(interval => clearInterval(interval));
});
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from {
    opacity: 0;
    transform: translateY(30px);
}

.toast-leave-to {
    opacity: 0;
    transform: translateY(-30px);
}
</style>
