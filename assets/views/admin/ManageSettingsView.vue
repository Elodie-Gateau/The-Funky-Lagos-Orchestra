<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth.js'
import { useI18n } from 'vue-i18n'
import { ref, onMounted } from 'vue'
import AdminLayout from '../../components/admin/layout/AdminLayout.vue'

const router = useRouter()
const auth = useAuthStore()
const { t } = useI18n()

const descriptionFr = ref('')
const descriptionEn = ref('')
const initialDescriptions = ref({})
const currentImage = ref(null)
const imagePreview = ref(null)
const imageFile = ref(null)
const email = ref(null)
const phone = ref(null)
const facebook = ref(null)
const instagram = ref(null)
const youtube = ref(null)
const soundcloud = ref(null)
const initialContact = ref({})

const isTranslating = ref(false)
const isSaving = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

onMounted(async () => {
  const res = await fetch('/api/settings', { credentials: 'include' })
  const data = await res.json()
  currentImage.value = data.image
  descriptionFr.value = data.descriptions?.description_fr ?? ''
  descriptionEn.value = data.descriptions?.description_en ?? ''
  email.value = data.email ?? ''
  phone.value = data.phone ?? ''
  facebook.value = data.facebook ?? ''
  instagram.value = data.instagram ?? ''
  youtube.value = data.youtube ?? ''
  soundcloud.value = data.soundcloud ?? ''
  initialDescriptions.value = {
    description_fr: data.descriptions?.description_fr ?? '',
    description_en: data.descriptions?.description_en ?? '',
  }
  initialContact.value = {
    email: data.email ?? '',
    phone: data.phone ?? '',
    facebook: data.facebook ?? '',
    instagram: data.instagram ?? '',
    youtube: data.youtube ?? '',
    soundcloud: data.soundcloud ?? '',
  }
})

function handleImageChange(event) {
  const file = event.target.files[0]
  if (!file) return
  imageFile.value = file
  imagePreview.value = URL.createObjectURL(file)
}

async function handleTranslate() {
  if (!descriptionFr.value.trim()) return
  isTranslating.value = true
  errorMessage.value = ''
  try {
    const res = await fetch('/api/admin/translate', {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ text: descriptionFr.value })
    })
    const data = await res.json()
    descriptionEn.value = data.translation
  } catch {
    errorMessage.value = 'Erreur lors de la traduction.'
  } finally {
    isTranslating.value = false
  }
}

async function handleSave() {
  isSaving.value = true
  successMessage.value = ''
  errorMessage.value = ''
  try {
    const descriptionsChanged =
      descriptionFr.value !== initialDescriptions.value.description_fr ||
      descriptionEn.value !== initialDescriptions.value.description_en

    if (descriptionsChanged) {
      await fetch('/api/admin/settings/descriptions', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ description_fr: descriptionFr.value, description_en: descriptionEn.value })
      })
      initialDescriptions.value = { description_fr: descriptionFr.value, description_en: descriptionEn.value }
    }

    if (imageFile.value) {
      const formData = new FormData()
      formData.append('imageFile', imageFile.value)
      await fetch('/api/admin/settings/image', { method: 'POST', credentials: 'include', body: formData })
    }

    const contactChanged =
      email.value !== initialContact.value.email ||
      phone.value !== initialContact.value.phone ||
      facebook.value !== initialContact.value.facebook ||
      instagram.value !== initialContact.value.instagram ||
      youtube.value !== initialContact.value.youtube ||
      soundcloud.value !== initialContact.value.soundcloud

    if (contactChanged) {
      await fetch('/api/admin/settings/contacts', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email.value, phone: phone.value, facebook: facebook.value, instagram: instagram.value, youtube: youtube.value, soundcloud: soundcloud.value })
      })
      initialContact.value = { email: email.value, phone: phone.value, facebook: facebook.value, instagram: instagram.value, youtube: youtube.value, soundcloud: soundcloud.value }
    }

    successMessage.value = 'Paramètres sauvegardés avec succès.'
  } catch {
    errorMessage.value = 'Une erreur est survenue lors de la sauvegarde.'
  } finally {
    isSaving.value = false
  }
}
</script>

