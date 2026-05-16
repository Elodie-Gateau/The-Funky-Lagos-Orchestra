<script setup>
import { ref, watch, computed } from "vue"

const props = defineProps({
    album: { type: Object, default: null }
})
const emit = defineEmits(["close"])

const isSaving = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const name = ref('')
const year = ref('')
const coverFile = ref(null)
const coverPreview = ref(null)

const handleImageUpload = (event) => {
    coverFile.value = event.target.files[0]
    coverPreview.value = URL.createObjectURL(coverFile.value)
}

// Pré-remplissage en mode édition
watch(() => props.album, (a) => {
    if (a) {
        name.value = a.name ?? ''
        year.value = a.year ?? ''
        coverPreview.value = a.cover ?? null
    }
}, { immediate: true })

const isEditMode = computed(() => props.album !== null)

async function handleSubmit() {
    isSaving.value = true
    successMessage.value = ''
    errorMessage.value = ''

    const formData = new FormData()
    formData.append('name', name.value)
    formData.append('year', year.value)
    if (coverFile.value) formData.append('cover', coverFile.value)

    const url = isEditMode.value
        ? `/api/admin/album/${props.album.id}`
        : '/api/admin/album/add'
    const method = isEditMode.value ? 'POST' : 'POST'

    try {
        await fetch(url, { method, credentials: 'include', body: formData })
        successMessage.value = isEditMode.value ? 'Album modifié.' : 'Album ajouté.'
        emit('close')
    } catch {
        errorMessage.value = 'Une erreur est survenue.'
    } finally {
        isSaving.value = false
    }
}
</script>

<template>
    <section class="album-form">
        <h3>{{ isEditMode ? "Modifier l'album" : 'Ajouter un album' }}</h3>
        <div>
            <div>
                <label for="name">Titre</label>
                <input type="text" id="name" v-model="name" />
            </div>
            <div>
                <label for="year">Année</label>
                <input type="number" id="year" v-model="year" min="1900" max="2100"/>
            </div>
            <div>
                <label for="image">Télécharger une image</label>
<!--                <img v-if="coverPreview" :src="coverPreview" alt="Aperçu" />-->
                <input type="file" id="image" accept="image/jpeg,image/png,image/webp" @change="handleImageUpload" />
            </div>
            <p v-if="successMessage">{{ successMessage }}</p>
            <p v-if="errorMessage">{{ errorMessage }}</p>
            <button @click="handleSubmit" :disabled="isSaving">
                {{ isSaving ? 'Sauvegarde...' : (isEditMode ? 'Modifier' : 'Ajouter') }}
            </button>
        </div>
    </section>
</template>
