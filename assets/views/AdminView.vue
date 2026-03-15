<script setup>
import {useRouter} from "vue-router";
import {useAuthStore} from "../stores/auth.js";
import {useI18n} from "vue-i18n";
const router = useRouter()
const auth = useAuthStore()
const { t, locale } = useI18n()

async function handleLogout() {
    await auth.logout()
    router.push('/login')
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
