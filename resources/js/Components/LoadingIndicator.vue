<!-- resources/js/Components/LoadingIndicator.vue -->
<template>
    <div v-if="loading" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-30">
        <div class="bg-white p-6 rounded-lg shadow-lg flex items-center space-x-4">
            <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700">{{ message || 'Cargando...' }}</span>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';


const props = defineProps({
    message: {
        type: String,
        default: ''
    }
});

const loading = ref(false);

// Función para mostrar el indicador de carga
const showLoading = (customMessage = '') => {
    loading.value = true;
    if (customMessage) {
        props.message = customMessage;
    }
};

// Función para ocultar el indicador de carga
const hideLoading = () => {
    loading.value = false;
};

// Exponer estas funciones para poder llamarlas desde fuera
defineExpose({
    showLoading,
    hideLoading
});

// Crear un evento global para mostrar/ocultar el indicador de carga
const handleShowLoading = (message = '') => {
    showLoading(message);
};

const handleHideLoading = () => {
    hideLoading();
};

// Escuchar eventos globales
onMounted(() => {
    window.addEventListener('show-loading', handleShowLoading);
    window.addEventListener('hide-loading', handleHideLoading);
});

onUnmounted(() => {
    window.removeEventListener('show-loading', handleShowLoading);
    window.removeEventListener('hide-loading', handleHideLoading);
});
</script>
