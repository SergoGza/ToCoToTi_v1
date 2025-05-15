<template>
    <Head title="Mis solicitudes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis solicitudes</h2>
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
                <!-- Mensajes de alerta -->
                <div v-if="flash('success')" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ flash('success') }}
                </div>
                <div v-if="flash('error')" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ flash('error') }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Lista de solicitudes -->
                        <div v-if="requests.data.length" class="space-y-4">
                            <div v-for="request in requests.data" :key="request.id" class="border rounded-lg overflow-hidden p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-bold">{{ request.title }}</h3>
                                        <div class="flex flex-wrap gap-2 my-2">
                                            <p class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                {{ request.category ? request.category.name : 'Sin categoría' }}
                                            </p>
                                            <p class="text-sm px-2 py-1 rounded"
                                               :class="{
                                                   'bg-green-100 text-green-800': request.status === 'active',
                                                   'bg-gray-100 text-gray-800': request.status === 'fulfilled',
                                                   'bg-red-100 text-red-800': request.status === 'cancelled'
                                               }">
                                                {{ statusText(request.status) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div v-if="request.location" class="text-sm text-gray-500">
                                        Ubicación: {{ request.location }}
                                    </div>
                                </div>

                                <p class="mt-2 line-clamp-2">{{ request.description }}</p>

                                <div class="mt-4 flex justify-between items-center">
                                    <div class="space-x-2">
                                        <Link
                                            :href="route('requests.show', request.id)"
                                            class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                        >
                                            Ver detalles
                                        </Link>
                                        <Link
                                            v-if="request.status === 'active'"
                                            :href="route('requests.edit', request.id)"
                                            class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                                        >
                                            Editar
                                        </Link>
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        Publicado {{ formatDate(request.created_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <p class="text-gray-500">No has publicado ninguna solicitud todavía.</p>
                            <Link
                                :href="route('requests.create')"
                                class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Publicar una solicitud
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
const props = defineProps({
    requests: Object
});

// Formatear el estado
const statusText = (status) => {
    const statusMap = {
        'active': 'Activa',
        'fulfilled': 'Satisfecha',
        'cancelled': 'Cancelada'
    };

    return statusMap[status] || status;
};

// Formatear fecha
const formatDate = (dateString) => {
    const date = new Date(dateString);
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
