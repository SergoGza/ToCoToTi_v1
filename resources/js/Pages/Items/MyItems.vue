<!-- resources/js/Pages/Items/MyItems.vue -->
<template>
    <Head title="Todo Tiro - Mis Items" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-[#333333] leading-tight">Todo Tiro</h2>
                    <p class="text-[#825028] text-sm mt-1">Los items que has compartido con la comunidad</p>
                </div>
                <Link
                    :href="route('items.create')"
                    class="inline-flex items-center px-4 py-2 bg-[#00913F] text-white rounded-lg hover:bg-[#007833] transition-colors duration-200"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Publicar nuevo item
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Estadísticas rápidas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-[#E0D5C7]">
                        <div class="flex items-center">
                            <div class="p-3 bg-[#00913F]/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#00913F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-[#825028]">Total publicados</p>
                                <p class="text-2xl font-semibold text-[#333333]">{{ stats.total }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6 border border-[#E0D5C7]">
                        <div class="flex items-center">
                            <div class="p-3 bg-[#00913F]/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#00913F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-[#825028]">Disponibles</p>
                                <p class="text-2xl font-semibold text-[#333333]">{{ stats.available }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6 border border-[#E0D5C7]">
                        <div class="flex items-center">
                            <div class="p-3 bg-[#825028]/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#825028]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-[#825028]">Reservados</p>
                                <p class="text-2xl font-semibold text-[#333333]">{{ stats.reserved }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6 border border-[#E0D5C7]">
                        <div class="flex items-center">
                            <div class="p-3 bg-gray-500/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-[#825028]">Entregados</p>
                                <p class="text-2xl font-semibold text-[#333333]">{{ stats.given }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel de filtros -->
                <div class="bg-white rounded-lg shadow-sm border border-[#E0D5C7] mb-6">
                    <div class="p-6">
                        <form @submit.prevent="search">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Búsqueda por término -->
                                <div>
                                    <InputLabel for="search" value="Buscar en mis items" />
                                    <TextInput
                                        id="search"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 focus:border-[#00913F] focus:ring-[#00913F] rounded-md shadow-sm"
                                        v-model="filters.search"
                                        placeholder="Título, descripción..."
                                    />
                                </div>

                                <!-- Filtro por categoría -->
                                <div>
                                    <InputLabel for="category" value="Categoría" />
                                    <select
                                        id="category"
                                        v-model="filters.category"
                                        class="mt-1 block w-full border-gray-300 focus:border-[#00913F] focus:ring-[#00913F] rounded-md shadow-sm"
                                    >
                                        <option value="">Todas las categorías</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Filtro por estado -->
                                <div>
                                    <InputLabel for="status" value="Estado" />
                                    <select
                                        id="status"
                                        v-model="filters.status"
                                        class="mt-1 block w-full border-gray-300 focus:border-[#00913F] focus:ring-[#00913F] rounded-md shadow-sm"
                                    >
                                        <option value="">Todos los estados</option>
                                        <option value="available">Disponible</option>
                                        <option value="reserved">Reservado</option>
                                        <option value="given">Entregado</option>
                                    </select>
                                </div>

                                <!-- Botones -->
                                <div class="flex items-end space-x-2">
                                    <PrimaryButton type="submit" class="flex-1">
                                        Filtrar
                                    </PrimaryButton>
                                    <SecondaryButton type="button" @click="resetFilters">
                                        Limpiar
                                    </SecondaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-[#E0D5C7]">
                    <div class="p-6 text-[#333333]">
                        <!-- Lista de items -->
                        <div v-if="items.data.length" class="space-y-6">
                            <div v-for="item in items.data" :key="item.id" class="border border-[#E0D5C7] rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200">
                                <div class="flex flex-col md:flex-row">
                                    <!-- Imagen -->
                                    <div class="md:w-1/4 bg-gray-100">
                                        <div class="h-48 md:h-full flex items-center justify-center">
                                            <img
                                                v-if="item.images && item.images.length"
                                                :src="'/storage/' + item.images[0]"
                                                :alt="item.title"
                                                class="w-full h-full object-cover"
                                            >
                                            <div v-else class="text-gray-400 flex flex-col items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="text-sm">Sin imagen</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contenido -->
                                    <div class="md:w-3/4 p-6">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <h3 class="text-xl font-bold text-[#333333] mb-2">{{ item.title }}</h3>
                                                <div class="flex flex-wrap gap-2 mb-3">
                                                    <span class="px-3 py-1 bg-[#00913F]/20 text-[#00913F] rounded-full text-sm font-semibold">
                                                        {{ item.category ? item.category.name : 'Sin categoría' }}
                                                    </span>
                                                    <span class="px-3 py-1 bg-[#825028]/20 text-[#825028] rounded-full text-sm font-semibold">
                                                        {{ formatCondition(item.condition) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <ItemStatusBadge :status="item.status" />
                                        </div>

                                        <p class="text-[#333333] mb-4 line-clamp-2">{{ item.description }}</p>

                                        <div v-if="item.location" class="mb-4 flex items-center text-sm text-[#825028]">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ item.location }}
                                        </div>

                                        <!-- Estadísticas del item -->
                                        <div class="mb-4 flex items-center space-x-4 text-sm text-[#825028]">
                                            <div v-if="item.interests_count > 0" class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                                {{ item.interests_count }} {{ item.interests_count === 1 ? 'interés' : 'intereses' }}
                                            </div>
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ formatDate(item.created_at) }}
                                            </div>
                                        </div>

                                        <!-- Acciones -->
                                        <div class="flex flex-wrap gap-3">
                                            <Link
                                                :href="route('items.show', item.id)"
                                                class="inline-flex items-center px-4 py-2 bg-[#00913F] text-white rounded-lg hover:bg-[#007833] transition-colors duration-200"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Ver detalles
                                            </Link>

                                            <Link
                                                :href="route('items.edit', item.id)"
                                                class="inline-flex items-center px-4 py-2 bg-[#825028] text-white rounded-lg hover:bg-[#6b3f1f] transition-colors duration-200"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Editar
                                            </Link>

                                            <button
                                                v-if="item.interests_count > 0"
                                                @click="viewInterests(item)"
                                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                                Ver intereses ({{ item.interests_count }})
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estado vacío -->
                        <div v-else class="text-center py-12">
                            <div class="mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <h3 class="text-lg font-semibold text-[#333333] mb-2">¡Aún no has tirado nada!</h3>
                                <p class="text-[#825028] mb-6">Comparte tus objetos con la comunidad y dale una segunda vida a las cosas que ya no uses.</p>
                                <Link
                                    :href="route('items.create')"
                                    class="inline-flex items-center px-6 py-3 bg-[#00913F] text-white rounded-lg hover:bg-[#007833] transition-colors duration-200"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tirar mi primer item
                                </Link>
                            </div>
                        </div>

                        <!-- Paginación -->
                        <div v-if="items.data.length" class="mt-8">
                            <Pagination :links="items.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ItemStatusBadge from '@/Components/ItemStatusBadge.vue';
import Pagination from '@/Components/Pagination.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    items: Object,
    stats: Object,
    categories: Array,
    filters: Object
});

// Copiar los filtros a un ref para poder modificarlos
const filters = ref({
    search: props.filters?.search || '',
    category: props.filters?.category || '',
    status: props.filters?.status || ''
});

const search = () => {
    router.get(route('items.my'), filters.value, {
        preserveState: true,
        preserveScroll: true,
        only: ['items']
    });
};

const resetFilters = () => {
    filters.value = {
        search: '',
        category: '',
        status: ''
    };
    search();
};

const viewInterests = (item) => {
    router.visit(route('items.show', item.id));
};

// Formatear condición
const formatCondition = (condition) => {
    const conditionMap = {
        'nuevo': 'Nuevo',
        'como_nuevo': 'Como nuevo',
        'buen_estado': 'Buen estado',
        'usado': 'Usado',
        'necesita_reparacion': 'Necesita reparación'
    };

    return conditionMap[condition] || condition;
};

// Formatear fecha
const formatDate = (dateString) => {
    if (!dateString) return '';

    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays < 1) {
        return 'Hoy';
    } else if (diffDays === 1) {
        return 'Ayer';
    } else if (diffDays < 7) {
        return `Hace ${diffDays} días`;
    } else {
        return date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
