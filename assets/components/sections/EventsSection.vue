<script setup>

import SectionTitle from "../ui/SectionTitle.vue";
import { useI18n } from "vue-i18n";
import {ref, onMounted} from "vue";
import { MapPin } from '@lucide/vue';
import EventCard from "../ui/EventCard.vue";

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
            <EventCard v-for="event in events" :key="event.id" :event="event" />
        </ul>
    </section>
</template>

<style scoped lang="scss">
@use '/assets/styles/utils/breakpoints' as *;
@use '/assets/styles/utils/variables' as *;

#events {
    ul{
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: $size-30;

        li{
        width: 100%;
            height: $size-96;
        }
    }
}

@media (min-width: $md) {
    #events {
        ul{
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: $size-40;
        }
    }
}
</style>
