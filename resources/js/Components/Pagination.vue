<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1">
            <template v-for="(link, key) in links" :key="key">
                <div v-if="link.url === null"
                     class="mr-1 mb-1 px-4 py-2 text-sm text-gray-400 border rounded"
                     v-html="link.label"></div>
                <Link v-else
                      class="mr-1 mb-1 px-4 py-2 text-sm border rounded"
                      :class="{ 'bg-blue-600 text-white': link.active }"
                      :href="link.url"
                      v-html="link.label"></Link>
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
