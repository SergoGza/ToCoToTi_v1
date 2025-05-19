<template>
    <Head :title="'Conversación con ' + contact.name" />

    <AuthenticatedLayout :breadcrumbItems="[
        { name: 'Mensajes', href: route('messages.index') },
        { name: `Conversación con ${contact.name}` }
    ]">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Conversación con {{ contact.name }}</h2>
                <Link
                    :href="route('messages.index')"
                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white"
                >
                    Volver a la bandeja de entrada
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col md:flex-row gap-6">
                <!-- Panel lateral con información contextual - Optimizado para móvil -->
                <div class="md:w-1/4 w-full order-2 md:order-1">
                    <!-- Botón para mostrar/ocultar panel en móvil -->
                    <button
                        class="w-full md:hidden mb-3 px-4 py-2 bg-gray-200 rounded flex justify-between items-center dark:bg-gray-700 dark:text-white"
                        @click="toggleSidebar"
                    >
                        <span>{{ showSidebar ? 'Ocultar detalles' : 'Mostrar detalles' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="{ 'transform rotate-180': showSidebar }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Panel con transición suave -->
                    <div
                        class="transition-all duration-300 overflow-hidden"
                        :class="{ 'max-h-0 opacity-0 md:max-h-full md:opacity-100': !showSidebar, 'max-h-[1000px] opacity-100': showSidebar }"
                    >
                        <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6 dark:bg-gray-800 dark:text-white">
                            <h3 class="font-semibold text-lg mb-4">{{ contact.name }}</h3>

                            <!-- Si el contacto está interesado en mis ítems -->
                            <div v-if="interestsInMyItems.length > 0" class="mb-4">
                                <h4 class="font-medium text-sm mb-2 text-gray-700 dark:text-gray-300">Items que le interesan:</h4>
                                <div v-for="interest in interestsInMyItems" :key="interest.id" class="mb-2 p-2 bg-blue-50 rounded text-sm dark:bg-blue-900">
                                    <p class="font-medium">{{ interest.item.title }}</p>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-xs"
                                              :class="{
                                                'text-yellow-600 dark:text-yellow-400': interest.status === 'pending',
                                                'text-green-600 dark:text-green-400': interest.status === 'accepted',
                                                'text-red-600 dark:text-red-400': interest.status === 'rejected'
                                            }"
                                        >
                                            {{ formatInterestStatus(interest.status) }}
                                        </span>
                                        <Link
                                            :href="route('items.show', interest.item.id)"
                                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            Ver item
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <!-- Si estoy interesado en sus ítems -->
                            <div v-if="myInterestsInTheirItems.length > 0">
                                <h4 class="font-medium text-sm mb-2 text-gray-700 dark:text-gray-300">Items que me interesan:</h4>
                                <div v-for="interest in myInterestsInTheirItems" :key="interest.id" class="mb-2 p-2 bg-green-50 rounded text-sm dark:bg-green-900">
                                    <p class="font-medium">{{ interest.item.title }}</p>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-xs"
                                              :class="{
                                                'text-yellow-600 dark:text-yellow-400': interest.status === 'pending',
                                                'text-green-600 dark:text-green-400': interest.status === 'accepted',
                                                'text-red-600 dark:text-red-400': interest.status === 'rejected'
                                            }"
                                        >
                                            {{ formatInterestStatus(interest.status) }}
                                        </span>
                                        <Link
                                            :href="route('items.show', interest.item.id)"
                                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            Ver item
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items relacionados a la conversación -->
                        <div v-if="itemsRelated.length > 0" class="bg-white shadow-sm sm:rounded-lg p-6 dark:bg-gray-800 dark:text-white">
                            <h3 class="font-semibold text-lg mb-4">Items relacionados</h3>
                            <div v-for="item in itemsRelated" :key="item.id" class="mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gray-200 rounded overflow-hidden dark:bg-gray-700">
                                        <img
                                            v-if="item.images && item.images.length"
                                            :src="'/storage/' + item.images[0]"
                                            alt="Item image"
                                            class="w-full h-full object-cover"
                                        >
                                        <div v-else class="w-full h-full flex items-center justify-center text-gray-500 text-xs">
                                            Sin img
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-sm">{{ item.title }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            De: {{ item.user.name }}
                                        </p>
                                    </div>
                                </div>
                                <Link
                                    :href="route('items.show', item.id)"
                                    class="mt-1 text-xs text-blue-600 hover:text-blue-800 block dark:text-blue-400 dark:hover:text-blue-300"
                                >
                                    Ver detalles
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel principal con mensajes - Optimizado para móvil -->
                <div class="md:w-3/4 w-full order-1 md:order-2">
                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden dark:bg-gray-800">
                        <!-- Estado de conexión - Solo visible cuando hay actividad de sincronización -->
                        <div v-if="isActivelyPolling && lastPollingTime"
                             class="bg-blue-50 dark:bg-blue-900 py-1 px-4 text-xs text-blue-700 dark:text-blue-300 flex justify-between items-center"
                             style="opacity: 0.7;">
                            <span class="flex items-center">
                                <svg v-if="isActivelyPolling" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span v-if="connectionStatus === 'syncing'">Sincronizando...</span>
                                <span v-else-if="connectionStatus === 'synced'">Sincronizado</span>
                            </span>
                            <button @click="manualRefresh" class="text-blue-600 dark:text-blue-400 hover:underline">
                                Actualizar
                            </button>
                        </div>

                        <!-- Mensajes con altura adaptada -->
                        <div class="h-[400px] md:h-[500px] overflow-y-auto p-4 md:p-6 dark:text-white" ref="messagesContainer">
                            <div v-if="currentMessages.length === 0" class="text-center py-10">
                                <p class="text-gray-500 dark:text-gray-400">No hay mensajes en esta conversación.</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Envía un mensaje para comenzar a chatear.</p>
                            </div>
                            <div v-else class="space-y-4">
                                <div
                                    v-for="(message, index) in currentMessages"
                                    :key="message.id"
                                    class="flex"
                                    :class="{ 'justify-end': message.sender_id === $page.props.auth.user.id }"
                                >
                                    <!-- Mostrar fecha si es el primer mensaje o si cambia el día -->
                                    <div
                                        v-if="shouldShowDate(message, index)"
                                        class="w-full text-center my-4"
                                    >
                                        <span class="inline-block px-4 py-1 bg-gray-100 rounded-full text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                            {{ formatDateHeader(message.created_at) }}
                                        </span>
                                    </div>

                                    <!-- Mensaje de otra persona -->
                                    <div
                                        v-if="message.sender_id !== $page.props.auth.user.id"
                                        class="max-w-[75%] bg-gray-100 p-3 rounded-lg dark:bg-gray-700"
                                    >
                                        <!-- Contenido del mensaje -->
                                        <p class="text-gray-800 break-words dark:text-white">{{ message.content }}</p>

                                        <!-- Imágenes adjuntas -->
                                        <div v-if="message.images && message.images.length" class="mt-2 grid grid-cols-2 gap-2">
                                            <div v-for="(image, imgIndex) in message.images" :key="imgIndex" class="relative">
                                                <img
                                                    :src="'/storage/' + image"
                                                    class="rounded max-h-32 max-w-full object-cover cursor-pointer"
                                                    @click="openImagePreview(image)"
                                                    alt="Imagen adjunta"
                                                >
                                            </div>
                                        </div>

                                        <!-- Referencias a ítems o intereses si existen -->
                                        <div v-if="message.item_id || (message.interest && message.interest.item_id)" class="mt-1 p-1 bg-gray-200 rounded text-xs text-gray-600 dark:bg-gray-600 dark:text-gray-300">
                                            Sobre: {{ message.item ? message.item.title : message.interest.item.title }}
                                        </div>

                                        <!-- Hora del mensaje -->
                                        <p class="text-right text-xs text-gray-500 mt-1 dark:text-gray-400">
                                            {{ formatMessageTime(message.created_at) }}
                                        </p>
                                    </div>

                                    <!-- Mensaje propio -->
                                    <div
                                        v-else
                                        class="max-w-[75%] bg-blue-100 p-3 rounded-lg dark:bg-blue-900"
                                    >
                                        <!-- Contenido del mensaje -->
                                        <p class="text-gray-800 break-words dark:text-white">{{ message.content }}</p>

                                        <!-- Imágenes adjuntas -->
                                        <div v-if="message.images && message.images.length" class="mt-2 grid grid-cols-2 gap-2">
                                            <div v-for="(image, imgIndex) in message.images" :key="imgIndex" class="relative">
                                                <img
                                                    :src="'/storage/' + image"
                                                    class="rounded max-h-32 max-w-full object-cover cursor-pointer"
                                                    @click="openImagePreview(image)"
                                                    alt="Imagen adjunta"
                                                >
                                            </div>
                                        </div>

                                        <!-- Referencias a ítems o intereses si existen -->
                                        <div v-if="message.item_id || (message.interest && message.interest.item_id)" class="mt-1 p-1 bg-blue-200 rounded text-xs text-gray-600 dark:bg-blue-800 dark:text-gray-300">
                                            Sobre: {{ message.item ? message.item.title : message.interest.item.title }}
                                        </div>

                                        <!-- Hora del mensaje y estado de lectura -->
                                        <div class="text-right text-xs text-gray-500 mt-1 flex justify-end items-center dark:text-gray-400">
                                            <!-- Indicador de estado -->
                                            <span v-if="String(message.id).startsWith('temp-')" class="mr-1 text-yellow-500 dark:text-yellow-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                            <span v-else-if="message.failed" class="mr-1 text-red-500 dark:text-red-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                            <span v-else-if="message.read" class="mr-1 text-blue-600 dark:text-blue-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                            {{ formatMessageTime(message.created_at) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Indicador de carga solo visible durante la carga de mensajes -->
                            <div v-if="isLoading" class="flex justify-center items-center py-4">
                                <div class="loader bg-white p-5 rounded-full flex space-x-3">
                                    <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce"></div>
                                    <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.15s"></div>
                                    <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
                                </div>
                                <span class="ml-3 text-sm text-gray-500">Cargando...</span>
                            </div>
                        </div>

                        <!-- Formulario para enviar mensaje -->
                        <div class="border-t p-4 dark:border-gray-700">
                            <form @submit.prevent="sendMessage" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <textarea
                                        v-model="messageForm.content"
                                        class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        rows="3"
                                        placeholder="Escribe tu mensaje..."
                                        required
                                    ></textarea>
                                </div>

                                <!-- Selector de contexto - organizado para móvil -->
                                <div class="flex flex-col md:flex-row gap-3 mb-3">
                                    <div v-if="itemsRelated.length > 0" class="w-full md:w-auto">
                                        <select
                                            v-model="messageForm.item_id"
                                            class="rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        >
                                            <option :value="null">Sin referencia a item</option>
                                            <option v-for="item in itemsRelated" :key="item.id" :value="item.id">
                                                Referente a: {{ item.title }}
                                            </option>
                                        </select>
                                    </div>

                                    <div v-if="interestsInMyItems.length > 0 || myInterestsInTheirItems.length > 0" class="w-full md:w-auto">
                                        <select
                                            v-model="messageForm.item_interest_id"
                                            class="rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        >
                                            <option :value="null">Sin referencia a interés</option>
                                            <optgroup v-if="interestsInMyItems.length > 0" label="Intereses en mis items">
                                                <option v-for="interest in interestsInMyItems" :key="interest.id" :value="interest.id">
                                                    Interés en: {{ interest.item.title }}
                                                </option>
                                            </optgroup>
                                            <optgroup v-if="myInterestsInTheirItems.length > 0" label="Mis intereses">
                                                <option v-for="interest in myInterestsInTheirItems" :key="interest.id" :value="interest.id">
                                                    Mi interés en: {{ interest.item.title }}
                                                </option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>

                                <!-- Adjuntar imágenes -->
                                <div class="mb-3">
                                    <div class="flex items-center space-x-2">
                                        <label class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded text-sm cursor-pointer dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white">
                                            <input
                                                type="file"
                                                id="images"
                                                ref="fileInput"
                                                multiple
                                                accept="image/*"
                                                class="hidden"
                                                @change="handleFileSelect"
                                            >
                                            Adjuntar imágenes
                                        </label>
                                        <span v-if="selectedFiles.length > 0" class="text-sm text-gray-600 dark:text-gray-300">
                                            {{ selectedFiles.length }} imagen(es) seleccionada(s)
                                        </span>
                                    </div>

                                    <!-- Vista previa de imágenes -->
                                    <div v-if="imagePreviewUrls.length > 0" class="mt-2 grid grid-cols-3 sm:grid-cols-4 gap-2">
                                        <div v-for="(url, index) in imagePreviewUrls" :key="index" class="relative">
                                            <img :src="url" class="h-20 w-20 object-cover rounded">
                                            <button
                                                @click.prevent="removeImage(index)"
                                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full h-5 w-5 flex items-center justify-center"
                                            >
                                                &times;
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800"
                                        :disabled="!messageForm.content || messageForm.content.trim() === ''"
                                    >
                                        Enviar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para vista previa de imágenes -->
        <div v-if="showImagePreview" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" @click="showImagePreview = false">
            <div class="max-w-3xl max-h-3xl p-2 bg-white rounded dark:bg-gray-800" @click.stop>
                <img :src="previewImageUrl" class="max-h-[80vh] max-w-full object-contain">
                <button
                    @click="showImagePreview = false"
                    class="absolute top-4 right-4 bg-red-500 text-white rounded-full h-8 w-8 flex items-center justify-center"
                >
                    &times;
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch, onMounted, nextTick, onBeforeUnmount, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