<template>
  <AdminLayout page-title="Paramètres">
    <div class="admin-page-header">
      <div>
        <h1 class="admin-page-header__title">Paramètres</h1>
        <p class="admin-page-header__sub">Configurez les informations et contacts du site</p>
      </div>
    </div>

    <p v-if="successMessage" class="admin-form-feedback admin-form-feedback--success">
      {{ successMessage }}
    </p>
    <p v-if="errorMessage" class="admin-form-feedback admin-form-feedback--error">
      {{ errorMessage }}
    </p>

    <!-- Photo de groupe -->
    <div class="admin-card" style="margin-bottom: 1.5rem;">
      <div class="admin-card__header">
        <span class="admin-card__title">Photo de groupe</span>
      </div>
      <div class="admin-settings-grid">
        <div class="admin-settings-section">
          <div v-if="imagePreview || currentImage" class="admin-img-preview">
            <img :src="imagePreview ?? currentImage" alt="Photo de groupe" />
          </div>
          <div v-else class="img-placeholder">Aucune image</div>
        </div>
        <div class="admin-settings-section">
          <div class="admin-form-group">
            <label>Image de groupe</label>
            <input type="file" accept="image/*" @change="handleImageChange" />
          </div>
          <p v-if="imageFile" class="admin-form-hint">{{ imageFile.name }}</p>
        </div>
      </div>
    </div>

    <!-- Description -->
    <div class="admin-card" style="margin-bottom: 1.5rem;">
      <div class="admin-card__header">
        <span class="admin-card__title">Description</span>
      </div>
      <div class="admin-settings-grid">
        <div class="admin-settings-section">
          <div class="admin-form-group">
            <label for="desc-fr">Français</label>
            <textarea id="desc-fr" v-model="descriptionFr" rows="6" placeholder="Description en français..." />
          </div>
          <button
            class="admin-btn admin-btn--secondary"
            @click="handleTranslate"
            :disabled="isTranslating || !descriptionFr.trim()"
          >
            {{ isTranslating ? 'Traduction en cours...' : '🌐 Traduire en anglais' }}
          </button>
        </div>
        <div class="admin-settings-section">
          <div class="admin-form-group">
            <label for="desc-en">English</label>
            <textarea id="desc-en" v-model="descriptionEn" rows="6" placeholder="English description..." />
          </div>
        </div>
      </div>
    </div>

    <!-- Contacts & Réseaux -->
    <div class="admin-card">
      <div class="admin-card__header">
        <span class="admin-card__title">Contact & Réseaux sociaux</span>
      </div>
      <div class="admin-settings-grid">
        <div class="admin-settings-section">
          <div class="admin-settings-section__title">Contact</div>
          <div class="admin-form-group">
            <label for="email">Email de contact</label>
            <input type="email" id="email" v-model="email" placeholder="contact@..." />
          </div>
          <div class="admin-form-group">
            <label for="phone">Téléphone</label>
            <input type="tel" id="phone" v-model="phone" placeholder="+33 6 ..." />
          </div>
        </div>
        <div class="admin-settings-section">
          <div class="admin-settings-section__title">Réseaux sociaux</div>
          <div class="admin-form-group">
            <label for="facebook">Facebook</label>
            <input type="url" id="facebook" v-model="facebook" placeholder="https://facebook.com/..." />
          </div>
          <div class="admin-form-group">
            <label for="instagram">Instagram</label>
            <input type="url" id="instagram" v-model="instagram" placeholder="https://instagram.com/..." />
          </div>
          <div class="admin-form-group">
            <label for="youtube">YouTube</label>
            <input type="url" id="youtube" v-model="youtube" placeholder="https://youtube.com/@..." />
          </div>
          <div class="admin-form-group">
            <label for="soundcloud">SoundCloud</label>
            <input type="url" id="soundcloud" v-model="soundcloud" placeholder="https://soundcloud.com/..." />
          </div>
        </div>
      </div>
      <div class="admin-settings-actions">
        <button class="admin-btn admin-btn--primary" @click="handleSave" :disabled="isSaving">
          {{ isSaving ? 'Sauvegarde...' : 'Enregistrer' }}
        </button>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.img-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f6f6f6;
  border: 1px dashed #d0c7a8;
  border-radius: 0.5rem;
  height: 120px;
  color: #838383;
  font-size: 0.875rem;
}
</style>
