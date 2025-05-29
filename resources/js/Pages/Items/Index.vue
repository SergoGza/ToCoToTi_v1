<!-- resources/js/Pages/Items/Index.vue -->
<template>
    <Head title="Todo Cojo - Items disponibles" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-[#333333] leading-tight">🎯 Todo Cojo</h2>
                    <p class="text-[#825028] text-sm mt-1">Descubre los tesoros que otros han compartido</p>
                </div>
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('items.create')"
                    class="inline-flex items-center px-4 py-2 bg-[#00913F] text-white rounded-lg hover:bg-[#007833] transition-colors duration-200"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tirar un item
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Mensajes de alerta -->
                <div v-if="$page.props.flash && $page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash && $page.props.flash.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ $page.props.flash.error }}
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-[#E0D5C7] mb-6">
                    <div class="p-6">
                        <!-- Panel de búsqueda y filtros -->
                        <div class="mb-6">
                            <form @submit.prevent="search">
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                    <!-- Búsqueda por término -->
                                    <div class="md:col-span-2">
                                        <InputLabel for="search" value="¿Qué buscas?" />
                                        <TextInput
                                            id="search"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 focus:border-[#00913F] focus:ring-[#00913F] rounded-md shadow-sm"
                                            v-model="filters.search"
                                            placeholder="Buscar por título, descripción..."
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

                                    <!-- Filtro por condición -->
                                    <div>
                                        <InputLabel for="condition" value="Estado" />
                                        <select
                                            id="condition"
                                            v-model="filters.condition"
                                            class="mt-1 block w-full border-gray-300 focus:border-[#00913F] focus:ring-[#00913F] rounded-md shadow-sm"
                                        >
                                            <option value="">Todos los estados</option>
                                            <option v-for="condition in conditions" :key="condition.value" :value="condition.value">
                                                {{ condition.label }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Ubicación -->
                                    <div>
                                        <InputLabel for="location" value="Ubicación" />
                                        <TextInput
                                            id="location"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 focus:border-[#00913F] focus:ring-[#00913F] rounded-md shadow-sm"
                                            v-model="filters.location"
                                            placeholder="Ciudad, barrio..."
                                        />
                                    </div>
                                </div>

                                <div class="flex justify-between mt-4">
                                    <!-- Selector de ordenación -->
                                    <div class="flex items-center space-x-4">
                                        <span class="text-sm text-[#825028]">Ordenar por:</span>
                                        <select
                                            v-model="filters.sort_by"
                                            class="border-gray-300 focus:border-[#00913F] focus:ring-[#00913F] rounded-md shadow-sm text-sm"
                                            @change="search"
                                        >
                                            <option value="created_at">Fecha</option>
                                            <option value="title">Título</option>
                                            <option value="condition">Estado</option>
                                        </select>
                                        <select
                                            v-model="filters.sort_order"
                                            class="border-gray-300 focus:border-[#00913F] focus:ring-[#00913F] rounded-md shadow-sm text-sm"
                                            @change="search"
                                        >
                                            <option value="desc">Más recientes</option>
                                            <option value="asc">Más antiguos</option>
                                        </select>
                                    </div>

                                    <div class="flex space-x-2">
                                        <PrimaryButton type="submit">
                                            🔍 Buscar
                                        </PrimaryButton>
                                        <SecondaryButton type="button" @click="resetFilters">
                                            Limpiar filtros
                                        </SecondaryButton>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-[#E0D5C7]">
                    <div class="p-6 text-[#333333]">
                        <!-- Resultados filtrados -->
                        <div v-if="items.total === 0" class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-[#333333] mb-2">No encontramos lo que buscas</h3>
                            <p class="text-[#825028] mb-4">Intenta ajustar tus filtros o crear una solicitud para que la comunidad sepa qué necesitas.</p>
                            <div class="space-x-3">
                                <SecondaryButton @click="resetFilters">Limpiar filtros</SecondaryButton>
                                <Link
                                    :href="route('requests.create')"
                                    class="inline-flex items-center px-4 py-2 bg-[#825028] text-white rounded-lg hover:bg-[#6b3f1f] transition-colors duration-200"
                                >
                                    🙋 Crear solicitud
                                </Link>
                            </div>
                        </div>

                        <!-- Lista de items -->
                        <div v-else>
                            <div class="flex justify-between items-center mb-6">
                                <p class="text-sm text-[#825028]">
                                    Mostrando {{ items.from }}-{{ items.to }} de {{ items.total }} artículos disponibles
                                </p>

                                <!-- Enlace rápido a solicitudes -->
                                <Link
                                    :href="route('requests.create')"
                                    class="text-sm text-[#825028] hover:text-[#6b3f1f] transition-colors duration-200"
                                >
                                    ¿No encuentras lo que buscas? 🙋 Créalo como solicitud
                                </Link>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div v-for="item in items.data" :key="item.id" class="border border-[#E0D5C7] rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-200">
                                    <div class="h-48 bg-gray-100 flex items-center justify-center">
                                        <img
                                            v-if="item.images && item.images.length"
                                            :src="'/storage/' + item.images[0]"
                                            alt="Imagen de item"
                                            class="w-full h-full object-cover"
                                        >
                                        <div v-else class="text-gray-400 flex flex-col items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-sm">Sin imagen</span>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <h3 class="text-lg font-bold text-[#333333] mb-2">{{ item.title }}</h3>
                                        <div class="flex space-x-2 mb-3">
                                            <span class="px-3 py-1 bg-[#00913F]/20 text-[#00913F] rounded-full text-sm font-semibold">
                                                {{ item.category ? item.category.name : 'Sin categoría' }}
                                            </span>
                                            <span class="px-3 py-1 bg-[#825028]/20 text-[#825028] rounded-full text-sm font-semibold">
                                                {{ formatCondition(item.condition) }}
                                            </span>
                                        </div>

                                        <p class="text-sm text-[#825028] mb-2 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            Por {{ item.user.name }}
                                        </p>

                                        <p v-if="item.location" class="text-sm text-[#825028] mb-3 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ item.location }}
                                        </p>

                                        <p class="mt-2 text-sm line-clamp-2 text-[#333333] mb-4">{{ item.description }}</p>

                                        <div class="flex justify-between items-center">
                                            <Link
                                                :href="route('items.show', item.id)"
                                                class="inline-flex items-center px-4 py-2 bg-[#00913F] text-white rounded-lg hover:bg-[#007833] transition-colors duration-200"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                ¡Lo quiero!
                                            </Link>
                                            <span class="text-xs text-[#825028]">
                                                {{ formatDate(item.created_at) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Paginación -->
                            <div class="mt-8">
                                <Pagination :links="items.links" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    items: Object,
    filters: Object,
    categories: Array,
    conditions: Array
});

// Copiar los filtros a un ref para poder modificarlos
const filters = ref({
    search: props.filters?.search || '',
    category: props.filters?.category || '',
    condition: props.filters?.condition || '',
    location: props.filters?.location || '',
    sort_by: props.filters?.sort_by || 'created_at',
    sort_order: props.filters?.sort_order || 'desc'
});

const search = () => {
    router.get(route('items.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
        only: ['items']
    });
};

const resetFilters = () => {
    filters.value = {
        search: '',
        category: '',
        condition: '',
        location: '',
        sort_by: 'created_at',
        sort_order: 'desc'
    };
    search();
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
            month: '2-digit'
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
