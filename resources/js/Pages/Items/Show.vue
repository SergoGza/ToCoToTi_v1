<template>
    <Head :title="item.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalles del Item</h2>
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
                                <div class="flex justify-between items-start">
                                    <h1 class="text-2xl font-bold mb-2">{{ item.title }}</h1>
                                    <ItemStatusBadge :status="item.status" />
                                </div>
                                <p class="mb-2 text-sm bg-blue-100 text-blue-800 inline-block px-2 py-1 rounded">
                                    {{ item.category ? item.category.name : 'Sin categoría' }}
                                </p>
                                <p class="mb-2 text-sm bg-gray-100 inline-block px-2 py-1 rounded">
                                    Condición: {{ formatCondition(item.condition) }}
                                </p>
                                <p class="mb-4 text-sm">
                                    Publicado por: {{ item.user.name }}
                                </p>

                                <!-- Botón para contactar con el propietario -->
                                <div v-if="$page.props.auth.user && $page.props.auth.user.id !== item.user_id">
                                    <Link
                                        :href="route('messages.fromItem', item.id)"
                                        class="inline-flex items-center px-4 py-2 mt-3 bg-green-500 text-white rounded hover:bg-green-600"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                        </svg>
                                        Contactar con {{ item.user.name }}
                                    </Link>
                                </div>

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
                                    <div class="flex flex-wrap gap-4">
                                        <Link
                                            :href="route('items.edit', item.id)"
                                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                        >
                                            Editar item
                                        </Link>

                                        <!-- Componente de cambio de estado -->
                                        <ItemStatusChanger
                                            :item-id="item.id"
                                            :item-status="item.status"
                                            :pending-interests="pendingInterests"
                                            @status-changed="onStatusChanged"
                                        />

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
                                            <div class="flex justify-between">
                                                <p class="font-medium">{{ interest.user.name }}</p>

                                                <!-- Botón para contactar con persona interesada -->
                                                <Link
                                                    :href="route('messages.fromInterest', interest.id)"
                                                    class="text-sm text-blue-600 hover:text-blue-800"
                                                >
                                                    Contactar
                                                </Link>
                                            </div>
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

                <!-- Modal de confirmación para eliminar -->
                <ConfirmationModal
                    :show="confirmDelete"
                    title="¿Estás seguro de que quieres eliminar este item?"
                    message="Esta acción no se puede deshacer."
                    confirm-text="Eliminar"
                    @confirm="deleteItem"
                    @cancel="confirmDelete = false"
                    type="danger"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import {ref, computed} from 'vue';
import {Head, Link, useForm, router} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ItemStatusBadge from '@/Components/ItemStatusBadge.vue';
import ItemStatusChanger from '@/Components/ItemStatusChanger.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    item: Object,
    hasInterest: Boolean,
    isOwner: Boolean
});

const confirmDelete = ref(false);

// Obtener todos los intereses pendientes para el modal de cambio de estado
const pendingInterests = computed(() => {
    if (!props.item.interests) return [];
    return props.item.interests.filter(interest => interest.status === 'pending');
});

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
// Mostrar interés en el item
const showInterest = () => {
    form.post(route('interests.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('message');
            window.showSuccessToast('Has mostrado interés en este item. El propietario será notificado.');
        },
        onError: (errors) => {
            // Si hay un mensaje de error específico, lo mostramos
            if (errors.message) {
                window.showErrorToast(errors.message);
            } else {
                window.showErrorToast('Error al mostrar interés. Por favor, inténtalo de nuevo.');
            }
        }
    });
};

// Actualizar el estado de un interés
const updateInterest = (interestId, status) => {
    useForm({status}).patch(route('interests.update', interestId), {
        preserveScroll: true,
        onSuccess: () => {
            window.showToast(`Interés marcado como ${status === 'accepted' ? 'aceptado' : 'rechazado'}`, 'success');
        },
        onError: () => {
            window.showToast('Error al actualizar el interés', 'error');
        }
    });
};

// Eliminar item

const deleteItem = () => {
    useForm({}).delete(route('items.destroy', props.item.id), {
        onSuccess: () => {
            window.showSuccessToast('Item eliminado correctamente');
            router.visit(route('items.index'));
        },
        onError: () => {
            window.showErrorToast('Error al eliminar el item. Por favor, inténtalo de nuevo.');
            confirmDelete.value = false;
        }
    });
};

// Manejar el cambio de estado del item
const onStatusChanged = (newStatus) => {
    // Recargar la página para mostrar el nuevo estado
    window.location.reload();
};
</script>
