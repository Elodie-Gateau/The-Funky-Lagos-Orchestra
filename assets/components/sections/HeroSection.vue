<script setup>
import {useI18n} from "vue-i18n";
import NavMenu from "../ui/NavMenu.vue";
import LangSwitcher from "../ui/LangSwitcher.vue";

const { t, locale } = useI18n()
</script>

<template>
    <div class="hero">
        <img src="/assets/images/logo.svg" :alt="t('main.logo')">
        <div class="title">
            <h1>{{ t('main.title') }}</h1>
            <p>{{ t('home.subtitle') }}</p>
        </div>
        <div class="menu">
            <div class="nav">
                <NavMenu/>
            </div>
            <LangSwitcher/>
        </div>
    </div>
</template>

<style scoped lang="scss">
@use '/assets/styles/utils/breakpoints' as *;

.nav {
    display:none;
}

@media (min-width: $md) {
    .nav {
        display: block;
    }
}

</style>
