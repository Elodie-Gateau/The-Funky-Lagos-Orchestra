<script setup>
import {useRouter} from "vue-router";
import {useAuthStore} from "../../stores/auth.js";
import {useI18n} from "vue-i18n";
import { ref, onMounted } from 'vue'

const router = useRouter()
const auth = useAuthStore()
const { t } = useI18n()

// --- État du formulaire ---
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

// --- États UI ---
const isTranslating = ref(false)
const isSaving = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

// --- Chargement des données existantes ---
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

// --- Aperçu image ---
function handleImageChange(event) {
    const file = event.target.files[0]
    if (!file) return
    imageFile.value = file
    imagePreview.value = URL.createObjectURL(file)
}

// --- Traduction DeepL ---
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

// --- Sauvegarde ---
async function handleSave() {
    isSaving.value = true
    successMessage.value = ''
    errorMessage.value = ''

    try {
        // 1. Sauvegarde des descriptions
        const descriptionsChanged =
            descriptionFr.value !== initialDescriptions.value.description_fr ||
            descriptionEn.value !== initialDescriptions.value.description_en

        if (descriptionsChanged) {
        await fetch('/api/admin/settings/descriptions', {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                description_fr: descriptionFr.value,
                description_en: descriptionEn.value
            })
        })
            initialDescriptions.value = {
                description_fr: descriptionFr.value,
                description_en: descriptionEn.value,
            }
        }

        // 2. Upload de l'image si un nouveau fichier a été sélectionné
        if (imageFile.value) {
            const formData = new FormData()
            formData.append('imageFile', imageFile.value)
            await fetch('/api/admin/settings/image', {
                method: 'POST',
                credentials: 'include',
                body: formData
            })
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
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    email: email.value,
                    phone: phone.value,
                    facebook: facebook.value,
                    instagram: instagram.value,
                    youtube: youtube.value,
                    soundcloud: soundcloud.value,
                })
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
    <div class="settings-form">
        <h2>Paramètres du site</h2>

        <!-- Feedback -->
        <p v-if="successMessage" class="message message--success">{{ successMessage }}</p>
        <p v-if="errorMessage" class="message message--error">{{ errorMessage }}</p>

        <!-- Photo de groupe -->
        <div class="photo">
            <h3>Photo de groupe</h3>
            <div class="settings-form__preview">
                <img
                    v-if="imagePreview || currentImage"
                    :src="imagePreview ?? currentImage"
                    alt="Photo de groupe"
                />
                <span v-else class="settings-form__no-image">Aucune image</span>
            </div>
            <label class="settings-form__upload-label">
                Choisir une image
                <input
                    type="file"
                    accept="image/*"
                    @change="handleImageChange"
                    class="settings-form__upload-input"
                />
            </label>
            <p v-if="imageFile" class="settings-form__filename">{{ imageFile.name }}</p>
        </div>

        <!-- Descriptions -->
        <div class="description">
            <h3>Description</h3>

            <div class="settings-form__field">
                <label for="desc-fr">Français</label>
                <textarea
                    id="desc-fr"
                    v-model="descriptionFr"
                    rows="6"
                    placeholder="Description en français..."
                />
            </div>

            <button
                class="btn btn--secondary"
                @click="handleTranslate"
                :disabled="isTranslating || !descriptionFr.trim()"
            >
                {{ isTranslating ? 'Traduction en cours...' : '🌐 Traduire en anglais' }}
            </button>

            <div class="settings-form__field">
                <label for="desc-en">English</label>
                <textarea
                    id="desc-en"
                    v-model="descriptionEn"
                    rows="6"
                    placeholder="English description..."
                />
            </div>
        </div>
        <div>
            <h3>Modalités de contact</h3>
            <div>
                <label for="email">Email de contact</label>
                <input type="text" id="email" v-model="email" />
            </div>

            <div>
                <label for="phone">Téléphone</label>
                <input type="text" id="phone" v-model="phone" />
            </div>

            <div>
                <label for="facebook">Adresse de la page Facebook</label>
                <input type="text" id="facebook" v-model="facebook" />
            </div>

            <div>
                <label for="instagram">Adresse du profil Instagram</label>
                <input type="text" id="instagram" v-model="instagram" />
            </div>

            <div>
                <label for="youtube">Adresse de la chaîne YouTube</label>
                <input type="text" id="youtube" v-model="youtube" />
            </div>

            <div>
                <label for="soundcloud">Adresse du profil SoundCloud</label>
                <input type="text" id="soundcloud" v-model="soundcloud" />
            </div>
        </div>
        <button
            class="btn btn--primary"
            @click="handleSave"
            :disabled="isSaving"
        >
            {{ isSaving ? 'Sauvegarde...' : 'Enregistrer' }}
        </button>
    </div>
</template>

<style scoped>
img {
    max-width: 100%;
}
</style>
