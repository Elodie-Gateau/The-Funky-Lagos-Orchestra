<script setup>

import SectionTitle from "../ui/SectionTitle.vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
</script>

<template>
<section id="about">
    <SectionTitle :title="t('nav.about')" />
    <h3>{{ t('about.subtitle')}}</h3>

</section>
</template>

<style scoped>

</style>
