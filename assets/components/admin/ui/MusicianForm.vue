<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
    musician: { type: Object, default: null }
})
const emit = defineEmits(['close'])

const isSaving = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const firstname = ref('')
const lastname = ref('')
const instrument = ref('')
const isEditMode = computed(() => props.musician !== null)

watch(() => props.musician, (t) => {
    if (t) {
        firstname.value = t.firstname ?? ''
        lastname.value = t.lastname ?? ''
        instrument.value = t.instrument ?? ''
    }
}, { immediate: true })

async function handleSubmit() {
    isSaving.value = true
    successMessage.value = ''
    errorMessage.value = ''
    const formData = new FormData()
    formData.append('firstname', firstname.value)
    formData.append('lastname', lastname.value)
    formData.append('instrument', instrument.value)
    const url = isEditMode.value ? `/api/admin/musician/update/${props.musician.id}` : '/api/admin/musician/add'
    try {
        await fetch(url, { method: 'POST', credentials: 'include', body: formData })
        successMessage.value = isEditMode.value ? 'Musicien modifié.' : 'Musicien enregistré.'
        emit('close')
    } catch {
        errorMessage.value = 'Une erreur est survenue.'
    } finally {
        isSaving.value = false
    }
}
</script>

<template>
    <div class="event-form">
        <div class="admin-form-group">
            <label for="mu-firstname">Prénom du musicien</label>
            <input type="text" id="mu-firstname" v-model="firstname" placeholder="Jean-Pierre" />
        </div>
        <div class="admin-form-group">
            <label for="mu-lastname">Nom du musicien</label>
            <input type="text" id="mu-lastname" v-model="lastname" placeholder="Gateau" />
        </div>
        <div class="admin-form-group">
            <label for="mu-instrument">Instrument</label>
            <select v-model="instrument" id="mu-instrument">
                <option value="">Choisir un instrument</option>
                <option value="Batterie">Batterie</option>
                <option value="Guitare">Guitare</option>
                <option value="Basse">Basse</option>
                <option value="Clavier">Clavier</option>
                <option value="Saxophone">Saxophone</option>
                <option value="Trompette">Trompette</option>
                <option value="Percussion">Percussion</option>
            </select>
        </div>

        <p v-if="successMessage" class="admin-form-feedback admin-form-feedback--success">{{ successMessage }}</p>
        <p v-if="errorMessage" class="admin-form-feedback admin-form-feedback--error">{{ errorMessage }}</p>

        <div class="form-footer">
            <button class="admin-btn admin-btn--primary" @click="handleSubmit" :disabled="isSaving">
                {{ isSaving ? 'Sauvegarde...' : (isEditMode ? 'Modifier' : 'Enregistrer') }}
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
