<script setup>
import {useI18n} from "vue-i18n";

const { t, locale } = useI18n()
</script>

<template>
    <nav>
        <ul>
            <li><a href="#">{{ t('nav.home')}}</a></li>
            <li><a href="#about">{{ t('nav.about')}}</a></li>
            <li><a href="#">{{ t('nav.music')}}</a></li>
            <li><a href="#">{{ t('nav.gallery')}}</a></li>
            <li><a href="#">{{ t('nav.events')}}</a></li>
            <li><a href="#">{{ t('nav.contact')}}</a></li>
        </ul>
    </nav>
</template>

<style scoped>

</style>
