<!-- resources/js/Components/PrimaryButton.vue -->
<template>
    <button
        :type="type"
        :class="[
            'inline-flex items-center px-4 py-2 rounded-md font-semibold text-sm tracking-widest',
            'transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2',
            'bg-primary text-white hover:bg-primary-light focus:ring-primary',
            disabled ? 'opacity-60 cursor-not-allowed' : 'active:bg-primary-dark'
        ]"
        :disabled="disabled"
    >
        <slot />
    </button>
</template>

<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});
</script>
