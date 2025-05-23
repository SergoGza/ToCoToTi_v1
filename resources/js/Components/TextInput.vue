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
        ref="input"
        v-model="model"
        class="rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary-light focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:focus:border-primary-light"
        v-bind="$attrs"
    />
</template>
