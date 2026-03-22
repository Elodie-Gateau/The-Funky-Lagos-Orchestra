<script setup>
import {useRouter} from "vue-router";
import {useAuthStore} from "../../stores/auth.js";
import {useI18n} from "vue-i18n";
import { ref, onMounted } from 'vue'

const router = useRouter()
const auth = useAuthStore()
const { t } = useI18n()

async function handleLogout() {
    await auth.logout()
    router.push('/login')
}


// --- État du formulaire ---
const descriptionFr = ref('')
const descriptionEn = ref('')
const currentImage = ref(null)
const imagePreview = ref(null)
const imageFile = ref(null)

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
        await fetch('/api/admin/settings/descriptions', {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                description_fr: descriptionFr.value,
                description_en: descriptionEn.value
            })
        })

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

        successMessage.value = 'Paramètres sauvegardés avec succès.'
    } catch {
        errorMessage.value = 'Une erreur est survenue lors de la sauvegarde.'
    } finally {
        isSaving.value = false
    }
}
</script>

<template>
<div class="admin-page">
    <div class="admin-header">
        <span>{{ auth.user?.email }}</span>
        <button @click="handleLogout">{{ t('admin.log-out') }}</button>
    </div>
    <main>
        <h1>{{ t('admin.title') }}</h1>
        <p>{{ t('admin.hello') }} {{ auth.user?.surname }}</p>

        <div class="settings-form">
            <h2>Paramètres du site</h2>

            <!-- Feedback -->
            <p v-if="successMessage" class="message message--success">{{ successMessage }}</p>
            <p v-if="errorMessage" class="message message--error">{{ errorMessage }}</p>

            <!-- Photo de groupe -->
            <section class="settings-form__section">
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
            </section>

            <!-- Descriptions -->
            <section class="settings-form__section">
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
            </section>
            <button
                class="btn btn--primary"
                @click="handleSave"
                :disabled="isSaving"
            >
                {{ isSaving ? 'Sauvegarde...' : 'Enregistrer' }}
            </button>
        </div>
    </main>
</div>
</template>

<style scoped>
img {
    max-width: 100%;
}
</style>
