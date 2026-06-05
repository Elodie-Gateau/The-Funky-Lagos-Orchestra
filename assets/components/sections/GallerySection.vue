<script setup>
import SectionTitle from "../ui/SectionTitle.vue";
import { useI18n } from "vue-i18n";
import {ref, onMounted} from "vue";
import GallerySwiper from "../ui/Swiper.vue";

const { t, locale } = useI18n();
const props = defineProps({
    photos: { type: Array, default: () => [] },
})

</script>

<template>
    <section v-if="props.photos.length > 0" id="gallery" class="section--medium">
        <SectionTitle :title="t('gallery.title')" :subtitle="t('gallery.subtitle')" />
        <div class="gallery-container">
            <GallerySwiper :photos="props.photos" />
        </div>
    </section>
</template>

<style scoped>
.gallery-container {
    width: 100%;
}
</style>
