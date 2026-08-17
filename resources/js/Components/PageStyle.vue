<script setup>
import { onMounted, onBeforeUnmount, useSlots } from 'vue';

const props = defineProps({
    href: { type: String, default: null },
});

const slots = useSlots();
let el = null;

onMounted(() => {
    if (props.href) {
        el = document.createElement('link');
        el.rel = 'stylesheet';
        el.href = props.href;
    } else {
        const content = (slots.default?.() ?? [])
            .map(vnode => (typeof vnode.children === 'string' ? vnode.children : ''))
            .join('');

        el = document.createElement('style');
        el.textContent = content;
    }

    document.head.appendChild(el);
});

onBeforeUnmount(() => {
    el?.remove();
});
</script>

<template></template>
