<script setup>
import {ref} from "vue";
import {useRouter} from "vue-router";
import {useAuthStore} from "../stores/auth.js";
import {useI18n} from "vue-i18n";

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

const router = useRouter()
const auth = useAuthStore()
const { t, locale } = useI18n()

async function handleLogin() {
    loading.value = true
    error.value = ''

    const success = await auth.login(email.value, password.value)
    if (success) {
        router.push('/admin')
    } else {
        error.value = 'Email ou mot de passe incorrect'
    }

    loading.value = false
}
</script>

<template>
<div class="login-page">
    <div class="login-card">
        <h1>{{ t('main.title') }}</h1>
        <h2>{{ t('login.subtitle') }}</h2>

        <form @submit.prevent="handleLogin">
            <div class="field">
                <label for="email">{{ t('login.email') }}</label>
                <input type="email"
                id="email"
                v-model="email"
                placeholder="votre@email.com"
                required
                :disabled="loading"/>
            </div>
            <div class="field">
                <label for="password">{{ t('login.password') }}</label>
                <input
                    id="password"
                    v-model="password"
                    type="password"
                    placeholder="••••••••"
                    required
                    :disabled="loading"
                />
            </div>

            <p class="error" v-if="error">{{ error }}</p>

            <button type="submit" :disabled="loading">
            {{ loading ? t('login.connexion')  : t('login.sign-in') }}
            </button>
        </form>
    </div>
</div>
</template>

<style scoped>

</style>
