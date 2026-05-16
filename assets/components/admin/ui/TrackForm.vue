<script setup>
import { ref, watch, computed } from "vue"

const props = defineProps({
    track: { type: Object, default: null }
    , albums: { type: Array, default: [] }
})
const emit = defineEmits(["close"])

const isSaving = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const title = ref('')
const artist = ref('')
const album = ref('')
const audioFile = ref(null)
const duration = ref(0)
const visibility = ref(false)

// Pré-remplissage en mode édition
watch(() => props.track, async (t) => {
    if (t) {
        title.value = t.title ?? ''
        artist.value = t.artist ?? ''
        album.value = t.album?.id ?? ''
        duration.value = t.duration ?? 0
        visibility.value = t.isVisible ?? false
    }
}, { immediate: true })

const isEditMode = computed(() => props.track !== null)

const handleAudioUpload = (event) => {
    audioFile.value = event.target.files[0]
    const tempUrl = URL.createObjectURL(audioFile.value)
    const audio = new Audio(tempUrl)
    audio.addEventListener('loadedmetadata', () => {
        const totalSeconds = Math.round(audio.duration)
        const minutes = Math.floor(totalSeconds / 60)
        const seconds = totalSeconds % 60
        duration.value = `${minutes}:${seconds.toString().padStart(2, '0')}`
        URL.revokeObjectURL(tempUrl)
    })
}

async function handleSubmit() {
    isSaving.value = true
    successMessage.value = ''
    errorMessage.value = ''

    const formData = new FormData()
    formData.append('title', title.value)
    formData.append('artist', artist.value)
    formData.append('album', album.value)
    formData.append('duration', duration.value)
    formData.append('isVisible', visibility.value)
    if (audioFile.value) formData.append('audioFile', audioFile.value)

    const url = isEditMode.value
        ? `/api/admin/track/${props.track.id}`
        : '/api/admin/track/add'
    const method = isEditMode.value ? 'POST' : 'POST'

    try {
        await fetch(url, { method, credentials: 'include', body: formData })
        successMessage.value = isEditMode.value ? 'Morceau modifié.' : 'Morceau ajouté.'
        emit('close')
    } catch {
        errorMessage.value = 'Une erreur est survenue.'
    } finally {
        isSaving.value = false
    }
}
</script>

<template>
    <section class="track-form">
        <h3>{{ isEditMode ? 'Modifier le morceau' : 'Ajouter un morceau' }}</h3>
        <div>
            <div>
                <label for="title">Titre</label>
                <input type="text" id="title" v-model="title" />
            </div>
            <div>
                <label for="artist">Artiste</label>
                <input type="text" id="artist" v-model="artist" />
            </div>
            <div>
                <label for="audio">Fichier audio{{ isEditMode ? ' (laisser vide pour conserver)' : '' }}</label>
                <input type="file" id="audio" accept="audio/mpeg,audio/ogg,video/mp4,audio/mp4,audio/x-m4a" @change="handleAudioUpload" />
            </div>
            <div>
                <label for="album">Album</label>
                <select id="album" v-model="album">
                    <option v-for="album in albums" :key="album.id" :value="album.id">{{ album.name }}</option>
                </select>
            </div>
            <div>
                <label for="visibility">Publier sur la page d'accueil ?</label>
                <input type="checkbox" name="visibility" id="visibility" v-model="visibility">
            </div>
            <p v-if="successMessage">{{ successMessage }}</p>
            <p v-if="errorMessage">{{ errorMessage }}</p>
            <button @click="handleSubmit" :disabled="isSaving">
                {{ isSaving ? 'Sauvegarde...' : (isEditMode ? 'Modifier' : 'Ajouter') }}
            </button>
        </div>
    </section>
</template>
