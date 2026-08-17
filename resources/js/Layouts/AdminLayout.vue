<script setup>
import AdminHeader from '../Components/Admin/Header.vue';
import AdminSidebar from '../Components/Admin/Sidebar.vue';
import AdminFooter from '../Components/Admin/Footer.vue';
</script>

<template>
    <!-- Page-specific styles (via <PageStyle>) get appended to <head> -->
    <slot name="css" />

    <div class="app-wrapper">
        <AdminHeader />
        <AdminSidebar />

        <main class="app-main">
            <slot />
        </main>

        <AdminFooter />
    </div>

    <!-- Page-specific scripts get appended to <body> -->
    <slot name="js" />
</template>
