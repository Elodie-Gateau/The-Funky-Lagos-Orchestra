<script setup>
import {Pause, Play, Trash2, X} from "@lucide/vue";
import TrackForm from "../../components/admin/ui/TrackForm.vue";
import {onMounted, ref} from "vue";
import AdminHeader from "../../components/admin/layout/AdminHeader.vue";
import AdminMenu from "../../components/admin/layout/AdminMenu.vue";
import { useAudioPlayer } from '../../composables/useAudioPlayer.js'
import AlbumForm from "../../components/admin/ui/AlbumForm.vue";

const { playingTrackId, togglePlay } = useAudioPlayer()
const tracks = ref([]);
const albums = ref([]);
const showModal = ref(false);
const modalType = ref(null);
const editingTrack = ref(null);
const editingAlbum = ref(null);

onMounted(async() => {
    const res = await fetch("/api/tracks", {credentials: 'include'})
    const resAlbums = await fetch("/api/albums", {credentials: 'include'})
    const data = await res.json();
    const dataAlbums = await resAlbums.json();
    albums.value = dataAlbums.albums;
    tracks.value = data.tracks;
})

const refreshData = async () => {
    const [resT, resA] = await Promise.all([
        fetch('/api/tracks', { credentials: 'include' }),
        fetch('/api/albums', { credentials: 'include' })
    ])
    const [dataT, dataA] = await Promise.all([resT.json(), resA.json()])
    tracks.value = dataT.tracks
    albums.value = dataA.albums
}

function openTrackModal(track = null, album = null) {
    if (track) {
        track.album = album;
    }
    editingTrack.value = track;
    modalType.value = 'track';
    showModal.value = true;
}

function openAlbumModal(album = null) {
    editingAlbum.value = album;
    modalType.value = 'album';
    showModal.value = true;
}

const onSaved = async () => {
    showModal.value = false
    editingTrack.value = null
    editingAlbum.value = null
    modalType.value = null

    await refreshData()
}

const deleteAlbum = async (id) => {
    await fetch(`/api/admin/album/${id}/delete`, {
        method: 'DELETE',
        credentials: 'include'
    })
    await refreshData()
}

const deleteTrack = async (id) => {
    await fetch(`/api/admin/track/${id}/delete`, {
        method: 'DELETE',
        credentials: 'include'
    })
    await refreshData()
}
</script>

<template>
    <AdminHeader />
    <AdminMenu />
    <a href="#" @click.prevent="openTrackModal()">Ajouter un morceau</a>
    <a href="#" @click.prevent="openAlbumModal()">Ajouter un album</a>

    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal">
            <button @click="closeModal"><X /></button>
            <TrackForm v-if="modalType === 'track'" :track="editingTrack" :albums="albums" @close="onSaved" />
            <AlbumForm v-if="modalType === 'album'" :album="editingAlbum" @close="onSaved" />
        </div>
    </div>

    <ul id="albums">
        <li v-for="album in albums" :key="album.id">
            <div class="wrapper-album">
                {{ album.name }}
                {{ album.year }}
                <img :src="album.cover" alt="Photo" />
                <button @click="openAlbumModal(album)">Modifier</button>
                <Trash2 @click="deleteAlbum(album.id)"/>
            </div>
            <ul id="tracks">
                <li v-for="track in album.tracks" :key="track.id">
                    <button @click="togglePlay(track)">
                        <Pause v-if="playingTrackId === track.id" />
                        <Play v-else />
                    </button>
                    {{ track.title }}
                    {{ track.artist }}
                    {{ track.duration }}
                    {{ track.status }}
                    {{ album.name }}
                    {{ track.isVisible }}
                    <button @click="openTrackModal(track, album)">Modifier</button>
                    <Trash2 @click="deleteTrack(track.id)"/>
                </li>
            </ul>
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
