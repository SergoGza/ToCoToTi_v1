<!-- resources/js/Components/ItemStatusBadge.vue -->
<template>
    <div>
        <!-- Badge de estado -->
        <span
            class="px-3 py-1 inline-flex text-sm font-medium rounded-full"
            :class="{
                'bg-green-100 text-green-800': status === 'available',
                'bg-yellow-100 text-yellow-800': status === 'reserved',
                'bg-blue-100 text-blue-800': status === 'given'
            }"
        >
            {{ statusText }}
        </span>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true
    }
});

// Texto descriptivo del estado
const statusText = computed(() => {
    const statusMap = {
        'available': 'Disponible',
        'reserved': 'Reservado',
        'given': 'Entregado'
    };

    return statusMap[props.status] || props.status;
});
</script>
