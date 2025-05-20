<template>
    <Head title="Mis conversaciones" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Mis conversaciones
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div v-if="conversations.length" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <div v-for="conversation in conversations" :key="conversation.id"
                                 class="py-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                                 @click="navigateToConversation(conversation.id)">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold">
                                            {{ conversation.title }}
                                            <span v-if="conversation.unread_count > 0"
                                                  class="ml-2 px-2 py-1 text-xs font-semibold bg-blue-500 text-white rounded-full">
                                                {{ conversation.unread_count }}
                                            </span>
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Con: {{ conversation.other_user.name }}
                                        </p>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ formatDate(conversation.last_message_at) }}
                                    </div>
                                </div>
                                <div v-if="conversation.latest_message" class="mt-2">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-1">
                                        <span v-if="conversation.latest_message.user_id === $page.props.auth.user.id">
                                            Tú:
                                        </span>
                                        {{ conversation.latest_message.content }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400 mb-4">No tienes conversaciones todavía.</p>
                            <Link :href="route('items.index')" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Explorar items
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    conversations: Array
});

const navigateToConversation = (conversationId) => {
    router.visit(route('messages.show', conversationId));
};

const formatDate = (dateString) => {
    if (!dateString) return '';

    const date = new Date(dateString);
    const now = new Date();
    const diff = (now - date) / 1000; // Diferencia en segundos

    if (diff < 60) {
        return 'Justo ahora';
    } else if (diff < 3600) {
        const minutes = Math.floor(diff / 60);
        return `Hace ${minutes} ${minutes === 1 ? 'minuto' : 'minutos'}`;
    } else if (diff < 86400) { // 24 horas
        const hours = Math.floor(diff / 3600);
        return `Hace ${hours} ${hours === 1 ? 'hora' : 'horas'}`;
    } else if (diff < 604800) { // 7 días
        const days = Math.floor(diff / 86400);
        return `Hace ${days} ${days === 1 ? 'día' : 'días'}`;
    } else {
        return date.toLocaleDateString();
    }
};
</script>
