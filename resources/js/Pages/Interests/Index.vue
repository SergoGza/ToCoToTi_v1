<template>
    <Head title="Mis intereses" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis intereses</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div v-if="interests.data.length" class="space-y-6">
                            <div v-for="interest in interests.data" :key="interest.id" class="border rounded-lg overflow-hidden">
                                <div class="flex flex-col md:flex-row">
                                    <div class="md:w-1/4 bg-gray-100">
                                        <div class="h-40 flex items-center justify-center">
                                            <img
                                                v-if="interest.item.images && interest.item.images.length"
                                                :src="'/storage/' + interest.item.images[0]"
                                                alt="Imagen del item"
                                                class="w-full h-full object-cover"
                                            >
                                            <div v-else class="text-gray-400">Sin imagen</div>
                                        </div>
                                    </div>
                                    <div class="md:w-3/4 p-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h3 class="text-lg font-bold">{{ interest.item.title }}</h3>
                                                <p class="text-sm text-gray-600">
                                                    Categoría: {{ interest.item.category ? interest.item.category.name : 'Sin categoría' }}
                                                </p>
                                                <p class="text-sm text-gray-600 mb-2">
                                                    Ofrecido por: {{ interest.item.user.name }}
                                                </p>
                                            </div>
                                            <div>
                                                <span
                                                    :class="{
                                                        'bg-yellow-100 text-yellow-800': interest.status === 'pending',
                                                        'bg-green-100 text-green-800': interest.status === 'accepted',
                                                        'bg-red-100 text-red-800': interest.status === 'rejected'
                                                    }"
                                                    class="px-2 py-1 rounded text-sm"
                                                >
                                                    {{ statusText(interest.status) }}
                                                </span>
                                            </div>
                                        </div>

                                        <p class="mt-2 text-sm line-clamp-2">{{ interest.item.description }}</p>

                                        <div v-if="interest.message" class="mt-2 p-3 bg-gray-50 rounded-lg">
                                            <p class="text-sm italic">Tu mensaje: "{{ interest.message }}"</p>
                                        </div>

                                        <div class="mt-4">
                                            <Link
                                                :href="route('items.show', interest.item.id)"
                                                class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                            >
                                                Ver detalles
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <p class="text-gray-500">No has mostrado interés en ningún item todavía.</p>
                            <Link
                                :href="route('items.index')"
                                class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Explorar items disponibles
                            </Link>
                        </div>

                        <!-- Paginación -->
                        <div class="mt-6">
                            <Pagination :links="interests.links" />
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
    interests: Object
});

// Convertir el estado a texto legible
const statusText = (status) => {
    const statusMap = {
        'pending': 'Pendiente',
        'accepted': 'Aceptado',
        'rejected': 'Rechazado'
    };

    return statusMap[status] || status;
};
</script>
