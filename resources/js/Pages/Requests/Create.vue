<template>
    <Head title="Publicar una solicitud" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Publicar una solicitud</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <!-- Título -->
                            <div class="mb-4">
                                <InputLabel for="title" value="¿Qué necesitas?" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.title"
                                    required
                                    autofocus
                                    placeholder="Describe brevemente lo que buscas"
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

                            <!-- Descripción -->
                            <div class="mb-4">
                                <InputLabel for="description" value="Descripción detallada" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="4"
                                    required
                                    placeholder="Describe en detalle qué buscas, para qué lo necesitas, etc."
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <!-- Ubicación -->
                            <div class="mb-4">
                                <InputLabel for="location" value="Ubicación (opcional)" />
                                <TextInput
                                    id="location"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.location"
                                    placeholder="Ciudad, barrio, zona..."
                                />
                                <InputError class="mt-2" :message="form.errors.location" />
                            </div>

                            <!-- Botones -->
                            <div class="flex items-center justify-end mt-6">
                                <Link
                                    :href="route('requests.index')"
                                    class="mr-4 px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                                >
                                    Cancelar
                                </Link>
                                <PrimaryButton
                                    class="ml-4"
                                    :disabled="form.processing"
                                >
                                    Publicar solicitud
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
    categories: Array
});

// Formulario de Inertia
const form = useForm({
    title: '',
    category_id: '',
    description: '',
    location: ''
});

// Envío del formulario
const submit = () => {
    form.post(route('requests.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>
