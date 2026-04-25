<script setup>
import { Icon } from '@iconify/vue';
import { useI18n } from 'vue-i18n';
import { useAudioPlayer } from '../../composables/useAudioPlayer.js';

const { t } = useI18n();
const { playingTrackId, togglePlay } = useAudioPlayer();

defineProps({
    track: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <li :class="{ active: playingTrackId === track.id }">
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
</template>

<style scoped lang="scss">
@use "/assets/styles/utils/variables" as *;

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
</style>