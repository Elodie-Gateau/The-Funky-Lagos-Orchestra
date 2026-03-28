<script setup>
import {ref} from "vue";

const emit = defineEmits(["close"]);

const isSaving = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const title = ref('');
const artist = ref('');
const album = ref('');
const status = ref('');
const audioFile = ref(null);
const duration = ref(0);

// Récupération de l'upload du fichier audio
const handleAudioUpload = (event) => {
    audioFile.value = event.target.files[0];

    // Récupération de la durée du track après chargement
    const tempUrl = URL.createObjectURL(audioFile.value);
    const audio = new Audio(tempUrl);
    audio.addEventListener('loadedmetadata', () => {
        const totalSeconds = Math.round(audio.duration);
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;
        duration.value = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        URL.revokeObjectURL(tempUrl);
    });
}


async function handleSubmit() {
    isSaving.value = true
    successMessage.value = ''
    errorMessage.value = ''
    const formData = new FormData();
    formData.append('title', title.value);
    formData.append('artist', artist.value);
    formData.append('album', album.value);
    formData.append('status', status.value);
    formData.append('duration', duration.value);
    formData.append('audioFile', audioFile.value);
    try {
        await fetch('/api/admin/music/add', {
            method: 'POST',
            credentials: 'include',
            body: formData
        })
        successMessage.value = 'Morceau ajouté avec succès.'
    } catch {
        errorMessage.value = 'Une erreur est survenue lors de la sauvegarde.'
    } finally {
        isSaving.value = false
    }
    emit('close');
}
</script>

<template>
<section class="track-form">
    <h3>Ajouter un morceau</h3>
    <div>
        <div>
            <label for="title">Titre du morceau</label>
            <input type="text" id="title" v-model="title" placeholder="Ex: Babayaga" />
        </div>
        <div>
            <label for="artist">Artiste</label>
            <input type="text" id="artist" v-model="artist" value="The Funky Lagos Orchestra" />
        </div>
        <div>
            <label for="audio">Fichier audio (MP3 / MP4)</label>
            <input type="file" id="audio" accept="audio/mpeg,audio/ogg,video/mp4,audio/mp4,audio/x-m4a" @change="handleAudioUpload" />
        </div>
        <div>
            <label for="album">Album</label>
            <input type="text" id="album" v-model="album" />
        </div>
        <div>
            <label for="status">Statut</label>
            <select id="status" v-model="status">
                <option value="Brouillon">Brouillon</option>
                <option value="Publié">Publié</option>
            </select>
        </div>
        <button
            class="btn btn--primary"
            @click="handleSubmit"
            :disabled="isSaving">
            {{ isSaving ? 'Sauvegarde...' : 'Ajouter' }}
        </button>
    </div>
</section>
</template>

<style scoped>

</style>
