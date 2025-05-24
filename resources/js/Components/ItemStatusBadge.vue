<template>
    <div>
        <!-- Badge de estado -->
        <span
            class="px-3 py-1 inline-flex text-sm font-medium rounded-full"
            :class="{
                'bg-[#00913F]/20 text-[#00913F] font-semibold': status === 'available',
                'bg-[#825028]/20 text-[#825028] font-semibold': status === 'reserved',
                'bg-[#333333]/20 text-[#333333] font-semibold': status === 'given'
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
