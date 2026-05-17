<script setup>
import {useI18n} from "vue-i18n";
import { Icon } from '@iconify/vue';
import {dateFormatDay, dateFormatMonth, dateFormatYear} from "../../composables/dateFormat.js";

const { t, locale } = useI18n();

defineProps({
    event: {
        type: Object,
        required: true
    }
})


</script>

<template>
    <li>
        <div class="event-date">
            <span>{{ dateFormatDay(event.date, locale) }}</span>
            <span>{{ dateFormatMonth(event.date, locale) }}</span>
            <span>{{ dateFormatYear(event.date) }}</span>
        </div>
        <div class="event-info">
            <span>{{ event.name }}</span>
            <span>{{ event.location }}</span>
            <div>
                <Icon icon="boxicons:location-pin-filled" /> <span>{{ event.city }}</span>
            </div>
        </div>
    </li>
</template>

<style scoped lang="scss">
@use "/assets/styles/utils/variables" as *;


</style>
