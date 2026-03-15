<script setup>
import { useI18n } from 'vue-i18n'
const { t, locale } = useI18n()
</script>

<template>
 <main>
     <h1>{{ t('main.title') }}</h1>
     <p>{{ t('home.subtitle') }}</p>

     <div class="lang-switcher">
         <button @click="locale = 'fr'" :class="{ active: locale === 'fr' }">🇫🇷</button>
         <button @click="locale = 'en'" :class="{ active: locale === 'en' }">🇬🇧</button>
     </div>
 </main>
</template>

<style scoped>

</style>
