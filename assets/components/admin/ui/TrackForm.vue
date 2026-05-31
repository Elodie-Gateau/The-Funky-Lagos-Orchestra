<script setup>
import { ref, watch, computed } from 'vue'
import UploadProgress from './UploadProgress.vue'

const props = defineProps({
  track: { type: Object, default: null },
  albums: { type: Array, default: () => [] }
})
const emit = defineEmits(['close'])

const isSaving = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const title = ref('')
const album = ref('')
const audioFile = ref(null)
const duration = ref('')
const visibility = ref(false)
/** @type {import('vue').Ref<HTMLInputElement|null>} */
const fileInput = ref(null)
const uploadProgress = ref(0)
const uploadState = ref('idle')

watch(() => props.track, async (t) => {
  if (t) {
    title.value = t.title ?? ''
    album.value = t.album?.id ?? ''
    duration.value = t.duration ?? ''
    visibility.value = t.isVisible ?? false
  }
}, { immediate: true })

const isEditMode = computed(() => props.track !== null)

async function simulateLocalRead() {
  uploadState.value = 'loading'
  for (const step of [15, 35, 60, 80, 95, 100]) {
    await new Promise(r => setTimeout(r, 70))
    uploadProgress.value = step
  }
  uploadState.value = 'ready'
}

const handleAudioUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return
  audioFile.value = file
  uploadProgress.value = 0
  const tempUrl = URL.createObjectURL(file)
  const audio = new Audio(tempUrl)
  audio.addEventListener('loadedmetadata', () => {
    const totalSeconds = Math.round(audio.duration)
    const minutes = Math.floor(totalSeconds / 60)
    const seconds = totalSeconds % 60
    duration.value = `${minutes}:${seconds.toString().padStart(2, '0')}`
    URL.revokeObjectURL(tempUrl)
  })
  simulateLocalRead()
}

function removeAudio() {
  audioFile.value = null
  uploadState.value = 'idle'
  uploadProgress.value = 0
  duration.value = ''
  if (fileInput.value) fileInput.value.value = ''
}

async function handleSubmit() {
  isSaving.value = true
  successMessage.value = ''
  errorMessage.value = ''

  const formData = new FormData()
  formData.append('title', title.value)
  formData.append('album', album.value)
  formData.append('duration', duration.value)
  formData.append('visibility', visibility.value)
  if (audioFile.value) {
    formData.append('audioFile', audioFile.value)
    uploadState.value = 'uploading'
    uploadProgress.value = 0
  }

  const url = isEditMode.value ? `/api/admin/track/${props.track.id}` : '/api/admin/track/add'

  return new Promise((resolve) => {
    const xhr = new XMLHttpRequest()

    if (audioFile.value) {
      xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
          uploadProgress.value = Math.round((e.loaded / e.total) * 100)
        }
      })
    }

    xhr.addEventListener('load', () => {
      isSaving.value = false
      if (xhr.status >= 200 && xhr.status < 300) {
        if (audioFile.value) uploadState.value = 'done'
        successMessage.value = isEditMode.value ? 'Morceau modifié.' : 'Morceau ajouté.'
        emit('close')
      } else {
        if (audioFile.value) uploadState.value = 'error'
        try {
          const response = JSON.parse(xhr.responseText)
          errorMessage.value = response.error || 'Une erreur est survenue.'
        } catch {
          errorMessage.value = 'Une erreur est survenue.'
        }
      }
      resolve()
    })

    xhr.addEventListener('error', () => {
      isSaving.value = false
      if (audioFile.value) uploadState.value = 'error'
      errorMessage.value = 'Une erreur est survenue.'
      resolve()
    })

    xhr.open('POST', url)
    xhr.withCredentials = true
    xhr.send(formData)
  })
}
</script>

<template>
  <div class="track-form">
    <div class="admin-form-group">
      <label for="tr-title">Titre *</label>
      <input type="text" id="tr-title" v-model="title" placeholder="For Lovers" />
    </div>
    <div class="admin-form-row">
      <div class="admin-form-group">
        <label for="tr-album">Album</label>
        <select id="tr-album" v-model="album">
          <option value="">— Choisir un album —</option>
          <option v-for="alb in albums" :key="alb.id" :value="alb.id">{{ alb.name }}</option>
        </select>
      </div>
    </div>

    <div class="admin-form-group">
      <label>Fichier audio{{ isEditMode ? ' (laisser vide pour conserver)' : '' }}</label>
      <input
        ref="fileInput"
        type="file"
        accept="audio/mpeg,audio/ogg,video/mp4,audio/mp4,audio/x-m4a"
        style="display:none"
        @change="handleAudioUpload"
      />
      <UploadProgress
        v-if="audioFile"
        :file="audioFile"
        :progress="uploadProgress"
        :state="uploadState"
        icon="ti-music"
        @remove="removeAudio"
        @retry="handleSubmit"
      />
      <button v-else type="button" class="upload-dropzone" @click="fileInput.click()">
        <i class="ti ti-upload" />
        <span>{{ isEditMode ? 'Remplacer le fichier audio' : 'Ajouter un fichier audio' }}</span>
        <span class="upload-dropzone__hint">MP3 · OGG · M4A</span>
      </button>
    </div>

    <div v-if="duration" class="admin-form-hint">Durée détectée : {{ duration }}</div>

    <div class="admin-form-group visibility-group">
      <label class="checkbox-label">
        <input type="checkbox" v-model="visibility" />
        Afficher sur la page d'accueil
      </label>
    </div>

    <p v-if="successMessage" class="admin-form-feedback admin-form-feedback--success">{{ successMessage }}</p>
    <p v-if="errorMessage" class="admin-form-feedback admin-form-feedback--error">{{ errorMessage }}</p>

    <div class="form-footer">
      <button class="admin-btn admin-btn--primary" @click="handleSubmit" :disabled="isSaving">
        {{ isSaving ? 'Sauvegarde...' : (isEditMode ? 'Modifier' : 'Ajouter') }}
      </button>
    </div>
  </div>
</template>

<style scoped>
.form-footer {
  display: flex;
  justify-content: flex-end;
  padding-top: 0.5rem;
}

.visibility-group label { text-transform: none; font-size: 0.875rem; }

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-family: "Nunito", system-ui, sans-serif;
  font-size: 0.875rem;
  color: #6b3510;
  font-weight: 600;

  input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #C8943A;
  }
}
</style>
