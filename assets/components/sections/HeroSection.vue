<script setup>
import {useI18n} from "vue-i18n";
import NavMenu from "../ui/NavMenu.vue";
import LangSwitcher from "../ui/LangSwitcher.vue";

const { t, locale } = useI18n()
</script>

<template>
    <section class="hero">
        <img src="/assets/images/logo.svg" :alt="t('main.logo')">
        <div class="title">
            <h1>{{ t('main.title_part1') }}
                <span>{{ t('main.title_part2') }}</span>
                <span>{{ t('main.title_part3') }}</span>
            </h1>
            <h2>{{ t('home.subtitle') }}</h2>
        </div>
        <div class="menu">
            <div class="nav">
                <NavMenu/>
            </div>
<!--            <LangSwitcher/>-->
        </div>
    </section>
</template>

<style scoped lang="scss">
@use '/assets/styles/utils/breakpoints' as *;
@use '/assets/styles/utils/variables' as *;

.nav {
    display:none;
}

.hero {
    height: 90vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 1.25rem;

    .title {

        h1 {
            text-align: center;
            display: flex;
            flex-direction: column;
            row-gap: 3.2px;
            line-height: 1;
            span {
                display: block;
            }

            span:nth-child(2) {
                color: var(--title-secondary);
                font-size: $size-22;
                margin-top: $size-6;
                font-weight: 800;
            }
        }

        h2 {
            text-align: center;
            font-size: $size-14;
            font-weight: 600;
            color: var(--title-secondary);
            margin-top: $size-10;
        }
    }
}

@media (min-width: $md) {
    .nav {
        display: block;
    }
}

img {
    max-height: 60vh;
}

</style>
