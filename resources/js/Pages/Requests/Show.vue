<template>
    <Head :title="request.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalles de la solicitud</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Mensajes de alerta -->
                <div v-if="$page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ $page.props.flash.error }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4">
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

                            <p class="text-sm text-gray-500 mb-4">
                                Publicado el {{ formatDate(request.created_at) }}
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
                            <p class="mb-4">Si tienes un item que crees que podría satisfacer esta solicitud, puedes contactar al solicitante o publicar tu item en la plataforma.</p>
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
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    request: Object,
    matchingItems: Array,
    isOwner: Boolean
});

// Formatear la fecha
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
</script>
