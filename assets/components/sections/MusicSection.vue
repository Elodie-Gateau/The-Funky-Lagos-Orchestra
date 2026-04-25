<script setup>

import SectionTitle from "../ui/SectionTitle.vue";
import TrackCard from "../ui/TrackCard.vue";
import { useI18n } from "vue-i18n";
import { ref, onMounted } from "vue";

const { t } = useI18n();
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
<section v-if="tracks.length > 0" id="music">
    <SectionTitle :title="t('music.title')" :subtitle="t('music.subtitle')" />
    <ul id="tracks">
        <TrackCard v-for="track in tracks" :key="track.id" :track="track" />
    </ul>
    <a class="button" href="/music">{{ t('music.link') }}</a>
</section>
</template>

<style scoped lang="scss">
@use "/assets/styles/utils/variables" as *;
@use "/assets/styles/utils/breakpoints" as *;

#tracks {
    display: flex;
    flex-direction: column;
    gap: $size-10;
}

.button {
    align-self: center;
    border: 2px solid color-mix(in srgb, var(--gradient-end) 35%, transparent);
    margin-top: $size-28;
}

.button:hover {
    background-color: color-mix(in srgb, var(--secondary) 12%, transparent);
    border: 2px solid var(--gradient-end);
}
</style>
