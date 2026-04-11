<script setup>

import SectionTitle from "../ui/SectionTitle.vue";
import { useI18n } from "vue-i18n";
import {ref, onMounted} from "vue";
import { MapPin } from '@lucide/vue';

const { t, locale } = useI18n();
const events = ref([]);


onMounted(async() => {
    const res = await fetch("/api/events/home", {credentials: 'include'})
    const data = await res.json();
    data.events.forEach((event) => {
        events.value.push(event);
    })
})

</script>

<template>
    <section v-if="events.length > 0" id="events">
        <SectionTitle :title="t('event.title')" :subtitle="t('event.subtitle')" />
        <ul>
            <li v-for="event in events" :key="event.id">
                {{ event.date }}
                {{ event.name }}
                <MapPin /> {{ event.location }}
                {{ event.host ?? '' }}
            </li>
        </ul>
    </section>
</template>

<style scoped>

</style>
