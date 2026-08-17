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
                            We're a team of designers and developers who build
                            fast, modern websites that help your business grow
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
                            We're a team of designers, developers, and
                            strategists dedicated to helping businesses grow
                            through thoughtful digital solutions.
                        </p>
                        <ul>
                            <li>
                                <i class="bi bi-check2-circle"></i>
                                <span
                                    >Years of hands-on experience across web,
                                    mobile, and cloud projects.</span
                                >
                            </li>
                            <li>
                                <i class="bi bi-check2-circle"></i>
                                <span
                                    >A collaborative process that keeps you
                                    informed at every step.</span
                                >
                            </li>
                            <li>
                                <i class="bi bi-check2-circle"></i>
                                <span
                                    >A track record of delivering on time and
                                    within budget.</span
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
                            From initial strategy through launch and beyond, we
                            partner closely with every client to understand
                            their goals and turn them into working products.
                            Our team combines technical expertise with a
                            genuine interest in what makes each business
                            unique, so the solutions we build actually fit the
                            problem at hand.
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
                    Everything you need to design, build, and grow your
                    digital presence
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
                                    >Web Development</a
                                >
                            </h4>
                            <p>
                                Custom, responsive websites and web apps built
                                for performance and ease of maintenance
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
                                    >UI/UX Design</a
                                >
                            </h4>
                            <p>
                                User-centered interfaces that make your product
                                intuitive and enjoyable to use
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
                                    >Digital Strategy</a
                                >
                            </h4>
                            <p>
                                Data-driven planning that aligns your product
                                roadmap with real business goals
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
                                <a href="" class="stretched-link"
                                    >Ongoing Support</a
                                >
                            </h4>
                            <p>
                                Reliable maintenance and support so your
                                product keeps running smoothly after launch
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
                <p>A clear, three-step process from first conversation to launch</p>
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
                                    We start by understanding your business,
                                    your users, and your competitors, so every
                                    decision that follows is grounded in real
                                    data rather than guesswork.
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
                                    From wireframes to polished UI, we map out
                                    the full product experience and agree on a
                                    clear plan before any code is written.
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
                                    Our team builds, tests, and ships your
                                    product, then stays on hand to support the
                                    launch and everything that comes after it.
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
                        <h3>Ready to start your project?</h3>
                        <p>
                            Tell us what you're trying to build and we'll get
                            back to you with next steps. No obligation, just a
                            conversation about what's possible.
                        </p>
                    </div>
                    <div class="col-xl-3 cta-btn-container text-center">
                        <a class="cta-btn align-middle" href="/contact"
                            >Get In Touch</a
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
                <p>Meet the people behind our work</p>
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
                    Answers to the questions we hear most often. Can't find
                    what you're looking for? Get in touch and we'll be happy
                    to help.
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
