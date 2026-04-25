<script setup>
import {useI18n} from "vue-i18n";
import {MapPin} from "@lucide/vue";

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
        {{ event.date }}
        {{ event.name }}
        <MapPin /> {{ event.location }}
        {{ event.host ?? '' }}
    </li>
</template>

<style scoped>

</style>
