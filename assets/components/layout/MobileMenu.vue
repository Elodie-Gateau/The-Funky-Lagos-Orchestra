<script setup>
import { X, Menu } from '@lucide/vue';
import { ref, onMounted, onUnmounted } from 'vue'
import NavMenu from "../ui/NavMenu.vue";
import LangSwitcher from "../ui/LangSwitcher.vue";

const isOpen = ref(false)
</script>

<template>
        <div class="mobile-menu">
            <LangSwitcher v-if="!isOpen" />
            <div>
                <Menu v-if="!isOpen" @click="isOpen = true" />
<!--                <img class="burger" v-if="!isOpen" @click="isOpen = true" src="/assets/images/logo.svg" alt="menu" />-->
            </div>
        </div>
        <div v-if="isOpen">
            <button @click="isOpen = false">
                <X :size="24" />
            </button>
        </div>
        <div v-if="isOpen">
            <NavMenu/>
        </div>

</template>

<style scoped lang="scss">
@use '/assets/styles/utils/variables' as *;
@use '/assets/styles/utils/breakpoints' as *;

.mobile-menu {
    background-color: var(--background);
    color: var(--title);
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    right: $size-10;
    top: 0;
    padding: $size-14;
    border-top: 1.5px solid var(--border-color);
    box-shadow: 0 -4px 20px rgba(42, 16, 0, .06);
    height: 10vh;
}
.burger {
    width: $size-38;
    cursor: pointer;
 }

@media(min-width: $md){
    .mobile-menu {
        display: none;
    }
}
</style>
