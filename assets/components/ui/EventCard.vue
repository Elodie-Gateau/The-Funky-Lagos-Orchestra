<script setup>
import {useI18n} from "vue-i18n";
import {MapPin} from "@lucide/vue";
import {dateFormatDay, dateFormatMonth} from "../../composables/dateFormat.js";

const { t } = useI18n();

defineProps({
    event: {
        type: Object,
        required: true
    }
})


</script>

<template>
    <li>
        <div>
            <span>{{ dateFormatDay(event.date) }}</span>
            <span>{{ dateFormatMonth(event.date) }}</span>
        </div>
        <div>
            <span>{{ event.name }}</span>
            <div>
                <MapPin /> <span>{{ event.location }}</span>
            </div>
            <span>{{ event.host ?? '' }}</span>
        </div>
    </li>
</template>

<style scoped>

</style>
