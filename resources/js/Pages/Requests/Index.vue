<template>
    <Head title="Solicitudes activas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Solicitudes activas</h2>
                <Link
                    :href="route('requests.create')"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                    Publicar una solicitud
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Filtros -->
                        <div class="mb-6">
                            <!-- Implementar filtros de categoría y ubicación en futuras versiones -->
                        </div>

                        <!-- Lista de solicitudes -->
                        <div v-if="requests.data.length" class="space-y-4">
                            <div v-for="request in requests.data" :key="request.id" class="border rounded-lg overflow-hidden p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-bold">{{ request.title }}</h3>
                                        <p class="text-sm text-gray-600">
                                            Categoría: {{ request.category ? request.category.name : 'Sin categoría' }}
                                        </p>
                                        <p class="text-sm text-gray-600 mb-2">
                                            Solicitado por: {{ request.user.name }}
                                        </p>
                                    </div>
                                    <div v-if="request.location" class="text-sm text-gray-500">
                                        Ubicación: {{ request.location }}
                                    </div>
                                </div>

                                <p class="mt-2 line-clamp-3">{{ request.description }}</p>

                                <div class="mt-4 flex justify-between items-center">
                                    <Link
                                        :href="route('requests.show', request.id)"
                                        class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                    >
                                        Ver detalles
                                    </Link>
                                    <span class="text-sm text-gray-500">
                                        Publicado {{ formatDate(request.created_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <p class="text-gray-500">No hay solicitudes activas en este momento.</p>
                            <Link
                                :href="route('requests.create')"
                                class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Sé el primero en publicar una solicitud
                            </Link>
                        </div>

                        <!-- Paginación -->
                        <div class="mt-6">
                            <Pagination :links="requests.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';

// Props
defineProps({
    requests: Object
});

// Formatear la fecha de creación
const formatDate = (dateString) => {
    const date = new Date(dateString);
    // Formatear como "hace X días" o fecha completa si es más antigua
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays < 1) {
        return 'hoy';
    } else if (diffDays === 1) {
        return 'ayer';
    } else if (diffDays < 7) {
        return `hace ${diffDays} días`;
    } else {
        return date.toLocaleDateString();
    }
};
</script>
