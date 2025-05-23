<template>
    <div>
        <!-- Badge de estado -->
        <span
            class="px-3 py-1 inline-flex text-sm font-medium rounded-full"
            :class="{
                'bg-primary-light/20 text-primary-dark dark:bg-primary-dark/30 dark:text-primary-light': status === 'available',
                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200': status === 'reserved',
                'bg-secondary-light/20 text-secondary-dark dark:bg-secondary-dark/30 dark:text-secondary-light': status === 'given'
            }"
        >
            {{ statusText }}
        </span>
    </div>
</template>

<script setup>
import {computed} from 'vue';

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
