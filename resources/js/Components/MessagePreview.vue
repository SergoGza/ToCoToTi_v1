<!-- resources/js/Components/MessagePreview.vue -->
<template>
    <div
        class="p-4 border rounded-lg hover:bg-gray-50 transition cursor-pointer"
        :class="{ 'border-blue-300 bg-blue-50 hover:bg-blue-50': unreadCount > 0 }"
        @click="goToConversation"
    >
        <div class="flex justify-between items-start">
            <div class="flex items-center space-x-3">
                <!-- Avatar (puede ser un placeholder) -->
                <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-semibold">
                    {{ contact.name.charAt(0).toUpperCase() }}
                </div>

                <div class="flex-1">
                    <h3 class="font-medium">{{ contact.name }}</h3>
                    <p class="text-sm text-gray-600 line-clamp-1">
                        <span v-if="lastMessage.is_mine" class="text-gray-500">Tú: </span>
                        {{ lastMessage.content }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col items-end">
                <span class="text-xs text-gray-500">{{ formatMessageDate(lastMessage.date) }}</span>
                <span v-if="unreadCount > 0" class="mt-1 bg-blue-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                    {{ unreadCount > 9 ? '9+' : unreadCount }}
                </span>
            </div>
        </div>

        <!-- Item relacionado si existe -->
        <div v-if="item" class="mt-2 flex items-center space-x-2 p-2 bg-gray-100 rounded text-sm">
            <div v-if="item.image" class="w-8 h-8 bg-gray-200 rounded overflow-hidden">
                <img :src="'/storage/' + item.image" alt="Item image" class="w-full h-full object-cover">
            </div>
            <div v-else class="w-8 h-8 bg-gray-300 rounded flex items-center justify-center text-gray-500 text-xs">
                No img
            </div>
            <span class="line-clamp-1">{{ item.title }}</span>
        </div>
    </div>
</template>

<script setup>
import { useRouter } from '@inertiajs/vue3';

const props = defineProps({
    contact: {
        type: Object,
        required: true
    },
    lastMessage: {
        type: Object,
        required: true
    },
    unreadCount: {
        type: Number,
        default: 0
    },
    item: {
        type: Object,
        default: null
    }
});

const router = useRouter();

const goToConversation = () => {
    router.visit(route('messages.show', props.contact.id));
};

const formatMessageDate = (dateString) => {
    if (!dateString) return '';

    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

    if (diffDays === 0) {
        // Hoy - mostrar hora
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    } else if (diffDays === 1) {
        // Ayer
        return 'Ayer';
    } else if (diffDays < 7) {
        // Esta semana - mostrar día
        const days = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        return days[date.getDay()];
    } else {
        // Más de una semana - mostrar fecha
        return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' });
    }
};
</script>