// Crea una instancia de Axios específica para polling que no active el indicador de carga
const pollingAxios = axios.create();

// Marcar todas las peticiones de esta instancia como "silenciosas"
pollingAxios.interceptors.request.use(config => {
    // Añadir headers para que Inertia y Laravel ignoren esta petición para el indicador de carga
    config.headers['X-Inertia-Polling'] = 'true';
    config.headers['X-Silent-Request'] = 'true';
    return config;
});

// Props
const props = defineProps({
    contact: Object,
    messages: Array,
    itemsRelated: Array,
    interestsInMyItems: Array,
    myInterestsInTheirItems: Array
});

// Referencias
const messagesContainer = ref(null);
const fileInput = ref(null);

// Estados
const selectedFiles = ref([]);
const imagePreviewUrls = ref([]);
const showImagePreview = ref(false);
const previewImageUrl = ref(null);
const showSidebar = ref(false); // Control de visibilidad del panel lateral en móvil
const currentMessages = ref([...props.messages]); // Crear una copia reactiva de los mensajes
const connectionStatus = ref('idle'); // Estado de conexión: 'idle', 'syncing', 'synced'
const isLoading = ref(false);
const lastPollingTime = ref(null);
const isActivelyPolling = ref(false);

// Estado para el polling optimizado
const lastMessageId = ref(0);
const pollingInterval = ref(null);
const pollingDelay = ref(10000); // 10 segundos por defecto (para reducir carga)
const isWindowActive = ref(true);
const pendingMessages = ref(new Map()); // Para mensajes enviados pero no confirmados
const lastPollTimestamp = ref(Math.floor(Date.now() / 1000));

