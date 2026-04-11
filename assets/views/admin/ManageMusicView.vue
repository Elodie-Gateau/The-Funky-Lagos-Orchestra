<script setup>

import {Pause, Play, X} from "@lucide/vue";
import TrackForm from "../../components/admin/ui/TrackForm.vue";
import {onMounted, ref} from "vue";
import AdminHeader from "../../components/admin/layout/AdminHeader.vue";
import AdminMenu from "../../components/admin/layout/AdminMenu.vue";
import { useAudioPlayer } from '../../composables/useAudioPlayer.js'

const { playingTrackId, togglePlay } = useAudioPlayer()
const tracks = ref([]);
const showModal = ref(false);
const editingTrack = ref(null)

onMounted(async() => {
    const res = await fetch("/api/tracks", {credentials: 'include'})
    const data = await res.json();
    tracks.value = data.tracks;
})
</script>

<template>
    <AdminHeader   />
    <AdminMenu />
    <a href="#" @click.prevent="editingTrack = null; showModal = true">Ajouter un morceau</a>

    <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
        <div class="modal">
            <button @click="showModal = false"><X /></button>
            <TrackForm :track="editingTrack" @close="showModal = false" />
        </div>
    </div>

    <ul id="tracks">
        <li v-for="track in tracks" :key="track.id">
            <button @click="togglePlay(track)">
                <Pause v-if="playingTrackId === track.id" />
                <Play v-else />
            </button>
            {{ track.title }}
            {{ track.artist }}
            {{ track.duration }}
            {{ track.status }}
            {{ track.visible }}
            <button @click="editingTrack = track; showModal = true">Modifier</button>
        </li>
    </ul>
</template>

<style scoped>
.modal-overlay {
    position: fixed;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
}

.modal {
    position: relative;
    background: white;
}
</style>
