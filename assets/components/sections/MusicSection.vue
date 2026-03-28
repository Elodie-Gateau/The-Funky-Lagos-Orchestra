<script setup>

import SectionTitle from "../ui/SectionTitle.vue";
import { useI18n } from "vue-i18n";
import {ref, onMounted} from "vue";
import { Play } from '@lucide/vue';

const { t, locale } = useI18n();

const tracks = ref([]);
onMounted(async() => {
    const res = await fetch("api/music", {credentials: 'include'})
    const data = await res.json();
    tracks.value = data.tracks;
})
</script>

<template>
<section id="music">
    <SectionTitle :title="t('nav.music')" />
    <ul id="tracks">
        <li v-for="track in tracks.value" :key="track.id">
            <div>
                <Play />
                {{ track.title }}
                {{ track.artist }}
                {{ track.duration }}
            </div>
        </li>
    </ul>

</section>
</template>

<style scoped>

</style>