// Formulario para enviar mensajes
const messageForm = useForm({
    receiver_id: props.contact.id,
    content: '',
    item_id: null,
    item_interest_id: null,
    images: []
});

// Mostrar información de polling solo temporalmente
let syncInfoTimeout;
const showSyncInfo = () => {
    isActivelyPolling.value = true;
    lastPollingTime.value = new Date();

    // Limpiar timeout existente antes de crear uno nuevo
    if (syncInfoTimeout) {
        clearTimeout(syncInfoTimeout);
    }

    // Ocultar información después de 3 segundos
    syncInfoTimeout = setTimeout(() => {
        isActivelyPolling.value = false;
    }, 3000);
};

// Función para mostrar/ocultar panel en móvil
const toggleSidebar = () => {
    showSidebar.value = !showSidebar.value;
};

// Calcular el último ID de mensaje conocido
const updateLastMessageId = () => {
    if (currentMessages.value.length > 0) {
        const realMessages = currentMessages.value.filter(m => !String(m.id).startsWith('temp-'));
        if (realMessages.length > 0) {
            lastMessageId.value = Math.max(...realMessages.map(m => m.id));
        }
    }
};

// Función mejorada para obtener mensajes recientes
// Función mejorada para obtener mensajes recientes sin activar el indicador de carga
const fetchRecentMessages = async (forceRefresh = false) => {
    try {
        // Solo hacer polling si la ventana está activa o se fuerza la actualización
        if ((!isWindowActive.value && !forceRefresh)) return;

        // Si no ha pasado suficiente tiempo, no actualizamos a menos que sea forzado
        const now = Math.floor(Date.now() / 1000);
        const timeSinceLastPoll = now - lastPollTimestamp.value;

        if (timeSinceLastPoll < 5 && !forceRefresh) {
            return;
        }

        // Actualizar la UI para mostrar sincronización solo cuando es manual o hay actividad
        if (forceRefresh) {
            connectionStatus.value = 'syncing';
            isLoading.value = true;
            showSyncInfo();
        }

        // Guardar el timestamp actual
        lastPollTimestamp.value = now;

        // Usar la instancia silenciosa de Axios para evitar el indicador global
        const response = await pollingAxios.get(route('messages.recent', props.contact.id), {
            params: {
                last_id: lastMessageId.value,
                timestamp: lastPollTimestamp.value
            }
        });

        // Procesar respuesta solo si hay mensajes nuevos o es una actualización forzada
        if ((response.data.messages && response.data.messages.length > 0) || forceRefresh) {
            if (response.data.messages && response.data.messages.length > 0) {
                // Procesar cada mensaje nuevo
                response.data.messages.forEach(message => {
                    // Verificar si ya tenemos este mensaje (evitar duplicados)
                    const exists = currentMessages.value.some(m => {
                        return m.id === message.id ||
                            (pendingMessages.value.has(m.id) &&
                                pendingMessages.value.get(m.id) === message.id);
                    });

                    if (!exists) {
                        // Reemplazar mensajes temporales correspondientes
                        const tempIndex = currentMessages.value.findIndex(m => {
                            return String(m.id).startsWith('temp-') &&
                                m.content === message.content &&
                                m.sender_id === message.sender_id;
                        });

                        if (tempIndex !== -1) {
                            // Reemplazar mensaje temporal con el real
                            const tempId = currentMessages.value[tempIndex].id;
                            pendingMessages.value.set(tempId, message.id);
                            currentMessages.value.splice(tempIndex, 1, message);
                        } else {
                            // Añadir nuevo mensaje
                            currentMessages.value.push(message);
                        }
                    }
                });

                // Actualizar último ID
                updateLastMessageId();

                // Hacer scroll al fondo si hay mensajes nuevos
                scrollToBottom();

                // Mostrar info de sincronización
                connectionStatus.value = 'synced';
                showSyncInfo();
            } else if (forceRefresh) {
                // Si es un refresh manual, actualizar estado
                connectionStatus.value = 'synced';
                showSyncInfo();
            }
        }

        // Ajustar el intervalo de polling según la actividad
        if (response.data.messages && response.data.messages.length > 0) {
            // Actualización más frecuente si hay actividad reciente
            pollingDelay.value = 5000; // 5 segundos
        } else if (response.data.throttled) {
            // Reducir frecuencia si el servidor está limitando las peticiones
            pollingDelay.value = Math.min(pollingDelay.value + 5000, 30000); // Máximo 30 segundos
        }

        isLoading.value = false;
    } catch (error) {
        console.error('Error al obtener mensajes recientes:', error);
        connectionStatus.value = 'idle';
        isLoading.value = false;
    }
};

