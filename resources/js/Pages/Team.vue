<script setup>
import { Link } from '@inertiajs/vue3';
import MainLayout from '../Layouts/MainLayout.vue';

defineProps({
    teams: {
        type: Array,
        default: () => [],
    },
});

const asset = (path) => `/${path}`;
const fallbackPhoto = asset('img/person/person-m-7.webp');
</script>

<template>
    <MainLayout>

        <!-- Page Title -->
        <div class="page-title">
            <div class="container d-lg-flex justify-content-between align-items-center">
                <h1 class="mb-2 mb-lg-0">Team</h1>
                <nav class="breadcrumbs">
                    <ol>
                        <li><Link href="/">Home</Link></li>
                        <li class="current">Team</li>
                    </ol>
                </nav>
            </div>
        </div><!-- End Page Title -->

        <!-- Team Section -->
        <section id="team" class="team section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Team</h2>
            <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p>
        </div><!-- End Section Title -->

        <div class="container">

            <div v-if="teams.length" class="row gy-4">

            <div
                v-for="(member, index) in teams"
                :key="member.id"
                class="col-lg-6"
                data-aos="fade-up"
                :data-aos-delay="100 * ((index % 4) + 1)"
            >
                <div class="team-member d-flex align-items-start">
                <div class="pic"><img :src="member.photo_url || fallbackPhoto" class="img-fluid" :alt="member.name"></div>
                <div class="member-info">
                    <h4>{{ member.name }}</h4>
                    <span>{{ member.designation }}</span>
                    <p v-if="member.bio">{{ member.bio }}</p>
                    <div
                        v-if="member.facebook_url || member.twitter_url || member.instagram_url || member.linkedin_url"
                        class="social"
                    >
                    <a v-if="member.twitter_url" :href="member.twitter_url" target="_blank" rel="noopener noreferrer"><i class="bi bi-twitter-x"></i></a>
                    <a v-if="member.facebook_url" :href="member.facebook_url" target="_blank" rel="noopener noreferrer"><i class="bi bi-facebook"></i></a>
                    <a v-if="member.instagram_url" :href="member.instagram_url" target="_blank" rel="noopener noreferrer"><i class="bi bi-instagram"></i></a>
                    <a v-if="member.linkedin_url" :href="member.linkedin_url" target="_blank" rel="noopener noreferrer"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                </div>
            </div><!-- End Team Member -->

            </div>

            <p v-else class="text-center">Our team page is being updated. Please check back soon.</p>

        </div>

        </section><!-- /Team Section -->

    </MainLayout>
</template>
