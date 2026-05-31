<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
  event: { type: Object, default: null }
})
const emit = defineEmits(['close'])

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
    date.value = t.date ?? ''
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
  const url = isEditMode.value ? `/api/admin/event/${props.event.id}` : '/api/admin/event/add'
  try {
    await fetch(url, { method: 'POST', credentials: 'include', body: formData })
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
  <div class="event-form">
    <div class="admin-form-group">
      <label for="ev-name">Nom de l'événement *</label>
      <input type="text" id="ev-name" v-model="name" placeholder="Festival de la Côte" />
    </div>
    <div class="admin-form-row">
      <div class="admin-form-group">
        <label for="ev-date">Date *</label>
        <input type="date" id="ev-date" v-model="date" />
      </div>
      <div class="admin-form-group">
        <label for="ev-city">Ville *</label>
        <input type="text" id="ev-city" v-model="city" placeholder="Les Sables d'Olonne" />
      </div>
    </div>
    <div class="admin-form-group">
      <label for="ev-location">Salle / Lieu</label>
      <input type="text" id="ev-location" v-model="location" placeholder="Salle des Atlantes" />
    </div>
    <div class="admin-form-group">
      <label for="ev-host">Organisateur</label>
      <input type="text" id="ev-host" v-model="host" placeholder="Nom de l'organisateur" />
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