// Actualización manual (desde botón de refresh)
const manualRefresh = () => {
    fetchRecentMessages(true);
};

// Función para iniciar el polling
// Función para iniciar el polling
const startPolling = () => {
    // Detener cualquier intervalo existente
    stopPolling();

    // Iniciar nuevo intervalo
    pollingInterval.value = setInterval(() => {
        fetchRecentMessages();
    }, pollingDelay.value);

    console.log(`🔄 Polling iniciado con intervalo de ${pollingDelay.value}ms`);
};

// Función para detener el polling
const stopPolling = () => {
    if (pollingInterval.value) {
        clearInterval(pollingInterval.value);
        pollingInterval.value = null;
    }
};

// Manejar la selección de archivos
const handleFileSelect = (event) => {
    const files = event.target.files;
    if (!files.length) return;

    selectedFiles.value = Array.from(files);

    // Generar URLs para la vista previa
    imagePreviewUrls.value = [];
    for (let i = 0; i < files.length; i++) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreviewUrls.value.push(e.target.result);
        };
        reader.readAsDataURL(files[i]);
    }
};

// Eliminar una imagen de la vista previa
const removeImage = (index) => {
    selectedFiles.value.splice(index, 1);
    imagePreviewUrls.value.splice(index, 1);
    // Resetear el input de archivo para reflejar los cambios
    if (selectedFiles.value.length === 0) {
        fileInput.value.value = '';
    }
};

