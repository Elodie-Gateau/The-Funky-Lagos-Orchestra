<script setup>
import { Icon } from '@iconify/vue'
import {ref} from "vue";

defineProps({
    facebook: String,
    instagram: String,
    youtube: String,
    soundcloud: String
})
</script>

<template>
    <div>
        <a v-if="facebook" :href="facebook"><Icon icon="streamline-logos:facebook-logo-2" /></a>
        <a v-if="instagram" :href="instagram"><Icon icon="streamline-logos:instagram-logo-2-solid" /></a>
        <a v-if="youtube" :href="youtube"><Icon icon="streamline-logos:youtube-clip-logo" /></a>
        <a v-if="soundcloud" :href="soundcloud"><Icon icon="streamline-logos:soundcloud-logo" /></a>
    </div>
</template>

<style scoped>

</style>
