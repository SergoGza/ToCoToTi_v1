<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import WelcomeTour from '@/Components/WelcomeTour.vue';
import { Head, Link, router } from '@inertiajs/vue3';
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
const handleTourComplete = () => {
    router.post(route('welcome-tour.complete'), {}, {
        onSuccess: () => {
            showTour.value = false;
        },
        onError: () => {
            showTour.value = false;
        }
    });
};

const handleTourSkip = () => {
    router.post(route('welcome-tour.complete'), {}, {
        onSuccess: () => {
            showTour.value = false;
        },
        onError: () => {
            showTour.value = false;
        }
    });
};
</script>

<template>
    <Head title="Inicio" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-[#333333] dark:text-gray-100 leading-tight">
                        ¡Hola, {{ $page.props.auth.user.name }}!
                    </h2>
                    <p class="text-[#825028] dark:text-amber-200 text-sm mt-1">Bienvenido a tu espacio de intercambio</p>
                </div>
                <div class="flex space-x-3">
                    <Link
                        :href="route('items.create')"
                        class="inline-flex items-center px-4 py-2 bg-[#00913F] text-white rounded-lg hover:bg-[#007833] transition-colors duration-200"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tirar algo
                    </Link>
                    <Link
                        :href="route('requests.create')"
                        class="inline-flex items-center px-4 py-2 bg-[#825028] text-white rounded-lg hover:bg-[#6b3f1f] transition-colors duration-200"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Pedir algo
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-[#E0D5C7] dark:border-gray-700">
                        <div class="flex items-center">
                            <div class="p-3 bg-[#00913F]/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#00913F] dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-[#825028] dark:text-gray-400">Cosas que tiro</p>
                                <p class="text-2xl font-semibold text-[#333333] dark:text-gray-100">{{ props.stats.itemsPublished }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-[#E0D5C7] dark:border-gray-700">
                        <div class="flex items-center">
                            <div class="p-3 bg-[#825028]/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#825028] dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" /></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-[#825028] dark:text-gray-400">Cosas entregadas</p>
                                <p class="text-2xl font-semibold text-[#333333] dark:text-gray-100">{{ props.stats.itemsGiven }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-[#E0D5C7] dark:border-gray-700">
                        <div class="flex items-center">
                            <div class="p-3 bg-[#00913F]/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#00913F] dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-[#825028] dark:text-gray-400">Cosas que pido</p>
                                <p class="text-2xl font-semibold text-[#333333] dark:text-gray-100">{{ props.stats.requestsActive }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-[#E0D5C7] dark:border-gray-700">
                        <div class="flex items-center">
                            <div class="p-3 bg-[#825028]/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#825028] dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-[#825028] dark:text-gray-400">Quién lo quiere</p>
                                <p class="text-2xl font-semibold text-[#333333] dark:text-gray-100">{{ props.stats.interestsReceived }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-[#E0D5C7] dark:border-gray-700">
                    <div class="border-b border-[#E0D5C7] dark:border-gray-700">
                        <nav class="flex space-x-8 px-6" aria-label="Tabs">
                            <button @click="activeTab = 'overview'" :class="[ activeTab === 'overview' ? 'border-green-500 dark:border-green-400 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600', 'py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200']">
                                Vista general
                            </button>
                            <button @click="activeTab = 'my-items'" :class="[ activeTab === 'my-items' ? 'border-green-500 dark:border-green-400 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600', 'py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200']">
                                Lo que tiro
                            </button>
                            <button @click="activeTab = 'recommendations'" :class="[ activeTab === 'recommendations' ? 'border-green-500 dark:border-green-400 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600', 'py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200']">
                                Recomendaciones
                            </button>
                        </nav>
                    </div>

                    <div class="p-6">
                        <div v-if="activeTab === 'overview'" class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-[#333333] dark:text-gray-100 mb-4">Cosas que la gente está tirando</h3>
                                <div v-if="recentItems.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <Link v-for="item in recentItems.slice(0, 3)" :key="item.id" :href="route('items.show', item.id)" class="border border-[#E0D5C7] dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                        <div class="aspect-w-16 aspect-h-9 mb-3">
                                            <div v-if="item.images && item.images.length > 0" class="h-32 bg-gray-200 dark:bg-gray-700 rounded"><img :src="`/storage/${item.images[0]}`" :alt="item.title" class="w-full h-full object-cover rounded"></div>
                                            <div v-else class="h-32 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center"><span class="text-gray-400 dark:text-gray-500">Sin imagen</span></div>
                                        </div>
                                        <h4 class="font-semibold text-[#333333] dark:text-gray-100">{{ item.title }}</h4>
                                        <p class="text-sm text-[#825028] dark:text-amber-300 mt-1">Por {{ item.user.name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ item.location || 'Sin ubicación' }}</p>
                                    </Link>
                                </div>
                                <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400"><p>No hay cosas recientes que se estén tirando</p></div>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-[#333333] dark:text-gray-100 mb-4">Cosas que la gente está pidiendo</h3>
                                <div v-if="recentRequests.length > 0" class="space-y-3">
                                    <Link v-for="request in recentRequests.slice(0, 3)" :key="request.id" :href="route('requests.show', request.id)" class="block border border-[#E0D5C7] dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-semibold text-[#333333] dark:text-gray-100">{{ request.title }}</h4>
                                                <p class="text-sm text-[#825028] dark:text-amber-300 mt-1">Pedido por {{ request.user.name }}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-2 line-clamp-2">{{ request.description }}</p>
                                            </div>
                                            <span class="bg-[#00913F]/20 text-[#00913F] dark:bg-green-900/50 dark:text-green-300 px-2 py-1 rounded text-xs font-semibold">{{ request.category?.name || 'Sin categoría' }}</span>
                                        </div>
                                    </Link>
                                </div>
                                <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400"><p>No hay peticiones recientes</p></div>
                            </div>
                        </div>

                        <div v-if="activeTab === 'my-items'" class="space-y-6">
                            <div v-if="userItems.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="item in userItems" :key="item.id" class="border border-[#E0D5C7] dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-[#333333] dark:text-gray-100">{{ item.title }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ item.category?.name || 'Sin categoría' }}</p>
                                            <div class="mt-2">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="{ 'bg-[#00913F]/20 text-[#00913F] dark:bg-green-900/50 dark:text-green-300': item.status === 'available', 'bg-[#825028]/20 text-[#825028] dark:bg-amber-900/50 dark:text-amber-300': item.status === 'reserved', 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-300': item.status === 'given' }">
                                                    {{ item.status === 'available' ? 'Disponible' : item.status === 'reserved' ? 'Reservado' : 'Entregado' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <Link :href="route('items.edit', item.id)" class="text-[#00913F] hover:text-[#007833] dark:text-green-400 dark:hover:text-green-300"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-12">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">Aún no has tirado nada</p>
                                <Link :href="route('items.create')" class="inline-flex items-center px-4 py-2 bg-[#00913F] text-white rounded-lg hover:bg-[#007833] transition-colors duration-200">Tirar mi primera cosa</Link>
                            </div>
                        </div>

                        <div v-if="activeTab === 'recommendations'" class="space-y-6">
                            <p class="text-[#825028] dark:text-amber-200 mb-4">Cosas que podrían interesarte según tus búsquedas</p>
                            <div v-if="matchingItems.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <Link v-for="item in matchingItems" :key="item.id" :href="route('items.show', item.id)" class="border border-[#E0D5C7] dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-all duration-200 hover:border-[#00913F]/30 dark:hover:border-green-400/30">
                                    <div class="flex items-center space-x-4">
                                        <div v-if="item.images && item.images.length > 0" class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded flex-shrink-0"><img :src="`/storage/${item.images[0]}`" :alt="item.title" class="w-full h-full object-cover rounded"></div>
                                        <div v-else class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded flex-shrink-0 flex items-center justify-center"><span class="text-gray-400 dark:text-gray-500 text-xs">Sin imagen</span></div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-[#333333] dark:text-gray-100">{{ item.title }}</h4>
                                            <p class="text-sm text-[#825028] dark:text-amber-300 mt-1">{{ item.location || 'Sin ubicación' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Por {{ item.user.name }}</p>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                            <div v-else class="text-center py-12">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">No hay recomendaciones disponibles</p>
                                <Link :href="route('items.index')" class="text-[#00913F] dark:text-green-400 hover:text-[#007833] dark:hover:text-green-300 font-semibold">Explorar todo lo que se tira</Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 bg-gradient-to-r from-[#00913F] to-[#007833] rounded-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold mb-2">¡Sigue compartiendo!</h3>
                            <p class="opacity-90">Cada objeto que compartes ayuda a construir una comunidad más sostenible.</p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </div>
                </div>
            </div>
        </div>

        <WelcomeTour :show="showTour" @complete="handleTourComplete" @skip="handleTourSkip"/>
    </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
