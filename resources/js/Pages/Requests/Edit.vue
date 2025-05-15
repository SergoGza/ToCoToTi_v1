<template>
    <Head title="Editar solicitud" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar solicitud</h2>
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
                        <form @submit.prevent="submit">
                            <!-- Título -->
                            <div class="mb-4">
                                <InputLabel for="title" value="Título de la solicitud" />
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

                            <!-- Descripción -->
                            <div class="mb-4">
                                <InputLabel for="description" value="Descripción detallada" />
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

                            <!-- Fecha de expiración (opcional) -->
                            <div class="mb-4">
                                <InputLabel for="expires_at" value="Fecha de expiración (opcional)" />
                                <TextInput
                                    id="expires_at"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.expires_at"
                                />
                                <p class="text-sm text-gray-500 mt-1">Deja en blanco si tu solicitud no tiene fecha límite</p>
                                <InputError class="mt-2" :message="form.errors.expires_at" />
                            </div>

                            <!-- Estado -->
                            <div class="mb-4">
                                <InputLabel for="status" value="Estado" />
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="active">Activa</option>
                                    <option value="fulfilled">Satisfecha</option>
                                    <option value="cancelled">Cancelada</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>

                            <!-- Botones -->
                            <div class="flex items-center justify-end mt-6">
                                <Link
                                    :href="route('requests.show', request.id)"
                                    class="mr-4 px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                                >
                                    Cancelar
                                </Link>
                                <PrimaryButton
                                    class="ml-4"
                                    :disabled="form.processing"
                                >
                                    Guardar cambios
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
    request: Object,
    categories: Array
});

// Preparar fecha de expiración en formato YYYY-MM-DD para el input type="date"
let formattedExpiresAt = null;
if (props.request.expires_at) {
    const date = new Date(props.request.expires_at);
    formattedExpiresAt = date.toISOString().split('T')[0]; // Format as YYYY-MM-DD
}

// Formulario de Inertia
const form = useForm({
    title: props.request.title,
    category_id: props.request.category_id,
    description: props.request.description,
    location: props.request.location || '',
    expires_at: formattedExpiresAt,
    status: props.request.status
});

// Envío del formulario
const submit = () => {
    form.patch(route('requests.update', props.request.id), {
        preserveScroll: true
    });
};
</script>
