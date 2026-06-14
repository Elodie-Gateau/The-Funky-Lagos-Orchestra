<script setup>

import AppHeader from "../components/layout/AppHeader.vue";
import AppFooter from "../components/layout/AppFooter.vue";
import {useI18n} from "vue-i18n";
import {onMounted, ref} from "vue";
import SectionTitle from "../components/ui/SectionTitle.vue";
import TrackCard from "../components/ui/TrackCard.vue";
const { t } = useI18n();
const tracks = ref([]);
const albums = ref([]);

onMounted(async() => {
    const [res, resAlbums] = await Promise.all([
        fetch("/api/tracks", {credentials: 'include'}),
        fetch("/api/albums", {credentials: 'include'})
        ]);
    const data = await res.json();
    const dataAlbums = await resAlbums.json();
    tracks.value = data.tracks;
    albums.value = dataAlbums.albums;
})
</script>

<template>
    <AppHeader/>
    <main>
        <section class="section--dark">
            <SectionTitle :title="t('discography.subtitle')" :subtitle="t('discography.title')" />
            <div class="albums" v-for="album in albums" :key="album.id">
                <div class="wrapper">
                    <img :src="album.cover" alt="album.name">
                    <SectionTitle :title="String(album.year)" :subtitle="album.name" />
                </div>
                <ul id="tracks">
                    <TrackCard v-for="track in album.tracks" :key="track.id" :track="track" />
                </ul>
            </div>
        </section>
    </main>
    <AppFooter/>
</template>

<style scoped lang="scss">
@use "/assets/styles/utils/variables" as *;
@use "/assets/styles/utils/breakpoints" as *;

main {
    padding-top: $size-40;
    background-color: #{$color-brown};

}
    .albums {
        margin-bottom: $size-56;
    }

img {
    width: 100%;
    border-radius: $size-16;
    margin-bottom: $size-40;
    box-shadow: 0 4px 18px var(--background-footer);
}

#tracks {
    display: flex;
    flex-direction: column;
    gap: $size-10;
    width: 100%;
}

@media (min-width: $md) {
    .albums {
        display: flex;
        flex-direction: row;
        gap: $size-20;
        justify-content: space-between;
        align-items: flex-start;

        .wrapper {
            width: 45%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            img {
                width: 50%;
            }
        }
        #tracks {
            width: 60%;
        }
    }
}

@media (min-width: $lg) {
    main {
        width: 100%;

    }
}
</style>
