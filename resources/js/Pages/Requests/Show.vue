<template>
    <Head :title="request.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalles de la solicitud</h2>
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
                        <div class="mb-6">
                            <h1 class="text-2xl font-bold mb-2">{{ request.title }}</h1>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <p class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                    {{ request.category ? request.category.name : 'Sin categoría' }}
                                </p>
                                <p class="text-sm bg-green-100 text-green-800 px-2 py-1 rounded" v-if="request.status === 'active'">
                                    Solicitud activa
                                </p>
                                <p class="text-sm bg-gray-100 text-gray-800 px-2 py-1 rounded" v-else-if="request.status === 'fulfilled'">
                                    Solicitud satisfecha
                                </p>
                                <p class="text-sm bg-red-100 text-red-800 px-2 py-1 rounded" v-else-if="request.status === 'cancelled'">
                                    Solicitud cancelada
                                </p>
                            </div>

                            <p class="mb-2 text-sm">
                                Solicitado por: {{ request.user.name }}
                            </p>

                            <p v-if="request.location" class="mb-2 text-sm">
                                Ubicación: {{ request.location }}
                            </p>

                            <p v-if="request.expires_at" class="mb-2 text-sm text-amber-600">
                                Expira el: {{ formatDateFull(request.expires_at) }}
                            </p>

                            <p class="text-sm text-gray-500 mb-4">
                                Publicado el {{ formatDateFull(request.created_at) }}
                            </p>

                            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                <h3 class="font-semibold mb-2">Descripción:</h3>
                                <p>{{ request.description }}</p>
                            </div>

                            <!-- Opciones para el propietario -->
                            <div class="mt-6" v-if="isOwner">
                                <div class="flex space-x-4">
                                    <Link
                                        :href="route('requests.edit', request.id)"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                    >
                                        Editar solicitud
                                    </Link>
                                    <button
                                        v-if="request.status === 'active'"
                                        @click="updateStatus('fulfilled')"
                                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                                    >
                                        Marcar como satisfecha
                                    </button>
                                    <button
                                        v-if="request.status === 'active'"
                                        @click="updateStatus('cancelled')"
                                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                                    >
                                        Cancelar solicitud
                                    </button>
                                    <button
                                        @click="confirmDelete = true"
                                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                                    >
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Items que coinciden con la solicitud -->
                        <div v-if="matchingItems.length && request.status === 'active'" class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-semibold mb-4">Items disponibles que podrían interesarte:</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div v-for="item in matchingItems" :key="item.id" class="border rounded-lg overflow-hidden">
                                    <div class="h-40 bg-gray-200 flex items-center justify-center">
                                        <img
                                            v-if="item.images && item.images.length"
                                            :src="'/storage/' + item.images[0]"
                                            alt="Imagen de item"
                                            class="w-full h-full object-cover"
                                        >
                                        <div v-else class="text-gray-400">Sin imagen</div>
                                    </div>
                                    <div class="p-4">
                                        <h4 class="font-bold">{{ item.title }}</h4>
                                        <p class="text-sm text-gray-600 mb-2">
                                            Ofrecido por: {{ item.user.name }}
                                        </p>
                                        <p class="text-sm line-clamp-2 mb-3">{{ item.description }}</p>
                                        <Link
                                            :href="route('items.show', item.id)"
                                            class="inline-block px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700"
                                        >
                                            Ver detalles
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mensaje para usuarios que quieran ayudar -->
                        <div v-if="!isOwner && request.status === 'active'" class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <h3 class="font-semibold mb-2">¿Puedes ayudar con esta solicitud?</h3>
                            <p class="mb-4">Si tienes un objeto que crees que podría satisfacer esta solicitud, puedes contactar al solicitante o publicar tu item en la plataforma.</p>
                            <Link
                                :href="route('items.create')"
                                class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Publicar un item
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación para eliminar -->
        <div v-if="confirmDelete" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg max-w-md w-full">
                <h3 class="text-lg font-bold mb-4">¿Estás seguro de que quieres eliminar esta solicitud?</h3>
                <p class="mb-4">Esta acción no se puede deshacer.</p>
                <div class="flex justify-end space-x-4">
                    <button
                        @click="confirmDelete = false"
                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                    >
                        Cancelar
                    </button>
                    <button
                        @click="deleteRequest"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                    >
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    request: Object,
    matchingItems: Array,
    isOwner: Boolean
});

const confirmDelete = ref(false);

// Formatear fecha completa
const formatDateFull = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Actualizar el estado de la solicitud
const updateStatus = (status) => {
    useForm({
        title: props.request.title,
        description: props.request.description,
        category_id: props.request.category_id,
        location: props.request.location,
        status: status
    }).patch(route('requests.update', props.request.id), {
        preserveScroll: true
    });
};

// Eliminar solicitud
const deleteRequest = () => {
    useForm({}).delete(route('requests.destroy', props.request.id), {
        onSuccess: () => window.location = route('requests.index')
    });
};
</script>
