<script setup>

import SectionTitle from "../ui/SectionTitle.vue";
import { useI18n } from "vue-i18n";
import { computed} from "vue";
const { t, locale } = useI18n();
import { useScrollTo } from '../../composables/scrollTo.js'
const { scrollTo } = useScrollTo()

const props = defineProps({
    tracks: Array,
    settings: Object,
})

const groupImage = computed(() => props.settings?.image)

const descriptionsData = computed(() => props.settings?.descriptions)

const description = computed(() => {
    if (locale.value === "en") {
        return descriptionsData.value?.description_en || descriptionsData.value?.description_fr;
    }
    return descriptionsData.value?.description_fr;
})
</script>

<template>
<section id="about">
    <SectionTitle :title="t('nav.about')" :subtitle="t('about.subtitle')" />
    <div class="about-container">
        <img v-if="groupImage" :src="groupImage" :alt="t('main.title')" />
        <div class="about-wrapper">
            <h4>{{ t('about.subtitle2.part1')}} <span>{{t('about.subtitle2.part2')}}</span> {{t('about.subtitle2.part3')}}</h4>
            <p v-if="description">{{ description }}</p>
            <div class="music-tags">
                <span>{{ t('about.tag1')}}</span>
                <span>{{ t('about.tag2')}}</span>
                <span>{{ t('about.tag3')}}</span>
                <span>{{ t('about.tag4')}}</span>
            </div>
            <a v-if = "props.tracks.length > 0" class="button" href="#" @click.prevent="scrollTo('music')">{{ t('about.link') }}</a>
        </div>
    </div>
</section>
</template>

<style scoped lang="scss">
@use "/assets/styles/utils/variables" as *;
@use "/assets/styles/utils/breakpoints" as *;

.about-container {
    display: flex;
    flex-direction: column;
    gap: $size-22;
        img {
            width: 100%;
            border-radius: $size-6;
            box-shadow: 0 $size-8 $size-32 rgba(42, 16, 0, .2);
            margin-bottom: $size-24;
        }

    .about-wrapper {
        display: flex;
        flex-direction: column;
        gap: $size-22;
        width: 100%;

        h4 {
            font-size: $size-26;
            font-weight: 800;
            line-height: 1.1;

            span {
                color: var(--secondary);
            }
        }


        .music-tags {
            display: flex;
            gap: $size-8;
            flex-wrap: wrap;

            span {
                font-family: "Raleway", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                font-weight: 700;
                font-size: $size-12;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: var(--title);
                border: 1.5px solid var(--accent);
                border-radius: $size-96;
                padding: $size-4 $size-14;
                background-color: var(--background-secondary);
            }
        }
    }
}


@media (min-width: $md){
    img {
        max-width: 60%;
        align-self: center;
        margin-bottom: $size-40;
        border-radius: $size-12;
    }
}

@media (min-width: $lg){

    .about-container {
        flex-direction: row;
        gap: $size-40;
        img {
            max-width: 40%;
            align-self: center;
        }
    }

    .button {
        margin : 0
    }
}
</style>
