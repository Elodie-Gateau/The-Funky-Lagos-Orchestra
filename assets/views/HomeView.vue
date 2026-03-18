<script setup>
import { useI18n } from 'vue-i18n'
const { t, locale } = useI18n()
</script>

<template>
 <main>
     <h1>{{ t('main.title') }}</h1>
     <p>{{ t('home.subtitle') }}</p>
 </main>
</template>

<style scoped>

</style>
