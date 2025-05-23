<!-- resources/js/Components/ApplicationLogo.vue -->
<template>
    <div class="flex items-center">
        <img
            src="/images/tocototi-logo.png"
            alt="ToCoToTi - Todo Cojo, Todo Tiro"
            class="h-8 w-auto object-contain"
            style="max-height: 32px;"
        />
    </div>
</template>

<script setup>
import { computed } from 'vue';

// Props para permitir personalización
const props = defineProps({
    variant: {
        type: String,
        default: 'default', // 'default', 'white', 'dark'
    },
    size: {
        type: String,
        default: 'normal' // 'small', 'normal', 'large'
    }
});

// Determinar qué versión del logo usar según el contexto
const logoSrc = computed(() => {
    // Si tienes diferentes versiones del logo (claro/oscuro), puedes manejarlas aquí
    return '/images/tocototi-logo.png';
});

// Clases CSS adicionales según el tamaño
const imageClasses = computed(() => ({
    'max-h-8': props.size === 'small',
    'max-h-12': props.size === 'normal',
    'max-h-16': props.size === 'large'
}));
</script>
