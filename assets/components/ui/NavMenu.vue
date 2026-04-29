<script setup>
import {useI18n} from "vue-i18n";
import { useRouter } from 'vue-router'
import {ref, onMounted} from "vue";

const { t, locale } = useI18n()
const router = useRouter()
const tracks = ref([]);
const events = ref([]);

function scrollTo(id) {
    router.push('/').then(() => {
        setTimeout(() => {
            document.getElementById(id)?.scrollIntoView({ behavior: 'smooth' })
        }, 100)
    })
}

onMounted(async() => {
    const res = await fetch("/api/tracks/home", {credentials: 'include'})
    const data = await res.json();
    data.tracks.forEach((track) => {
        tracks.value.push(track);
    })
})

onMounted(async() => {
    const res = await fetch("/api/events/home", {credentials: 'include'})
    const data = await res.json();
    data.events.forEach((event) => {
        events.value.push(event);
    })
})

</script>

<template>
    <nav>
        <ul>
            <li><a href="/">{{ t('nav.home') }}</a></li>
            <li><a href="#" @click.prevent="scrollTo('about')">{{ t('nav.about') }}</a></li>
            <li v-if="tracks.length > 0"><a href="#" @click.prevent="scrollTo('music')">{{ t('nav.music') }}</a></li>
            <li v-if="tracks.length > 0"><a href="#" @click.prevent="scrollTo('gallery')">{{ t('nav.gallery') }}</a></li>
            <li v-if="events.length > 0"><a href="#" @click.prevent="scrollTo('events')">{{ t('nav.events') }}</a></li>
            <li><a href="#">{{ t('nav.contact')}}</a></li>
        </ul>
    </nav>
</template>

<style scoped lang="scss">
@use '/assets/styles/utils/variables' as *;
@use '/assets/styles/utils/breakpoints' as *;

nav {
    height: 100%;
    width: 100%;
    ul {
        height: 100%;
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-around;

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
                border : 2px solid red;
                color: var(--background);
            }
        }
    }
}

</style>
