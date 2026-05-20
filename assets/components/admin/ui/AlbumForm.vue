<script setup>
import { ref, watch, computed } from 'vue'
import UploadProgress from './UploadProgress.vue'

const props = defineProps({
  album: { type: Object, default: null }
})
const emit = defineEmits(['close'])

const isSaving = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const name = ref('')
const year = ref('')
const coverFile = ref(null)
const coverPreview = ref(null)
const fileInput = ref(null)
const uploadProgress = ref(0)
const uploadState = ref('idle')

async function simulateLocalRead() {
  uploadState.value = 'loading'
  for (const step of [15, 35, 60, 80, 95, 100]) {
    await new Promise(r => setTimeout(r, 70))
    uploadProgress.value = step
  }
  uploadState.value = 'ready'
}

const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return
  coverFile.value = file
  uploadProgress.value = 0
  simulateLocalRead()
}

watch(() => props.album, (a) => {
  if (a) {
    name.value = a.name ?? ''
    year.value = a.year ?? ''
    coverPreview.value = a.cover ?? null
  }
}, { immediate: true })

const isEditMode = computed(() => props.album !== null)

function removeCover() {
  coverFile.value = null
  uploadState.value = 'idle'
  uploadProgress.value = 0
  if (fileInput.value) fileInput.value.value = ''
}

async function handleSubmit() {
  isSaving.value = true
  successMessage.value = ''
  errorMessage.value = ''

  const formData = new FormData()
  formData.append('name', name.value)
  formData.append('year', year.value)
  if (coverFile.value) {
    formData.append('cover', coverFile.value)
    uploadState.value = 'uploading'
    uploadProgress.value = 0
  }

  const url = isEditMode.value ? `/api/admin/album/${props.album.id}` : '/api/admin/album/add'

  return new Promise((resolve) => {
    const xhr = new XMLHttpRequest()

    if (coverFile.value) {
      xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
          uploadProgress.value = Math.round((e.loaded / e.total) * 100)
        }
      })
    }

    xhr.addEventListener('load', () => {
      isSaving.value = false
      if (xhr.status >= 200 && xhr.status < 300) {
        if (coverFile.value) uploadState.value = 'done'
        successMessage.value = isEditMode.value ? 'Album modifié.' : 'Album ajouté.'
        emit('close')
      } else {
        if (coverFile.value) uploadState.value = 'error'
        errorMessage.value = 'Une erreur est survenue.'
      }
      resolve()
    })

    xhr.addEventListener('error', () => {
      isSaving.value = false
      if (coverFile.value) uploadState.value = 'error'
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
  <div class="album-form">
    <div class="admin-form-group">
      <label for="alb-name">Titre de l'album *</label>
      <input type="text" id="alb-name" v-model="name" placeholder="The Funky Lagos Orchestra" />
    </div>
    <div class="admin-form-group">
      <label for="alb-year">Année de sortie</label>
      <input type="number" id="alb-year" v-model="year" min="1900" max="2100" placeholder="2024" />
    </div>

    <div class="admin-form-group">
      <label>Couverture{{ isEditMode ? ' (laisser vide pour conserver)' : '' }}</label>
      <input
        ref="fileInput"
        type="file"
        accept="image/jpeg,image/png,image/webp"
        style="display:none"
        @change="handleImageUpload"
      />
      <UploadProgress
        v-if="coverFile"
        :file="coverFile"
        :progress="uploadProgress"
        :state="uploadState"
        @remove="removeCover"
        @retry="handleSubmit"
      />
      <template v-else>
        <div v-if="coverPreview" class="admin-img-preview" style="margin-bottom: 0.5rem;">
          <img :src="coverPreview" alt="Aperçu couverture" />
        </div>
        <button type="button" class="upload-dropzone" @click="fileInput.click()">
          <i class="ti ti-upload" />
          <span>{{ coverPreview ? 'Remplacer la couverture' : 'Ajouter une couverture' }}</span>
          <span class="upload-dropzone__hint">JPEG · PNG · WebP</span>
        </button>
      </template>
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
</style>
