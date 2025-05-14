<template>
    <Head title="Items disponibles" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Items disponibles</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Filtradores y buscador -->
                        <div class="mb-6 flex justify-between items-center">
                            <div>
                                <!-- Filtros aquí -->
                            </div>
                            <Link
                                v-if="$page.props.auth.user"
                                :href="route('items.create')"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Publicar un nuevo item
                            </Link>
                        </div>

                        <!-- Lista de items -->
                        <div v-if="items.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="item in items.data" :key="item.id" class="border rounded-lg overflow-hidden">
                                <div class="h-48 bg-gray-200 flex items-center justify-center">
                                    <img
                                        v-if="item.images && item.images.length"
                                        :src="'/storage/' + item.images[0]"
                                        alt="Imagen de item"
                                        class="w-full h-full object-cover"
                                    >
                                    <div v-else class="text-gray-400">Sin imagen</div>
                                </div>
                                <div class="p-4">
                                    <h3 class="text-lg font-bold">{{ item.title }}</h3>
                                    <p class="text-sm text-gray-600">
                                        Categoría: {{ item.category ? item.category.name : 'Sin categoría' }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Estado: {{ item.condition }}
                                    </p>
                                    <p class="mt-2 text-sm line-clamp-2">{{ item.description }}</p>
                                    <div class="mt-4">
                                        <Link
                                            :href="route('items.show', item.id)"
                                            class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                        >
                                            Ver detalles
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <p class="text-gray-500">No hay items disponibles en este momento.</p>
                        </div>

                        <!-- Paginación -->
                        <div class="mt-6">
                            <Pagination :links="items.links" />
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

// items es una prop proporcionada por el controlador
defineProps({
    items: Object,
});
</script>
