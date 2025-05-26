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
                <!-- Mensajes de alerta -->
                <div v-if="flash('success')" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ flash('success') }}
                </div>
                <div v-if="flash('error')" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ flash('error') }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <!-- Panel de búsqueda y filtros -->
                        <div class="mb-6">
                            <form @submit.prevent="search">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <!-- Búsqueda por término -->
                                    <div class="md:col-span-2">
                                        <InputLabel for="search" value="Buscar" />
                                        <TextInput
                                            id="search"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="filters.search"
                                            placeholder="Buscar por título, descripción..."
                                        />
                                    </div>

                                    <!-- Filtro por categoría -->
                                    <div>
                                        <InputLabel for="category" value="Categoría" />
                                        <select
                                            id="category"
                                            v-model="filters.category"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        >
                                            <option value="">Todas las categorías</option>
                                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                                {{ category.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Ubicación -->
                                    <div>
                                        <InputLabel for="location" value="Ubicación" />
                                        <TextInput
                                            id="location"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="filters.location"
                                            placeholder="Ciudad, barrio..."
                                        />
                                    </div>
                                </div>

                                <div class="flex justify-between mt-4">
                                    <!-- Selector de ordenación -->
                                    <div class="flex items-center space-x-4">
                                        <span class="text-sm text-gray-600">Ordenar por:</span>
                                        <select
                                            v-model="filters.sort_by"
                                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                            @change="search"
                                        >
                                            <option value="created_at">Fecha</option>
                                            <option value="title">Título</option>
                                        </select>
                                        <select
                                            v-model="filters.sort_order"
                                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                            @change="search"
                                        >
                                            <option value="desc">Descendente</option>
                                            <option value="asc">Ascendente</option>
                                        </select>
                                    </div>

                                    <div class="flex space-x-2">
                                        <PrimaryButton type="submit">
                                            Buscar
                                        </PrimaryButton>
                                        <SecondaryButton type="button" @click="resetFilters">
                                            Limpiar filtros
                                        </SecondaryButton>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Resultados filtrados -->
                        <div v-if="requests.total === 0" class="text-center py-10">
                            <p class="text-gray-500 mb-4">No se encontraron solicitudes que coincidan con tus criterios de búsqueda.</p>
                            <SecondaryButton @click="resetFilters">Limpiar filtros</SecondaryButton>
                        </div>

                        <!-- Lista de solicitudes -->
                        <div v-else>
                            <p class="text-sm text-gray-500 mb-4">
                                Mostrando {{ requests.from }}-{{ requests.to }} de {{ requests.total }} resultados
                            </p>

                            <div class="space-y-4">
                                <div v-for="request in requests.data" :key="request.id" class="border rounded-lg overflow-hidden p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-bold">{{ request.title }}</h3>
                                            <p class="text-sm bg-blue-100 text-blue-800 inline-block px-2 py-1 rounded mb-2">
                                                {{ request.category ? request.category.name : 'Sin categoría' }}
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
                                            {{ formatDate(request.created_at) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Paginación -->
                            <div class="mt-6">
                                <Pagination :links="requests.links" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

// Props
const props = defineProps({
    requests: Object,
    filters: Object,
    categories: Array
});

// Copiar los filtros a un ref para poder modificarlos
const filters = ref({...props.filters});

const search = () => {
    router.get(route('requests.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
        only: ['requests']
    });
};

const resetFilters = () => {
    filters.value = {
        search: '',
        category: '',
        location: '',
        sort_by: 'created_at',
        sort_order: 'desc'
    };
    search();
};

// Formatear fecha
const formatDate = (dateString) => {
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
