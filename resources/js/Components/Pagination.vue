<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <ul v-if="links.length > 3" class="pagination pagination-sm m-0 float-end">
        <li
            v-for="(link, index) in links"
            :key="index"
            class="page-item"
            :class="{ active: link.active, disabled: !link.url }"
        >
            <Link v-if="link.url" :href="link.url" class="page-link" preserve-scroll v-html="link.label" />
            <span v-else class="page-link" v-html="link.label" />
        </li>
    </ul>
</template>
