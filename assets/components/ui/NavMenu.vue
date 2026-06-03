<script setup>
import {useI18n} from "vue-i18n";
import { useRouter } from 'vue-router'
import {ref, onMounted} from "vue";
import { useScrollTo } from '../../composables/scrollTo.js'
const { scrollTo } = useScrollTo()

const { t, locale } = useI18n()
const router = useRouter()
const tracks = ref([]);
const events = ref([]);
const photos = ref([]);

onMounted(async() => {
    const [resTracks, resEvents, resPhotos] = await Promise.all([
        fetch("/api/tracks/home", {credentials: 'include'}),
        fetch("/api/events/home", {credentials: 'include'}),
        fetch("/api/photos", {credentials: 'include'})
        ]);
    const dataTracks = await res.json();
    const dataEvents = await res.json();
    const dataPhotos = await res.json();
    dataTracks.tracks.forEach((track) => {
        tracks.value.push(track);
    })
    dataEvents.events.forEach((event) => {
        tracks.value.push(event);
    })
    dataPhotos.photos.forEach((photo) => {
        photos.value.push(photo);
    })
})

</script>

<template>
    <nav>
        <ul>
            <li><a href="/">{{ t('nav.home') }}</a></li>
            <li><a href="#" @click.prevent="scrollTo('about')">{{ t('nav.about') }}</a></li>
            <li v-if="tracks.length > 0"><a href="#" @click.prevent="scrollTo('music')">{{ t('nav.music') }}</a></li>
            <li v-if="events.length > 0"><a href="#" @click.prevent="scrollTo('events')">{{ t('nav.events') }}</a></li>
            <li v-if="photos.length > 0"><a href="#" @click.prevent="scrollTo('gallery')">{{ t('nav.gallery') }}</a></li>
            <li><a href="#" @click.prevent="scrollTo('contact')">{{ t('nav.contact') }}</a></li>
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
