<!-- resources/js/Components/Pagination.vue -->
<template>
    <div v-if="links.length > 3" class="flex justify-center">
        <div class="flex flex-wrap -mb-1 rounded-md overflow-hidden shadow-sm">
            <template v-for="(link, key) in links" :key="key">
                <div
                    v-if="link.url === null"
                    class="mr-1 mb-1 px-4 py-2 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed"
                    v-html="link.label"
                ></div>
                <Link
                    v-else
                    class="mr-1 mb-1 px-4 py-2 text-sm rounded-md transition-colors"
                    :class="{
                        'bg-primary text-white hover:bg-primary-light': link.active,
                        'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200': !link.active
                    }"
                    :href="link.url"
                    v-html="link.label"
                ></Link>
            </template>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array
});
</script>
