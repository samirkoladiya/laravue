<script setup>
import { Link } from '@inertiajs/vue3';
import MainLayout from '../Layouts/MainLayout.vue';
const asset = (path) => `/${path}`;
</script>

<template>
    <MainLayout>

        <!-- Page Title -->
        <div class="page-title">
            <div class="container d-lg-flex justify-content-between align-items-center">
                <h1 class="mb-2 mb-lg-0">About</h1>
                <nav class="breadcrumbs">
                    <ol>
                        <li><Link href="/">Home</Link></li>
                        <li class="current">About</li>
                    </ol>
                </nav>
            </div>
        </div><!-- End Page Title -->

        <!-- About Section -->
        <section id="about" class="about section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>About Us</h2>
        </div><!-- End Section Title -->

        <div class="container">

            <div class="row gy-4">

            <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
                <p>
                We're a team of designers, developers, and strategists dedicated to helping businesses grow
                through thoughtful digital solutions.
                </p>
                <ul>
                <li><i class="bi bi-check2-circle"></i> <span>Years of hands-on experience across web, mobile, and cloud projects.</span></li>
                <li><i class="bi bi-check2-circle"></i> <span>A collaborative process that keeps you informed at every step.</span></li>
                <li><i class="bi bi-check2-circle"></i> <span>A track record of delivering on time and within budget.</span></li>
                </ul>
            </div>

            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                <p>From initial strategy through launch and beyond, we partner closely with every client to understand their goals and turn them into working products. Our team combines technical expertise with a genuine interest in what makes each business unique, so the solutions we build actually fit the problem at hand.</p>
                <a href="#" class="read-more"><span>Read More</span><i class="bi bi-arrow-right"></i></a>
            </div>

            </div>

        </div>

        </section><!-- /About Section -->

    </MainLayout>
</template>
