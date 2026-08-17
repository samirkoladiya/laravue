<script setup>
import { onMounted, onBeforeUnmount, useSlots } from 'vue';

const props = defineProps({
    src: { type: String, default: null },
});

const slots = useSlots();
let el = null;

onMounted(() => {
    el = document.createElement('script');

    if (props.src) {
        el.src = props.src;
        // Preserve document order across multiple dynamically-inserted
        // <script src> tags (e.g. Popper must run before Bootstrap).
        el.async = false;
    } else {
        const content = (slots.default?.() ?? [])
            .map(vnode => (typeof vnode.children === 'string' ? vnode.children : ''))
            .join('');

        el.textContent = content;
    }

    document.body.appendChild(el);
});

onBeforeUnmount(() => {
    el?.remove();
});
</script>

<template></template>
