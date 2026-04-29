<script setup>
import { Icon } from '@iconify/vue'
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();
</script>

<template>
<section class="footer">
    <p>
    <Icon icon="ph:copyright-bold" />
    <span>2026</span>
    <span> - {{ t('main.title') }}</span>
    <span> - {{ t('footer.mentions')}}</span>
    <span> - {{ t('footer.made_part1')}}</span>
    <Icon icon="fa:music" />
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
