<script setup>

import SectionTitle from "../ui/SectionTitle.vue";
import { useI18n } from "vue-i18n";
import { ref, onMounted } from "vue";

const groupImage = ref(null);

onMounted(async() => {
    const res = await fetch('/api/settings', {credentials: 'include'})
    const data = await res.json();
    groupImage.value = data.image;
})

const { t } = useI18n();
</script>

<template>
<section id="about">
    <SectionTitle :title="t('nav.about')" />
    <h3>{{ t('about.subtitle')}}</h3>
    <img v-if="groupImage" :src="groupImage" :alt="t('main.title')" />
    <h4>{{ t('about.subtitle2.part1')}} <span>{{t('about.subtitle2.part2')}}</span> {{t('about.subtitle2.part3')}}</h4>
    <p>{{ t('about.bloc-text1')}}</p>
    <p>{{ t('about.bloc-text2')}}</p>
</section>
</template>

<style scoped>

</style>
