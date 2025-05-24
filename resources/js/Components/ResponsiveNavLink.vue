<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[#00913F] text-start text-base font-semibold text-[#00913F] bg-[#00913F]/10 focus:outline-none focus:text-[#007833] focus:bg-[#00913F]/20 focus:border-[#007833] transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-[#333333] hover:text-[#00913F] hover:bg-[#FAF6F0] hover:border-[#00913F]/30 focus:outline-none focus:text-[#00913F] focus:bg-[#FAF6F0] focus:border-[#00913F]/30 transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
