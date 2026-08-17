<script setup>
import { onMounted, onBeforeUnmount, ref } from 'vue';
import Swiper from 'swiper';
import { Autoplay, Pagination } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/pagination';

const asset = (path) => `/${path}`;

const swiperElement = ref(null);

let swiper = null;

onMounted(() => {
    swiper = new Swiper(swiperElement.value, {
        modules: [Autoplay, Pagination],

        loop: true,

        speed: 600,

        autoplay: {
            delay: 5000,
        },

        slidesPerView: 2,

        spaceBetween: 40,

        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },

        breakpoints: {
            320: {
                slidesPerView: 2,
                spaceBetween: 40,
            },

            480: {
                slidesPerView: 3,
                spaceBetween: 60,
            },

            640: {
                slidesPerView: 4,
                spaceBetween: 80,
            },

            992: {
                slidesPerView: 5,
                spaceBetween: 120,
            },

            1200: {
                slidesPerView: 6,
                spaceBetween: 120,
            },
        },
    });
});

onBeforeUnmount(() => {
    if (swiper) {
        swiper.destroy();
    }
});
</script>

<template>

    <div class="container" data-aos="zoom-in">

        <div
            ref="swiperElement"
            class="swiper"
        >

            <div class="swiper-wrapper align-items-center">

                <div class="swiper-slide">
                    <img
                        :src="asset('img/clients/clients-1.webp')"
                        class="img-fluid"
                        alt=""
                    >
                </div>

                <div class="swiper-slide">
                    <img
                        :src="asset('img/clients/clients-2.webp')"
                        class="img-fluid"
                        alt=""
                    >
                </div>

                <div class="swiper-slide">
                    <img
                        :src="asset('img/clients/clients-3.webp')"
                        class="img-fluid"
                        alt=""
                    >
                </div>

                <div class="swiper-slide">
                    <img
                        :src="asset('img/clients/clients-4.webp')"
                        class="img-fluid"
                        alt=""
                    >
                </div>

                <div class="swiper-slide">
                    <img
                        :src="asset('img/clients/clients-5.webp')"
                        class="img-fluid"
                        alt=""
                    >
                </div>

                <div class="swiper-slide">
                    <img
                        :src="asset('img/clients/clients-6.webp')"
                        class="img-fluid"
                        alt=""
                    >
                </div>

                <div class="swiper-slide">
                    <img
                        :src="asset('img/clients/clients-7.webp')"
                        class="img-fluid"
                        alt=""
                    >
                </div>

                <div class="swiper-slide">
                    <img
                        :src="asset('img/clients/clients-8.webp')"
                        class="img-fluid"
                        alt=""
                    >
                </div>

            </div>

            <div class="swiper-pagination"></div>

        </div>

    </div>

</template>