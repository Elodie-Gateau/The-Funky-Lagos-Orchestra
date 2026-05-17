<script setup>
import { useI18n } from 'vue-i18n'
import HeroSection from "../components/sections/HeroSection.vue";
import AboutSection from "../components/sections/AboutSection.vue";
import MusicSection from "../components/sections/MusicSection.vue";
import GallerySection from "../components/sections/GallerySection.vue";
import EventsSection from "../components/sections/EventsSection.vue";
import ContactSection from "../components/sections/ContactSection.vue";
import AppHeader from "../components/layout/AppHeader.vue";
import AppFooter from "../components/layout/AppFooter.vue";
const { t, locale } = useI18n()
</script>

<template>
    <HeroSection />
    <AppHeader/>
    <main>
        <AboutSection />
        <MusicSection />
        <EventsSection />
        <GallerySection />
        <ContactSection />
    </main>
    <AppFooter/>
</template>

<style scoped>

</style>
