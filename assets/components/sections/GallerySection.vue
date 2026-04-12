<script setup>
import SectionTitle from "../ui/SectionTitle.vue";
import { useI18n } from "vue-i18n";
import {ref, onMounted} from "vue";
import GallerySwiper from "../ui/Swiper.vue";

const { t, locale } = useI18n();
const photos = ref([]);


onMounted(async() => {
    const res = await fetch("/api/photos", {credentials: 'include'})
    const data = await res.json();
    data.photos.forEach((photo) => {
        photos.value.push(photo);
    })
})
</script>

<template>
    <section v-if="photos.length > 0" id="gallery">
        <SectionTitle :title="t('gallery.title')" :subtitle="t('gallery.subtitle')" />
        <GallerySwiper :photos="photos" />
    </section>
</template>

<style scoped>

</style>