// Abrir vista previa de imagen
const openImagePreview = (imagePath) => {
    previewImageUrl.value = '/storage/' + imagePath;
    showImagePreview.value = true;
};

// Enviar mensaje (versión optimizada para UI)
const sendMessage = () => {
    // Validar mensaje
    if (!messageForm.content || messageForm.content.trim() === '') {
        console.error("El contenido del mensaje no puede estar vacío");
        return;
    }

    // Crear un mensaje temporal para mostrar inmediatamente (UI optimista)
    const tempId = 'temp-' + Date.now();
    const tempMessage = {
        id: tempId,
        sender_id: usePage().props.auth.user.id,
        receiver_id: props.contact.id,
        content: messageForm.content,
        item_id: messageForm.item_id,
        item_interest_id: messageForm.item_interest_id,
        images: [],
        read: false,
        created_at: new Date().toISOString(),
        sender: {
            id: usePage().props.auth.user.id,
            name: usePage().props.auth.user.name
        }
    };

    // Añadir mensaje temporal a la lista
    currentMessages.value.push(tempMessage);

    // Hacer scroll al fondo
    scrollToBottom();

    // Crear y enviar FormData
    const formData = new FormData();
    formData.append('receiver_id', props.contact.id);
    formData.append('content', messageForm.content);

    if (messageForm.item_id) {
        formData.append('item_id', messageForm.item_id);
    }

    if (messageForm.item_interest_id) {
        formData.append('item_interest_id', messageForm.item_interest_id);
    }

    // Añadir imágenes
    for (let i = 0; i < selectedFiles.value.length; i++) {
        formData.append(`images[${i}]`, selectedFiles.value[i]);
    }

    // Resetear el formulario inmediatamente para mejor experiencia
    const contentCopy = messageForm.content;
    messageForm.content = '';
    messageForm.item_id = null;
    messageForm.item_interest_id = null;
    selectedFiles.value = [];
    imagePreviewUrls.value = [];
    if (fileInput.value) {
        fileInput.value.value = '';
    }

    // Mostrar estado de sincronización
    connectionStatus.value = 'syncing';
    showSyncInfo();

    // Enviar mensaje al servidor
    axios.post(route('messages.store'), formData)
        .then(response => {
            // Forzar una actualización inmediata
            fetchRecentMessages(true);

            // Cambiar estado a sincronizado
            connectionStatus.value = 'synced';
        })
        .catch(error => {
            console.error("❌ Error al enviar mensaje:", error);

            // Marcar el mensaje temporal como fallido
            const failedIndex = currentMessages.value.findIndex(m => m.id === tempId);
            if (failedIndex !== -1) {
                currentMessages.value[failedIndex].failed = true;
                currentMessages.value[failedIndex].content = contentCopy; // Restaurar contenido original
            }

            // Cambiar estado de conexión
            connectionStatus.value = 'idle';
        });
};

