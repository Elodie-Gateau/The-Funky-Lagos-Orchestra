<script setup>
import {useI18n} from "vue-i18n";
import { useRouter } from 'vue-router'
const { t, locale } = useI18n()
const router = useRouter()

function scrollTo(id) {
    router.push('/').then(() => {
        setTimeout(() => {
            document.getElementById(id)?.scrollIntoView({ behavior: 'smooth' })
        }, 100)
    })
}

</script>

<template>
    <nav>
        <ul>
            <li><a href="#">{{ t('nav.home')}}</a></li>
            <li><a href="#" @click.prevent="scrollTo('about')">{{ t('nav.about') }}</a></li>
            <li><a href="#" @click.prevent="scrollTo('music')">{{ t('nav.music') }}</a></li>
            <li><a href="#">{{ t('nav.gallery')}}</a></li>
            <li><a href="#">{{ t('nav.events')}}</a></li>
            <li><a href="#">{{ t('nav.contact')}}</a></li>
        </ul>
    </nav>
</template>

<style scoped>

</style>
