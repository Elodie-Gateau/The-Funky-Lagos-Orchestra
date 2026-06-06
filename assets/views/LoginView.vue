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
      <div class="admin-login__logo"><img src="/assets/images/logo.svg" alt="Logo"></div>
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

        <button type="submit" :aria-label="loading ? t('login.connexion') : t('login.sign-in')" class="admin-btn admin-btn--primary" :disabled="loading">
          {{ loading ? t('login.connexion') : t('login.sign-in') }}
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped lang="scss">
@use '../styles/utils/variables' as *;
@use '../styles/utils/breakpoints' as *;

.admin-login {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: $color-brown;
  padding: $size-16;

  &__card {
    background: $color-white;
    border-radius: $size-8;
    box-shadow: 0 8px 32px rgba($color-brown, .3);
    padding: $size-32 $size-24;
    width: 100%;
    max-width: 420px;

    @media (min-width: $sm) {
      padding: $size-40 $size-32;
    }
  }

  &__logo {
    width: $size-56;
    height: $size-56;
    border-radius: 50%;
    background: $color-gold;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: $size-24;
    margin: 0 auto $size-20;
  }

  &__title {
    font-family: "Raleway", system-ui, sans-serif;
    font-weight: 900;
    font-size: $size-20;
    color: $color-brown;
    text-transform: uppercase;
    text-align: center;
    letter-spacing: .04em;
    margin: 0 0 $size-6;

    @media (min-width: $sm) {
      font-size: $size-22;
    }
  }

  &__subtitle {
    font-size: $size-14;
    color: $color-gray-300;
    text-align: center;
    margin: 0 0 $size-28;
    font-weight: 400;
    text-transform: none;
    font-family: "Nunito", system-ui, sans-serif;
  }

  form {
    display: flex;
    flex-direction: column;
    gap: $size-16;
    margin: 0;

    .admin-form-group input { background: $color-gray-100; }
  }

  &__error {
    padding: $size-10 $size-14;
    border-radius: $size-6;
    background: rgba($color-red, .1);
    color: $color-red;
    font-size: $size-14;
    text-align: center;
  }

  .admin-btn--primary {
    width: 100%;
    justify-content: center;
    margin-top: $size-8;
    padding: $size-14 $size-20;
    font-size: $size-14;
  }
}
</style>
