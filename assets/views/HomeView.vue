<script setup>
import { useI18n } from 'vue-i18n'
import { ref, onMounted } from 'vue'
import HeroSection from "../components/sections/HeroSection.vue";
import AboutSection from "../components/sections/AboutSection.vue";
import MusicSection from "../components/sections/MusicSection.vue";
import GallerySection from "../components/sections/GallerySection.vue";
import EventsSection from "../components/sections/EventsSection.vue";
import ContactSection from "../components/sections/ContactSection.vue";
import AppHeader from "../components/layout/AppHeader.vue";
import AppFooter from "../components/layout/AppFooter.vue";

const settings = ref(null);
const tracks = ref([]);
const events = ref([]);
const photos = ref([]);

onMounted(async () => {
    const [resSettings, resTracks, resEvents, resPhotos] = await Promise.all([
        fetch('/api/settings', { credentials: 'include' }),
        fetch('/api/tracks', { credentials: 'include' }),
        fetch('/api/events/home', { credentials: 'include' }),
        fetch('/api/photos', { credentials: 'include' })
    ])

    const [dataSettings, dataTracks, dataEvents, dataPhotos] = await Promise.all([
        resSettings.json(),
        resTracks.json(),
        resEvents.json(),
        resPhotos.json()
    ])

    settings.value = dataSettings
    tracks.value = dataTracks.tracks ?? []
    events.value = dataEvents.events ?? []
    photos.value = dataPhotos.photos ?? []
})

</script>

<template>
    <HeroSection />
    <AppHeader :tracks="tracks" :events="events" :photos="photos"/>
    <main>
        <AboutSection :settings="settings" :tracks="tracks"/>
        <MusicSection :tracks="tracks"/>
        <EventsSection :events="events"/>
        <GallerySection :photos="photos"/>
        <ContactSection :settings="settings"/>
    </main>
    <AppFooter/>
</template>

<style scoped>

</style>
