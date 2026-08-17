<script setup>
import { onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import MainLayout from '../Layouts/MainLayout.vue';
const asset = (path) => `/${path}`;

let isotopeInstance = null;
let filterHandlers = [];

function initIsotope() {
    const isotopeItem = document.querySelector('.isotope-layout');
    if (!isotopeItem || typeof Isotope === 'undefined' || typeof imagesLoaded === 'undefined') return;

    const layout = isotopeItem.getAttribute('data-layout') ?? 'masonry';
    const filter = isotopeItem.getAttribute('data-default-filter') ?? '*';
    const sort = isotopeItem.getAttribute('data-sort') ?? 'original-order';
    const container = isotopeItem.querySelector('.isotope-container');

    imagesLoaded(container, function () {
        isotopeInstance = new Isotope(container, {
            itemSelector: '.isotope-item',
            layoutMode: layout,
            filter,
            sortBy: sort,
        });
    });

    isotopeItem.querySelectorAll('.isotope-filters li').forEach((button) => {
        const handler = () => {
            isotopeItem.querySelector('.isotope-filters .filter-active')?.classList.remove('filter-active');
            button.classList.add('filter-active');
            isotopeInstance?.arrange({ filter: button.getAttribute('data-filter') });
        };
        button.addEventListener('click', handler);
        filterHandlers.push({ button, handler });
    });
}

onMounted(() => {
    initIsotope();
});

onUnmounted(() => {
    filterHandlers.forEach(({ button, handler }) => button.removeEventListener('click', handler));
    filterHandlers = [];
    isotopeInstance?.destroy();
    isotopeInstance = null;
});
</script>

<template>
    <MainLayout>

        <!-- Page Title -->
        <div class="page-title">
            <div class="container d-lg-flex justify-content-between align-items-center">
                <h1 class="mb-2 mb-lg-0">Portfolio</h1>
                <nav class="breadcrumbs">
                    <ol>
                        <li><Link href="/">Home</Link></li>
                        <li class="current">Portfolio</li>
                    </ol>
                </nav>
            </div>
        </div><!-- End Page Title -->

        <!-- Portfolio Section -->
        <section id="portfolio" class="portfolio section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Portfolio</h2>
            <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p>
        </div><!-- End Section Title -->

        <div class="container">

            <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">

            <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
                <li data-filter="*" class="filter-active">All</li>
                <li data-filter=".filter-app">App</li>
                <li data-filter=".filter-product">Card</li>
                <li data-filter=".filter-branding">Web</li>
            </ul><!-- End Portfolio Filters -->

            <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
                <img :src="asset('img/portfolio/portfolio-portrait-1.webp')" class="img-fluid" alt="">
                <div class="portfolio-info">
                    <h4>App 1</h4>
                    <p>Lorem ipsum, dolor sit</p>
                    <a :href="asset('img/portfolio/portfolio-portrait-1.webp')" title="App 1" data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                    <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
                </div><!-- End Portfolio Item -->

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
                <img :src="asset('img/portfolio/portfolio-1.webp')" class="img-fluid" alt="">
                <div class="portfolio-info">
                    <h4>Product 1</h4>
                    <p>Lorem ipsum, dolor sit</p>
                    <a :href="asset('img/portfolio/portfolio-1.webp')" title="Product 1" data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                    <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
                </div><!-- End Portfolio Item -->

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
                <img :src="asset('img/portfolio/portfolio-3.webp')" class="img-fluid" alt="">
                <div class="portfolio-info">
                    <h4>Branding 1</h4>
                    <p>Lorem ipsum, dolor sit</p>
                    <a :href="asset('img/portfolio/portfolio-3.webp')" title="Branding 1" data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                    <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
                </div><!-- End Portfolio Item -->

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
                <img :src="asset('img/portfolio/portfolio-4.webp')" class="img-fluid" alt="">
                <div class="portfolio-info">
                    <h4>App 2</h4>
                    <p>Lorem ipsum, dolor sit</p>
                    <a :href="asset('img/portfolio/portfolio-4.webp')" title="App 2" data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                    <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
                </div><!-- End Portfolio Item -->

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
                <img :src="asset('img/portfolio/portfolio-portrait-2.webp')" class="img-fluid" alt="">
                <div class="portfolio-info">
                    <h4>Product 2</h4>
                    <p>Lorem ipsum, dolor sit</p>
                    <a :href="asset('img/portfolio/portfolio-portrait-2.webp')" title="Product 2" data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                    <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
                </div><!-- End Portfolio Item -->

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
                <img :src="asset('img/portfolio/portfolio-portrait-3.webp')" class="img-fluid" alt="">
                <div class="portfolio-info">
                    <h4>Branding 2</h4>
                    <p>Lorem ipsum, dolor sit</p>
                    <a :href="asset('img/portfolio/portfolio-portrait-3.webp')" title="Branding 2" data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                    <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
                </div><!-- End Portfolio Item -->

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
                <img :src="asset('img/portfolio/portfolio-7.webp')" class="img-fluid" alt="">
                <div class="portfolio-info">
                    <h4>App 3</h4>
                    <p>Lorem ipsum, dolor sit</p>
                    <a :href="asset('img/portfolio/portfolio-7.webp')" title="App 3" data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                    <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
                </div><!-- End Portfolio Item -->

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
                <img :src="asset('img/portfolio/portfolio-8.webp')" class="img-fluid" alt="">
                <div class="portfolio-info">
                    <h4>Product 3</h4>
                    <p>Lorem ipsum, dolor sit</p>
                    <a :href="asset('img/portfolio/portfolio-8.webp')" title="Product 3" data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                    <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
                </div><!-- End Portfolio Item -->

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
                <img :src="asset('img/portfolio/portfolio-9.webp')" class="img-fluid" alt="">
                <div class="portfolio-info">
                    <h4>Branding 3</h4>
                    <p>Lorem ipsum, dolor sit</p>
                    <a :href="asset('img/portfolio/portfolio-9.webp')" title="Branding 2" data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                    <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                </div>
                </div><!-- End Portfolio Item -->

            </div><!-- End Portfolio Container -->

            </div>

        </div>

        </section><!-- /Portfolio Section -->

    </MainLayout>
</template>
