<template>
    <Head title="Mis mensajes" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mensajes</h2>
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

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div v-if="conversations.length" class="space-y-3">
                            <MessagePreview
                                v-for="conversation in conversations"
                                :key="conversation.contact.id"
                                :contact="conversation.contact"
                                :lastMessage="conversation.last_message"
                                :unreadCount="conversation.unread_count"
                                :item="conversation.item"
                            />
                        </div>
                        <div v-else class="text-center py-8">
                            <p class="text-gray-500 mb-4">No tienes mensajes todavía.</p>
                            <p class="text-sm text-gray-400">
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
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MessagePreview from '@/Components/MessagePreview.vue';

// Props
const props = defineProps({
    conversations: {
        type: Array,
        default: () => []
    }
});
</script>
