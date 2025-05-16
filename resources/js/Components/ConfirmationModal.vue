<!-- resources/js/Components/ConfirmationModal.vue -->
<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg max-w-md w-full">
            <div class="mb-4">
                <h3 class="text-lg font-bold">{{ title }}</h3>
                <p class="mt-2 text-gray-600">{{ message }}</p>
            </div>

            <div class="flex justify-end space-x-4">
                <button
                    @click="cancel"
                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                >
                    {{ cancelText }}
                </button>
                <button
                    @click="confirm"
                    class="px-4 py-2 text-white rounded"
                    :class="confirmButtonClass"
                >
                    {{ confirmText }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    title: {
        type: String,
        default: '¿Estás seguro?'
    },
    message: {
        type: String,
        default: 'Esta acción no se puede deshacer.'
    },
    confirmText: {
        type: String,
        default: 'Confirmar'
    },
    cancelText: {
        type: String,
        default: 'Cancelar'
    },
    type: {
        type: String,
        default: 'danger',
        validator: (value) => ['danger', 'warning', 'info', 'success'].includes(value)
    }
});

const emit = defineEmits(['confirm', 'cancel']);

const confirm = () => {
    emit('confirm');
};

const cancel = () => {
    emit('cancel');
};

const confirmButtonClass = computed(() => {
    const classes = {
        'danger': 'bg-red-600 hover:bg-red-700',
        'warning': 'bg-yellow-600 hover:bg-yellow-700',
        'info': 'bg-blue-600 hover:bg-blue-700',
        'success': 'bg-green-600 hover:bg-green-700'
    };

    return classes[props.type] || classes.danger;
});
</script>
