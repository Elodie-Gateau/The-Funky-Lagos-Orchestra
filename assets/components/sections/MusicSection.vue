<script setup>

import SectionTitle from "../ui/SectionTitle.vue";
import TrackCard from "../ui/TrackCard.vue";
import { useI18n } from "vue-i18n";
import { computed } from "vue";

const { t } = useI18n();

const props = defineProps({
    tracks: Array, default: () => [],
})

const homeTracks = computed(() => props.tracks.filter(track => track.visibility))

</script>

<template>
<section v-if="props.tracks.length > 0" id="music" class="section--dark">
    <SectionTitle :title="t('music.title')" :subtitle="t('music.subtitle')" />
    <ul id="tracks">
        <TrackCard v-for="track in homeTracks" :key="track.id" :track="track" />
    </ul>
    <a v-if = "props.tracks.length > homeTracks.length" class="button" href="/music">{{ t('music.link') }}</a>
</section>
</template>

<style scoped lang="scss">
@use "/assets/styles/utils/variables" as *;
@use "/assets/styles/utils/breakpoints" as *;

#tracks {
    display: flex;
    flex-direction: column;
    gap: $size-10;
    width: 100%;
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


@media (min-width: $md) {
    #tracks {
        max-width: 70%;
    }
}

@media (min-width: $lg) {
    #tracks {
        max-width: 40%;
    }
}
</style>
