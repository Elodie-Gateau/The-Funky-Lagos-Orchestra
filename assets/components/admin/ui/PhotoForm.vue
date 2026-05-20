<script setup>
import { ref, onUnmounted } from 'vue'

const props = defineProps({
  photo: { type: Object, default: null }
})
const emit = defineEmits(['close'])

const photoPreview = ref(null)
const isSaving = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const photoFile = ref(null)

const handleImageUpload = (event) => {
  photoFile.value = event.target.files[0]
  photoPreview.value = URL.createObjectURL(photoFile.value)
}

onUnmounted(() => {
  if (photoPreview.value) URL.revokeObjectURL(photoPreview.value)
})

async function handleSubmit() {
  isSaving.value = true
  successMessage.value = ''
  errorMessage.value = ''
  const formData = new FormData()
  if (photoFile.value) formData.append('image', photoFile.value)
  try {
    await fetch('/api/admin/photo/add', { method: 'POST', credentials: 'include', body: formData })
    successMessage.value = 'Photo ajoutée.'
    emit('close')
  } catch {
    errorMessage.value = 'Une erreur est survenue.'
  } finally {
    isSaving.value = false
  }
}
</script>

<template>
  <div class="photo-form">
    <div v-if="photoPreview" class="admin-img-preview" style="margin-bottom: 0.5rem;">
      <img :src="photoPreview" alt="Aperçu" />
    </div>
    <div class="admin-form-group">
      <label for="ph-image">Image (JPEG, PNG, WebP)</label>
      <input type="file" id="ph-image" accept="image/jpeg,image/png,image/webp" @change="handleImageUpload" />
    </div>

    <p v-if="successMessage" class="admin-form-feedback admin-form-feedback--success">{{ successMessage }}</p>
    <p v-if="errorMessage" class="admin-form-feedback admin-form-feedback--error">{{ errorMessage }}</p>

    <div class="form-footer">
      <button
        class="admin-btn admin-btn--primary"
        @click="handleSubmit"
        :disabled="isSaving || !photoFile"
      >
        {{ isSaving ? 'Upload...' : 'Enregistrer' }}
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