// Scroll al fondo del chat
const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

// Determinar si se debe mostrar la fecha en el encabezado
const shouldShowDate = (message, index) => {
    if (index === 0) return true;

    const currentDate = new Date(message.created_at).toLocaleDateString();
    const prevDate = new Date(currentMessages.value[index - 1].created_at).toLocaleDateString();

    return currentDate !== prevDate;
};

// Formatear fecha para el encabezado
const formatDateHeader = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const yesterday = new Date(now);
    yesterday.setDate(now.getDate() - 1);

    // Si es hoy
    if (date.toDateString() === now.toDateString()) {
        return 'Hoy';
    }

    // Si es ayer
    if (date.toDateString() === yesterday.toDateString()) {
        return 'Ayer';
    }

    // Otro día
    return date.toLocaleDateString('es-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

// Formatear hora del mensaje
const formatMessageTime = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
};

// Formatear estado del interés
const formatInterestStatus = (status) => {
    const statusMap = {
        'pending': 'Pendiente',
        'accepted': 'Aceptado',
        'rejected': 'Rechazado'
    };

    return statusMap[status] || status;
};

// Manejar visibilidad de la página
const handleVisibilityChange = () => {
    isWindowActive.value = document.visibilityState === 'visible';

    if (isWindowActive.value) {
        // Si la ventana está activa, hacer una actualización inmediata
        fetchRecentMessages(true);

        // Reiniciar polling cada 10 segundos
        pollingDelay.value = 10000;
        startPolling();
    } else {
        // Reducir frecuencia de polling cuando la ventana está inactiva
        pollingDelay.value = 30000; // 30 segundos
        startPolling();
    }
};

