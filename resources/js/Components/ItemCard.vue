<!-- resources/js/Components/ItemCard.vue -->
<template>
    <div class="border rounded-lg overflow-hidden transition-shadow hover:shadow-md bg-white">
        <!-- Imagen del ítem -->
        <div class="h-48 bg-bg-light flex items-center justify-center overflow-hidden">
            <img
                v-if="item.images && item.images.length"
                :src="'/storage/' + item.images[0]"
                alt="Imagen de item"
                class="w-full h-full object-cover"
            >
            <div v-else class="text-gray-400 flex items-center justify-center h-full w-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <!-- Contenido -->
        <div class="p-4">
            <h3 class="text-lg font-bold text-text-dark truncate">{{ item.title }}</h3>

            <div class="flex space-x-2 mb-2 mt-1 flex-wrap">
                <span class="px-2 py-1 bg-primary bg-opacity-10 text-primary rounded-full text-xs font-medium">
                    {{ item.category ? item.category.name : 'Sin categoría' }}
                </span>
                <span class="px-2 py-1 bg-secondary bg-opacity-10 text-secondary rounded-full text-xs font-medium">
                    {{ formatCondition(item.condition) }}
                </span>
            </div>

            <p v-if="item.location" class="text-sm text-gray-600 mb-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ item.location }}
            </p>

            <p class="mt-2 text-sm line-clamp-2 text-gray-700">{{ item.description }}</p>

            <div class="mt-4 flex justify-between items-center">
                <Link
                    :href="route('items.show', item.id)"
                    class="inline-flex items-center px-3 py-1.5 bg-primary text-white rounded-full hover:bg-primary-light text-sm font-medium transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Ver detalles
                </Link>

                <span class="text-xs text-gray-500">
                    {{ formatDate(item.created_at) }}
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    item: {
        type: Object,
        required: true
    }
});

const formatCondition = (condition) => {
    const conditionMap = {
        'nuevo': 'Nuevo',
        'como_nuevo': 'Como nuevo',
        'buen_estado': 'Buen estado',
        'usado': 'Usado',
        'necesita_reparacion': 'Necesita reparación'
    };

    return conditionMap[condition] || condition;
};

const formatDate = (dateString) => {
    if (!dateString) return '';

    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays < 1) {
        return 'Hoy';
    } else if (diffDays === 1) {
        return 'Ayer';
    } else if (diffDays < 7) {
        return `Hace ${diffDays} días`;
    } else {
        return date.toLocaleDateString();
    }
};
</script>
