<script setup>
import {useI18n} from "vue-i18n";
import NavMenu from "../ui/NavMenu.vue";
import LangSwitcher from "../ui/LangSwitcher.vue";
import MobileMenu from "./MobileMenu.vue";

const { t, locale } = useI18n()
</script>

<template>
    <div class="sticky-nav">
        <div class="logo">
            <img src="/assets/images/logo.svg" :alt="t('main.logo')">
            <NavMenu/>
            <LangSwitcher/>
        </div>
    </div>
</template>

<style scoped lang="scss">
@use '/assets/styles/utils/breakpoints' as *;
.sticky-nav {
    display: none;
    position: sticky;
    top: 0;
    z-index: 100;
    width: 100%;
}

@media (min-width: $md) {
    .sticky-nav {
        display: block;
    }

}

.logo {
    display: flex;
    width: 100%;
    height: 5rem;
    border-bottom: solid 2px grey;

    img {
        width: 30%;
    }
}

</style>
