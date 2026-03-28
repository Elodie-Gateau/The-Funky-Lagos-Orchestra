<script setup>

import SectionTitle from "../ui/SectionTitle.vue";
import { useI18n } from "vue-i18n";
import {ref, onMounted, computed} from "vue";
const { t, locale } = useI18n();

const groupImage = ref(null);
const descriptionsData = ref(null);

onMounted(async() => {
    const res = await fetch('/api/settings', {credentials: 'include'})
    const data = await res.json();
    groupImage.value = data.image;
    descriptionsData.value = data.descriptions;
})
const description = computed(() => {
    if (locale.value === "en") {
        return descriptionsData.value?.description_en || descriptionsData.value?.description_fr;
    }
    return descriptionsData.value?.description_fr;
})
</script>

<template>
<section id="about">
    <SectionTitle :title="t('nav.about')" />
    <h3>{{ t('about.subtitle')}}</h3>
    <img v-if="groupImage" :src="groupImage" :alt="t('main.title')" />
    <h4>{{ t('about.subtitle2.part1')}} <span>{{t('about.subtitle2.part2')}}</span> {{t('about.subtitle2.part3')}}</h4>
    <p v-if="description">{{ description }}</p>
    <div class="music-tags">
        <span>{{ t('about.tag1')}}</span>
        <span>{{ t('about.tag2')}}</span>
        <span>{{ t('about.tag3')}}</span>
        <span>{{ t('about.tag4')}}</span>
        <span>{{ t('about.tag5')}}</span>
    </div>
    <a href="#music">{{ t('about.link')}}</a>
</section>
</template>

<style scoped>
img {
    max-width: 100%;
}
</style>
