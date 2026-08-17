<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const currentPath = computed(() => usePage().url.split('?')[0]);
const isActive = (href) => currentPath.value === href;
</script>
<template>
    <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <Link href="/" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.webp" alt=""> -->
        <h1 class="sitename">Lara Vue</h1>
      </Link>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><Link href="/" :class="{ active: isActive('/') }">Home</Link></li>
          <li><Link href="/about" class="nav-link" :class="{ active: isActive('/about') }">About</Link></li>
          <li><Link href="/services" class="nav-link" :class="{ active: isActive('/services') }">Services</Link></li>
          <li><Link href="/team" class="nav-link" :class="{ active: isActive('/team') }">Team</Link></li>
          <li><Link href="/contact" class="nav-link" :class="{ active: isActive('/contact') }">Contact</Link></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
    </div>
  </header>
</template>