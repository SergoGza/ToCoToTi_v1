<!-- resources/js/Components/PrimaryButton.vue -->
<template>
    <button
        v-bind="$attrs"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2"
        style="background-color: #00913F; border-color: #00913F;"
        :style="{
            backgroundColor: disabled ? '#6B7280' : '#00913F',
            borderColor: disabled ? '#6B7280' : '#00913F'
        }"
        @mouseover="$event.target.style.backgroundColor = disabled ? '#6B7280' : '#006E2F'"
        @mouseout="$event.target.style.backgroundColor = disabled ? '#6B7280' : '#00913F'"
        @focus="$event.target.style.backgroundColor = disabled ? '#6B7280' : '#006E2F'"
        @blur="$event.target.style.backgroundColor = disabled ? '#6B7280' : '#00913F'"
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
