<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/zoom';
import 'swiper/css/navigation';
import 'swiper/css/thumbs';
import 'swiper/css/free-mode';


import { Pagination, Navigation, Zoom, FreeMode, Thumbs } from 'swiper/modules';
import {ref} from "vue";

const modules = [Pagination, Navigation, Zoom, FreeMode, Thumbs]

const thumbsSwiper = ref(null);
const setThumbsSwiper = (swiper) => {
    thumbsSwiper.value = swiper;
};

defineProps({
    photos: { type: Array, default: () => [] }
})
</script>

<template>
    <swiper
        :pagination="{ dynamicBullets: true }"
        :modules="modules"
        :zoom="true"
        :navigation="true"
        :loop="true"
        :spaceBetween="10"
        :thumbs="{ swiper: thumbsSwiper }"
        class="mainSwiper">
        <swiper-slide v-for="photo in photos" :key="photo.id">
            <div class="swiper-zoom-container">
                <img :src="photo.path" :alt="'Photo ' + photo.id" />
            </div>
        </swiper-slide>
    </swiper>
    <swiper
        @swiper="setThumbsSwiper"
        :loop="true"
        :spaceBetween="10"
        :slidesPerView="4"
        :freeMode="true"
        :watchSlidesProgress="true"
        :modules="modules"
        class="thumbsSwiper">
        <swiper-slide v-for="photo in photos" :key="photo.id">
            <div class="swiper-zoom-container">
                <img :src="photo.path" :alt="'Photo ' + photo.id" />
            </div>
        </swiper-slide>
    </swiper>
</template>
<style scoped>
img {
    max-width: 100%;
}
/*
.mySwiper {
    --swiper-pagination-color: #your-color;
    --swiper-pagination-bullet-inactive-color: #your-color;
    --swiper-pagination-bullet-inactive-opacity: 0.4;
}
 */
</style>
