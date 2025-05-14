<template>
    <Head :title="item.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalles del Item</h2>
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
                        <div class="flex flex-col md:flex-row">
                            <!-- Imágenes -->
                            <div class="md:w-1/2 pr-0 md:pr-4 mb-4 md:mb-0">
                                <div v-if="item.images && item.images.length" class="h-64 bg-gray-200 rounded-lg overflow-hidden">
                                    <img :src="'/storage/' + item.images[0]" alt="Imagen principal del item" class="w-full h-full object-cover">
                                </div>
                                <div v-else class="h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <span class="text-gray-500">Sin imágenes</span>
                                </div>

                                <!-- Galería de imágenes adicionales (si hay más de una) -->
                                <div v-if="item.images && item.images.length > 1" class="mt-4 grid grid-cols-4 gap-2">
                                    <div v-for="(image, index) in item.images.slice(1)" :key="index" class="h-16 bg-gray-200 rounded overflow-hidden">
                                        <img :src="'/storage/' + image" alt="Imagen adicional" class="w-full h-full object-cover">
                                    </div>
                                </div>
                            </div>

                            <!-- Detalles -->
                            <div class="md:w-1/2">
                                <h1 class="text-2xl font-bold mb-2">{{ item.title }}</h1>
                                <p class="mb-2 text-sm bg-blue-100 text-blue-800 inline-block px-2 py-1 rounded">
                                    {{ item.category ? item.category.name : 'Sin categoría' }}
                                </p>
                                <p class="mb-2 text-sm bg-gray-100 inline-block px-2 py-1 rounded">
                                    Condición: {{ formatCondition(item.condition) }}
                                </p>
                                <p class="mb-4 text-sm">
                                    Publicado por: {{ item.user.name }}
                                </p>

                                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                    <h3 class="font-semibold mb-2">Descripción:</h3>
                                    <p>{{ item.description }}</p>
                                </div>

                                <div v-if="item.location" class="mb-4">
                                    <h3 class="font-semibold">Ubicación:</h3>
                                    <p>{{ item.location }}</p>
                                </div>

                                <!-- Sección de interés -->
                                <div class="mt-6" v-if="$page.props.auth.user && $page.props.auth.user.id !== item.user_id">
                                    <div v-if="hasInterest">
                                        <p class="text-green-600 font-semibold">Ya has mostrado interés en este item</p>
                                    </div>
                                    <div v-else-if="item.status === 'available'">
                                        <form @submit.prevent="showInterest">
                                            <div class="mb-4">
                                                <InputLabel for="message" value="Mensaje para el propietario (opcional)" />
                                                <textarea
                                                    id="message"
                                                    v-model="form.message"
                                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                                    rows="3"
                                                    placeholder="Me interesa este item porque..."
                                                ></textarea>
                                            </div>
                                            <PrimaryButton type="submit" :disabled="form.processing">
                                                Mostrar interés
                                            </PrimaryButton>
                                        </form>
                                    </div>
                                    <div v-else>
                                        <p class="text-orange-600">Este item ya no está disponible</p>
                                    </div>
                                </div>

                                <!-- Opciones para el propietario -->
                                <div class="mt-6" v-if="$page.props.auth.user && $page.props.auth.user.id === item.user_id">
                                    <div class="flex space-x-4">
                                        <Link
                                            :href="route('items.edit', item.id)"
                                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                        >
                                            Editar item
                                        </Link>
                                        <button
                                            @click="confirmDelete = true"
                                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                                        >
                                            Eliminar
                                        </button>
                                    </div>

                                    <!-- Lista de personas interesadas -->
                                    <div v-if="item.interests && item.interests.length" class="mt-6">
                                        <h3 class="font-semibold mb-2">Personas interesadas:</h3>
                                        <div v-for="interest in item.interests" :key="interest.id" class="p-4 border rounded-lg mb-2">
                                            <p class="font-medium">{{ interest.user.name }}</p>
                                            <p v-if="interest.message" class="text-sm mt-1 mb-2">{{ interest.message }}</p>
                                            <div v-if="interest.status === 'pending'" class="flex space-x-2 mt-2">
                                                <button
                                                    @click="updateInterest(interest.id, 'accepted')"
                                                    class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700"
                                                >
                                                    Aceptar
                                                </button>
                                                <button
                                                    @click="updateInterest(interest.id, 'rejected')"
                                                    class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700"
                                                >
                                                    Rechazar
                                                </button>
                                            </div>
                                            <p v-else-if="interest.status === 'accepted'" class="text-green-600 mt-2">
                                                Aceptado
                                            </p>
                                            <p v-else-if="interest.status === 'rejected'" class="text-red-600 mt-2">
                                                Rechazado
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Diálogo de confirmación para eliminar -->
                <div v-if="confirmDelete" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white p-6 rounded-lg max-w-md w-full">
                        <h3 class="text-lg font-bold mb-4">¿Estás seguro de que quieres eliminar este item?</h3>
                        <p class="mb-4">Esta acción no se puede deshacer.</p>
                        <div class="flex justify-end space-x-4">
                            <button
                                @click="confirmDelete = false"
                                class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                            >
                                Cancelar
                            </button>
                            <button
                                @click="deleteItem"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                            >
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    item: Object,
    hasInterest: Boolean
});

const confirmDelete = ref(false);

// Formulario para mostrar interés
const form = useForm({
    item_id: props.item.id,
    message: ''
});

// Formatear el texto de la condición
const formatCondition = (condition) => {
    const conditions = {
        'nuevo': 'Nuevo',
        'como_nuevo': 'Como nuevo',
        'buen_estado': 'Buen estado',
        'usado': 'Usado',
        'necesita_reparacion': 'Necesita reparación'
    };

    return conditions[condition] || condition;
};

// Mostrar interés en el item
const showInterest = () => {
    form.post(route('interests.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('message');
        }
    });
};

// Actualizar el estado de un interés
const updateInterest = (interestId, status) => {
    useForm({ status }).patch(route('interests.update', interestId), {
        preserveScroll: true
    });
};

// Eliminar item
const deleteItem = () => {
    useForm({}).delete(route('items.destroy', props.item.id), {
        onSuccess: () => window.location = route('items.index')
    });
};
</script>
