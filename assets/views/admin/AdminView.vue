<script setup>
import {useRouter} from "vue-router";
import {useAuthStore} from "../../stores/auth.js";
import {useI18n} from "vue-i18n";
const router = useRouter()
const auth = useAuthStore()
const { t, locale } = useI18n()

async function handleLogout() {
    await auth.logout()
    router.push('/login')
}

async function translateDescription() {
    isTranslating.value = true
    const response = await fetch('/api/admin/translate', {
        method: 'POST',
        credentials: 'include',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({text: form.description_fr})
    })
    const data = await response.json()
    form.description_en = data.translation
    isTranslating.value = false
}

</script>

<template>
<div class="admin-page">
    <header>
        <span>{{ auth.user?.email }}</span>
        <button @click="handleLogout">{{ t('admin.log-out') }}</button>
    </header>
    <main>
        <h1>{{ t('admin.title') }}</h1>
        <p>{{ t('admin.hello') }} {{ auth.user?.surname }}</p>
    </main>
</div>
</template>

<style scoped>

</style>
