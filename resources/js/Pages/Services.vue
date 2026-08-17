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
                <h1 class="mb-2 mb-lg-0">Services</h1>
                <nav class="breadcrumbs">
                    <ol>
                        <li><Link href="/">Home</Link></li>
                        <li class="current">Services</li>
                    </ol>
                </nav>
            </div>
        </div><!-- End Page Title -->

        <!-- Services Section -->
        <section id="services" class="services section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Services</h2>
            <p>Everything you need to design, build, and grow your digital presence</p>
        </div><!-- End Section Title -->

        <div class="container">

            <div class="row gy-4">

            <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="100">
                <div class="service-item position-relative">
                <div class="icon"><i class="bi bi-activity icon"></i></div>
                <h4><a href="" class="stretched-link">Web Development</a></h4>
                <p>Custom, responsive websites and web apps built for performance and ease of maintenance</p>
                </div>
            </div><!-- End Service Item -->

            <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="200">
                <div class="service-item position-relative">
                <div class="icon"><i class="bi bi-bounding-box-circles icon"></i></div>
                <h4><a href="" class="stretched-link">UI/UX Design</a></h4>
                <p>User-centered interfaces that make your product intuitive and enjoyable to use</p>
                </div>
            </div><!-- End Service Item -->

            <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="300">
                <div class="service-item position-relative">
                <div class="icon"><i class="bi bi-calendar4-week icon"></i></div>
                <h4><a href="" class="stretched-link">Digital Strategy</a></h4>
                <p>Data-driven planning that aligns your product roadmap with real business goals</p>
                </div>
            </div><!-- End Service Item -->

            <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="400">
                <div class="service-item position-relative">
                <div class="icon"><i class="bi bi-broadcast icon"></i></div>
                <h4><a href="" class="stretched-link">Ongoing Support</a></h4>
                <p>Reliable maintenance and support so your product keeps running smoothly after launch</p>
                </div>
            </div><!-- End Service Item -->

            </div>

        </div>

        </section><!-- /Services Section -->

        <!-- Work Process Section -->
        <section id="work-process" class="work-process section light-background">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Work Process</h2>
            <p>A clear, three-step process from first conversation to launch</p>
        </div><!-- End Section Title -->

        <div class="container" data-aos="fade-up" data-aos-delay="100">

            <div class="row gy-5">

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="steps-item">
                <div class="steps-image">
                    <img :src="asset('img/steps/steps-1.webp')" alt="Step 1" class="img-fluid" loading="lazy">
                </div>
                <div class="steps-content">
                    <div class="steps-number">01</div>
                    <h3>Research &amp; Analysis</h3>
                    <p>We start by understanding your business, your users, and your competitors, so every decision that follows is grounded in real data rather than guesswork.</p>
                    <div class="steps-features">
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>Market Research</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>Data Analysis</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>User Feedback</span>
                    </div>
                    </div>
                </div>
                </div><!-- End Steps Item -->
            </div>

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="steps-item">
                <div class="steps-image">
                    <img :src="asset('img/steps/steps-2.webp')" alt="Step 2" class="img-fluid" loading="lazy">
                </div>
                <div class="steps-content">
                    <div class="steps-number">02</div>
                    <h3>Design &amp; Planning</h3>
                    <p>From wireframes to polished UI, we map out the full product experience and agree on a clear plan before any code is written.</p>
                    <div class="steps-features">
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>Wireframing</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>UI/UX Design</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>Prototyping</span>
                    </div>
                    </div>
                </div>
                </div><!-- End Steps Item -->
            </div>

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="400">
                <div class="steps-item">
                <div class="steps-image">
                    <img :src="asset('img/steps/steps-3.webp')" alt="Step 3" class="img-fluid" loading="lazy">
                </div>
                <div class="steps-content">
                    <div class="steps-number">03</div>
                    <h3>Development &amp; Launch</h3>
                    <p>Our team builds, tests, and ships your product, then stays on hand to support the launch and everything that comes after it.</p>
                    <div class="steps-features">
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>Development</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>Testing</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>Deployment</span>
                    </div>
                    </div>
                </div>
                </div><!-- End Steps Item -->
            </div>

            </div>

        </div>

        </section><!-- /Work Process Section -->

    </MainLayout>
</template>
