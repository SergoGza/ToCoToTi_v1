<!-- resources/js/Components/PrimaryButton.vue -->
<template>
    <button
        class="inline-flex items-center rounded-md border border-transparent bg-[#00913F] px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-[#007833] focus:bg-[#007833] focus:outline-none focus:ring-2 focus:ring-[#00913F] focus:ring-offset-2 active:bg-[#006b2d] disabled:opacity-25"
    >
        <slot />
    </button>
</template>

<script setup>
defineProps({
    disabled: {
        type: Boolean,
        default: false,
    },
});
</script>
