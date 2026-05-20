<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'
import { useI18n } from 'vue-i18n'

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

const router = useRouter()
const auth = useAuthStore()
const { t } = useI18n()

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
  <div class="admin-login">
    <div class="admin-login__card">
      <div class="admin-login__logo">🎭</div>
      <h1 class="admin-login__title">{{ t('main.title') }}</h1>
      <p class="admin-login__subtitle">{{ t('login.subtitle') }}</p>

      <form @submit.prevent="handleLogin">
        <div class="admin-form-group">
          <label for="email">{{ t('login.email') }}</label>
          <input
            id="email"
            v-model="email"
            type="email"
            placeholder="votre@email.com"
            required
            :disabled="loading"
          />
        </div>
        <div class="admin-form-group">
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

        <p v-if="error" class="admin-login__error">{{ error }}</p>

        <button type="submit" class="admin-btn admin-btn--primary" :disabled="loading">
          {{ loading ? t('login.connexion') : t('login.sign-in') }}
        </button>
      </form>
    </div>
  </div>
</template>
