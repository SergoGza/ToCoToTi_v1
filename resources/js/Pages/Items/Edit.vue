<template>
    <Head title="Editar item" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-[#333333] leading-tight">Editar item</h2>
                <Link
                    :href="route('items.show', item.id)"
                    class="inline-flex items-center px-4 py-2 bg-[#825028] text-white rounded-lg hover:bg-[#6b3f1f] transition-colors duration-200"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al item
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow-sm border border-[#E0D5C7]">
                    <div class="p-6 text-[#333333]">
                        <form @submit.prevent="submit">
                            <!-- Título -->
                            <div class="mb-6">
                                <InputLabel for="title" value="Título del item" class="text-[#333333] font-semibold" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full border-gray-300 focus:border-[#00913F] focus:ring-[#00913F] rounded-md shadow-sm"
                                    v-model="form.title"
                                    required
                                    autofocus
                                    placeholder="Describe brevemente tu item"
                                />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <!-- Categoría -->
                            <div class="mb-6">
                                <InputLabel for="category_id" value="Categoría" class="text-[#333333] font-semibold" />
                                <select
                                    id="category_id"
                                    v-model="form.category_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-[#00913F] focus:ring-[#00913F] rounded-md shadow-sm"
                                    required
                                >
                                    <option value="" disabled>Selecciona una categoría</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.category_id" />
                            </div>

                            <!-- Condición -->
                            <div class="mb-6">
                                <InputLabel for="condition" value="Estado del item" class="text-[#333333] font-semibold" />
                                <select
                                    id="condition"
                                    v-model="form.condition"
                                    class="mt-1 block w-full border-gray-300 focus:border-[#00913F] focus:ring-[#00913F] rounded-md shadow-sm"
                                    required
                                >
                                    <option value="" disabled>Selecciona el estado</option>
                                    <option value="nuevo">Nuevo</option>
                                    <option value="como_nuevo">Como nuevo</option>
                                    <option value="buen_estado">Buen estado</option>
                                    <option value="usado">Usado</option>
                                    <option value="necesita_reparacion">Necesita reparación</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.condition" />
                            </div>

                            <!-- Descripción -->
                            <div class="mb-6">
                                <InputLabel for="description" value="Descripción detallada" class="text-[#333333] font-semibold" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full border-gray-300 focus:border-[#00913F] focus:ring-[#00913F] rounded-md shadow-sm"
                                    rows="4"
                                    required
                                    placeholder="Describe tu item en detalle: características, uso, motivo por el que lo compartes..."
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <!-- Ubicación -->
                            <div class="mb-6">
                                <InputLabel for="location" value="Ubicación (opcional)" class="text-[#333333] font-semibold" />
                                <TextInput
                                    id="location"
                                    type="text"
                                    class="mt-1 block w-full border-gray-300 focus:border-[#00913F] focus:ring-[#00913F] rounded-md shadow-sm"
                                    v-model="form.location"
                                    placeholder="Ciudad, barrio, zona de entrega..."
                                />
                                <InputError class="mt-2" :message="form.errors.location" />
                            </div>

                            <!-- Imágenes existentes -->
                            <div v-if="item.images && item.images.length" class="mb-6">
                                <InputLabel value="Imágenes actuales" class="text-[#333333] font-semibold" />
                                <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div
                                        v-for="(image, index) in item.images"
                                        :key="index"
                                        class="relative group"
                                    >
                                        <div class="h-24 bg-gray-200 rounded-lg overflow-hidden">
                                            <img
                                                :src="'/storage/' + image"
                                                :alt="'Imagen ' + (index + 1)"
                                                class="w-full h-full object-cover"
                                            />
                                        </div>
                                        <button
                                            type="button"
                                            @click="removeExistingImage(index)"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                        >
                                            ×
                                        </button>
                                    </div>
                                </div>
                                <p class="text-sm text-[#825028] mt-2">
                                    Haz clic en la "×" para eliminar una imagen
                                </p>
                            </div>

                            <!-- Nuevas imágenes -->
                            <div class="mb-6">
                                <InputLabel for="images" value="Añadir nuevas imágenes (opcional)" class="text-[#333333] font-semibold" />
                                <input
                                    id="images"
                                    type="file"
                                    class="mt-1 block w-full text-sm text-[#333333]
                                           file:mr-4 file:py-2 file:px-4
                                           file:rounded-lg file:border-0
                                           file:text-sm file:font-semibold
                                           file:bg-[#00913F] file:text-white
                                           hover:file:bg-[#007833]
                                           file:cursor-pointer cursor-pointer"
                                    @input="handleFileInput"
                                    multiple
                                    accept="image/*"
                                />
                                <p class="text-sm text-[#825028] mt-1">
                                    Puedes seleccionar múltiples imágenes. Las nuevas imágenes se añadirán a las existentes.
                                </p>
                                <InputError class="mt-2" :message="form.errors.images" />
                            </div>

                            <!-- Estado del item -->
                            <div class="mb-6">
                                <InputLabel for="status" value="Estado de disponibilidad" class="text-[#333333] font-semibold" />
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-1 block w-full border-gray-300 focus:border-[#00913F] focus:ring-[#00913F] rounded-md shadow-sm"
                                >
                                    <option value="available">Disponible</option>
                                    <option value="reserved">Reservado</option>
                                    <option value="given">Entregado</option>
                                </select>
                                <p class="text-sm text-[#825028] mt-1">
                                    Cambia el estado según la situación actual de tu item
                                </p>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>

                            <!-- Botones -->
                            <div class="flex items-center justify-between pt-6 border-t border-[#E0D5C7]">
                                <Link
                                    :href="route('items.show', item.id)"
                                    class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-xs text-[#333333] uppercase tracking-widest hover:bg-gray-200 transition-colors duration-200"
                                >
                                    Cancelar
                                </Link>

                                <div class="flex space-x-3">
                                    <button
                                        type="button"
                                        @click="confirmDelete = true"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 transition-colors duration-200"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Eliminar
                                    </button>

                                    <PrimaryButton
                                        :disabled="form.processing"
                                        class="inline-flex items-center"
                                    >
                                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="mt-6 bg-[#FAF6F0] rounded-lg p-4 border border-[#E0D5C7]">
                    <div class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#825028] mt-0.5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm text-[#825028]">
                            <p class="font-semibold mb-1">Consejos para editar tu item:</p>
                            <ul class="space-y-1">
                                <li>• Mantén la información actualizada y precisa</li>
                                <li>• Si cambias el estado a "Entregado", el item dejará de mostrarse como disponible</li>
                                <li>• Las nuevas imágenes se añadirán a las existentes</li>
                                <li>• Puedes eliminar imágenes individuales haciendo clic en la "×"</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación para eliminar -->
        <ConfirmationModal
            :show="confirmDelete"
            title="¿Estás seguro de que quieres eliminar este item?"
            message="Esta acción no se puede deshacer. Se eliminarán todas las imágenes y datos asociados."
            confirm-text="Eliminar permanentemente"
            cancel-text="Cancelar"
            @confirm="deleteItem"
            @cancel="confirmDelete = false"
            type="danger"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        required: true
    }
});

const confirmDelete = ref(false);

const form = useForm({
    title: props.item.title || '',
    category_id: props.item.category_id || '',
    condition: props.item.condition || '',
    description: props.item.description || '',
    location: props.item.location || '',
    status: props.item.status || 'available',
    images: null,
    remove_images: []
});

const handleFileInput = (event) => {
    const files = event.target.files;
    form.images = files && files.length > 0 ? Array.from(files) : null;
};

const removeExistingImage = (index) => {
    const imageToRemove = props.item.images[index];
    if (!form.remove_images.includes(imageToRemove)) {
        form.remove_images.push(imageToRemove);
    }

    // Crear una nueva copia del array sin la imagen eliminada
    props.item.images.splice(index, 1);
};

const submit = () => {
    if (!window.verifyCsrfToken()) {
        console.error('Token CSRF no disponible');
        if (window.showErrorToast) {
            window.showErrorToast('Error de seguridad. Por favor, recarga la página.');
        }
        return;
    }

    // Debug: Ver qué datos se están enviando
    console.log('Datos del formulario:', {
        title: form.title,
        category_id: form.category_id,
        condition: form.condition,
        description: form.description,
        location: form.location,
        status: form.status,
        hasImages: !!form.images,
        remove_images: form.remove_images
    });

    // Solo usar forceFormData si hay archivos
    const hasFiles = form.images && form.images.length > 0;

    const submitOptions = {
        preserveScroll: true,
        onBefore: () => {
            console.log('Enviando formulario...');
            return true;
        },
        onSuccess: () => {
            console.log('Item actualizado exitosamente');
            if (window.showSuccessToast) {
                window.showSuccessToast('Item actualizado correctamente');
            }
        },
        onError: (errors) => {
            console.error('Errores de validación:', errors);

            const errorMessages = [];
            Object.keys(errors).forEach(field => {
                if (Array.isArray(errors[field])) {
                    errorMessages.push(...errors[field]);
                } else {
                    errorMessages.push(errors[field]);
                }
            });

            if (errorMessages.length > 0 && window.showErrorToast) {
                window.showErrorToast(errorMessages[0]);
            }
        },
        onFinish: () => {
            console.log('Procesamiento del formulario terminado');
        }
    };

    // Solo agregar forceFormData si hay archivos que subir
    if (hasFiles) {
        submitOptions.forceFormData = true;
    }

    form.patch(route('items.update', props.item.id), submitOptions);
};

const deleteItem = () => {
    useForm({}).delete(route('items.destroy', props.item.id), {
        onSuccess: () => {
            if (window.showSuccessToast) {
                window.showSuccessToast('Item eliminado correctamente');
            }
            router.visit(route('items.index'));
        },
        onError: () => {
            if (window.showErrorToast) {
                window.showErrorToast('Error al eliminar el item. Por favor, inténtalo de nuevo.');
            }
            confirmDelete.value = false;
        }
    });
};
</script>

<style scoped>
/* Animación suave para los elementos */
.transition-colors {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Estilo para el file input */
input[type="file"]::-webkit-file-upload-button {
    cursor: pointer;
}

/* Estilo para las imágenes en hover */
.group:hover .group-hover\:opacity-100 {
    opacity: 1;
}
</style>
