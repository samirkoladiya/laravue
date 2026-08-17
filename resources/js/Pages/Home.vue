<script setup>
import { onMounted, onUnmounted } from "vue";
import MainLayout from "../Layouts/MainLayout.vue";
import ClientSlider from "../Components/ClientSlider.vue";
const asset = (path) => `/${path}`;

defineProps({
    faqs: {
        type: Array,
        default: () => [],
    },
    teams: {
        type: Array,
        default: () => [],
    },
});

const fallbackTeamPhoto = asset("img/person/person-m-7.webp");

let isotopeInstance = null;
let filterHandlers = [];
let faqHandlers = [];

function initIsotope() {
    const isotopeItem = document.querySelector(".isotope-layout");
    if (
        !isotopeItem ||
        typeof Isotope === "undefined" ||
        typeof imagesLoaded === "undefined"
    )
        return;

    const layout = isotopeItem.getAttribute("data-layout") ?? "masonry";
    const filter = isotopeItem.getAttribute("data-default-filter") ?? "*";
    const sort = isotopeItem.getAttribute("data-sort") ?? "original-order";
    const container = isotopeItem.querySelector(".isotope-container");

    imagesLoaded(container, function () {
        isotopeInstance = new Isotope(container, {
            itemSelector: ".isotope-item",
            layoutMode: layout,
            filter,
            sortBy: sort,
        });
    });

    isotopeItem.querySelectorAll(".isotope-filters li").forEach((button) => {
        const handler = () => {
            isotopeItem
                .querySelector(".isotope-filters .filter-active")
                ?.classList.remove("filter-active");
            button.classList.add("filter-active");
            isotopeInstance?.arrange({
                filter: button.getAttribute("data-filter"),
            });
        };
        button.addEventListener("click", handler);
        filterHandlers.push({ button, handler });
    });
}

function initFaqToggle() {
    document
        .querySelectorAll(".faq-item h3, .faq-item .faq-toggle")
        .forEach((faqItem) => {
            const handler = () => {
                faqItem.parentNode.classList.toggle("faq-active");
            };
            faqItem.addEventListener("click", handler);
            faqHandlers.push({ faqItem, handler });
        });
}

onMounted(() => {
    initIsotope();
    initFaqToggle();
});

onUnmounted(() => {
    filterHandlers.forEach(({ button, handler }) =>
        button.removeEventListener("click", handler),
    );
    filterHandlers = [];
    isotopeInstance?.destroy();
    isotopeInstance = null;

    faqHandlers.forEach(({ faqItem, handler }) =>
        faqItem.removeEventListener("click", handler),
    );
    faqHandlers = [];
});
</script>

