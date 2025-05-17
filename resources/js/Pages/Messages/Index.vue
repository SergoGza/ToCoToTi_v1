<template>
    <Head title="Mis mensajes" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">Mensajes</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Mensajes de alerta -->
                <div v-if="$page.props.flash && $page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash && $page.props.flash.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ $page.props.flash.error }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-white">
                        <div v-if="conversationsList.length" class="space-y-3">
                            <MessagePreview
                                v-for="conversation in conversationsList"
                                :key="conversation.contact.id"
                                :contact="conversation.contact"
                                :lastMessage="conversation.last_message"
                                :unreadCount="conversation.unread_count"
                                :item="conversation.item"
                            />
                        </div>
                        <div v-else class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400 mb-4">No tienes mensajes todavía.</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500">
                                Puedes iniciar una conversación desde un ítem o cuando alguien muestre interés en tus ítems.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MessagePreview from '@/Components/MessagePreview.vue';

// Props
const props = defineProps({
    conversations: {
        type: Array,
        default: () => []
    }
});

// Crear una ref reactiva para las conversaciones
const conversationsList = ref([...props.conversations]);

// Actualizar la lista de conversaciones cuando llega un nuevo mensaje
const updateConversationsList = (message) => {
    console.log("🔄 Actualizando lista de conversaciones con nuevo mensaje:", message);

    // Obtener el ID del usuario actual
    const currentUserId = usePage().props.auth.user.id;

    // Determinar el ID del contacto (el otro participante en la conversación)
    const contactId = message.sender_id === currentUserId ? message.receiver_id : message.sender_id;

    // Buscar si la conversación ya existe en la lista
    const conversationIndex = conversationsList.value.findIndex(
        conv => conv.contact.id === contactId
    );

    if (conversationIndex !== -1) {
        console.log("🔍 Encontrada conversación existente con contacto ID:", contactId);

        // Hacer una copia de la conversación existente
        const conversation = {...conversationsList.value[conversationIndex]};

        // Actualizar último mensaje
        conversation.last_message = {
            content: message.content.length > 50 ? message.content.substring(0, 50) + '...' : message.content,
            date: message.created_at,
            is_mine: message.sender_id === currentUserId
        };

        // Incrementar contador de no leídos si somos el destinatario
        if (message.receiver_id === currentUserId && message.sender_id !== currentUserId) {
            conversation.unread_count++;
            console.log("🔔 Incrementado contador de no leídos a:", conversation.unread_count);
        }

        // Remover la conversación de la lista
        conversationsList.value.splice(conversationIndex, 1);

        // Añadirla al principio (ordenar por mensaje más reciente)
        conversationsList.value.unshift(conversation);

        console.log("✅ Lista de conversaciones actualizada correctamente");
    } else {
        console.log("⚠️ Conversación no encontrada en la lista, se necesita recargar la página");
        // Si es una nueva conversación, podríamos necesitar hacer una petición
        // para obtener los detalles del contacto o simplemente recargar la página
    }
};

// Configurar los listeners para nuevos mensajes
onMounted(() => {
    console.log("🔄 Componente de lista de mensajes montado");

    // Escuchar por nuevos mensajes a través del evento global
    if (window.emitter) {
        console.log("📡 Configurando listener para eventos 'new-message'");
        window.emitter.on('new-message', updateConversationsList);
    }

    // También escuchar directamente a través de Echo para mensajes dirigidos a este usuario
    console.log(`🔊 Configurando Echo listener para canal chat.${usePage().props.auth.user.id}`);
    window.Echo.private(`chat.${usePage().props.auth.user.id}`)
        .listen('.new.message', (e) => {
            console.log("🔔 Mensaje recibido directamente vía Echo:", e);

            // Solo procesamos mensajes si no estamos viendo esa conversación actualmente
            const currentRoute = window.location.pathname;
            const messageRoute = `/messages/${e.sender_id === usePage().props.auth.user.id ? e.receiver_id : e.sender_id}`;

            if (currentRoute !== messageRoute) {
                console.log("📨 Procesando mensaje para actualizar lista mientras no estamos en la conversación");
                updateConversationsList(e);
            } else {
                console.log("🔕 No actualizamos lista porque ya estamos en esa conversación");
            }
        })
        .subscribed(() => {
            console.log("✅ Suscrito al canal de chat correctamente");
        })
        .error(error => {
            console.error("❌ Error al suscribirse al canal:", error);
        });
});

// Limpiar los listeners al desmontar el componente
onBeforeUnmount(() => {
    console.log("🧹 Limpiando listeners");

    if (window.emitter) {
        window.emitter.off('new-message', updateConversationsList);
    }

    if (window.Echo) {
        window.Echo.leave(`chat.${usePage().props.auth.user.id}`);
    }
});
</script>
