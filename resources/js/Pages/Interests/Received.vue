<template>
    <Head title="Intereses recibidos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Intereses recibidos en mis items</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Mensajes de alerta -->
                <div v-if="$page.props.flash && $page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash && $page.props.flash.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ $page.props.flash.error }}
                </div>

                <!-- Panel de filtros -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <form @submit.prevent="applyFilters">
                            <div class="flex flex-wrap gap-4">
                                <div class="w-full md:w-auto">
                                    <InputLabel for="status-filter" value="Estado" />
                                    <select
                                        id="status-filter"
                                        v-model="filters.status"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    >
                                        <option value="">Todos</option>
                                        <option value="pending">Pendientes</option>
                                        <option value="accepted">Aceptados</option>
                                        <option value="rejected">Rechazados</option>
                                    </select>
                                </div>

                                <div class="w-full md:w-auto md:flex-grow">
                                    <InputLabel for="item-filter" value="Item" />
                                    <select
                                        id="item-filter"
                                        v-model="filters.item"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    >
                                        <option value="">Todos mis items</option>
                                        <option v-for="item in uniqueItems" :key="item.id" :value="item.id">
                                            {{ item.title }}
                                        </option>
                                    </select>
                                </div>

                                <div class="w-full md:w-auto flex items-end">
                                    <PrimaryButton type="submit" class="mt-1">Filtrar</PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Intereses recibidos -->
                        <div v-if="interests.data.length" class="space-y-4">
                            <div v-for="interest in interests.data" :key="interest.id"
                                 class="border rounded-lg overflow-hidden p-4"
                                 :class="{
                                    'border-yellow-400 bg-yellow-50': interest.status === 'pending',
                                    'border-green-400 bg-green-50': interest.status === 'accepted',
                                    'border-red-400 bg-red-50': interest.status === 'rejected'
                                 }">
                                <div class="flex flex-col md:flex-row gap-4">
                                    <!-- Imagen del item y datos básicos -->
                                    <div class="md:w-1/4">
                                        <div class="h-32 bg-gray-200 rounded-lg overflow-hidden">
                                            <img
                                                v-if="interest.item.images && interest.item.images.length"
                                                :src="'/storage/' + interest.item.images[0]"
                                                class="w-full h-full object-cover"
                                                alt="Imagen del item"
                                            >
                                            <div v-else class="w-full h-full flex items-center justify-center text-gray-500">
                                                Sin imagen
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <h3 class="font-semibold">{{ interest.item.title }}</h3>
                                            <p class="text-sm text-gray-600">{{ interest.item.category ? interest.item.category.name : 'Sin categoría' }}</p>
                                        </div>
                                    </div>

                                    <!-- Datos del interesado y mensaje -->
                                    <div class="md:w-2/4">
                                        <div class="mb-2">
                                            <span class="font-semibold">Interesado:</span> {{ interest.user.name }}
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold">Fecha:</span> {{ formatDate(interest.created_at) }}
                                        </div>
                                        <div v-if="interest.message" class="mt-2 p-3 bg-gray-100 rounded-lg">
                                            <span class="font-semibold">Mensaje:</span> "{{ interest.message }}"
                                        </div>

                                        <!-- Badge de estado -->
                                        <div class="mt-3">
                                            <span
                                                class="px-2 py-1 rounded text-sm"
                                                :class="{
                                                    'bg-yellow-100 text-yellow-800': interest.status === 'pending',
                                                    'bg-green-100 text-green-800': interest.status === 'accepted',
                                                    'bg-red-100 text-red-800': interest.status === 'rejected'
                                                }"
                                            >
                                                {{ statusText(interest.status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Acciones -->
                                    <div class="md:w-1/4 flex flex-col justify-center space-y-2">
                                        <!-- Ver detalles del item -->
                                        <Link
                                            :href="route('items.show', interest.item.id)"
                                            class="px-4 py-2 bg-blue-600 text-white text-center rounded hover:bg-blue-700"
                                        >
                                            Ver item
                                        </Link>

                                        <!-- Botones de acción para intereses pendientes -->
                                        <template v-if="interest.status === 'pending'">
                                            <button
                                                @click="updateInterestStatus(interest.id, 'accepted')"
                                                class="px-4 py-2 bg-green-600 text-white text-center rounded hover:bg-green-700"
                                            >
                                                Aceptar interés
                                            </button>
                                            <button
                                                @click="updateInterestStatus(interest.id, 'rejected')"
                                                class="px-4 py-2 bg-red-600 text-white text-center rounded hover:bg-red-700"
                                            >
                                                Rechazar
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <p class="text-gray-500">No has recibido intereses en tus items todavía.</p>
                            <Link
                                :href="route('items.create')"
                                class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Publicar un nuevo item
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
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

// Props
const props = defineProps({
    interests: Object,
    filters: Object,
    uniqueItems: Array
});

// Copiar los filtros a un ref para poder modificarlos
const filters = ref({...props.filters});

// Método para aplicar filtros
const applyFilters = () => {
    router.get(route('interests.received'), filters.value, {
        preserveState: true,
        preserveScroll: true,
        only: ['interests']
    });
};

// Método para actualizar estado del interés
const updateInterestStatus = (interestId, status) => {
    useForm({ status }).patch(route('interests.update', interestId), {
        preserveScroll: true,
        onSuccess: () => {
            // El servidor ya se encarga de actualizar la página
        }
    });
};

// Formatear fecha
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

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
