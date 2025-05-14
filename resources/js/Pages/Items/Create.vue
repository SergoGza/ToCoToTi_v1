<template>
    <Head title="Publicar nuevo item" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Publicar un nuevo item</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <!-- Título -->
                            <div class="mb-4">
                                <InputLabel for="title" value="Título" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.title"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <!-- Categoría -->
                            <div class="mb-4">
                                <InputLabel for="category_id" value="Categoría" />
                                <select
                                    id="category_id"
                                    v-model="form.category_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
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
                            <div class="mb-4">
                                <InputLabel for="condition" value="Condición" />
                                <select
                                    id="condition"
                                    v-model="form.condition"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="" disabled>Selecciona una condición</option>
                                    <option value="nuevo">Nuevo</option>
                                    <option value="como_nuevo">Como nuevo</option>
                                    <option value="buen_estado">Buen estado</option>
                                    <option value="usado">Usado</option>
                                    <option value="necesita_reparacion">Necesita reparación</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.condition" />
                            </div>

                            <!-- Descripción -->
                            <div class="mb-4">
                                <InputLabel for="description" value="Descripción" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="4"
                                    required
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <!-- Ubicación -->
                            <div class="mb-4">
                                <InputLabel for="location" value="Ubicación" />
                                <TextInput
                                    id="location"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.location"
                                />
                                <InputError class="mt-2" :message="form.errors.location" />
                            </div>

                            <!-- Imágenes -->
                            <div class="mb-4">
                                <InputLabel for="images" value="Imágenes" />
                                <input
                                    id="images"
                                    type="file"
                                    class="mt-1 block w-full"
                                    @input="form.images = $event.target.files"
                                    multiple
                                    accept="image/*"
                                />
                                <p class="text-sm text-gray-500 mt-1">Puedes seleccionar múltiples imágenes</p>
                                <InputError class="mt-2" :message="form.errors.images" />
                            </div>

                            <!-- Botones -->
                            <div class="flex items-center justify-end mt-6">
                                <Link
                                    :href="route('items.index')"
                                    class="mr-4 px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                                >
                                    Cancelar
                                </Link>
                                <PrimaryButton
                                    class="ml-4"
                                    :disabled="form.processing"
                                >
                                    Publicar item
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

// Props recibidas del controlador
const props = defineProps({
    categories: Array,
});

// Formulario de Inertia
const form = useForm({
    title: '',
    category_id: '',
    condition: '',
    description: '',
    location: '',
    images: null,
});

// Envío del formulario
const submit = () => {
    form.post(route('items.store'), {
        // Para enviar archivos
        forceFormData: true,
        // Conserva los archivos adjuntos en el formulario después del envío
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>
