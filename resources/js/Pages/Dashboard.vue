<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import WelcomeTour from '@/Components/WelcomeTour.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

// Props con datos del backend
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            itemsPublished: 0,
            itemsGiven: 0,
            requestsActive: 0,
            interestsReceived: 0
        })
    },
    recentItems: {
        type: Array,
        default: () => []
    },
    recentRequests: {
        type: Array,
        default: () => []
    },
    userItems: {
        type: Array,
        default: () => []
    },
    matchingItems: {
        type: Array,
        default: () => []
    },
    showWelcomeTour: {
        type: Boolean,
        default: false
    }
});

// Estado local
const activeTab = ref('overview');
const showTour = ref(props.showWelcomeTour);

// Handlers para el tour
const handleTourComplete = async () => {
    try {
        // Enviar petición al backend para marcar como completado
        await fetch(route('welcome-tour.complete'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        showTour.value = false;

        // Opcional: mostrar algún mensaje de confirmación
        console.log('¡Tour completado! ¡Bienvenido a ToCoToTi!');
    } catch (error) {
        console.error('Error al completar el tour:', error);
        // Aún así ocultar el tour para no bloquear al usuario
        showTour.value = false;
    }
};

const handleTourSkip = async () => {
    try {
        // También marcar como completado cuando se salta
        await fetch(route('welcome-tour.complete'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        showTour.value = false;
    } catch (error) {
        console.error('Error al saltar el tour:', error);
        showTour.value = false;
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-[#333333] leading-tight">
                        ¡Hola, {{ $page.props.auth.user.name }}! 👋
                    </h2>
                    <p class="text-[#825028] text-sm mt-1">Bienvenido a tu espacio de intercambio</p>
                </div>
                <div class="flex space-x-3">
                    <Link
                        :href="route('items.create')"
                        class="inline-flex items-center px-4 py-2 bg-[#00913F] text-white rounded-lg hover:bg-[#007833] transition-colors duration-200"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Publicar ítem
                    </Link>
                    <Link
                        :href="route('requests.create')"
                        class="inline-flex items-center px-4 py-2 bg-[#825028] text-white rounded-lg hover:bg-[#6b3f1f] transition-colors duration-200"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Crear solicitud
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Estadísticas principales -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <!-- Items publicados -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-[#E0D5C7]">
                        <div class="flex items-center">
                            <div class="p-3 bg-[#00913F]/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#00913F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-[#825028]">Items publicados</p>
                                <p class="text-2xl font-semibold text-[#333333]">{{ props.stats.itemsPublished }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Items entregados -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-[#E0D5C7]">
                        <div class="flex items-center">
                            <div class="p-3 bg-[#825028]/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#825028]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-[#825028]">Items entregados</p>
                                <p class="text-2xl font-semibold text-[#333333]">{{ props.stats.itemsGiven }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Solicitudes activas -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-[#E0D5C7]">
                        <div class="flex items-center">
                            <div class="p-3 bg-[#00913F]/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#00913F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-[#825028]">Solicitudes activas</p>
                                <p class="text-2xl font-semibold text-[#333333]">{{ props.stats.requestsActive }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Intereses recibidos -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-[#E0D5C7]">
                        <div class="flex items-center">
                            <div class="p-3 bg-[#825028]/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#825028]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-[#825028]">Intereses recibidos</p>
                                <p class="text-2xl font-semibold text-[#333333]">{{ props.stats.interestsReceived }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs de contenido -->
                <div class="bg-white rounded-lg shadow-sm border border-[#E0D5C7]">
                    <div class="border-b border-[#E0D5C7]">
                        <nav class="flex space-x-8 px-6" aria-label="Tabs">
                            <button
                                @click="activeTab = 'overview'"
                                :class="[
                                    activeTab === 'overview'
                                        ? 'border-[#00913F] text-[#00913F]'
                                        : 'border-transparent text-[#825028] hover:text-[#333333] hover:border-[#825028]/30',
                                    'py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200'
                                ]"
                            >
                                Vista general
                            </button>
                            <button
                                @click="activeTab = 'my-items'"
                                :class="[
                                    activeTab === 'my-items'
                                        ? 'border-[#00913F] text-[#00913F]'
                                        : 'border-transparent text-[#825028] hover:text-[#333333] hover:border-[#825028]/30',
                                    'py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200'
                                ]"
                            >
                                Mis items
                            </button>
                            <button
                                @click="activeTab = 'recommendations'"
                                :class="[
                                    activeTab === 'recommendations'
                                        ? 'border-[#00913F] text-[#00913F]'
                                        : 'border-transparent text-[#825028] hover:text-[#333333] hover:border-[#825028]/30',
                                    'py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200'
                                ]"
                            >
                                Recomendaciones
                            </button>
                        </nav>
                    </div>

                    <div class="p-6">
                        <!-- Vista general -->
                        <div v-if="activeTab === 'overview'" class="space-y-6">
                            <!-- Items recientes en la comunidad -->
                            <div>
                                <h3 class="text-lg font-semibold text-[#333333] mb-4">Items recientes en la comunidad</h3>
                                <div v-if="recentItems.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <Link
                                        v-for="item in recentItems.slice(0, 3)"
                                        :key="item.id"
                                        :href="route('items.show', item.id)"
                                        class="border border-[#E0D5C7] rounded-lg p-4 hover:shadow-md transition-shadow duration-200"
                                    >
                                        <div class="aspect-w-16 aspect-h-9 mb-3">
                                            <div v-if="item.images && item.images.length > 0" class="h-32 bg-gray-200 rounded">
                                                <img :src="`/storage/${item.images[0]}`" :alt="item.title" class="w-full h-full
