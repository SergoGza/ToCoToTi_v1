<template>
    <Head :title="'Conversación con ' + contact.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Conversación con {{ contact.name }}</h2>
                <Link
                    :href="route('messages.index')"
                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm"
                >
                    Volver a la bandeja de entrada
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col md:flex-row gap-6">
                <!-- Panel lateral con información contextual -->
                <div class="md:w-1/4">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                        <h3 class="font-semibold text-lg mb-4">{{ contact.name }}</h3>

                        <!-- Si el contacto está interesado en mis ítems -->
                        <div v-if="interestsInMyItems.length > 0" class="mb-4">
                            <h4 class="font-medium text-sm mb-2 text-gray-700">Items que le interesan:</h4>
                            <div v-for="interest in interestsInMyItems" :key="interest.id" class="mb-2 p-2 bg-blue-50 rounded text-sm">
                                <p class="font-medium">{{ interest.item.title }}</p>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-xs"
                                          :class="{
                                            'text-yellow-600': interest.status === 'pending',
                                            'text-green-600': interest.status === 'accepted',
                                            'text-red-600': interest.status === 'rejected'
                                        }"
                                    >
                                        {{ formatInterestStatus(interest.status) }}
                                    </span>
                                    <Link
                                        :href="route('items.show', interest.item.id)"
                                        class="text-xs text-blue-600 hover:text-blue-800"
                                    >
                                        Ver item
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Si estoy interesado en sus ítems -->
                        <div v-if="myInterestsInTheirItems.length > 0">
                            <h4 class="font-medium text-sm mb-2 text-gray-700">Items que me interesan:</h4>
                            <div v-for="interest in myInterestsInTheirItems" :key="interest.id" class="mb-2 p-2 bg-green-50 rounded text-sm">
                                <p class="font-medium">{{ interest.item.title }}</p>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-xs"
                                          :class="{
                                            'text-yellow-600': interest.status === 'pending',
                                            'text-green-600': interest.status === 'accepted',
                                            'text-red-600': interest.status === 'rejected'
                                        }"
                                    >
                                        {{ formatInterestStatus(interest.status) }}
                                    </span>
                                    <Link
                                        :href="route('items.show', interest.item.id)"
                                        class="text-xs text-blue-600 hover:text-blue-800"
                                    >
                                        Ver item
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items relacionados a la conversación -->
                    <div v-if="itemsRelated.length > 0" class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-semibold text-lg mb-4">Items relacionados</h3>
                        <div v-for="item in itemsRelated" :key="item.id" class="mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gray-200 rounded overflow-hidden">
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
                                    <p class="text-xs text-gray-500">
                                        De: {{ item.user.name }}
                                    </p>
                                </div>
                            </div>
                            <Link
                                :href="route('items.show', item.id)"
                                class="mt-1 text-xs text-blue-600 hover:text-blue-800 block"
                            >
                                Ver detalles
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Panel principal con mensajes -->
                <div class="md:w-3/4">
                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                        <!-- Mensajes -->
                        <div class="h-96 overflow-y-auto p-6" ref="messagesContainer">
                            <div v-if="messages.length === 0" class="text-center py-10">
                                <p class="text-gray-500">No hay mensajes en esta conversación.</p>
                                <p class="text-sm text-gray-400 mt-2">Envía un mensaje para comenzar a chatear.</p>
                            </div>
                            <div v-else class="space-y-4">
                                <div
                                    v-for="(message, index) in messages"
                                    :key="message.id"
                                    class="flex"
                                    :class="{ 'justify-end': message.sender_id === $page.props.auth.user.id }"
                                >
                                    <!-- Mostrar fecha si es el primer mensaje o si cambia el día -->
                                    <div
                                        v-if="shouldShowDate(message, index)"
                                        class="w-full text-center my-4"
                                    >
                                        <span class="inline-block px-4 py-1 bg-gray-100 rounded-full text-xs text-gray-600">
                                            {{ formatDateHeader(message.created_at) }}
                                        </span>
                                    </div>

                                    <!-- Mensaje de otra persona -->
                                    <div
                                        v-if="message.sender_id !== $page.props.auth.user.id"
                                        class="max-w-3/4 bg-gray-100 p-3 rounded-lg"
                                    >
                                        <!-- Contenido del mensaje -->
                                        <p class="text-gray-800">{{ message.content }}</p>

                                        <!-- Referencias a ítems o intereses si existen -->
                                        <div v-if="message.item_id || (message.interest && message.interest.item_id)" class="mt-1 p-1 bg-gray-200 rounded text-xs text-gray-600">
                                            Sobre: {{ message.item ? message.item.title : message.interest.item.title }}
                                        </div>

                                        <!-- Hora del mensaje -->
                                        <p class="text-right text-xs text-gray-500 mt-1">
                                            {{ formatMessageTime(message.created_at) }}
                                        </p>
                                    </div>

                                    <!-- Mensaje propio -->
                                    <div
                                        v-else
                                        class="max-w-3/4 bg-blue-100 p-3 rounded-lg"
                                    >
                                        <!-- Contenido del mensaje -->
                                        <p class="text-gray-800">{{ message.content }}</p>

                                        <!-- Referencias a ítems o intereses si existen -->
                                        <div v-if="message.item_id || (message.interest && message.interest.item_id)" class="mt-1 p-1 bg-blue-200 rounded text-xs text-gray-600">
                                            Sobre: {{ message.item ? message.item.title : message.interest.item.title }}
                                        </div>

                                        <!-- Hora del mensaje y estado de lectura -->
                                        <div class="text-right text-xs text-gray-500 mt-1 flex justify-end items-center">
                                            <span v-if="message.read" class="mr-1 text-blue-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                            {{ formatMessageTime(message.created_at) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Formulario para enviar mensaje -->
                        <div class="border-t p-4">
                            <form @submit.prevent="sendMessage">
                                <div class="mb-3">
                                    <textarea
                                        v-model="messageForm.content"
                                        class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                        rows="3"
                                        placeholder="Escribe tu mensaje..."
                                        required
                                    ></textarea>
                                </div>

                                <!-- Selector de contexto -->
                                <div class="flex flex-wrap gap-3 mb-3">
                                    <div v-if="itemsRelated.length > 0" class="w-full md:w-auto">
                                        <select
                                            v-model="messageForm.item_id"
                                            class="rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
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
                                            class="rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
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

                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                        :disabled="messageForm.processing"
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
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Props
const props = defineProps({
    contact: Object,
    messages: Array,
    itemsRelated: Array,
    interestsInMyItems: Array,
    myInterestsInTheirItems: Array
});

// Referencia al contenedor de mensajes para scroll
const messagesContainer = ref(null);

// Formulario para enviar mensajes
const messageForm = useForm({
    receiver_id: props.contact.id,
    content: '',
    item_id: null,
    item_interest_id: null
});

// Enviar mensaje
const sendMessage = () => {
    messageForm.post(route('messages.store'), {
        preserveScroll: true,
        onSuccess: () => {
            messageForm.reset('content', 'item_id', 'item_interest_id');
            scrollToBottom();
        }
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
    const prevDate = new Date(props.messages[index - 1].created_at).toLocaleDateString();

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

// Hacer scroll al fondo cuando se cargan los mensajes o cuando hay nuevos
watch(() => props.messages.length, () => {
    scrollToBottom();
});

// Al montar el componente
onMounted(() => {
    scrollToBottom();
});
</script>
