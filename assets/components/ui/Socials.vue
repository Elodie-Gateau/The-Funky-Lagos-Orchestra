<script setup>
import { Icon } from '@iconify/vue'

defineProps({
    facebook: String,
    instagram: String,
    youtube: String,
    soundcloud: String
})
</script>

<template>
    <div class="socials">
        <div v-if="facebook" class="icon">
            <a  :href="facebook">
                <Icon icon="streamline-logos:facebook-logo-2" />
            </a>
        </div>
        <div v-if="instagram" class="icon">
            <a :href="instagram">
                <Icon icon="streamline-logos:instagram-logo-2-solid" />
            </a>
        </div>
        <div v-if="youtube" class="icon">
            <a :href="youtube">
                <Icon icon="streamline-logos:youtube-clip-logo" />
            </a>
        </div>
        <div v-if="soundcloud" class="icon">
            <a :href="soundcloud">
                <Icon icon="streamline-logos:soundcloud-logo" />
            </a>
        </div>
    </div>
</template>

<style scoped lang="scss">
@use "/assets/styles/utils/variables" as *;

.socials {
    display: flex;
    gap: $size-16;
}

</style>
