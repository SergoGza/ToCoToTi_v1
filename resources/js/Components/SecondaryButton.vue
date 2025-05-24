<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 bg-white border border-[#825028] rounded-md font-semibold text-xs text-[#825028] uppercase tracking-widest shadow-sm hover:bg-[#825028]/10 focus:outline-none focus:ring-2 focus:ring-[#825028] focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
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
});
</script>
