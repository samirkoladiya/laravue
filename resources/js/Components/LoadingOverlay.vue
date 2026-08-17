<script setup>
defineProps({
    label: {
        type: String,
        default: 'Loading...',
    },
});
</script>

<template>
    <div class="loading-overlay d-flex align-items-center justify-content-center" role="status">
        <span class="spinner-border" aria-hidden="true"></span>
        <span class="visually-hidden">{{ label }}</span>
    </div>
</template>

<style scoped>
.loading-overlay {
    position: absolute;
    inset: 0;
    background-color: rgba(255, 255, 255, 0.75);
    z-index: 10;
}
</style>
