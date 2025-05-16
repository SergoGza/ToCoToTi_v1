<!-- resources/js/Components/ImageViewer.vue -->
<template>
    <div>
        <!-- Galería de miniaturas -->
        <div v-if="images && images.length > 1" class="grid grid-cols-5 gap-2 mb-4">
            <div
                v-for="(image, index) in images"
                :key="index"
                class="h-16 bg-gray-200 rounded overflow-hidden cursor-pointer border-2"
                :class="{ 'border-blue-500': currentIndex === index, 'border-transparent': currentIndex !== index }"
                @click="setCurrentImage(index)"
            >
                <img :src="getImageUrl(image)" alt="Miniatura" class="w-full h-full object-cover">
            </div>
        </div>

        <!-- Imagen principal con zoom y controles -->
        <div v-if="images && images.length" class="relative">
            <div
                class="h-64 md:h-96 bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center"
                @mousemove="handleMouseMove"
                @mouseleave="isZoomed = false"
                @click="toggleZoom"
                :class="{ 'cursor-zoom-in': !isZoomed, 'cursor-zoom-out': isZoomed }"
                ref="imageContainer"
            >
                <img
                    :src="getImageUrl(images[currentIndex])"
                    alt="Imagen principal"
                    class="max-w-full max-h-full transition-transform duration-200"
                    :style="zoomStyles"
                >
            </div>

            <!-- Controles de navegación -->
            <button
                v-if="images.length > 1"
                @click.stop="prevImage"
                class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-75 rounded-full p-2 hover:bg-opacity-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button
                v-if="images.length > 1"
                @click.stop="nextImage"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-75 rounded-full p-2 hover:bg-opacity-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Botones de acciones -->
            <div class="absolute bottom-2 right-2 space-x-2">
                <button
                    @click.stop="toggleFullscreen"
                    class="bg-white bg-opacity-75 rounded-full p-2 hover:bg-opacity-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    title="Pantalla completa"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                    </svg>
                </button>
            </div>

            <!-- Indicador de índice de imagen -->
            <div v-if="images.length > 1" class="absolute bottom-2 left-2 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">
                {{ currentIndex + 1 }} / {{ images.length }}
            </div>
        </div>

        <!-- Vista de pantalla completa -->
        <div
            v-if="isFullscreen"
            class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center"
            @click="closeFullscreen"
        >
            <div class="relative max-w-7xl max-h-screen p-4">
                <img
                    :src="getImageUrl(images[currentIndex])"
                    alt="Imagen en pantalla completa"
                    class="max-w-full max-h-[calc(100vh-8rem)] object-contain"
                >

                <!-- Controles de navegación -->
                <button
                    v-if="images.length > 1"
                    @click.stop="prevImage"
                    class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 rounded-full p-3 hover:bg-opacity-75 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button
                    v-if="images.length > 1"
                    @click.stop="nextImage"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 rounded-full p-3 hover:bg-opacity-75 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Botón de cierre -->
                <button
                    @click.stop="closeFullscreen"
                    class="absolute top-4 right-4 bg-white bg-opacity-50 rounded-full p-3 hover:bg-opacity-75 focus:outline-none focus:ring-2 focus:ring-red-500"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Contador de imágenes -->
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-50 text-white px-4 py-2 rounded-full">
                    {{ currentIndex + 1 }} / {{ images.length }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    images: {
        type: Array,
        required: true
    },
    storagePath: {
        type: String,
        default: '/storage/'
    }
});

const currentIndex = ref(0);
const isZoomed = ref(false);
const zoomLevel = ref(1.5); // Nivel de zoom
const zoomPosition = ref({ x: 0, y: 0 });
const isFullscreen = ref(false);
const imageContainer = ref(null);

// Obtener la URL completa de la imagen
const getImageUrl = (image) => {
    if (!image) return '';

    // Si ya es una URL completa, devolverla tal cual
    if (image.startsWith('http://') || image.startsWith('https://')) {
        return image;
    }

    // Combinar con la ruta de almacenamiento
    return props.storagePath + image;
};

// Calcular estilos para el zoom
const zoomStyles = computed(() => {
    if (!isZoomed.value) return {};

    return {
        transform: `scale(${zoomLevel.value})`,
        transformOrigin: `${zoomPosition.value.x * 100}% ${zoomPosition.value.y * 100}%`
    };
});

// Cambiar a imagen anterior
const prevImage = (event) => {
    if (event) event.stopPropagation();
    currentIndex.value = (currentIndex.value - 1 + props.images.length) % props.images.length;
};

// Cambiar a siguiente imagen
const nextImage = (event) => {
    if (event) event.stopPropagation();
    currentIndex.value = (currentIndex.value + 1) % props.images.length;
};

// Seleccionar una imagen específica
const setCurrentImage = (index) => {
    currentIndex.value = index;
};

// Manejar el movimiento del mouse para el zoom
const handleMouseMove = (event) => {
    if (!imageContainer.value) return;

    const rect = imageContainer.value.getBoundingClientRect();

    // Calcular posición relativa del cursor (0 a 1)
    zoomPosition.value = {
        x: (event.clientX - rect.left) / rect.width,
        y: (event.clientY - rect.top) / rect.height
    };
};

// Alternar el estado de zoom
const toggleZoom = () => {
    isZoomed.value = !isZoomed.value;
};

// Alternar pantalla completa
const toggleFullscreen = (event) => {
    if (event) event.stopPropagation();
    isFullscreen.value = !isFullscreen.value;

    // Evitar scroll del body cuando está en pantalla completa
    if (isFullscreen.value) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
};

// Cerrar vista de pantalla completa
const closeFullscreen = () => {
    isFullscreen.value = false;
    document.body.style.overflow = '';
};

// Exponer métodos para uso externo
defineExpose({
    nextImage,
    prevImage,
    setCurrentImage,
    toggleFullscreen
});
</script>
