<script setup>
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()
</script>

<template>
    <div class="lang-switcher">
        <button aria-label="Français" @click="locale = 'fr'" :class="{ active: locale === 'fr' }">FR</button>
        <span>|</span>
        <button aria-label="English" @click="locale = 'en'" :class="{ active: locale === 'en' }">EN</button>
    </div>
</template>

<style scoped lang="scss">
@use '/assets/styles/utils/variables' as *;

    .lang-switcher {
        display: flex;
        align-items: center;
        gap: $size-6;
        color: var(--border-color);

        button {
            font-family: "Raleway", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: $size-12;
            font-weight: 800;
            color: var(--border-color);
        }

        .active {
            color: var(--title);
        }
    }
</style>
