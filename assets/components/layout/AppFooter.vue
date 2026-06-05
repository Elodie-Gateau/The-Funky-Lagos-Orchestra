<script setup>
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();
</script>

<template>
<section class="footer">
    <p>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z"/><path d="M11 7.5v9l-6-4.5 6-4.5z" style="display:none"/><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/><path d="M15 9.354a4 4 0 1 0 0 5.292" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        <span>2026</span>
        <span> - {{ t('main.title') }}</span>
        <span> - {{ t('footer.mentions')}}</span>
        <span> - {{ t('footer.made_part1')}}</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/></svg>
        <span>{{ t('footer.made_part2')}}</span>
    </p>
</section>
</template>

<style scoped lang="scss">
@use "/assets/styles/utils/variables" as *;
.footer {
    background-color: var(--background-footer);
    font-family: "Raleway", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    height: $size-80;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: $size-14;
    color: color-mix(in srgb, var(--background) 50%, transparent);

    p {
        text-align: center;
        svg {
            color: color-mix(in srgb, var(--background) 50%, transparent);
            width: $size-16;
        }

        span:nth-child(3) {
            font-weight: 700;
            color: var(--gradient-end);
        }
    }
}
</style>