<template>
    <MainLayout>
        <!-- Hero Section -->
        <section id="hero" class="hero section dark-background">
            <div class="container">
                <div class="row gy-4">
                    <div
                        class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center"
                        data-aos="zoom-out"
                    >
                        <h1>Better Solutions For Your Business</h1>
                        <p>
                            We are team of talented designers making websites
                            with Bootstrap
                        </p>
                        <div class="d-flex">
                            <a href="#about" class="btn-get-started"
                                >Get Started</a
                            >
                            <a
                                href="https://www.youtube.com/watch?v=Y7f98aduVJ8"
                                class="glightbox btn-watch-video d-flex align-items-center"
                                ><i class="bi bi-play-circle"></i
                                ><span>Watch Video</span></a
                            >
                        </div>
                    </div>
                    <div
                        class="col-lg-6 order-1 order-lg-2 hero-img"
                        data-aos="zoom-out"
                        data-aos-delay="200"
                    >
                        <img
                            :src="asset('img/hero-img.png')"
                            class="img-fluid animated"
                            alt=""
                        />
                    </div>
                </div>
            </div>
        </section>
        <!-- /Hero Section -->

        <!-- Clients Section -->
        <section id="clients" class="clients section light-background">
            <ClientSlider />
        </section>
        <!-- /Clients Section -->

        <!-- About Section -->
        <section id="about" class="about section">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>About Us</h2>
            </div>
            <!-- End Section Title -->

            <div class="container">
                <div class="row gy-4">
                    <div
                        class="col-lg-6 content"
                        data-aos="fade-up"
                        data-aos-delay="100"
                    >
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipiscing
                            elit, sed do eiusmod tempor incididunt ut labore et
                            dolore magna aliqua.
                        </p>
                        <ul>
                            <li>
                                <i class="bi bi-check2-circle"></i>
                                <span
                                    >Ullamco laboris nisi ut aliquip ex ea
                                    commodo consequat.</span
                                >
                            </li>
                            <li>
                                <i class="bi bi-check2-circle"></i>
                                <span
                                    >Duis aute irure dolor in reprehenderit in
                                    voluptate velit.</span
                                >
                            </li>
                            <li>
                                <i class="bi bi-check2-circle"></i>
                                <span
                                    >Ullamco laboris nisi ut aliquip ex ea
                                    commodo</span
                                >
                            </li>
                        </ul>
                    </div>

                    <div
                        class="col-lg-6"
                        data-aos="fade-up"
                        data-aos-delay="200"
                    >
                        <p>
                            Ullamco laboris nisi ut aliquip ex ea commodo
                            consequat. Duis aute irure dolor in reprehenderit in
                            voluptate velit esse cillum dolore eu fugiat nulla
                            pariatur. Excepteur sint occaecat cupidatat non
                            proident, sunt in culpa qui officia deserunt mollit
                            anim id est laborum.
                        </p>
                        <a href="#" class="read-more"
                            ><span>Read More</span
                            ><i class="bi bi-arrow-right"></i
                        ></a>
                    </div>
                </div>
            </div>
        </section>
        <!-- /About Section -->

        <!-- Services Section -->
        <section id="services" class="services section light-background">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Services</h2>
                <p>
                    Necessitatibus eius consequatur ex aliquid fuga eum quidem
                    sint consectetur velit
                </p>
            </div>
            <!-- End Section Title -->

            <div class="container">
                <div class="row gy-4">
                    <div
                        class="col-xl-3 col-md-6 d-flex"
                        data-aos="fade-up"
                        data-aos-delay="100"
                    >
                        <div class="service-item position-relative">
                            <div class="icon">
                                <i class="bi bi-activity icon"></i>
                            </div>
                            <h4>
                                <a href="" class="stretched-link"
                                    >Lorem Ipsum</a
                                >
                            </h4>
                            <p>
                                Voluptatum deleniti atque corrupti quos dolores
                                et quas molestias excepturi
                            </p>
                        </div>
                    </div>
                    <!-- End Service Item -->

                    <div
                        class="col-xl-3 col-md-6 d-flex"
                        data-aos="fade-up"
                        data-aos-delay="200"
                    >
                        <div class="service-item position-relative">
                            <div class="icon">
                                <i class="bi bi-bounding-box-circles icon"></i>
                            </div>
                            <h4>
                                <a href="" class="stretched-link"
                                    >Sed ut perspici</a
                                >
                            </h4>
                            <p>
                                Duis aute irure dolor in reprehenderit in
                                voluptate velit esse cillum dolore
                            </p>
                        </div>
                    </div>
                    <!-- End Service Item -->

                    <div
                        class="col-xl-3 col-md-6 d-flex"
                        data-aos="fade-up"
                        data-aos-delay="300"
                    >
                        <div class="service-item position-relative">
                            <div class="icon">
                                <i class="bi bi-calendar4-week icon"></i>
                            </div>
                            <h4>
                                <a href="" class="stretched-link"
                                    >Magni Dolores</a
                                >
                            </h4>
                            <p>
                                Excepteur sint occaecat cupidatat non proident,
                                sunt in culpa qui officia
                            </p>
                        </div>
                    </div>
                    <!-- End Service Item -->

                    <div
                        class="col-xl-3 col-md-6 d-flex"
                        data-aos="fade-up"
                        data-aos-delay="400"
                    >
                        <div class="service-item position-relative">
                            <div class="icon">
                                <i class="bi bi-broadcast icon"></i>
                            </div>
                            <h4>
                                <a href="" class="stretched-link">Nemo Enim</a>
                            </h4>
                            <p>
                                At vero eos et accusamus et iusto odio
                                dignissimos ducimus qui blanditiis
                            </p>
                        </div>
                    </div>
                    <!-- End Service Item -->
                </div>
            </div>
        </section>
        <!-- /Services Section -->

        <!-- Work Process Section -->
        <section id="work-process" class="work-process section">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Work Process</h2>
                <p>
                    Necessitatibus eius consequatur ex aliquid fuga eum quidem
                    sint consectetur velit
                </p>
            </div>
            <!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-5">
                    <div
                        class="col-lg-4"
                        data-aos="fade-up"
                        data-aos-delay="200"
                    >
                        <div class="steps-item">
                            <div class="steps-image">
                                <img
                                    :src="asset('img/steps/steps-1.webp')"
                                    alt="Step 1"
                                    class="img-fluid"
                                    loading="lazy"
                                />
                            </div>
                            <div class="steps-content">
                                <div class="steps-number">01</div>
                                <h3>Research &amp; Analysis</h3>
                                <p>
                                    Nemo enim ipsam voluptatem quia voluptas sit
                                    aspernatur aut odit aut fugit, sed quia
                                    consequuntur magni dolores eos qui ratione.
                                </p>
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
                        </div>
                        <!-- End Steps Item -->
                    </div>

                    <div
                        class="col-lg-4"
                        data-aos="fade-up"
                        data-aos-delay="300"
                    >
                        <div class="steps-item">
                            <div class="steps-image">
                                <img
                                    :src="asset('img/steps/steps-2.webp')"
                                    alt="Step 2"
                                    class="img-fluid"
                                    loading="lazy"
                                />
                            </div>
                            <div class="steps-content">
                                <div class="steps-number">02</div>
                                <h3>Design &amp; Planning</h3>
                                <p>
                                    Ut enim ad minima veniam, quis nostrum
                                    exercitationem ullam corporis suscipit
                                    laboriosam, nisi ut aliquid ex ea commodi
                                    consequatur.
                                </p>
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
                        </div>
                        <!-- End Steps Item -->
                    </div>

                    <div
                        class="col-lg-4"
                        data-aos="fade-up"
                        data-aos-delay="400"
                    >
                        <div class="steps-item">
                            <div class="steps-image">
                                <img
                                    :src="asset('img/steps/steps-3.webp')"
                                    alt="Step 3"
                                    class="img-fluid"
                                    loading="lazy"
                                />
                            </div>
                            <div class="steps-content">
                                <div class="steps-number">03</div>
                                <h3>Development &amp; Launch</h3>
                                <p>
                                    Et harum quidem rerum facilis est et
                                    expedita distinctio. Nam libero tempore, cum
                                    soluta nobis est eligendi optio cumque
                                    nihil.
                                </p>
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
                        </div>
                        <!-- End Steps Item -->
                    </div>
                </div>
            </div>
        </section>
        <!-- /Work Process Section -->

        <!-- Call To Action Section -->
        <section
            id="call-to-action"
            class="call-to-action section dark-background"
        >
            <img :src="asset('img/bg/bg-8.webp')" alt="" />

            <div class="container">
                <div class="row" data-aos="zoom-in" data-aos-delay="100">
                    <div class="col-xl-9 text-center text-xl-start">
                        <h3>Call To Action</h3>
                        <p>
                            Duis aute irure dolor in reprehenderit in voluptate
                            velit esse cillum dolore eu fugiat nulla pariatur.
                            Excepteur sint occaecat cupidatat non proident, sunt
                            in culpa qui officia deserunt mollit anim id est
                            laborum.
                        </p>
                    </div>
                    <div class="col-xl-3 cta-btn-container text-center">
                        <a class="cta-btn align-middle" href="#"
                            >Call To Action</a
                        >
                    </div>
                </div>
            </div>
        </section>
        <!-- /Call To Action Section -->

        <!-- Team Section -->
        <section id="team" class="team section">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Team</h2>
                <p>
                    Necessitatibus eius consequatur ex aliquid fuga eum quidem
                    sint consectetur velit
                </p>
            </div>
            <!-- End Section Title -->

            <div class="container">
                <div v-if="teams.length" class="row gy-4">
                    <div
                        v-for="(member, index) in teams"
                        :key="member.id"
                        class="col-lg-6"
                        data-aos="fade-up"
                        :data-aos-delay="100 * (index + 1)"
                    >
                        <div class="team-member d-flex align-items-start">
                            <div class="pic">
                                <img
                                    :src="member.photo_url || fallbackTeamPhoto"
                                    class="img-fluid"
                                    :alt="member.name"
                                />
                            </div>
                            <div class="member-info">
                                <h4>{{ member.name }}</h4>
                                <span>{{ member.designation }}</span>
                                <p v-if="member.bio">{{ member.bio }}</p>
                                <div
                                    v-if="
                                        member.facebook_url ||
                                        member.twitter_url ||
                                        member.instagram_url ||
                                        member.linkedin_url
                                    "
                                    class="social"
                                >
                                    <a
                                        v-if="member.twitter_url"
                                        :href="member.twitter_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        ><i class="bi bi-twitter-x"></i
                                    ></a>
                                    <a
                                        v-if="member.facebook_url"
                                        :href="member.facebook_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        ><i class="bi bi-facebook"></i
                                    ></a>
                                    <a
                                        v-if="member.instagram_url"
                                        :href="member.instagram_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        ><i class="bi bi-instagram"></i
                                    ></a>
                                    <a
                                        v-if="member.linkedin_url"
                                        :href="member.linkedin_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        ><i class="bi bi-linkedin"></i
                                    ></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Team Member -->
                </div>

                <p v-else class="text-center">
                    Our team page is being updated. Please check back soon.
                </p>
            </div>
        </section>
        <!-- /Team Section -->

        <!-- Faq 2 Section -->
        <section id="faq-2" class="faq-2 section light-background">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Frequently Asked Questions</h2>
                <p>
                    Magnam dolores commodi suscipit. Necessitatibus eius
                    consequatur ex aliquid fuga eum quidem. Sit sint consectetur
                    velit. Quisquam quos quisquam cupiditate. Et nemo qui
                    impedit suscipit alias ea. Quia fugiat sit in iste officiis
                    commodi quidem hic quas.
                </p>
            </div>
            <!-- End Section Title -->

            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div v-if="faqs.length" class="faq-container">
                            <div
                                v-for="(faq, index) in faqs"
                                :key="faq.id"
                                class="faq-item"
                                :class="{ 'faq-active': index === 0 }"
                                data-aos="fade-up"
                                :data-aos-delay="100 * (index + 2)"
                            >
                                <i class="faq-icon bi bi-question-circle"></i>
                                <h3>{{ faq.question }}</h3>
                                <div class="faq-content">
                                    <p>{{ faq.answer }}</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div>
                            <!-- End Faq item-->
                        </div>

                        <p v-else class="text-center">FAQs are coming soon.</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Faq 2 Section -->
    </MainLayout>
</template>
