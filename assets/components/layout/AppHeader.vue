<script setup>
import HeroSection from "../sections/HeroSection.vue";
import StickyMenu from "./StickyMenu.vue";

const props = defineProps({
    tracks: Array,
    events: Array,
    photos: Array,
})
</script>

<template>

    <StickyMenu :tracks = "tracks" :events = "events" :photos = "photos"/>
</template>

<style scoped>

</style>
