<script setup>

import SectionTitle from "../ui/SectionTitle.vue";
import { useI18n } from "vue-i18n";
import {ref, onMounted} from "vue";
import { Icon } from '@iconify/vue';
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
<section v-if="tracks.length > 0" id="music">
    <SectionTitle :title="t('music.title')" :subtitle="t('music.subtitle')" />
    <ul id="tracks">
        <li v-for="track in tracks" :key="track.id" :class="{ active: playingTrackId === track.id }">
            <button @click="togglePlay(track)">
                <Icon icon="solar:pause-bold" v-if="playingTrackId === track.id" />
                <Icon icon="mingcute:play-fill" v-else />
            </button>
            <div class="track-info">
                <span>{{ track.title }}</span>
                <span>{{ track.album }}</span>
                <span>{{ t('main.title') }}</span>
            </div>
            <span class="track-duration">{{ track.duration }}</span>
        </li>
    </ul>
    <a class="button" href="/music">{{ t('music.link') }}</a>
</section>
</template>

<style scoped lang="scss">
@use "/assets/styles/utils/variables" as *;
@use "/assets/styles/utils/breakpoints" as *;

#tracks {
    display:flex;
    flex-direction: column;
    gap: $size-10;

    li {
        border: 1px solid color-mix(in srgb, var(--text-button) 7%, transparent);
        cursor: pointer;
        border-radius: $size-6;
        display: grid;
        grid-template-columns: $size-48 1fr auto;
        align-items: center;
        gap: $size-14;
        padding: $size-14 $size-20;
        background-color: color-mix(in srgb, var(--background-accent) 12%, transparent);
        color: color-mix(in srgb, var(--text-button) 38%, transparent);

        button {
            background: linear-gradient(135deg, var(--background-accent), var(--background));
            width: $size-48;
            height: $size-48;
            border-radius: $size-6;
        }

        button > svg {
            color: var(--title);
            width: $size-24;
            height: $size-24;
        }

        .track-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: $size-4;

            & > span {
                &:first-child {
                    color: var(--title);
                    line-height: 1.1;
                    font-weight: 700;
                }
                &:last-child,
                &:nth-child(2){
                    font-size: $size-14;
                    line-height: 1;
                }
            }
        }

        .track-duration {
            justify-self: center;
            font-size: $size-14;
            font-weight: 700;
        }

    }
        li:hover {
            background-color: color-mix(in srgb, var(--background-accent-hover) 12%, transparent);
        }
        li.active {
            background-color: color-mix(in srgb, var(--background-accent-hover) 12%, transparent);
            button > svg {
                color: var(--gradient-end);
            }
        }
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
