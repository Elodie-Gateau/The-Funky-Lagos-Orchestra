<script setup>
import {useI18n} from "vue-i18n";

const { t, locale } = useI18n()
</script>

<template>
    <img src="/assets/images/logo.svg" alt="{{ t('main.logo')}}">
    <nav>
        <ul>
            <li><a href="/">{{ t('nav.home')}}</a></li>
            <li><a href="/">{{ t('nav.about')}}</a></li>
            <li><a href="/">{{ t('nav.music')}}</a></li>
            <li><a href="/">{{ t('nav.gallery')}}</a></li>
            <li><a href="/">{{ t('nav.events')}}</a></li>
            <li><a href="/">{{ t('nav.contact')}}</a></li>
        </ul>
    </nav>
    <div class="lang">
        <div class="lang-switcher">
            <button @click="locale = 'fr'" :class="{ active: locale === 'fr' }">FR</button>
            <span>|</span>
            <button @click="locale = 'en'" :class="{ active: locale === 'en' }">EN</button>
        </div>
    </div>
</template>

<style scoped>

</style>
