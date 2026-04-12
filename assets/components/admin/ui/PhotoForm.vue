<script setup>
import {ref, watch, computed, onUnmounted} from "vue"

const props = defineProps({
    photo: { type: Object, default: null }
})
const emit = defineEmits(["close"])
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

    const url = '/api/admin/photo/add'
    const method = 'POST'

    try {
        await fetch(url, { method, credentials: 'include', body: formData })
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
    <section class="photo-form">
        <h3>Ajouter une photo</h3>
        <div>
            <div>
                <label for="image">Télécharger une image</label>
                <img v-if="photoPreview" :src="photoPreview" alt="Aperçu" />
                <input type="file" id="image" accept="image/jpeg,image/png,image/webp" @change="handleImageUpload" />
            </div>
            <p v-if="successMessage">{{ successMessage }}</p>
            <p v-if="errorMessage">{{ errorMessage }}</p>
            <button @click="handleSubmit" :disabled="isSaving">
                {{ isSaving ? 'Sauvegarde...' : 'Enregistrer' }}
            </button>
        </div>
    </section>
</template>

<style scoped>

</style>
