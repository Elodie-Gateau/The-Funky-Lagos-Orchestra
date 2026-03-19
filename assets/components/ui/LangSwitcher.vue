<script setup>

</script>

<template>
    <div class="lang-switcher">
        <button @click="locale = 'fr'" :class="{ active: locale === 'fr' }">FR</button>
        <span>|</span>
        <button @click="locale = 'en'" :class="{ active: locale === 'en' }">EN</button>
    </div>
</template>

<style scoped>

</style>
