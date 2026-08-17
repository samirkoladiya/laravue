<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const currentPath = computed(() => usePage().url.split('?')[0]);
</script>

<template>
    <ul class="nav nav-pills mb-4">
        <li class="nav-item">
            <Link href="/admin/profile" class="nav-link" :class="{ active: currentPath === '/admin/profile' }">
                <i class="bi bi-person me-1"></i>Profile
            </Link>
        </li>
        <li class="nav-item">
            <Link href="/admin/profile/password" class="nav-link" :class="{ active: currentPath === '/admin/profile/password' }">
                <i class="bi bi-shield-lock me-1"></i>Change Password
            </Link>
        </li>
    </ul>
</template>
