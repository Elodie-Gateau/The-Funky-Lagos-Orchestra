<script setup>

import SectionTitle from "../ui/SectionTitle.vue";
import { useI18n } from "vue-i18n";
import {ref, onMounted} from "vue";
import { Play, Pause } from '@lucide/vue';
import { useAudioPlayer } from '../../composables/useAudioPlayer.js'

const { playingTrackId, togglePlay } = useAudioPlayer()
const { t, locale } = useI18n();
const tracks = ref([]);


onMounted(async() => {
    const res = await fetch("/api/tracks/home", {credentials: 'include'})
    const data = await res.json();
    data.tracks.forEach((track) => {
            tracks.value.push(track);
    })
})

</script>

<template>
<section id="music">
    <SectionTitle :title="t('music.title')" :subtitle="t('music.subtitle')" />
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
