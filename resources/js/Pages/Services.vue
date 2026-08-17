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
            <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p>
        </div><!-- End Section Title -->

        <div class="container">

            <div class="row gy-4">

            <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="100">
                <div class="service-item position-relative">
                <div class="icon"><i class="bi bi-activity icon"></i></div>
                <h4><a href="" class="stretched-link">Lorem Ipsum</a></h4>
                <p>Voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi</p>
                </div>
            </div><!-- End Service Item -->

            <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="200">
                <div class="service-item position-relative">
                <div class="icon"><i class="bi bi-bounding-box-circles icon"></i></div>
                <h4><a href="" class="stretched-link">Sed ut perspici</a></h4>
                <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</p>
                </div>
            </div><!-- End Service Item -->

            <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="300">
                <div class="service-item position-relative">
                <div class="icon"><i class="bi bi-calendar4-week icon"></i></div>
                <h4><a href="" class="stretched-link">Magni Dolores</a></h4>
                <p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia</p>
                </div>
            </div><!-- End Service Item -->

            <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="400">
                <div class="service-item position-relative">
                <div class="icon"><i class="bi bi-broadcast icon"></i></div>
                <h4><a href="" class="stretched-link">Nemo Enim</a></h4>
                <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis</p>
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
            <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p>
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
                    <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione.</p>
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
                    <p>Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur.</p>
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
                    <p>Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil.</p>
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