// Al montar el componente
onMounted(() => {
    // Verificar tamaño de la pantalla para decidir si mostrar el panel lateral
    const checkScreenSize = () => {
        if (window.innerWidth >= 768) { // md breakpoint en Tailwind
            showSidebar.value = true;
        } else {
            showSidebar.value = false;
        }
    };

    // Verificar al cargar
    checkScreenSize();

    // Verificar al redimensionar
    window.addEventListener('resize', checkScreenSize);

    // Inicializar último ID
    updateLastMessageId();

    // Iniciar polling con intervalo de 10 segundos
    pollingDelay.value = 10000;
    startPolling();

    // Detectar cambios de visibilidad
    document.addEventListener('visibilitychange', handleVisibilityChange);

    // Hacer scroll al fondo
    scrollToBottom();

    // Hacer una primera llamada para obtener mensajes recientes
    fetchRecentMessages(true);

    // Para compatibilidad, también intentamos configurar Echo si está disponible
    if (window.Echo) {
        try {
            const currentUserId = usePage().props.auth.user.id;

            window.Echo.private(`chat.${currentUserId}`)
                .listen('.new.message', (data) => {
                    // Forzar una actualización inmediata si se recibe un evento
                    fetchRecentMessages(true);
                });
        } catch (error) {
            console.log("No se pudo configurar Echo, usando solo polling");
        }
    }
});

// Limpiar al desmontar el componente
onBeforeUnmount(() => {
    // Detener polling
    stopPolling();

    // Limpiar timeout de sincronización
    if (syncInfoTimeout) {
        clearTimeout(syncInfoTimeout);
    }

    // Quitar listeners
    document.removeEventListener('visibilitychange', handleVisibilityChange);
    window.removeEventListener('resize', () => {});

    // Desconectar de Echo si existe
    if (window.Echo) {
        try {
            window.Echo.leave(`chat.${usePage().props.auth.user.id}`);
        } catch (error) {
            console.log("Error al desconectar de Echo, ignorado");
        }
    }
});

// Hacer scroll al fondo cuando cambia el número de mensajes
watch(() => currentMessages.value.length, () => {
    scrollToBottom();
});

// Actualizar mensajes si cambia la prop
watch(() => props.messages, (newMessages) => {
    currentMessages.value = [...newMessages];
    updateLastMessageId();
}, {deep: true});
</script>
