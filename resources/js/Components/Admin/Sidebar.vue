<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const asset = (path) => `/${path}`;
const currentPath = computed(() => usePage().url.split('?')[0]);
const isActive = (href) => currentPath.value === href || currentPath.value.startsWith(`${href}/`);
</script>

<template>
    <!--begin::Sidebar-->
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand">
            <!--begin::Brand Link-->
            <Link href="/admin/dashboard" class="brand-link">
                <!--begin::Brand Text-->
                <span class="brand-text fw-light">Lara Vue Admin</span>
                <!--end::Brand Text-->
            </Link>
            <!--end::Brand Link-->
        </div>
        <!--end::Sidebar Brand-->
        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <!--begin::Sidebar Menu-->
                <ul
                    class="nav sidebar-menu flex-column"
                    data-lte-toggle="treeview"
                    role="navigation"
                    aria-label="Main navigation"
                    data-accordion="false"
                    id="navigation"
                >
                    <li class="nav-item">
                        <Link href="/admin/dashboard" class="nav-link" :class="{ active: isActive('/admin/dashboard') }">
                            <i class="nav-icon bi bi-speedometer"></i>
                            <p>Dashboard</p>
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link href="/admin/inquiry" class="nav-link" :class="{ active: isActive('/admin/inquiry') }">
                            <i class="nav-icon bi bi-envelope"></i>
                            <p>Inquiry</p>
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link href="/admin/team" class="nav-link" :class="{ active: isActive('/admin/team') }">
                            <i class="nav-icon bi bi-people"></i>
                            <p>Team</p>
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link href="/admin/faq" class="nav-link" :class="{ active: isActive('/admin/faq') }">
                            <i class="nav-icon bi bi-question-circle"></i>
                            <p>FAQ</p>
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link href="/admin/analytics" class="nav-link" :class="{ active: isActive('/admin/analytics') }">
                            <i class="nav-icon bi bi-graph-up"></i>
                            <p>Analytics</p>
                        </Link>
                    </li>
                </ul>
                <!--end::Sidebar Menu-->
            </nav>
        </div>
        <!--end::Sidebar Wrapper-->
    </aside>
    <!--end::Sidebar-->
</template>
