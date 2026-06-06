<script setup>
import {useI18n} from "vue-i18n";
import { useRouter } from 'vue-router'
import {ref, onMounted} from "vue";
import { useScrollTo } from '../../composables/scrollTo.js'
const { scrollTo } = useScrollTo()

const { t, locale } = useI18n()
const router = useRouter()

const props = defineProps({
    tracks: { type: Array, default: () => [] },
    events: { type: Array, default: () => [] },
    photos: { type: Array, default: () => [] },
})

</script>

<template>
    <nav>
        <ul>
            <li><a href="/" :aria-label="t('nav.home')">{{ t('nav.home') }}</a></li>
            <li><a href="#" :aria-label="t('nav.about')" @click.prevent="scrollTo('about')">{{ t('nav.about') }}</a></li>
            <li v-if="props.tracks?.length > 0"><a href="#" :aria-label="t('nav.music')" @click.prevent="scrollTo('music')">{{ t('nav.music') }}</a></li>
            <li v-if="props.events?.length > 0"><a href="#" :aria-label="t('nav.events')" @click.prevent="scrollTo('events')">{{ t('nav.events') }}</a></li>
            <li v-if="props.photos?.length > 0"><a href="#" :aria-label="t('nav.gallery')" @click.prevent="scrollTo('gallery')">{{ t('nav.gallery') }}</a></li>
            <li><a href="#" :aria-label="t('nav.contact')" @click.prevent="scrollTo('contact')">{{ t('nav.contact') }}</a></li>
        </ul>
    </nav>
</template>

<style scoped lang="scss">
@use '/assets/styles/utils/variables' as *;
@use '/assets/styles/utils/breakpoints' as *;

nav {
    ul {
        display: flex;
        justify-content: space-between;

        li {
            display: flex;
            background-color: inherit;
            text-transform: uppercase;
            width: 100%;
            height: 100%;
            justify-content: flex-end;
            align-items: center;

            a{
                display: flex;
                align-items: center;
                width: 100%;
                text-align: right;
                height: 100%;
                color: var(--background);
            }
        }
    }
}

</style>
