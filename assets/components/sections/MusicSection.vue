<script setup>

import SectionTitle from "../ui/SectionTitle.vue";
import { useI18n } from "vue-i18n";
import {ref, onMounted} from "vue";
import { Play, Pause } from '@lucide/vue';

const { t, locale } = useI18n();
const currentAudio = ref(null);
const playingTrackId = ref(null);
const tracks = ref([]);


onMounted(async() => {
    const res = await fetch("api/music", {credentials: 'include'})
    const data = await res.json();
    tracks.value = data.tracks;
})

const togglePlay = (track) => {
    console.log(track.audioFile);
    if(playingTrackId.value === track.id) {
        currentAudio.value.pause();
        playingTrackId.value = null;
        return;
    }
    if(currentAudio.value) {
        currentAudio.value.pause();
    }
    currentAudio.value = new Audio(track.audioFile);
    currentAudio.value.play();
    playingTrackId.value = track.id;

    currentAudio.value.addEventListener('ended', () => {
        playingTrackId.value = null;
    });
}
</script>

<template>
<section id="music">
    <SectionTitle :title="t('nav.music')" />
    <ul id="tracks">
        <li v-for="track in tracks" :key="track.id">
            <button @click="togglePlay(track)">
                <Pause v-if="playingTrackId === track.id" />
                <Play v-else />
            </button>
            {{ track.title }}
            {{ track.artist }}
            {{ track.duration }}
        </li>
    </ul>

</section>
</template>

<style scoped>

</style>
