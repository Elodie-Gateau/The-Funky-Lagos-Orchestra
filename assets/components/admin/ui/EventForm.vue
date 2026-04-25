<script setup>
import { ref, watch, computed } from "vue"

const props = defineProps({
    event: { type: Object, default: null }
})
const emit = defineEmits(["close"])

const isSaving = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const name = ref('')
const location = ref('')
const city = ref('')
const date = ref('')
const host = ref('')
const isEditMode = computed(() => props.event !== null)

watch(() => props.event, (t) => {
    if (t) {
        name.value = t.name ?? ''
        location.value = t.location ?? ''
        date.value = t.city ?? ''
        city.value = t.city ?? ''
        host.value = t.host ?? ''
    }
}, { immediate: true })

async function handleSubmit() {
    isSaving.value = true
    successMessage.value = ''
    errorMessage.value = ''

    const formData = new FormData()
    formData.append('name', name.value)
    formData.append('location', location.value)
    formData.append('city', city.value)
    formData.append('date', date.value)
    formData.append('host', host.value)

    const url = isEditMode.value
        ? `/api/admin/event/${props.event.id}`
        : '/api/admin/event/add'
    const method = isEditMode.value ? 'POST' : 'POST'

    try {
        await fetch(url, { method, credentials: 'include', body: formData })
        successMessage.value = isEditMode.value ? 'Concert modifié.' : 'Concert enregistré.'
        emit('close')
    } catch {
        errorMessage.value = 'Une erreur est survenue.'
    } finally {
        isSaving.value = false
    }
}
</script>

<template>
    <section class="event-form">
        <h3>{{ isEditMode ? 'Modifier le concert' : 'Ajouter un concert' }}</h3>
        <div>
            <div>
                <label for="name">Nom de l'événement</label>
                <input type="text" id="name" v-model="name" />
            </div>
            <div>
                <label for="date">Date du concert</label>
                <input type="date" id="date" v-model="date" />
            </div>
            <div>
                <label for="location">Lieu de l'événement</label>
                <input type="text" id="location" v-model="location" />
            </div>
            <div>
                <label for="city">Ville de l'événement</label>
                <input type="text" id="city" v-model="city" />
            </div>
            <div>
                <label for="host">Organisateur de l'événement</label>
                <input type="text" id="host" v-model="host" />
            </div>
            <p v-if="successMessage">{{ successMessage }}</p>
            <p v-if="errorMessage">{{ errorMessage }}</p>
            <button @click="handleSubmit" :disabled="isSaving">
                {{ isSaving ? 'Sauvegarde...' : (isEditMode ? 'Modifier' : 'Enregistrer') }}
            </button>
        </div>
    </section>
</template>

<style scoped>

</style>
