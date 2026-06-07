<script setup>
import { useI18n } from 'vue-i18n';
import { useAudioPlayer } from '../../composables/useAudioPlayer.js';
import { computed } from "vue";

const { t } = useI18n();
const { playingTrackId, togglePlay, currentTime } = useAudioPlayer();

const props = defineProps({
    track: { type: Object, required: true }
})

const trackTitle = computed(() => {
    const name = props.track.title
    if (!name) return ''
    return name.charAt(0).toUpperCase() + name.slice(1).toLowerCase()
})
</script>

<template>
    <li :class="{ active: playingTrackId === track.id }">
        <button :aria-label="`${t('music.play')} ${track.title}`" @click="togglePlay(track)">
            <svg v-if="playingTrackId === track.id" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5.14v14l11-7-11-7z"/></svg>
        </button>
        <img :src="track.album.cover" :alt="track.album.name" width="75" height="75">
        <div class="track-info">
            <span>{{ trackTitle }}</span>
            <span>{{ track.album.name }}</span>
            <span>{{ t('main.title') }}</span>
        </div>
        <span class="track-duration">
            <template v-if="playingTrackId === track.id">
                0:{{ String(30 - currentTime).padStart(2, '0') }}
            </template>
            <template v-else>
                {{ track.duration }}
            </template>
        </span>
    </li>
</template>

<style scoped lang="scss">
@use "/assets/styles/utils/variables" as *;
@use "/assets/styles/utils/breakpoints" as *;

li {
    border: 1px solid color-mix(in srgb, var(--text-button) 7%, transparent);
    cursor: pointer;
    border-radius: $size-6;
    display: grid;
    grid-template-columns: $size-48 $size-48 1fr auto;
    align-items: center;
    gap: $size-14;
    padding: $size-14 $size-20;
    background-color: color-mix(in srgb, var(--background-accent) 12%, transparent);
    color: color-mix(in srgb, var(--text-button) 50%, transparent);

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

    img {
        border-radius: $size-6;
        object-fit: cover;
        width: $size-72;
        height: $size-72;
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
            &:nth-child(2) {
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

@media (min-width: $md) {
    li {
        grid-template-columns: $size-72 $size-72 1fr $size-72;
    }
}
</style>
