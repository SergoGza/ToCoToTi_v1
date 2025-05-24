<script setup>
import { onMounted, ref } from 'vue';

// Definir el modelo correctamente para que funcione con v-model
const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value && input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

// Exponer el método focus para que otros componentes puedan usarlo
defineExpose({
    focus: () => input.value?.focus()
});
</script>

<template>
    <input
        class="rounded-md border-gray-300 shadow-sm focus:border-[#00913F] focus:ring-[#00913F]"
        v-model="model"
        ref="input"
    />
</template>
