<!-- resources/js/Pages/Messages/Show.vue -->
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
                                            <span v-if="message.read" class="mr-1 text-blue-600 dark:text-blue-400">
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
import { ref, watch, onMounted, nextTick, onBeforeUnmount } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

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

// Formulario para enviar mensajes
const messageForm = useForm({
    receiver_id: props.contact.id,
    content: '',
    item_id: null,
    item_interest_id: null,
    images: []
});

// Función para mostrar/ocultar panel en móvil
const toggleSidebar = () => {
    showSidebar.value = !showSidebar.value;
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

// Enviar mensaje
const sendMessage = () => {
    // Crear un objeto FormData para enviar el formulario con archivos
    const formData = new FormData();
    formData.append('receiver_id', props.contact.id);
    formData.append('content', messageForm.content);

    if (messageForm.item_id) {
        formData.append('item_id', messageForm.item_id);
    }

    if (messageForm.item_interest_id) {
        formData.append('item_interest_id', messageForm.item_interest_id);
    }

    // Añadir las imágenes seleccionadas
    for (let i = 0; i < selectedFiles.value.length; i++) {
        formData.append(`images[${i}]`, selectedFiles.value[i]);
    }

    console.log("Enviando mensaje a: ", props.contact.id);

    // Enviar el formulario
    messageForm.post(route('messages.store'), {
        forceFormData: true,
        data: formData,
        preserveScroll: true,
        onSuccess: () => {
            console.log("✅ Mensaje enviado correctamente");
            // Resetear el formulario
            messageForm.reset('content', 'item_id', 'item_interest_id');
            selectedFiles.value = [];
            imagePreviewUrls.value = [];
            if (fileInput.value) {
                fileInput.value.value = '';
            }

            // Hacer scroll al fondo
            scrollToBottom();
        },
        onError: (errors) => {
            console.error("❌ Error al enviar mensaje:", errors);
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

// Manejar la recepción de un nuevo mensaje a través de Echo
const handleNewMessage = (e) => {
    console.log("🔔 Evento de mensaje recibido:", e);

    // Verificar si el mensaje es para esta conversación
    const isSameConversation = (
        (e.sender_id === props.contact.id && e.receiver_id === usePage().props.auth.user.id) ||
        (e.sender_id === usePage().props.auth.user.id && e.receiver_id === props.contact.id)
    );

    if (isSameConversation) {
        console.log("👥 Mensaje relacionado con esta conversación");

        // Verificar si el mensaje ya existe para evitar duplicados
        const messageExists = currentMessages.value.some(m => m.id === e.id);

        if (!messageExists) {
            console.log("➕ Añadiendo nuevo mensaje a la conversación");
            // Añadir mensaje a la lista
            currentMessages.value.push(e);

            // Marcar como leído si somos el destinatario
            if (e.receiver_id === usePage().props.auth.user.id) {
                fetch(route('messages.read', props.contact.id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(() => {
                    console.log("✓ Mensaje marcado como leído");
                }).catch(error => {
                    console.error("❌ Error al marcar como leído:", error);
                });
            }

            // Hacer scroll al fondo
            scrollToBottom();

            // Emitir evento global para actualizar la lista de conversaciones
            if (window.emitter) {
                console.log("📢 Emitiendo evento 'new-message' para actualizar lista de conversaciones");
                window.emitter.emit('new-message', e);
            }
        } else {
            console.log("⚠️ Mensaje duplicado, no se añade a la conversación");
        }
    } else {
        console.log("🔀 Mensaje pertenece a otra conversación");
    }
};

// Configurar Echo para escuchar mensajes en tiempo real
const setupEchoListeners = () => {
    console.log(`Escuchando en canal: chat.${usePage().props.auth.user.id}`);

    try {
        // Primero, verificamos que Echo esté disponible
        if (!window.Echo) {
            console.error("Echo no está inicializado");
            return;
        }

        // Suscribirse al canal personal con manejo de errores
        window.Echo.private(`chat.${usePage().props.auth.user.id}`)
            .listen('.new.message', (e) => {
                console.log('¡MENSAJE RECIBIDO!', e);
                alert('¡Nuevo mensaje recibido!');

                // Si lo deseas, puedes seguir con tu lógica actual aquí
                // handleNewMessage(e);
            })
            .subscribed(() => {
                console.log("✅ Suscrito al canal correctamente");
            })
            .error(error => {
                console.error("❌ Error al suscribirse al canal:", error);
                alert("Error de suscripción: " + JSON.stringify(error));
            });

        console.log("Escucha configurada exitosamente");
    } catch (error) {
        console.error("Error al configurar la escucha:", error);
    }
};

// Al montar el componente
onMounted(() => {
    console.log("🔄 Componente de mensajes montado");

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

    // Hacer scroll al fondo
    scrollToBottom();

    // Configurar Echo para mensajes en tiempo real
    setupEchoListeners();

    // Registrar que estamos listos para recibir mensajes
    console.log("📨 Listo para recibir mensajes en tiempo real");
});

// Limpiar listeners al desmontar el componente
onBeforeUnmount(() => {
    console.log("🧹 Limpiando listeners y desconectando de canales");

    if (window.Echo) {
        window.Echo.leave(`chat.${usePage().props.auth.user.id}`);
    }

    // Eliminar listener de resize
    window.removeEventListener('resize', () => {});
});

// Hacer scroll al fondo cuando cambia el número de mensajes
watch(() => currentMessages.value.length, () => {
    scrollToBottom();
});

// Actualizar mensajes si cambia la prop
watch(() => props.messages, (newMessages) => {
    currentMessages.value = [...newMessages];
}, { deep: true });
</script>
