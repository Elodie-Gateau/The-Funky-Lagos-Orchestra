<script setup>
import { Icon } from '@iconify/vue'
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();
</script>

<template>

    <Icon icon="arcticons:emoji-copyright-sign" />
    <span>2026</span>
    <span> - {{ t('main.title') }}</span>
    <span> - {{ t('footer.mentions')}}</span>
    <span> - {{ t('footer.made_part1')}}</span>
    <Icon icon="fa:music" />
    <span>{{ t('footer.made_part2')}}</span>

</template>

<style scoped>

</style>
