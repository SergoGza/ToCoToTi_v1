<!-- resources/js/Components/ItemStatusChanger.vue -->
<template>
    <div>
        <!-- Botón para abrir el modal -->
        <button
            @click="showModal = true"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
            Cambiar estado
        </button>

        <!-- Modal de cambio de estado -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
            <div class="bg-white p-6 rounded-lg max-w-md w-full">
                <h3 class="text-lg font-bold mb-4">Cambiar estado del item</h3>

                <div class="mb-4">
                    <p class="mb-2">Estado actual:
                        <span
                            class="px-2 py-1 text-sm rounded-full"
                            :class="{
                                'bg-green-100 text-green-800': itemStatus === 'available',
                                'bg-yellow-100 text-yellow-800': itemStatus === 'reserved',
                                'bg-blue-100 text-blue-800': itemStatus === 'given'
                            }"
                        >
                            {{ statusText(itemStatus) }}
                        </span>
                    </p>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Selecciona el nuevo estado:
                        </label>

                        <div class="space-y-2">
                            <label class="flex items-center space-x-2">
                                <input
                                    type="radio"
                                    v-model="newStatus"
                                    value="available"
                                    :disabled="itemStatus === 'available'"
                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                >
                                <span>Disponible</span>
                            </label>

                            <label class="flex items-center space-x-2">
                                <input
                                    type="radio"
                                    v-model="newStatus"
                                    value="reserved"
                                    :disabled="itemStatus === 'reserved'"
                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                >
                                <span>Reservado</span>
                            </label>

                            <label class="flex items-center space-x-2">
                                <input
                                    type="radio"
                                    v-model="newStatus"
                                    value="given"
                                    :disabled="itemStatus === 'given'"
                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                >
                                <span>Entregado</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div v-if="itemStatus === 'reserved' && newStatus === 'given'" class="mb-4 p-4 bg-yellow-50 rounded border border-yellow-200">
                    <p class="text-sm text-yellow-800">
                        <strong>Nota:</strong> Al marcar un ítem como entregado, se cerrará permanentemente y no podrá volver a estar disponible.
                    </p>
                </div>

                <div v-if="showInterestedUsers && newStatus === 'reserved'" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Selecciona el usuario interesado:
                    </label>

                    <select
                        v-model="selectedInterest"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                        <option :value="null">Selecciona un usuario</option>
                        <option
                            v-for="interest in pendingInterests"
                            :key="interest.id"
                            :value="interest.id"
                        >
                            {{ interest.user.name }} - "{{ truncateMessage(interest.message) }}"
                        </option>
                    </select>
                </div>

                <div class="flex justify-end space-x-4">
                    <button
                        @click="showModal = false"
                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
                    >
                        Cancelar
                    </button>
                    <button
                        @click="confirmStatusChange"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                        :disabled="!canChangeStatus"
                    >
                        Confirmar cambio
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    itemId: {
        type: Number,
        required: true
    },
    itemStatus: {
        type: String,
        required: true
    },
    pendingInterests: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['statusChanged']);

const showModal = ref(false);
const newStatus = ref(props.itemStatus);
const selectedInterest = ref(null);

// Reset the form when opening modal
watch(showModal, (value) => {
    if (value) {
        newStatus.value = props.itemStatus;
        selectedInterest.value = null;
    }
});

// Determinar si se muestra el selector de usuarios interesados
const showInterestedUsers = computed(() => {
    return props.pendingInterests && props.pendingInterests.length > 0;
});

// Verificar si se puede cambiar el estado
const canChangeStatus = computed(() => {
    if (newStatus.value === props.itemStatus) return false;

    if (newStatus.value === 'reserved' && showInterestedUsers.value) {
        return selectedInterest.value !== null;
    }

    return true;
});

// Texto descriptivo del estado
const statusText = (status) => {
    const statusMap = {
        'available': 'Disponible',
        'reserved': 'Reservado',
        'given': 'Entregado'
    };

    return statusMap[status] || status;
};

// Truncar mensaje largo para el dropdown
const truncateMessage = (message) => {
    if (!message) return 'Sin mensaje';
    return message.length > 30 ? message.substring(0, 30) + '...' : message;
};

// Confirmar cambio de estado
const confirmStatusChange = () => {
    const form = useForm({
        status: newStatus.value,
        interest_id: selectedInterest.value
    });

    form.patch(route('items.updateStatus', props.itemId), {
        onSuccess: () => {
            showModal.value = false;
            emit('statusChanged', newStatus.value);
        }
    });
};
</script>
